# Estado actual y realineamiento del proyecto

Fecha: 2026-06-29

## 1. Objetivo

Este documento deja una fotografia actual del proyecto Club Baloncesto Navalcarnero y realinea el trabajo con las decisiones mas recientes del club:

- Lanzar una primera web MVP en plazo corto.
- Incluir tienda con productos reales.
- Incluir pagos completos para tienda e inscripciones.
- Preparar la base para importar datos deportivos desde federacion.
- Mantener fuera de la primera fase las automatizaciones avanzadas, portal privado, CRM e IA transaccional.

## 2. Estado Git y trazabilidad

Estado local revisado:

- Rama actual: `main`.
- `main` esta sincronizada con `origin/main`.
- Ultimo commit remoto/local: `8ce06f1 chore: add GitHub multiagent workflow`.
- El flujo repo-local de agentes ya esta publicado en GitHub.
- Hay un documento nuevo sin commitear:
  - `docs/redesign/propuesta-mvp-cliente-2026-06-29.md`.

Elementos pendientes de decision:

- Rama local antigua `codex/quality-base`, con remoto eliminado.
- Rama local `codex/team-templates` en `92dcd3a`.
- `stash@{0}` con trabajo aparcado de plantillas de equipos:
  - `wp-content/themes/cbn-theme/archive-cbn_team.php`.
  - `wp-content/themes/cbn-theme/single-cbn_team.php`.
  - cambios en `wp-content/themes/cbn-theme/assets/src/css/main.css`.

Riesgo:

- Recuperar el stash sin revisarlo puede mezclar trabajo visual antiguo con el alcance actual.

Recomendacion:

- Antes de desarrollar equipos, revisar el stash en una rama separada y decidir si se recupera, se adapta o se descarta.

## 3. Base tecnica existente

El proyecto ya tiene una base tecnica valida para seguir avanzando:

- WordPress como CMS.
- Tema custom: `wp-content/themes/cbn-theme`.
- MU plugin propio: `wp-content/mu-plugins/cbn-core`.
- Vite para assets frontend.
- GSAP + ScrollTrigger para animaciones selectivas.
- Lenis para smooth scroll progresivo.
- Swiper para carruseles reales.
- Docker local WordPress + MariaDB.
- CI en GitHub Actions.
- ACF JSON versionado.
- Home MVP con fallbacks si no hay ACF o contenido real.

## 4. Modelo de contenido actual

El plugin MU registra:

- `cbn_team`.
- `cbn_player`.
- `cbn_match`.
- `cbn_sponsor`.
- `cbn_document`.

Taxonomias registradas:

- `cbn_season`.
- `cbn_sport_category`.
- `cbn_competition`.
- `cbn_venue`.
- `cbn_sponsor_tier`.

ACF JSON existente:

- Home content.
- Team details.
- Match details.
- Sponsor details.

Estado:

- La arquitectura esta preparada para contenido estructurado.
- La validacion local con Docker, WordPress y ACF esta documentada.
- Falta construir plantillas publicas completas para equipos, partidos, sponsors, tienda e inscripciones.

## 5. Estado visual y frontend

Existe una home MVP con:

- Header.
- Hero.
- Ticker de partidos/resultados.
- Bloque de equipos.
- Noticias.
- Bloques de tienda e inscripcion.
- Sponsors.
- Footer.

Reglas visuales vigentes:

- 60% blanco.
- 30% rojo.
- 10% negro.
- Blanco como superficie dominante.
- Rojo para energia, CTAs, estados y bandas.
- Negro para texto, estructura y contraste.

Estado:

- La direccion visual esta alineada con la propuesta del cliente: deportiva, limpia, institucional y mantenible.
- Antes de cerrar cambios visuales debe hacerse QA en navegador con capturas desktop y mobile.

## 6. Realineamiento del MVP

El MVP realineado debe cubrir una web publica util y operativa, no solo una maqueta visual.

### Debe entrar en Fase 1

- Home moderna.
- Pagina del club.
- Equipos basicos.
- Partidos/resultados basicos.
- Noticias/comunicados.
- Sponsors.
- Contacto.
- Inscripciones basicas.
- Tienda con productos reales definidos por el club.
- Pagos completos para tienda e inscripciones, primero en modo test.
- Preparacion tecnica para federacion.
- Responsive movil.

