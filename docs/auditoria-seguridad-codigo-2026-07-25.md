# Auditoría de seguridad y calidad de código — CBN Sol

**Fecha:** 2026-07-25
**Alcance:** código propio del proyecto (`wp-content/mu-plugins/cbn-core`, `wp-content/themes/cbn-theme`, `compose.yaml`, `.github/workflows`, `scripts/`, assets JS).
**Excluido:** núcleo de WordPress, plugins de terceros (Akismet, Hello Dolly) y temas por defecto, salvo por su presencia como superficie de ataque.
**Nota:** revisión estática, sin ejecución.

> **Estado (2026-07-25):** cerrados **A-1, A-2, A-3, A-4, M-1, M-3, M-4, M-6,
> M-7, M-8 y M-9**, más la parte de código de **M-2**. Ver
> `docs/despliegue-produccion.md` y el registro en `AGENT_CHANGELOG.md`.
>
> **Sigue abierto y requiere acción fuera del código:** la parte de
> infraestructura de **M-2** (transporte SMTP autenticado y registros SPF,
> DKIM y DMARC en el DNS). Sin eso, la cabecera `From` corregida no basta
> para que los correos entreguen. También siguen abiertos **M-5**
> (minimización de datos: DNI, peso y estatura de menores), **C-4**
> (acoplamiento a slugs) y **C-11** (cobertura de pruebas).
>
> Corrección posterior a la primera entrega: el hallazgo **C-1** (consultas
> `posts_per_page => -1`) estaba sobrevalorado. A la escala real del club
> —unos 15 equipos y cientos de partidos— es irrelevante. Trátese como
> cosmético, no como deuda a pagar.

---

## Resumen ejecutivo

La base de código está **notablemente por encima de la media** de un tema WordPress a medida: escapado de salida sistemático (248 `esc_html`, 155 `esc_url`, 58 `esc_attr`), cero SQL directo, cero `eval`/`innerHTML`, nonces en todos los formularios, honeypot, `wp_validate_redirect` en los retornos y un modelo de capabilities propio para los CPT. No se ha encontrado ninguna vulnerabilidad de inyección clásica (SQLi, XSS reflejado, RCE, LFI).

Los riesgos reales están en otro plano:

1. **Falta de rate limiting en dos endpoints públicos que envían email** (`admin-post.php`), con datos personales de menores en uno de ellos.
2. **Lista negra de capabilities incompleta** en el control de acceso: no cubre gestión de usuarios ni de plugins/temas.
3. **Ausencia total de endurecimiento de plataforma**: sin cabeceras de seguridad, sin bloqueo de XML-RPC, sin protección de `debug.log`, sin restricción de enumeración de usuarios vía REST.
4. **Cálculo de edad frágil** en el formulario de inscripción, del que depende la exigencia de consentimiento del tutor legal.

Se listan **29 hallazgos**: 0 críticos, 6 altos, 10 medios, 13 bajos/mejoras.
Los cinco últimos (**N-1** a **N-5**) salieron de una tercera pasada motivada por
la revisión que Banco Sabadell va a hacer de la web antes de dar de alta el TPV;
están en la sección 3 bis, junto con la checklist previa a enviarles el enlace.

**Si solo se lee un párrafo de este informe, que sea este:** lo que con más
probabilidad hará fallar la revisión del banco no es ninguna vulnerabilidad
técnica, sino **N-3** — la web no tiene aviso legal, política de privacidad,
política de cookies, condiciones de compra ni política de devoluciones. Las
páginas existen vacías.

---

## 1. Seguridad — severidad ALTA

### A-1 · Formularios públicos sin límite de envíos (abuso de correo / DoS de buzón)

**Archivos:** `wp-content/themes/cbn-theme/inc/contact-content.php:147`, `inc/registration-content.php:105`

Ambos handlers están registrados en `admin_post_nopriv_*` y sus únicas barreras son un nonce y un honeypot. El detalle importante es que **el nonce de WordPress para usuarios anónimos es idéntico para todos los visitantes** dentro de su ventana de validez (~12-24 h), porque se genera con `user_id = 0` y token de sesión vacío. Es decir: un bot descarga `/contacto/` una vez, extrae el nonce y puede lanzar miles de POST válidos durante un día entero. El honeypot solo filtra bots ingenuos.

Consecuencias: inundación del buzón `admin_email`, agotamiento de la cuota SMTP del hosting, posible bloqueo del dominio por el proveedor, y — vía el formulario de inscripción — generación masiva de correos etiquetados como "datos personales, tratar según RGPD".

