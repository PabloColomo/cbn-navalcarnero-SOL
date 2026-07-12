# Modelo de acceso CBN: público y administración

Fecha: 2026-07-10

Entorno validado: WordPress local `cbn_sol` en `http://localhost:8090`

## Objetivo

La web diferencia dos experiencias sin crear un sistema de autenticación
paralelo:

1. **Público no registrado.** Navega y usa los servicios públicos sin cuenta.
2. **Administrador CBN.** Inicia sesión con la autenticación nativa de
   WordPress y gestiona el contenido del club.

La instalación local auditada contiene una sola cuenta con rol
`administrator`; el registro público de cuentas está desactivado. No se han
creado, modificado ni documentado contraseñas.

## Matriz de permisos

| Acción                                                                              | Público        | Administrador CBN                         |
| ----------------------------------------------------------------------------------- | -------------- | ----------------------------------------- |
| Ver páginas, noticias, equipos, partidos, sponsors y documentos publicados          | Sí             | Sí                                        |
| Buscar contenido público                                                            | Sí             | Sí                                        |
| Activar sonidos y experiencia Pista Viva                                            | Sí             | Sí                                        |
| Enviar contacto o solicitud de inscripción                                          | Sí, sin cuenta | Sí                                        |
| Ver datos privados de jugadores                                                     | No             | Solo en `wp-admin`                        |
| Acceder a `wp-admin`                                                                | No             | Sí                                        |
| Crear o editar noticias y páginas                                                   | No             | Sí                                        |
| Subir imágenes a la biblioteca                                                      | No             | Sí                                        |
| Gestionar equipos, partidos, sponsors y documentos                                  | No             | Sí                                        |
| Gestionar temporadas, categorías, competiciones, instalaciones y niveles de sponsor | No             | Sí                                        |
| Editar portada, contacto, inscripción y El Club                                     | No             | Sí, mediante el bloque **Datos CBN**      |
| Publicar, programar, guardar borradores o revisar contenido                         | No             | Sí                                        |
| Gestionar navegación                                                                | No             | Sí                                        |
| Crear una cuenta desde la web                                                       | No             | No; las cuentas las crea un administrador |

## Experiencia pública

- No requiere registro ni inicio de sesión.
- No muestra barra de administración.
- Dispone de navegación, búsqueda, contacto e inscripción.
- El formulario de contacto y el de inscripción usan `admin-post.php`, nonce,
  honeypot, validación y saneado del servidor.
- Las solicitudes viajan por email y no se almacenan en WordPress.
- `cbn_player` permanece completamente privado, fuera de archivos, búsqueda y
  REST público.
- El pie incluye un enlace discreto **Acceso administrador** hacia el login
  nativo.

## Experiencia de administración

Acceso: `/wp-login.php` en la URL configurada para el entorno. En una copia
nueva con `.env.example`, la URL predeterminada es
`http://localhost:8080/wp-login.php`.

Tras iniciar sesión, WordPress dirige al usuario a **Panel CBN**. El panel
incluye:

- botón directo para publicar una noticia;
- acceso a portada y vista pública;
- marcador de publicados y borradores para cada tipo de contenido;
- altas y listados de noticias, páginas, equipos, partidos, patrocinadores,
  documentos y jugadores privados;
- accesos a biblioteca multimedia, menús, temporadas y niveles de sponsor;
- recordatorios de verificación, derechos de imagen y privacidad de cantera;
- aviso de que contacto e inscripciones se reciben por email.

El login y el panel usan la identidad visual CBN. Cuando un administrador
navega por la web pública, el CTA **Inscribirse** cambia a **Panel CBN**.

## Edición sin depender de ACF

El repositorio incluye definiciones ACF JSON, pero la copia local no incluye el
plugin ACF. Para que la gestión no quede bloqueada, `cbn-core` incorpora un
editor nativo compatible con esas definiciones:

- aparece como bloque **Datos CBN** en el editor correspondiente;
- cubre portada, contacto, inscripción, El Club, equipos, partidos y sponsors;
- admite texto, texto largo, números, fechas, horas, selectores, booleanos,
  relaciones y selección de imagen desde la biblioteca;
- aplica nonce, comprobación de permisos y saneado por tipo;
- se desactiva automáticamente si ACF se instala en el futuro, evitando
  campos duplicados;
- los helpers del tema leen ACF cuando existe y `post_meta` nativo cuando no.

## Seguridad y privacidad

- Los CPT del club usan capacidades propias (`edit_cbn_*`, `publish_cbn_*`,
  etc.) y solo el rol Administrator las recibe.
- Las taxonomías deportivas requieren `manage_cbn_content`.
- Cualquier cuenta heredada con rol Editor/Autor conserva lectura, pero sus
  capacidades de escritura se deniegan en ejecución.
- Los no administradores autenticados son devueltos a la web pública al
  intentar entrar en `wp-admin`.
- `admin-post.php` y `admin-ajax.php` no se bloquean para no romper los
  formularios públicos ni integraciones legítimas.
- No se eliminan roles ni usuarios existentes de la base de datos.
- No se guardan credenciales en el repositorio.

## Flujo editorial recomendado

1. Entrar por `/wp-login.php`.
2. Abrir **Panel CBN**.
3. Crear o editar el contenido.
4. Completar **Datos CBN** y la imagen destacada.
5. Guardar borrador si existe cualquier dato pendiente.
6. Previsualizar y comprobar móvil/escritorio.
7. Publicar solo con fechas, nombres, enlaces y permisos confirmados.

Para marcadores, un resultado solo se muestra cuando ambos tanteos existen y
el club marca el resultado como validado. Para plantillas de equipos, la opción
de publicación permanece apagada por defecto.

## Dependencias operativas pendientes

- Configurar SMTP o un proveedor de correo en producción.
- Confirmar destinatario administrativo y textos RGPD definitivos.
- Crear cuentas adicionales únicamente si el club necesita más
  administradores; usar contraseñas únicas y 2FA mediante una solución
  aprobada.
- WooCommerce, catálogo y pagos siguen fuera de este cambio.