### Debe quedar preparado, pero condicionado

- Importacion inicial desde federacion.
- Datos reales de equipos, jugadores, partidos, resultados y estadisticas.
- Productos finales, tallas, precios y stock.
- Formularios definitivos de inscripcion.
- Paso de pagos de test a produccion.

Estas partes dependen de accesos, datos reales, pasarela, textos legales y validacion del club.

### Debe quedar fuera de Fase 1

- Portal privado del socio.
- CRM completo.
- Automatizacion de seguros o listas de espera.
- IA que inscriba jugadores o tramite pagos.
- Integracion federativa automatica completa si no hay fuente clara.
- Estadisticas avanzadas por jugador si la federacion no las expone de forma fiable.

## 7. Pagos y tienda

El club ha pedido pagos y tienda dentro de la primera fase.

Alcance de Fase 1:

- Tienda con productos reales.
- Productos iniciales: camisetas, pantalones, camisetas de entrenamiento, camiseta de calentamiento y sudaderas, si el club confirma catalogo.
- Variaciones por talla cuando aplique.
- Recogida local y/o envio segun decision final.
- Pago online para tienda.
- Pago online para inscripciones cuando proceda.

Decisiones pendientes:

- Redsys/TPV, Stripe u otra pasarela.
- Proveedor textil y responsable de tienda.
- Precios, tallas y stock.
- Politica de devoluciones/cambios.
- Textos legales de compra e inscripcion.
- Responsable de conciliacion de pagos.

Regla de seguridad:

- La pasarela debe probarse primero en modo test.
- Ningun dato de tarjeta debe tocar el servidor del club.
- No se deben guardar credenciales ni claves en Git, documentos o chats.

## 8. Federacion y datos deportivos

El club ha pedido importar o sincronizar:

- Equipos.
- Jugadores.
- Partidos.
- Resultados.
- Clasificaciones.
- Estadisticas.
- Competiciones.
- Temporadas.

Estado actual:

- El modelo interno ya contempla trazabilidad externa.
- No hay integracion real con federacion.
- No se conoce todavia si la fuente sera API, CSV/Excel, panel privado, web publica u otro sistema.

Realineamiento:

- En Fase 1 se debe preparar estructura y visualizacion publica.
- Si hay acceso viable en plazo, se puede hacer importacion inicial.
- Si no hay acceso claro, se debe permitir carga manual o importacion sencilla y dejar la automatizacion para fase posterior.

Reglas:

- No scraping autenticado sin autorizacion.
- No credenciales en Git.
- No logs con datos sensibles.
- Usar `external_source`, `external_id`, `external_url`, `external_updated_at` y estado de sincronizacion para evitar duplicados.

## 9. Calidad y verificaciones actuales

Checks disponibles:

- `npm run format:check`.
- `npm run build`.
- `npm run verify`.
- CI remoto con:
  - `npm ci`.
  - `npm run format:check`.
  - `npm run build`.
  - `php -l` sobre `wp-content`.

Checks ejecutados en esta auditoria:

- `npm run verify`: correcto.
- Validacion JSON/TOML de package, theme, ACF JSON y agentes: correcta.
- `git diff --check`: correcto.
- Busqueda basica de secretos tipo Stripe/API key/password/token: sin coincidencias.
- `php -v`: no disponible en PATH local; el lint PHP queda cubierto por CI o por un entorno con PHP/Docker.

Validaciones ya documentadas:

- Docker local.
- WordPress local.
- Tema activo.
- MU plugin activo.
- ACF activo localmente.
- ACF JSON detectado.
- Creacion de contenido local de prueba.

Gaps actuales:

- No hay tests e2e.
- No hay visual regression automatizada.
- No hay Lighthouse documentado.
- No hay auditoria de accesibilidad final.
- No hay staging real conectado.
- No hay WooCommerce/pasarela configurada todavia.
- No hay importador federativo.
- El codigo actual solo muestra CTAs de tienda e inscripcion; todavia no existe checkout real.
- Los CPTs existen, pero faltan plantillas publicas completas para equipos, partidos, sponsors y documentos.
- La home puede volver a contenido de muestra si hay menos contenido real del minimo esperado.
- Los campos ACF de destacado/trazabilidad existen, pero aun no gobiernan todas las queries publicas.
- `assets/dist` esta ignorado; el despliegue final debe construir y publicar assets compilados.