**Solución propuesta**

- Rate limit por IP + por dirección de correo con transients, antes de `wp_mail()`:

  ```php
  $bucket = 'cbn_rl_' . md5($action . '|' . $ip);
  $hits   = (int) get_transient($bucket);
  if ($hits >= 5) { /* redirigir con estado 'error' */ }
  set_transient($bucket, $hits + 1, HOUR_IN_SECONDS);
  ```

- Rechazar envíos con menos de ~3 s desde el render del formulario (campo `timestamp` firmado con `wp_hash()`).
- Activar Akismet (ya está instalado) sobre estos dos handlers vía `akismet_comment_check` o su API, o añadir un captcha sin cookies (hCaptcha / Turnstile) solo en inscripción.
- Registrar los rechazos con `error_log()` para poder detectar campañas.

---

### A-2 · Lista negra de capabilities incompleta → posible escalada de privilegios

**Archivo:** `wp-content/mu-plugins/cbn-core/inc/access-control.php:104-152`

`cbn_enforce_administrator_only_writes()` deniega en runtime un conjunto fijo de capabilities a todo usuario sin `manage_options`. La lista cubre bien contenido (`edit_posts`, `publish_pages`, `upload_files`…) pero **omite por completo**:

- Gestión de usuarios: `list_users`, `create_users`, `edit_users`, `delete_users`, **`promote_users`**
- Plugins y temas: `install_plugins`, `activate_plugins`, `edit_plugins`, `edit_themes`, `switch_themes`, `update_plugins`, `update_themes`, `update_core`
- Otros: `export`, `import`, `unfiltered_html`, `edit_files`

Un rol heredado o creado por un plugin que tenga `promote_users` pero no `manage_options` podría **ascenderse a administrador** y saltarse todo el modelo. Es un escenario poco probable en la instalación actual, pero el control está diseñado precisamente para blindar ese caso.

**Solución propuesta**

Invertir la lógica: en lugar de lista negra, aplicar **lista blanca**. Para cualquier usuario logueado sin `manage_options`, denegar toda capability que no esté en un conjunto mínimo explícito (`read`, `level_0`).

```php
$allowed = ['read' => true, 'level_0' => true];
foreach ($allcaps as $cap => $granted) {
    if (!isset($allowed[$cap])) {
        $allcaps[$cap] = false;
    }
}
```

Esto es a prueba de futuro: cualquier capability nueva introducida por un plugin queda denegada por defecto. Complementar con las constantes `DISALLOW_FILE_EDIT` y `DISALLOW_FILE_MODS` en `wp-config.php` de producción.

---

### A-3 · Cálculo de edad frágil: de él depende el consentimiento del tutor legal

**Archivo:** `wp-content/themes/cbn-theme/inc/registration-content.php:246-277`

```php
$birth_date = new DateTimeImmutable($raw_date);   // acepta "yesterday", "-20 years"…
$today      = new DateTimeImmutable(wp_date('Y-m-d'));
```

Tres problemas encadenados:

1. `new DateTimeImmutable($string)` acepta **cualquier expresión relativa** de PHP. `sanitize_text_field` no lo impide. Un POST con `cbn_reg_player_birthdate=-20 years` produce una edad de 20 años válida, se salta la rama de menor de edad y el email al club muestra literalmente `Fecha de nacimiento: -20 years`.
2. **Mezcla de zonas horarias:** `wp_date()` usa la zona del sitio; `new DateTimeImmutable()` usa la zona por defecto de PHP (UTC en la mayoría de instalaciones WP). En el límite de día esto puede desplazar la edad un día — justo el borde de los 18 años que determina si se exige el consentimiento del tutor.
3. El valor crudo `$player_birthdate_raw` se interpola sin normalizar en el cuerpo del correo (línea 213).

Impacto: un formulario que existe precisamente para proteger datos de menores puede clasificar mal a un menor como adulto y omitir la validación del tutor. Es un riesgo de cumplimiento RGPD, no solo un bug.

**Solución propuesta**

```php
$birth_date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw_date, wp_timezone());
$errors = DateTimeImmutable::getLastErrors();
if (!$birth_date || !empty($errors['warning_count']) || !empty($errors['error_count'])) {
    return null;
}
$today = new DateTimeImmutable('today', wp_timezone());
```

Y usar `$birth_date->format('Y-m-d')` (ya normalizado) en el cuerpo del email, nunca el valor crudo.

