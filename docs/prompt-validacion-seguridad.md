# Prompt de traspaso — validación del endurecimiento de seguridad

Pegar en un agente **con acceso de ejecución** sobre este repositorio (Claude
Code o Codex en local, con Docker disponible). El trabajo de código ya está
hecho y sin commitear; lo que falta es **ejecutarlo, probarlo y corregir lo que
se rompa**.

Copia todo lo que hay entre las líneas.

---

```text
Goal:
Validar y dejar estable la tanda de endurecimiento de seguridad que hay sin
commitear en el árbol de trabajo. Ejecutar la verificación del proyecto,
probar en navegador las superficies afectadas, corregir las regresiones que
aparezcan y dejar el repositorio listo para que un humano revise el diff.
No commitear ni hacer push sin confirmación explícita.

Context:
Rama actual: codex/reproducible-cbn-sol. Todos los cambios están sin commitear.
Los introdujo un agente SIN acceso de ejecución: no se ha ejecutado NUNCA
`php -l`, ni `npm run verify`, ni la web en navegador. Nada de esto está
probado. Asume que puede haber errores de sintaxis o de tiempo de ejecución.

El detalle completo está en:
  - docs/auditoria-seguridad-codigo-2026-07-25.md  (29 hallazgos, secciones 1 a 3 bis)
  - AGENT_CHANGELOG.md                              (4 entradas del 2026-07-25)
  - docs/despliegue-produccion.md                   (lo que va en el servidor)
  - docs/pasarela-redsys-sabadell.md                (contexto del TPV, no tocar aún)

Archivos NUEVOS:
  wp-content/mu-plugins/cbn-core/inc/hardening.php
  wp-content/mu-plugins/cbn-core/inc/login-security.php
  wp-content/themes/cbn-theme/inc/form-security.php
  .github/dependabot.yml
  scripts/check-security.ps1
  docs/auditoria-seguridad-codigo-2026-07-25.md
  docs/despliegue-produccion.md
  docs/pasarela-redsys-sabadell.md
  docs/prompt-validacion-seguridad.md

Archivos MODIFICADOS:
  wp-content/mu-plugins/cbn-core/cbn-core.php
  wp-content/mu-plugins/cbn-core/inc/access-control.php
  wp-content/mu-plugins/cbn-core/inc/native-fields.php
  wp-content/mu-plugins/cbn-core/inc/admin-dashboard.php
  wp-content/themes/cbn-theme/functions.php
  wp-content/themes/cbn-theme/inc/contact-content.php
  wp-content/themes/cbn-theme/inc/registration-content.php
  wp-content/themes/cbn-theme/inc/sports-content.php
  wp-content/themes/cbn-theme/page-contacto.php
  wp-content/themes/cbn-theme/page-inscripcion.php
  wp-content/themes/cbn-theme/footer.php
  wp-content/themes/cbn-theme/front-page.php
  .github/workflows/ci.yml
  AGENT_CHANGELOG.md

Qué hace cada cosa, en una línea:
  - hardening.php: cabeceras de seguridad, HSTS opcional por constante,
    XML-RPC off, /wp-json/wp/v2/users y /wp/v2/comments cerrados, ?author=N
    redirigido, contraseñas de aplicación off, generator fuera del head,
    error de login genérico, comentarios desactivados, DISALLOW_FILE_EDIT.
  - login-security.php: limitador de intentos por IP con transients,
    5 fallos = 15 min, 10 fallos = 1 hora, se limpia al acertar.
  - form-security.php: rate limiting por IP y por email de los dos
    formularios públicos, trampa temporal firmada, límites de longitud,
    cabecera From del dominio propio, aviso en el escritorio si falla el correo.
  - access-control.php: lista blanca de capabilities (antes lista negra);
    todo usuario sin manage_options queda en read + level_0. profile.php
    permitido.
  - sports-content.php: cláusulas meta_query nombradas en cbn_query_matches().

Constraints:
- Lee AGENTS.md primero. Manda sobre este prompt.
- NO instalar dependencias sin aprobación explícita.
- NO commitear, push, PR ni merge sin confirmación humana explícita.
- NO editar wp-content/themes/cbn-theme/assets/dist a mano.
- NO redactar los textos legales (aviso legal, privacidad, cookies,
  condiciones de compra, devoluciones). Son responsabilidad jurídica del
  club. El guion de contenidos está en docs/pasarela-redsys-sabadell.md
  sección 6. Si no existen, se reporta; no se inventan.
- NO tocar compose.yaml: sus riesgos están documentados a propósito en
  docs/despliegue-produccion.md sección 6.
- NO instalar WooCommerce todavía.
- Mantener la proporción visual 60% blanco / 30% rojo / 10% negro.
- Registrar lo que hagas en AGENT_CHANGELOG.md siguiendo el formato existente.

Files likely involved:
Los 22 listados arriba. Si una regresión obliga a tocar otro archivo,
justifícalo en el changelog.

Done when:
1. `npm run verify` pasa.
2. `php -l` pasa sobre todos los .php de wp-content (igual que hace la CI).
3. Las 11 rutas públicas devuelven HTTP 200 y no hay errores en consola ni
   en el log de PHP.
4. Los dos formularios se envían correctamente de extremo a extremo.
5. Los 6 puntos de riesgo de la sección siguiente están verificados.
6. AGENT_CHANGELOG.md actualizado y diff resumido mostrado al humano.

Verification:

  # 0. Arranque
  powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1

  # 1. Sintaxis y build
  npm run verify
  docker compose run --rm wpcli bash -lc 'find /var/www/html/wp-content -name "*.php" -print0 | xargs -0 -n1 php -l'

  # 2. Comprobaciones externas (script nuevo, NUNCA ejecutado: puede fallar
  #    y necesitar ajustes; corrígelo si es el caso)
  powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1

  # 3. Log de PHP tras navegar por el sitio
  docker compose logs wordpress --tail 200

PUNTOS DE RIESGO — verificar uno a uno, son lo que más probablemente falle:

R1. Consulta de partidos (el cambio con más riesgo funcional).
    En inc/sports-content.php se sustituyó el par meta_key + orderby =>
    meta_value por cláusulas meta_query nombradas, y en la rama de partidos
    pasados se añadió una cláusula EXISTS explícita para conservar el
    comportamiento anterior (antes el meta_key implícito ya exigía que
    cbn_match_date existiera).
    Comprobar: /partidos/ lista próximos y pasados, en el orden correcto por
    fecha, y la portada sigue mostrando los mismos partidos que antes.
    Si aparecen partidos de más o de menos, es esta regresión.

R2. Limitador de acceso. PUEDE DEJARTE FUERA.
    Antes de nada, ten a mano cómo desactivarlo: comentar la línea
    'inc/login-security.php' en cbn-core.php, o borrar los transients
    cbn_login_* con wp-cli.
    Comprobar: 5 intentos fallidos bloquean 15 minutos con mensaje claro;
    un acierto antes del quinto limpia el contador; tras el bloqueo, la
    contraseña correcta también se rechaza hasta que expira.
    OJO: bloquea por IP. En local todo viene de la misma IP.

R3. Lista blanca de capabilities.
    Comprobar que un administrador sigue pudiendo hacer TODO: publicar,
    subir medios, editar menús, gestionar usuarios, acceder al Panel CBN.
    Crear un usuario con rol editor y confirmar que no puede editar nada y
    que sí puede entrar en su perfil (profile.php) a cambiar la contraseña.

R4. Formularios.
    Enviar contacto e inscripción de verdad y confirmar que llega el correo
    (en local probablemente NO salga: verificar entonces que aparece el aviso
    de error en el escritorio, que es justo lo que debe pasar).
    Probar: envío en menos de 3 segundos debe rechazarse con mensaje
    'invalid'; a partir del 6º envío en una hora debe salir el mensaje de
    'rate_limited'; una inscripción de un menor sin datos del tutor debe
    dar 'minor_guardian'; una fecha de nacimiento manipulada por POST con
    el valor '-20 years' debe rechazarse.

R5. Comentarios.
    Confirmar que /wp-json/wp/v2/comments devuelve 404 y que un POST a
    /wp-comments-post.php no crea nada.

R6. Panel de administración.
    Recorrer wp-admin buscando errores PHP: el filtro gettext de
    admin-dashboard.php ahora usa una variable estática, y native-fields.php
    cambió la construcción del selector de relaciones.

Si algo de R1 a R6 falla: corrígelo, no lo revientes. Cada cambio tiene su
razón documentada en el informe de auditoría; si crees que una decisión está
mal, dilo en el changelog en lugar de revertirla en silencio.

Al terminar, informa de: qué pasó, qué falló, qué corregiste, y qué queda
abierto. NO commitees.
```

---

## Qué queda fuera de este prompt, y por qué

| Pendiente                                        | Por qué no lo hace el agente                                |
| ------------------------------------------------ | ----------------------------------------------------------- |
| Textos legales (N-3)                             | Responsabilidad jurídica del club. Bloquea el alta del TPV. |
| SMTP autenticado + SPF/DKIM/DMARC (M-2)          | Configuración de hosting y DNS.                             |
| HTTPS y HSTS (N-5)                               | Certificado en el servidor. La constante ya está lista.     |
| Quitar DNI, peso y estatura del formulario (M-5) | Decisión de negocio del club.                               |
| Enqueues duplicados (C-3)                        | Mantenimiento; exige validación visual. Opcional.           |
| WooCommerce y Redsys                             | Fase posterior. Ver `docs/pasarela-redsys-sabadell.md`.     |
| Modelo de datos de jugadores                     | Trabajo aparte; `cbn_player` no tiene ningún campo.         |
