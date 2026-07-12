# Pista Viva - rediseño experimental CBN Sol

Fecha: 2026-07-10

## Objetivo

Crear en la copia exterior de `VERSIONES/cbn sol` una nueva experiencia web
para Club Baloncesto Navalcarnero. La implementación conserva WordPress, el
tema `cbn-theme`, el plugin MU de contenido y los fallbacks editables, pero
replantea la Home con una dirección visual propia y efectos progresivos.

## Concepto

`Pista Viva` convierte la página en una cancha continua:

- Una jugada roja recorre la Home con un balón que avanza según el scroll.
- El hero mezcla líneas de cancha, fotografía, marcador de 24 segundos y un
  titular editorial propio.
- Marcador, accesos rápidos, equipos, manifiesto, noticias, inscripción,
  tienda, instalaciones y sponsors forman zonas de juego distintas.
- El sistema mantiene la proporción del proyecto: blanco dominante, rojo para
  energía y negro para contraste.

La referencia deportiva previa se usa solo como inspiración conceptual. No se
ha copiado código, layout, texto, recursos ni identidad de otra web.

## Interacción y sonido

- Cursor del escudo CBN para ratón/trackpad, con cursor estándar en táctil,
  reduced motion y forced colors.
- Control de sonido visible y apagado por defecto en la primera visita de la
  sesión.
- Pisadas y chirridos de zapatilla sobre parqué sintetizados con Web Audio al
  recorrer la página o mover el puntero.
- Sonido de balón y red al activar enlaces o botones.
- Los sonidos se generan en el navegador: no hay audios externos, descargas ni
  licencias adicionales.
- Filtros de categorías, tarjetas con tilt moderado, reveals y movimiento de la
  jugada.
- En móvil se simplifican la ruta y el grano para mantener rendimiento.
- `prefers-reduced-motion` desactiva reveals, parallax, tilt, balón y cursor
  animado; el control de sonido sigue siendo independiente.

## Datos utilizados

Se han conservado únicamente datos verificados en
`docs/clubs/club-baloncesto-navalcarnero.md`:

- Misión de deporte de base y acceso al baloncesto.
- Categorías publicadas para 2025/2026 como base de agrupación deportiva.
- Pabellón Municipal La Estación y Colegio María Martín.
- Email `administracion@cbnavalcarnero.es`.
- Teléfono `(+34) 696 849 235`.
- Facebook, X/Twitter y YouTube confirmados; no se inventa Instagram.
- Ayuntamiento de Navalcarnero, ELEVA, Domino's Pizza Navalcarnero y
  Multiópticas Navalcarnero.
- CIF G-80115298.

Los resultados, rivales, horarios, estadísticas y noticias con fechas que eran
contenido de demostración se han sustituido por estados explícitos de datos por
confirmar o por copy editorial no numérico. Si WordPress contiene publicaciones
reales, sus consultas siguen prevaleciendo sobre los fallbacks.

## Archivos de implementación

- `wp-content/themes/cbn-theme/front-page.php`
- `wp-content/themes/cbn-theme/header.php`
- `wp-content/themes/cbn-theme/footer.php`
- `wp-content/themes/cbn-theme/inc/assets.php`
- `wp-content/themes/cbn-theme/inc/home-content.php`
- `wp-content/themes/cbn-theme/inc/contact-content.php`
- `wp-content/themes/cbn-theme/assets/src/css/sol.css`
- `wp-content/themes/cbn-theme/assets/src/js/sol.js`

`sol.css` y `sol.js` son una capa sin dependencias nuevas. Se cargan después del
bundle existente y también funcionan en el fallback previo al build, de modo
que la nueva Home, el sonido, el cursor y la navegación móvil se pueden revisar
en un checkout sin `node_modules`.

## Expansión completa por superficies

Tras validar la Home, el sistema visual se extendió por separado a:

- El Club.
- Equipos y ficha de equipo.
- Partidos y ficha de partido.
- Noticias, categorías y noticia individual.
- Patrocinadores y ficha de patrocinador.
- Contacto e inscripción.
- Tienda honesta sin catálogo o pagos ficticios.
- Documentación, documentos individuales, privacidad y aviso legal.
- Búsqueda y error 404.

Cada superficie carga su propia hoja `sol-{surface}.css` solo cuando corresponde.
Los templates conservan consultas, metadatos, paginación, formularios y estados
vacíos de WordPress. Los singles deportivos, sponsors y documentos permanecen
preparados para datos reales, aunque la base local todavía no contiene registros
publicados con los que capturar esas fichas.

El modelo de acceso público/administrador y la gestión editorial se documentan
en `docs/access-model-cbn-2026-07-10.md`.

## Preview local aislada

El entorno de esta copia usa:

```text
COMPOSE_PROJECT_NAME=cbn_sol
WORDPRESS_PORT=8090
PHPMYADMIN_PORT=8091
```

La `.env` local está ignorada por Git y solo contiene valores de desarrollo.

URL de revisión:

```text
http://localhost:8090
```

## Validación realizada

- PHP 8.3: lint limpio en los seis PHP modificados.
- `node --check`: limpio para `sol.js`.
- Navegador integrado: escritorio 1440 x 900 y móvil 390/430 px.
- Sin overflow horizontal en escritorio o móvil.
- Sin errores ni warnings de consola.
- Imágenes críticas cargadas correctamente.
- Sonido OFF/ON verificado mediante `aria-pressed`, estado visible y clase de
  documento.
- Filtro de equipos verificado: deja visible solo la categoría seleccionada.
- URLs principales locales verificadas con HTTP 200.

`npm run verify` no puede completarse en esta copia porque `node_modules` no
existe. El check se intentó y se detuvo en `prettier` no encontrado. No se
ejecutó `npm ci` porque `AGENTS.md` exige aprobación previa para instalar
dependencias.

## Pendientes de producción

- Sustituir las fotografías generadas de ejemplo por fotos autorizadas del
  club cuando estén disponibles.
- Cargar noticias, equipos, partidos y sponsors reales en WordPress/ACF.
- Confirmar textos legales definitivos.
- Completar tienda y pagos solo dentro de su alcance y entorno de prueba.
- Ejecutar `npm ci && npm run verify` tras aprobación de instalación.
