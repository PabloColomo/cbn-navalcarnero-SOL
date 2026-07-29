# Runbook de despliegue a producción

**Creado:** 2026-07-25
**Origen:** hallazgos A-4, M-6, M-7 y M-8 de `docs/auditoria-seguridad-codigo-2026-07-25.md`.

Este documento cubre lo que **no** viaja en el repositorio: la configuración del
entorno donde se sirve el sitio. El endurecimiento que sí es código ya está en
`wp-content/mu-plugins/cbn-core/inc/hardening.php` y se aplica solo.

> `compose.yaml` es **exclusivamente para desarrollo local**. No lo uses como
> base de un despliegue. Levanta WordPress en modo depuración, publica
> phpMyAdmin y trae contraseñas por defecto.

---

## 1. Antes de subir nada

- [ ] SSL activo y forzado en todo el dominio (no solo en `wp-login.php`).
- [ ] Copias de seguridad configuradas y **restauración probada una vez**.
- [ ] Acceso al panel del hosting y al DNS documentado, con 2FA.
- [ ] Buzón `admin_email` operativo, con 2FA, y con alguien del club revisándolo.

## 2. Constantes de `wp-config.php`

```php
// Depuración: nunca activa en producción.
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', false);
define('SCRIPT_DEBUG', false);

// Entorno: activa los avisos de WordPress y bloquea acciones peligrosas.
define('WP_ENVIRONMENT_TYPE', 'production');

// Bloquea el editor de archivos y la instalación/actualización desde wp-admin.
// DISALLOW_FILE_EDIT ya lo define el mu-plugin de endurecimiento; se repite
// aquí por si algún día se despliega sin él.
define('DISALLOW_FILE_EDIT', true);
// Actívalo solo si las actualizaciones se gestionan por despliegue, no a mano.
// define('DISALLOW_FILE_MODS', true);

// Fuerza HTTPS en el login y en el escritorio.
define('FORCE_SSL_ADMIN', true);

// Sal criptográfica: regenerar en https://api.wordpress.org/secret-key/1.1/salt/
// Nunca reutilizar las de local ni las de otro proyecto.
```

Si necesitas log de errores puntualmente, escríbelo **fuera del docroot**:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', '/var/log/wordpress/cbn.log');
```

Nunca `WP_DEBUG_LOG = true` a secas: eso escribe en `wp-content/debug.log`, que
el servidor sirve por HTTP y expone rutas, prefijos de tabla y trazas.

## 3. Reglas de servidor

### Apache — añadir al `.htaccess` del docroot

```apache
# Archivos que nunca deben servirse.
<FilesMatch "\.(log|sql|bak|orig|swp|ini|env|dist|json\.bak)$">
  Require all denied
</FilesMatch>

# Archivos de proyecto que no pintan nada en el docroot.
<FilesMatch "^(readme\.html|license\.txt|wp-config-sample\.php|composer\.(json|lock)|package(-lock)?\.json)$">
  Require all denied
</FilesMatch>

# XML-RPC: el mu-plugin ya lo desactiva a nivel de WordPress; esto evita
# incluso el coste de arrancar PHP.
<Files "xmlrpc.php">
  Require all denied
</Files>

# Nada de PHP ejecutable dentro de uploads.
<Directory "/ruta/al/docroot/wp-content/uploads">
  <FilesMatch "\.(php|phtml|php\d)$">
    Require all denied
  </FilesMatch>