---

### A-4 · `wp-content/debug.log` accesible por HTTP en producción

**Archivo:** `compose.yaml:19-24`

```
define('WP_DEBUG_LOG', true);
```

`WP_DEBUG_LOG` a `true` escribe en `wp-content/debug.log`, servido directamente por Apache. No hay ningún `.htaccess` en el repositorio (`find . -name ".htaccess"` no devuelve nada propio). El log expone rutas absolutas del servidor, prefijos de tabla, consultas y trazas de error — información de reconocimiento de primer nivel.

Aunque `compose.yaml` está pensado para local, `WORDPRESS_DEBUG` y `WP_DEBUG_LOG` no están condicionados al entorno y el archivo no documenta que no debe usarse en despliegue.

**Solución propuesta**

- En producción: `WP_DEBUG = false`, o si se necesita log, redirigirlo fuera del docroot con `define('WP_DEBUG_LOG', '/var/log/wp/cbn.log')`.
- Añadir al `.htaccess` (o equivalente nginx) del despliegue:

  ```apache
  <FilesMatch "\.(log|sql|bak|orig|ini|env)$">
    Require all denied
  </FilesMatch>
  ```

- Documentar explícitamente en `docs/LOCAL_DOCKER.md` que `compose.yaml` es **solo desarrollo**.

---

## 2. Seguridad — severidad MEDIA

### M-1 · Sin endurecimiento de plataforma (cabeceras, XML-RPC, enumeración de usuarios)

Ninguna búsqueda de `Content-Security-Policy`, `X-Frame-Options`, `Referrer-Policy`, `Strict-Transport-Security`, `xmlrpc`, `rest_endpoints` ni `the_generator` devuelve resultados en el código propio.

Falta, por tanto:

- **Cabeceras de seguridad.** Sin `X-Frame-Options`/`frame-ancestors` el sitio es enmarcable (clickjacking sobre el formulario de inscripción). Sin `Referrer-Policy` se filtran URLs completas a terceros.
- **XML-RPC abierto** (`/xmlrpc.php`): vector clásico de fuerza bruta amplificada (`system.multicall`) y de DDoS por pingback.
- **Enumeración de usuarios vía REST:** `/wp-json/wp/v2/users` y `/?author=1` revelan los `user_login` de los administradores. Combinado con `wp-login.php` accesible y enlazado desde el footer ("Acceso administrador"), facilita el ataque de credenciales.
- **`<meta name="generator">`** expone la versión exacta de WordPress.

**Solución propuesta** — un solo mu-plugin de endurecimiento:

```php
add_action('send_headers', function (): void {
    if (is_admin()) { return; }
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
});

add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'wp_generator');

add_filter('rest_endpoints', function (array $endpoints): array {
    if (!current_user_can('list_users')) {
        unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});
```

Para CSP, empezar en modo `Content-Security-Policy-Report-Only` porque el tema usa `<style>`/`<script>` inline (`header.php:12`, `wp_add_inline_script` en `inc/assets.php:110`) y requerirá nonces.

---

### M-2 · `wp_mail()` sin cabecera `From` ni transporte controlado

**Archivos:** `inc/contact-content.php:225`, `inc/registration-content.php:236`

Solo se pasa `Reply-To`. Sin `From` explícito, WordPress usa `wordpress@<dominio-del-servidor>`, que casi nunca pasa SPF/DKIM/DMARC. Resultado práctico: **los mensajes de contacto e inscripción acaban en spam o se descartan silenciosamente**. Como no hay persistencia en base de datos (decisión de privacidad correcta), un fallo de entrega significa **pérdida definitiva** de una solicitud de inscripción.

Además, `$sent === false` solo redirige con estado `error`; no se registra nada.

**Solución propuesta**

- Configurar SMTP autenticado del dominio del club (`phpmailer_init` o plugin SMTP), con `From: web@cbnavalcarnero.es`.
- Añadir `add_filter('wp_mail_from', ...)` y `wp_mail_from_name`.
- `error_log()` en cada fallo de `wp_mail()`, y `add_action('wp_mail_failed', ...)` para capturar el `WP_Error` de PHPMailer.
- Considerar una copia de respaldo (p. ej. envío a un segundo buzón) mientras no exista almacenamiento.

---

### M-3 · Sin límite de longitud en los campos de texto

`sanitize_textarea_field()` no trunca. Un POST con un `cbn_contact_message` de varios MB genera un correo del mismo tamaño; combinado con **A-1** es un multiplicador de abuso.

