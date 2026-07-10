# Plan de siguientes pasos

Fecha: 2026-06-27

## Objetivo

Definir que falta para convertir la base actual en una web WordPress publicable, mantenible y preparada para:

- Dominio real del club.
- Datos oficiales de federacion.
- Tienda de ropa.
- Inscripciones con pago.
- Gestion sencilla por parte del club.

Este plan no borra el trabajo existente. Lo organiza por capas y dependencias.

## Estado actual

Ya existe:

- Repositorio GitHub con flujo PR + CI.
- Tema WordPress custom `cbn-theme`.
- MU plugin `cbn-core`.
- Home MVP visual.
- Sistema visual 60% blanco, 30% rojo y 10% negro.
- Home conectada a una capa de datos con fallbacks.
- ACF JSON inicial para portada.
- Documentacion de dominio y federacion.
- Reglas para capturas/diff antes de publicar cambios.

No existe todavia:

- WordPress real validado en staging.
- ACF instalado y probado en admin.
- WooCommerce/Stripe configurado.
- Integracion real con federacion.
- Modelo definitivo de partidos/resultados/estadisticas.
- Dominio conectado.
- Contenido real completo.
- QA final de lanzamiento.

## Reglas de trabajo

Para cada avance:

1. Crear rama `codex/...`.
2. Aplicar cambios.
3. Ejecutar checks disponibles.
4. Si es visual: mostrar captura desktop y movil antes de commit/push/PR/merge.
5. Si no es visual: mostrar resumen/diff antes de commit/push/PR/merge.
6. Commit pequeno.
7. Push.
8. PR.
9. Esperar CI verde.
10. Merge solo si esta validado.

No se deben guardar credenciales, tokens, dominios privados sensibles o contrasenas en Git.

## Fase 1: base de contenido WordPress

Estado: se puede empezar ya.

Objetivo:

Dejar preparados los modelos internos para que WordPress pueda recibir datos de la federacion mas adelante sin rehacer estructura.

Tareas:

- Definir campos ACF JSON para `cbn_team`.
- Definir campos ACF JSON para `cbn_match`.
- Definir campos ACF JSON para `cbn_sponsor`.
- Definir campos de trazabilidad externa:
  - `external_source`.
  - `external_id`.
  - `external_url`.
  - `external_updated_at`.
- Definir campos editoriales separados de los oficiales:
  - visibilidad.
  - destacado.
  - cronica.
  - imagen.
  - relacion con noticia.
- Documentar que campos vienen de federacion y cuales puede editar el club.

Entrega esperada:

- ACF JSON versionado.
- Documentacion del modelo.
- Home preparada para usar datos mas ricos cuando existan.

Criterio de aceptacion:

- `npm run verify` pasa.
- No hay dependencia fatal de ACF Pro.
- Los campos no obligan a conocer todavia el formato exacto de la federacion.

## Fase 2: staging WordPress

Estado: se puede preparar parcialmente ya; necesita hosting para completar.

Objetivo:

Tener un entorno real donde validar WordPress, admin, tema, plugins y contenido antes de tocar el dominio principal.

Tareas:

- Elegir hosting WordPress.
- Crear entorno staging.
- Instalar WordPress.
- Activar tema.
- Instalar ACF.
- Instalar plugins base aprobados.
- Probar carga de assets Vite.
- Probar admin y edicion de home.
- Configurar backups.
- Configurar SSL.

Entrega esperada:

- URL de staging.
- Checklist de instalacion.
- Capturas desktop/movil de la home real.
- Validacion de admin.

Criterio de aceptacion:

- La home carga en staging.
- ACF muestra campos de portada.
- No hay errores visibles en frontend.
- Backups y SSL activos.

## Fase 3: dominio y DNS

Estado: espera datos del dominio.

Objetivo:

Conectar el dominio sin romper correo ni servicios existentes.

Datos necesarios:

- Dominio exacto.
- Registrador.
- Acceso al panel DNS o nameservers.
- Confirmacion de si hay correo activo.
- Registros DNS actuales.
- Hosting final.
- Decision sobre Cloudflare.
- Dominio canonico: raiz o `www`.

Tareas:

- Auditar DNS actual.
- Guardar captura/exportacion de registros DNS fuera del repo si contiene datos sensibles.
- Confirmar MX, SPF, DKIM y DMARC.
- Configurar staging si aplica.
- Configurar SSL.
- Definir redirecciones.
- Programar ventana de cambio.
- Validar produccion tras propagacion.

Entrega esperada:

- Checklist DNS completado.
- Dominio apuntando a produccion.
- SSL activo.
- Correo verificado.

Criterio de aceptacion:

- Web accesible por dominio canonico.
- `www` redirige correctamente o funciona segun decision.
- Correo del club no se interrumpe.
- SSL sin errores.

## Fase 4: auditoria de federacion

Estado: espera credenciales.

Objetivo:

Entender como obtener datos oficiales sin asumir formato ni hacer scraping no autorizado.

Datos necesarios:

- URL de la plataforma.
- Tipo de usuario.
- Permisos.
- Condiciones de uso.
- Equipos/competiciones del club.
- Frecuencia deseada de actualizacion.

Modo de trabajo:

- Solo lectura.
- No guardar credenciales.
- No automatizar hasta documentar fuente.
- No scraping si no esta permitido.

Tareas:

- Revisar si existe API.
- Revisar si hay CSV/Excel.
- Revisar si hay endpoints o panel descargable.
- Identificar IDs oficiales.
- Mapear temporadas, competiciones, equipos y partidos.
- Documentar campos disponibles.
- Detectar limitaciones.

Entrega esperada:

- Informe de fuente federativa.
- Tabla de datos disponibles.
- Recomendacion de importacion:
  - API.
  - CSV/Excel.
  - importacion manual.
  - scraping autorizado como ultimo recurso.

Criterio de aceptacion:

- Sabemos como leer datos.
- Sabemos que datos no estan disponibles.
- Sabemos si se puede automatizar.
- No hay credenciales en repo.

## Fase 5: importador federacion -> WordPress

Estado: espera fase 4.

Objetivo:

Sincronizar datos oficiales en WordPress con trazabilidad y sin duplicados.

Tareas:

- Crear capa de cliente para la fuente elegida.
- Crear normalizador de datos.
- Crear upsert por `external_id`.
- Guardar logs de sincronizacion.
- Gestionar errores.
- Crear modo dry-run.
- Crear importacion manual desde admin o WP-CLI.
- Definir sincronizacion programada solo si la manual es fiable.

Datos a importar inicialmente:

- Temporadas.
- Competiciones.
- Equipos.
- Partidos.
- Resultados.
- Clasificaciones si estan disponibles.

Entrega esperada:

- Importador inicial.
- Logs.
- Documentacion.
- Prueba con datos reales.

Criterio de aceptacion:

- No duplica partidos.
- Puede actualizar resultados.
- Puede fallar sin romper la web.
- No expone credenciales.

## Fase 6: tienda e inscripciones

Estado: se puede planificar ya; implementacion requiere decision operativa.

Objetivo:

Permitir tienda de ropa e inscripciones/pagos del club.

Decisiones pendientes:

- Stripe o tambien Redsys/TPV bancario.
- Envio, recogida local o ambos.
- Productos iniciales.
- Tallas y stock.
- Inscripciones con pago unico, reserva, cuota o combinacion.
- Textos legales.
- Responsable de pedidos.

Tareas:

- Instalar WooCommerce.
- Configurar Stripe en modo test.
- Crear producto base de ropa.
- Crear flujo base de inscripcion.
- Configurar emails.
- Configurar politicas legales.
- Probar checkout:
  - pago correcto.
  - pago fallido.
  - cancelacion.
  - reembolso.
  - email al club.
  - email al usuario.

Entrega esperada:

- Checkout de prueba funcionando.
- Producto de ropa MVP.
- Inscripcion MVP.
- Documentacion de operativa.

Criterio de aceptacion:

- Ningun dato de tarjeta toca el servidor del club.
- Modo test validado antes de produccion.
- Emails revisados.
- Politicas legales visibles.

## Fase 7: contenido real y administracion

Estado: se puede preparar parcialmente ya.

Objetivo:

Que el club pueda gestionar la web sin depender de codigo para cambios normales.

Tareas:

- Crear paginas base:
  - Inicio.
  - Equipos.
  - Partidos.
  - Noticias.
  - Tienda.
  - Inscripcion.
  - Sponsors.
  - Contacto.
  - Legal.
- Cargar logo y assets reales aprobados.
- Cargar textos del club.
- Cargar sponsors.
- Cargar equipos reales.
- Definir roles de usuario.
- Crear guia corta de uso del admin.

Entrega esperada:

- Contenido minimo real.
- Guia de administracion.
- Usuarios/roles configurados.

Criterio de aceptacion:

- El club puede actualizar noticias.
- El club puede revisar partidos.
- El club puede editar CTAs principales.
- El club puede gestionar tienda/inscripciones si aplica.

## Fase 8: QA y lanzamiento

Estado: final.

Objetivo:

Publicar con seguridad, rendimiento y accesibilidad basica.

Checklist:

- Responsive desktop/movil.
- Navegacion teclado.
- Estados focus.
- Contraste.
- Formularios accesibles.
- `prefers-reduced-motion`.
- Core Web Vitals basicos.
- Imagenes optimizadas.
- Cache/CDN.
- SEO tecnico.
- Sitemap.
- Robots.
- Analytics si procede.
- Backups.
- Seguridad basica.
- SSL.
- Checkout test.
- DNS y correo.

Entrega esperada:

- Informe QA.
- Capturas finales.
- Checklist firmado.
- Web publicada.

Criterio de aceptacion:

- El club valida contenido.
- Pagos test pasan.
- No hay errores criticos.
- Dominio y correo funcionan.

## Dependencias externas

Bloquean tareas concretas:

- Credenciales federacion: bloquean integracion real e importador real.
- Dominio/registrador: bloquea DNS final.
- Hosting: bloquea staging real.
- Textos legales: bloquean checkout real.
- Productos/tallas/precios: bloquean tienda real.
- Equipos/temporada oficial: bloquean carga final de calendario.

No bloquean:

- Modelo flexible.
- ACF JSON base.
- Documentacion.
- Preparacion de staging.
- Diseño de componentes.
- QA local.

## Siguiente paso recomendado

Crear el modelo flexible de datos deportivos:

1. Campos ACF JSON para `cbn_match`.
2. Campos ACF JSON para `cbn_team`.
3. Campos ACF JSON para `cbn_sponsor`.
4. Documentacion del modelo.
5. Sin integracion real con federacion todavia.

Este paso prepara la base para importar datos cuando lleguen credenciales sin rehacer la web.