## 10. Riesgos principales

### Plazo

El plazo corto obliga a priorizar. Pagos completos y productos reales en Fase 1 son posibles solo si el club entrega datos y accesos rapido.

### Diferencia entre documentacion y codigo actual

La documentacion realineada ya incluye pagos, tienda e inscripciones operativas en Fase 1. El codigo actual todavia esta en una fase anterior: home visual, modelo de contenido y CTAs. La siguiente fase debe cerrar esa distancia con implementacion real.

### Pasarela

Redsys/TPV y Stripe tienen implicaciones diferentes. La decision afecta plugins, configuracion, pruebas y soporte.

### Federacion

La importacion real depende de una fuente tecnica viable. No debe prometerse automatizacion completa antes de auditar la fuente.

### Contenido real

La web puede construirse con estructura, pero necesita textos, fotos, equipos, sponsors, productos y datos legales para cerrar entrega.

### Contenido de muestra en home

La home usa fallbacks para mantener la composicion visual cuando falta contenido. Para produccion debe decidirse si se prefiere mostrar contenido real parcial o completar manualmente los minimos antes de publicar.

### Assets compilados

El build genera assets en `assets/dist`, pero no estan versionados. Antes de publicar hay que confirmar que el proceso de despliegue ejecuta `npm run build` y sube el manifest y assets compilados al servidor.

### Privacidad de menores

Debe definirse que datos se publican. Por defecto conviene mostrar informacion minima hasta tener autorizaciones claras.

### Staging y dominio

No se debe tocar DNS ni produccion sin staging, SSL, backup, correo verificado y validacion del club.

## 11. Plan de trabajo recomendado

### Paso 1: Cierre operativo inmediato

- Confirmar fecha de entrega.
- Confirmar pasarela.
- Confirmar productos, precios, tallas y stock.
- Confirmar campos de inscripcion.
- Confirmar dominio/hosting/correo.
- Confirmar fuente federativa.

### Paso 2: Primera rama de desarrollo

Crear una rama pequena para llevar el MVP hacia contenido real:

- Revisar stash de plantillas de equipos.
- Crear o adaptar plantillas de equipos.
- Crear plantillas/listados de partidos y sponsors.
- Crear paginas base.
- Conectar CTAs de inscripcion, tienda y contacto.

### Paso 3: Pagos y tienda

- Instalar/configurar WooCommerce en entorno local/staging.
- Elegir pasarela.
- Configurar modo test.
- Crear productos reales.
- Probar checkout.
- Documentar flujo de devoluciones, emails y conciliacion.

### Paso 4: Datos deportivos

- Crear vistas publicas de equipos y partidos.
- Cargar datos manuales iniciales o importar si hay fuente viable.
- Documentar trazabilidad federativa.
- Ajustar la home para usar contenido real parcial o reglas de destacado cuando existan.

### Paso 5: QA y entrega

- Ejecutar `npm run verify`.
- Validar WordPress local/staging.
- Capturas desktop y mobile.
- Revisión de accesibilidad basica.
- Validacion de pagos en modo test.
- Validacion del club antes de publicar.

## 12. Criterio de avance seguro

Antes de cualquier commit, push, PR o merge:

- Cambios visuales: mostrar capturas desktop y mobile y esperar validacion.
- Cambios no visuales: mostrar diff/resumen y esperar validacion.
- Changelog actualizado.
- Checks ejecutados.
- Sin secretos.
- Sin credenciales.
- Sin trabajo ajeno revertido.

## 13. Conclusion

El proyecto esta bien encaminado y la base tecnica debe conservarse. La prioridad ahora es convertir la base actual en un MVP publico y operativo, con pagos y productos reales dentro de Fase 1, pero sin prometer automatizaciones que dependen de terceros.

La ruta correcta es avanzar con piezas pequenas y verificables: primero estructura y contenido publico, despues tienda/pagos, despues datos deportivos y finalmente integraciones avanzadas.