**Solución:** validar longitudes máximas server-side antes de enviar (nombre ≤ 120, mensaje ≤ 5.000, teléfono ≤ 30, DNI ≤ 12) y devolver estado `invalid`.

---

### M-4 · `Reply-To` construido con el nombre facilitado por el usuario

```php
['Reply-To: ' . $name . ' <' . $email . '>']
```

`sanitize_text_field()` colapsa `\r\n` y `wp_strip_all_tags()` elimina lo que parezca una etiqueta, así que **la inyección de cabeceras está mitigada de hecho**. Aun así, la construcción depende de un efecto colateral de la sanitización, no de una validación explícita.

**Solución:** usar solo la dirección validada (`Reply-To: <$email>`) o pasar el nombre por `mb_substr(preg_replace('/[^\p{L}\p{N} .\'-]/u', '', $name), 0, 80)`.

---

### M-5 · Datos de menores en correo en texto plano, sin política de retención

**Archivo:** `inc/registration-content.php:180-235`

El formulario recoge nombre completo, fecha de nacimiento, sexo, **DNI**, estatura, peso del jugador/a e identidad y contacto del tutor. Todo viaja en un email de texto plano al buzón `admin_email`. La decisión de no persistir en base de datos es correcta y está bien documentada, pero el correo se convierte en el sistema de registro de facto, sin cifrado en reposo, sin control de acceso y sin borrado programado.

`PRIVACY_NOTES.md` clasifica DNI y datos del tutor como "Administrative Data — must stay private", lo que es coherente, pero no hay control técnico que lo garantice.

**Solución propuesta**

- Hacer el DNI **opcional y diferido**: no pedirlo en el alta web, solicitarlo solo tras confirmar plaza y por un canal controlado. Igual para estatura/peso, que no son necesarios para tramitar la solicitud.
- Exigir TLS obligatorio en el envío SMTP y buzón con 2FA.
- Documentar en `PRIVACY_NOTES.md` un plazo de retención del buzón y responsable de borrado.
- Añadir en el pie del correo la referencia a la base legal y al plazo de conservación.

---

### M-6 · Contraseñas por defecto en `compose.yaml`

```yaml
WORDPRESS_DB_PASSWORD: ${WORDPRESS_DB_PASSWORD:-cbn_password}
MARIADB_ROOT_PASSWORD: ${WORDPRESS_DB_ROOT_PASSWORD:-root_password}
```

Los _fallbacks_ permiten levantar la pila sin `.env` y con credenciales conocidas. `.env` está correctamente en `.gitignore` y no está versionado (verificado con `git ls-files`), lo cual es lo importante — pero el valor por defecto invita al error.

**Solución:** eliminar los `:-` de los tres secretos para que `docker compose up` **falle** si no hay `.env`.

---

### M-7 · Puertos publicados en todas las interfaces

```yaml
ports:
  - "${WORDPRESS_PORT:-8080}:80"
  - "${PHPMYADMIN_PORT:-8081}:80"
```

Docker publica en `0.0.0.0`. En un portátil conectado a una red compartida, WordPress en modo debug y **phpMyAdmin con credenciales preinyectadas** (`PMA_USER`/`PMA_PASSWORD`) quedan accesibles desde la red local. El perfil `tools` limita cuándo arranca, no a quién escucha.

**Solución:** prefijar con loopback → `"127.0.0.1:${WORDPRESS_PORT:-8080}:80"`.

---

### M-8 · Temas y plugins por defecto presentes

`wp-content/themes/` contiene `twentytwentythree`, `twentytwentyfour` y `twentytwentyfive` además de `cbn-theme`; `wp-content/plugins/` contiene `hello.php`. Están en `.gitignore`, así que es estado local, pero si el despliegue replica el directorio, son superficie de ataque sin uso (los temas por defecto han tenido CVEs históricos y se actualizan en bloque).

**Solución:** conservar un único tema por defecto como _fallback_ de emergencia, eliminar el resto y Hello Dolly en producción; documentarlo en el runbook de despliegue.

---

### M-9 · Sin auditoría de dependencias ni actualizaciones automatizadas

`.github/workflows/` contiene únicamente `ci.yml`. No hay Dependabot, ni `npm audit`, ni escaneo de PHP. `vite`, `gsap`, `lenis` y `swiper` están fijados a versiones exactas y se quedarán congeladas indefinidamente.

Además el workflow carece de bloque `permissions:`, por lo que el `GITHUB_TOKEN` hereda permisos amplios por defecto.