</Directory>
```

### Nginx — equivalente

```nginx
location ~* \.(log|sql|bak|orig|swp|ini|env)$ { deny all; }
location = /xmlrpc.php { deny all; }
location ~* ^/wp-content/uploads/.*\.(php|phtml)$ { deny all; }
```

## 4. Limpieza del contenido de WordPress

- [ ] Eliminar los temas por defecto sin usar. Conservar **uno** solo como
      recambio de emergencia (p. ej. `twentytwentyfive`); ahora hay tres.
- [ ] Eliminar `wp-content/plugins/hello.php` (Hello Dolly).
- [ ] Revisar que `wp-content/debug.log` no existe en el servidor.
- [ ] Comprobar que no queda ningún usuario de prueba del bootstrap local
      (`scripts/bootstrap-local.ps1` crea un administrador con contraseña
      aleatoria; no debe replicarse en producción).
- [ ] Un único administrador por persona real. Sin cuentas compartidas.

## 5. Correo saliente

Sin esto, los formularios de contacto e inscripción **se pierden en silencio**
(hallazgo M-2). Como no se guarda nada en base de datos por diseño, un correo
no entregado es una solicitud perdida.

- [ ] SMTP autenticado del dominio del club, con TLS obligatorio.
- [ ] `From:` con una dirección real del dominio (p. ej. `web@cbnavalcarnero.es`).
- [ ] Registros SPF, DKIM y DMARC publicados en el DNS.
- [ ] Prueba de extremo a extremo de los dos formularios, revisando también spam.

## 6. Si algún día se despliega con Docker

`compose.yaml` publica los puertos en todas las interfaces
(`"8080:80"` equivale a `0.0.0.0:8080`) y define contraseñas por defecto
(`cbn_password`, `root_password`). Para cualquier entorno que no sea el
portátil de desarrollo:

- Publicar solo en loopback detrás de un proxy inverso:
  `"127.0.0.1:${WORDPRESS_PORT}:80"`.
- Eliminar los valores por defecto para que el arranque **falle** sin `.env`:
  `${WORDPRESS_DB_PASSWORD:?falta WORDPRESS_DB_PASSWORD}`.
- No desplegar nunca el servicio `phpmyadmin` (perfil `tools`): lleva
  `PMA_USER`/`PMA_PASSWORD` preinyectados.
- Secretos vía el gestor del proveedor o `secrets:` de Docker, no vía `.env`.

## 7. Antes de instalar WooCommerce

WooCommerce multiplica la superficie: cuentas de cliente, carrito, endpoints
REST propios, correos transaccionales y datos de pedido. Requisitos previos
(ver también `PAYMENTS.md` §7):

- [ ] Todo lo anterior de este documento, hecho y verificado.
- [ ] Revisar `cbn_get_non_admin_allowed_capabilities()` en
      `access-control.php`: los clientes de WooCommerce son usuarios con rol
      `customer`, y la lista blanca actual les deniega todo salvo `read`.
      Habrá que ampliarla de forma explícita y mínima mediante el filtro
      `cbn_non_admin_allowed_capabilities`.
- [ ] Decidir si los clientes necesitan cuenta o si el checkout es de invitado
      (recomendado: invitado, menos datos que custodiar).
- [ ] Redsys en modo test con la matriz de pruebas de `PAYMENTS.md` §5.
- [ ] Página de política de cookies si Woo introduce cookies no esenciales.

## 8. Verificación posterior al despliegue

```bash
# Cabeceras de seguridad presentes
curl -sI https://cbnavalcarnero.es | grep -iE 'x-frame|x-content-type|referrer|permissions'

# XML-RPC cerrado (esperado: 403 o 405)
curl -s -o /dev/null -w '%{http_code}\n' https://cbnavalcarnero.es/xmlrpc.php

# Enumeración de usuarios cerrada (esperado: 401 y 301)
curl -s -o /dev/null -w '%{http_code}\n' https://cbnavalcarnero.es/wp-json/wp/v2/users
curl -s -o /dev/null -w '%{http_code}\n' 'https://cbnavalcarnero.es/?author=1'

# El log de depuración no se sirve (esperado: 403 o 404)
curl -s -o /dev/null -w '%{http_code}\n' https://cbnavalcarnero.es/wp-content/debug.log

# La versión de WordPress no aparece en el HTML (esperado: sin salida)
curl -s https://cbnavalcarnero.es | grep -i 'name="generator"'
```

## 9. Mantenimiento continuo

- Actualizar WordPress core, plugins y temas mensualmente, en staging primero.
- Revisar los PR semanales de Dependabot (`.github/dependabot.yml`).
- Revisar `npm audit` en CI; cuando esté limpio, quitar `continue-on-error` de
  `.github/workflows/ci.yml` para convertirlo en bloqueante.
- Comprobar trimestralmente que la restauración de la copia de seguridad sigue
  funcionando.