**Solución**

- `.github/dependabot.yml` con ecosistemas `npm` y `github-actions`, cadencia semanal.
- Paso `npm audit --audit-level=high` en CI.
- Añadir al workflow:

  ```yaml
  permissions:
    contents: read
  ```

---

## 3. Calidad de código y rendimiento

### C-1 · Consultas sin límite superior (MEDIA)

| Archivo                    | Línea | Problema                                                                                                                 |
| -------------------------- | ----- | ------------------------------------------------------------------------------------------------------------------------ |
| `single-cbn_match.php`     | 137   | `posts_per_page => -1` sobre **todos** los partidos, en **cada** vista de partido, solo para calcular anterior/siguiente |
| `inc/sponsors-content.php` | 87    | `posts_per_page => -1` sobre todos los sponsors                                                                          |
| `inc/sports-content.php`   | 65    | `posts_per_page => 50` con reordenación posterior en PHP                                                                 |

El caso de `single-cbn_match.php` es el más costoso: carga todos los partidos publicados, invoca `cbn_get_match_meta()` (≈8 lecturas de meta) por cada uno y los reordena en PHP, todo para obtener dos enlaces. Con 3-4 temporadas cargadas son cientos de posts por página vista.

**Solución propuesta**

- Sustituir la navegación por dos consultas acotadas (`posts_per_page => 1`) con `meta_query` sobre `cbn_match_date` (`<` y `>` respecto al partido actual) y `orderby => meta_value`.
- Para sponsors, paginar o cachear el resultado filtrado con un transient invalidado en `save_post_cbn_sponsor`.
- Añadir `'fields' => 'ids'` donde solo se necesiten IDs.

### C-2 · `meta_key` y `meta_query` sobre la misma clave (BAJA)

**Archivo:** `inc/sports-content.php:150-195`

Se declara `meta_key => 'cbn_match_date'` para ordenar y, además, una cláusula `meta_query` sobre la misma clave. `WP_Meta_Query::parse_query_vars()` fusiona ambas y genera un **JOIN redundante** sobre `wp_postmeta`. Funciona, pero es frágil ante cambios y penaliza la consulta.

**Solución:** usar cláusulas nombradas.

```php
'meta_query' => [
    'fecha' => ['key' => 'cbn_match_date', 'value' => $today, 'compare' => '>=', 'type' => 'DATE'],
    'estado' => ['key' => 'cbn_match_status', 'value' => ['scheduled','live'], 'compare' => 'IN'],
],
'orderby' => ['fecha' => 'ASC'],
```

y eliminar `meta_key`/`meta_type` del nivel superior.

### C-3 · Enqueues duplicados entre el registro central y las plantillas (BAJA)

`inc/assets.php:126-175` (`cbn_enqueue_pista_viva_surface_assets`) ya registra `cbn-sol-contact`, `cbn-sol-registration`, `cbn-sol-shop` y `cbn-sol-system`. Las plantillas `page-contacto.php:15`, `page-inscripcion.php:16`, `page-tienda.php:16`, `search.php:14` y `404.php:12` vuelven a hacer `wp_enqueue_style()` **con el mismo handle** desde un `add_action(..., 20)`.

Como el handle ya está registrado, la segunda llamada es inocua — es **código muerto** que además duplica la lógica de versionado (`filemtime`) en cinco sitios. Cuando alguien cambie la ruta en `inc/assets.php`, las plantillas quedarán desincronizadas sin fallar visiblemente.

**Solución:** eliminar los bloques `add_action('wp_enqueue_scripts', ...)` de las plantillas y dejar `inc/assets.php` como fuente única.

### C-4 · Acoplamiento a slugs literales (MEDIA — riesgo funcional)

Las relaciones entre plantilla, CSS y campos personalizados se resuelven por el _slug_ de la página:

- `inc/assets.php:130-146` → `is_page('el-club')`, `is_page('contacto')`, `is_page('inscripcion')`, `is_page('tienda')`
- `inc/contact-content.php:122` → `get_page_by_path('contacto')`
- `mu-plugins/cbn-core/inc/native-fields.php:38-52` → `'contacto' === $slug`, `'inscripcion' === $slug`, `'el-club' === $slug`

Si un administrador renombra el slug desde el escritorio, se pierden en silencio los estilos, los campos del meta box **y** los valores previamente guardados (en el siguiente guardado, `cbn_native_fields_save()` no encuentra el grupo y no persiste nada).

**Solución:** anclar a IDs guardados en opciones (`get_option('cbn_page_contacto')`) resueltos una sola vez, o priorizar siempre `get_page_template_slug()` sobre el slug, o registrar las páginas clave con un meta `_cbn_page_key` inmutable.

### C-5 · `get_the_content()` sin filtros para comprobar si hay contenido (BAJA)

`page-documentacion.php:35`, `page-tienda.php:40`, `page.php:39`, `single-cbn_document.php:35`, `single-cbn_team.php:42` usan `trim((string) get_the_content())` para decidir si renderizar el bloque de prosa. En un editor de bloques, una página con solo bloques vacíos devuelve los comentarios `<!-- wp:... -->` y **cuenta como contenido**.

**Solución:** `'' !== trim(wp_strip_all_tags(apply_filters('the_content', get_the_content())))`, o `has_blocks()` combinado con una comprobación de texto.

### C-6 · `date('Y')` en lugar de `wp_date('Y')` (BAJA)

**Archivo:** `footer.php:79`. Usa la zona horaria de PHP, no la del sitio. Cada 31 de diciembre el copyright puede mostrar el año equivocado durante unas horas.

### C-7 · Desreferencia sin comprobación de nulo (BAJA)

**Archivo:** `mu-plugins/cbn-core/inc/native-fields.php:284`

```php
get_post_type_object($related_post->post_type)->labels->singular_name
```

Si el CPT no está registrado (plugin desactivado, contenido huérfano), `get_post_type_object()` devuelve `null` → **error fatal en el editor**. Envolver en una comprobación con _fallback_ al `post_type` crudo.

### C-8 · Ordenación no sensible a acentos (BAJA)

`inc/sports-content.php:80` usa `strcasecmp()`. En castellano, "Álava" se ordena después de "Zamora". Usar `Collator` (intl) o `strcoll()` con locale `es_ES`, o normalizar con `remove_accents()` antes de comparar.

### C-9 · Filtro `gettext` global (BAJA — rendimiento)

**Archivo:** `mu-plugins/cbn-core/inc/admin-dashboard.php:280-330`

`cbn_admin_translate_essential_core_labels()` se engancha a `gettext` y `gettext_with_context`, que se disparan **en cada llamada a traducción** — miles por carga de wp-admin. El _early return_ por dominio limita el coste, pero el array de 30 entradas se reconstruye en cada invocación.

**Solución:** mover el array a `static` dentro de la función, o mejor, instalar el paquete de idioma `es_ES` (ya hay `.mo`/`.po` en `wp-content/languages/`) y eliminar el filtro.

### C-10 · Usuarios no administradores no pueden acceder a su perfil (BAJA)

`access-control.php:158-175` redirige a `home_url('/')` cualquier acceso a wp-admin de un usuario sin `manage_options`, incluido `profile.php`. No podrían cambiar su contraseña ni cerrar sesión desde el escritorio. Si el modelo es "solo administradores tienen cuenta", es coherente; si en el futuro existen cuentas de entrenador, hará falta permitir `profile.php`.

### C-11 · Cobertura de pruebas inexistente (MEDIA — calidad)

CI ejecuta `prettier --check`, `vite build` y `php -l`. Es decir: **formato y sintaxis**, nada de comportamiento. No hay PHPUnit, ni PHPCS con el estándar WordPress, ni los tests de Playwright automatizados (los artefactos de `.playwright-cli/` y `output/playwright/` son ejecuciones manuales).

La lógica que más se beneficiaría de tests es precisamente la sensible: `cbn_registration_calculate_age()` (ver **A-3**), `cbn_native_fields_sanitize_value()` y `cbn_sponsor_in_date_window()`.

**Solución propuesta**

- Añadir `squizlabs/php_codesniffer` + `wp-coding-standards/wpcs` con `--standard=WordPress-Extra` sobre `wp-content/mu-plugins` y `wp-content/themes/cbn-theme`. Detecta automáticamente escapado y nonces ausentes.
- Tests unitarios (Brain Monkey o PHPUnit con stubs de WP) para las funciones puras de validación.
- Promover los recorridos de Playwright existentes a un job de CI con `docker compose up`.

---

## 3 bis. Tercera pasada — hallazgos nuevos y revisión del banco

Contexto añadido por el club el 2026-07-25: **Banco Sabadell ha pedido el enlace
de la web para revisarla antes de dar de alta el TPV virtual (Redsys).** Eso
cambia las prioridades: además de la seguridad real importa la superficie
externa, que es lo que el revisor va a ver.

### N-1 · Los comentarios de WordPress estaban abiertos e invisibles (ALTA)

El tema no tiene `comments.php` y nunca llama a `comments_template()`, así que
los comentarios no se muestran en ninguna página. **Pero nunca se desactivaron.**
`wp-comments-post.php` seguía aceptando POST anónimos y `/wp-json/wp/v2/comments`
también, con `GET` divulgando además el nombre de quien comenta.

El efecto práctico es spam acumulándose en la base de datos, sin moderar y sin
que nadie lo vea, con enlaces salientes almacenados. Akismet está instalado pero
inerte: sin clave de API no filtra nada. Para una revisión externa, una web que
almacena spam es exactamente el tipo de señal que se penaliza.

**Estado: corregido.** `inc/hardening.php` cierra `comments_open` y `pings_open`,
retira el soporte de comentarios de todos los tipos públicos y elimina las rutas
REST de comentarios.

### N-2 · `wp-login.php` sin límite de intentos (ALTA — lo primero que se mira)

No hay bloqueo por intentos fallidos, ni retardo progresivo, ni segundo factor.
El endurecimiento ya aplicado unificó el mensaje de error (evita distinguir
"usuario inexistente" de "contraseña incorrecta") y cerró XML-RPC, que era el
vector de fuerza bruta amplificada. Pero **el ataque por diccionario contra el
formulario sigue siendo posible.**

**Solución propuesta** — requiere aprobación porque implica una dependencia:

- Un plugin mantenido de limitación de accesos, o
- limitar `/wp-login.php` en el servidor web (`mod_evasive`, `limit_req` de
  nginx, o reglas del WAF del hosting), o
- restringir `/wp-login.php` por IP si el club administra desde ubicaciones
  fijas.
- **Segundo factor obligatorio en todas las cuentas de administrador.** Con una
  única persona administradora es barato y es lo que más peso tiene en una
  revisión externa.

### N-3 · Textos legales inexistentes (BLOQUEANTE para el alta del TPV)

`scripts/bootstrap-local.ps1` crea las páginas `Privacidad` y `Aviso legal`
**vacías**, y no existen ni política de cookies, ni condiciones de compra, ni
política de devoluciones, ni condiciones de inscripción. El texto de
consentimiento de ambos formularios dice literalmente "texto legal definitivo
pendiente de aprobación".

Esto no es un detalle de estilo: para dar de alta un comercio en Redsys el banco
comprueba que la web publique identificación fiscal del titular, condiciones de
contratación, política de devoluciones y de privacidad. **Es lo que con más
probabilidad va a hacer fallar la revisión**, por delante de cualquier hallazgo
técnico de este informe.

Lo que sí existe ya, y sirve de base, está en
`docs/clubs/club-baloncesto-navalcarnero.md`: denominación oficial, CIF
G-80115298, domicilio y teléfono, extraídos de la web antigua del club.

**Solución propuesta:** encargar los textos a la asesoría del club antes de pedir
la revisión. No los redacte un agente ni el equipo técnico: son documentos con
efectos jurídicos y responsabilidad del club como responsable del tratamiento.

### N-4 · `rel="noreferrer"` sin `noopener` (BAJA)

`front-page.php:396` y `:402` usan `target="_blank" rel="noreferrer"`, mientras
que el resto del tema usa `rel="noopener noreferrer"`. En navegadores actuales
`noreferrer` ya implica `noopener`, así que el riesgo real es nulo; se anota por
consistencia y porque un escáner automático puede marcarlo.

### N-5 · Sin HSTS (MEDIA, condicionada a HTTPS)

No se ha añadido `Strict-Transport-Security` desde PHP a propósito: activarlo
antes de tener HTTPS correctamente configurado en todo el dominio deja el sitio
inaccesible y es difícil de revertir por el cacheo del navegador. Debe
configurarse **en el servidor web**, después de verificar el certificado, y
empezando con un `max-age` corto.

### Verificación

`scripts/check-security.ps1` reproduce estas comprobaciones contra una instancia
en marcha:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1 -BaseUrl https://cbnavalcarnero.es
```

Solo hace peticiones HTTP de lectura. **No** cubre fuerza bruta, configuración
TLS, entrega real de correo ni revisión jurídica de los textos.

### Checklist antes de enviar el enlace al banco

| #   | Requisito                                                   | Estado         |
| --- | ----------------------------------------------------------- | -------------- |
| 1   | HTTPS válido en todo el dominio, con redirección desde HTTP | Pendiente      |
| 2   | HSTS activo tras verificar el certificado (N-5)             | Pendiente      |
| 3   | Aviso legal con CIF, domicilio y datos registrales (N-3)    | **Bloqueante** |
| 4   | Política de privacidad y de cookies (N-3)                   | **Bloqueante** |
| 5   | Condiciones de compra y de devoluciones (N-3)               | **Bloqueante** |
| 6   | Límite de intentos de login y 2FA de administrador (N-2)    | Pendiente      |
| 7   | `WP_DEBUG` desactivado y `debug.log` no servido (A-4)       | Documentado    |
| 8   | Correo saliente entregando con SPF/DKIM/DMARC (M-2)         | Pendiente      |
| 9   | Comentarios cerrados (N-1)                                  | Corregido      |
| 10  | Cabeceras de seguridad y XML-RPC cerrado (M-1)              | Corregido      |
| 11  | Copias de seguridad con restauración probada                | Pendiente      |
| 12  | Temas y plugins sin usar eliminados (M-8)                   | Documentado    |

---

## 4. Lo que está bien hecho

Merece registrarse, porque condiciona el esfuerzo de corrección:

- **Escapado disciplinado.** No se ha encontrado ni un solo punto de salida sin escapar. Las pocas concatenaciones directas (`archive-cbn_match.php:59`, `single-cbn_match.php:236`…) son ternarios sobre valores booleanos internos, sin datos de usuario.
- **Cero SQL directo.** No hay una sola referencia a `$wpdb`.
- **JavaScript limpio.** Sin `innerHTML`, `document.write`, `eval` ni `new Function` en todo el tema. `admin.js` usa `replaceChildren()` y `createElement`. El único almacenamiento es `sessionStorage` para una preferencia de sonido.
- **Modelo de capabilities propio por CPT** con `map_meta_cap => true`, en lugar de reutilizar las de `post`. Es la decisión correcta y poco habitual.
- **`cbn_player` correctamente aislado**: `public => false`, `publicly_queryable => false`, `exclude_from_search => true`, `show_in_rest => false`, con el razonamiento documentado en el propio código (`post-types.php:12-18`). Además `search.php:29` vuelve a filtrarlo defensivamente.
- **Nonces, honeypot y `wp_validate_redirect()`** en ambos formularios públicos.
- **`.env` correctamente excluido** del control de versiones, con `.env.example` que documenta la política de secretos.
- **Decisión explícita de no persistir datos de formularios**, documentada en el docblock de `registration-content.php` y visible para el administrador en el Panel CBN.

---

## 5. Plan de acción sugerido

| Orden | Acción                                                                               | Hallazgos | Esfuerzo                   |
| ----- | ------------------------------------------------------------------------------------ | --------- | -------------------------- |
| 1     | Rate limiting + Akismet en los dos formularios públicos                              | A-1, M-3  | Medio                      |
| 2     | Endurecer `cbn_registration_calculate_age()` (formato estricto + zona horaria)       | A-3       | Bajo                       |
| 3     | Cambiar el control de acceso a lista blanca + `DISALLOW_FILE_EDIT`                   | A-2       | Bajo                       |
| 4     | mu-plugin de endurecimiento (cabeceras, XML-RPC, REST users, generator)              | M-1       | Bajo                       |
| 5     | Configurar SMTP autenticado con `From` del dominio + logging de fallos               | M-2, A-4  | Medio                      |
| 6     | Runbook de despliegue: `WP_DEBUG=false`, `.htaccess`, retirar temas/plugins sin usar | A-4, M-8  | Bajo                       |
| 7     | Acotar las consultas `posts_per_page => -1`                                          | C-1       | Medio                      |
| 8     | PHPCS/WPCS + Dependabot + `npm audit` en CI                                          | M-9, C-11 | Bajo                       |
| 9     | Eliminar enqueues duplicados y desacoplar de los slugs                               | C-3, C-4  | Medio                      |
| 10    | Revisar minimización de datos en el formulario de inscripción (DNI, peso, estatura)  | M-5       | Bajo (decisión de negocio) |

Los puntos 2, 3, 4, 6 y 8 son cambios pequeños y de alto retorno: se pueden abordar en una sola pasada.

---

_Auditoría de revisión estática. No sustituye a una prueba de penetración sobre el entorno desplegado, ni a una revisión jurídica del tratamiento de datos de menores._
