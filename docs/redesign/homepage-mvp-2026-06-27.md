# Home MVP: investigacion, direccion visual e implementacion

Fecha: 2026-06-27

## Objetivo

Crear una primera pagina de inicio sencilla para el Club Baloncesto Navalcarnero con energia deportiva moderna, inspirada solo a nivel conceptual por Manchester Basketball Club. No se copia codigo, estructura exacta, textos, imagenes, assets ni identidad visual de la web de referencia.

La pagina debe respetar la proporcion cromatica obligatoria del proyecto:

- 60% blanco: superficies principales, lectura, secciones y fondo dominante.
- 30% rojo: identidad, CTAs, bandas de energia, ticker y estados destacados.
- 10% negro: texto, header/footer y contraste estructural.

## Estado de partida

El repo ya tenia:

- WordPress como CMS.
- Tema custom `wp-content/themes/cbn-theme`.
- Vite para assets frontend.
- MU plugin `cbn-core` con CPTs iniciales.
- Dependencias ya declaradas: `gsap`, `lenis` y `swiper`.
- Documentacion previa del rediseño, pagos, color y alcance.

## Fuentes consultadas

- Manchester Basketball Club: https://manchesterbasketballclub.co.uk/
- WordPress Theme Handbook, CSS/JS: https://developer.wordpress.org/themes/basics/including-css-javascript/
- WordPress `theme.json`: https://developer.wordpress.org/themes/global-settings-and-styles/
- WordPress Accessibility: https://developer.wordpress.org/themes/functionality/accessibility/
- WordPress JavaScript Best Practices: https://developer.wordpress.org/themes/advanced-topics/javascript-best-practices/
- ACF Local JSON: https://www.advancedcustomfields.com/resources/local-json/
- Vite Backend Integration: https://vite.dev/guide/backend-integration
- GSAP ScrollTrigger: https://gsap.com/docs/v3/Plugins/ScrollTrigger/
- Lenis: https://www.lenis.dev/
- Swiper: https://swiperjs.com/get-started
- Claude Code Overview: https://code.claude.com/docs/en/overview
- Claude Code Best Practices: https://code.claude.com/docs/en/best-practices
- Codex manual local: secciones de buenas practicas, prompting, `AGENTS.md`, skills y MCP.

## Concepto visual generado

Concepto guardado en:

```text
docs/redesign/concepts/home-concept-2026-06-27.png
```

El concepto define:

- Header limpio con marca, navegacion y CTA.
- Hero blanco con energia roja y composicion deportiva.
- Ticker de partido o resultados en rojo.
- Bloque de proximo partido.
- Bloque de escuela y equipos.
- Noticias.
- CTAs de tienda e inscripcion.
- Sponsors.
- Footer negro.

## Asset generado para produccion

Asset guardado en:

```text
wp-content/themes/cbn-theme/assets/src/images/home-hero-basketball.jpg
```

Uso previsto:

- Imagen visual del hero.
- Sin texto.
- Sin logos.
- Sin referencias a Manchester.
- Generica, deportiva y propia para este proyecto.

El escudo oficial se copia al tema como:

```text
wp-content/themes/cbn-theme/assets/src/images/cbn-logo.png
```

## Prompt de concepto

Resumen del prompt usado:

```text
Crear un concepto desktop completo para la home de Club Baloncesto Navalcarnero.
Debe incluir header, hero, ticker, partido, equipos, noticias, tienda, inscripcion,
sponsors y footer. La proporcion visual debe ser 60% blanco, 30% rojo y 10% negro.
La inspiracion de Manchester Basketball Club solo puede ser conceptual. No copiar
layout exacto, textos, codigo, identidad, imagenes ni colores. UI implementable en
WordPress/Vite, accesible, con texto nativo y sin elementos decorativos innecesarios.
```

## Prompt de asset hero

Resumen del prompt usado:

```text
Crear un asset hero generico de baloncesto para una home WordPress. Fondo blanco
con lineas de cancha, trazo rojo de movimiento y jugador generico en rojo/negro
sobre la parte derecha. Sin texto, sin logos, sin marcas, sin Manchester, sin
imagenes de equipos reales y con espacio negativo a la izquierda para titular y CTAs.
```

## Decisiones de implementacion

- La primera home se implementa en `front-page.php`.
- El sistema visual se concentra en `assets/src/css/main.css`.
- Las mejoras JS se concentran en `assets/src/js/main.js`.
- Se usa GSAP + ScrollTrigger solo para reveals y entrada del hero.
- Se usa Lenis solo si no existe `prefers-reduced-motion`.
- Se usa Swiper solo en carruseles concretos.
- No se usa jQuery.
- No se usa ScrollMagic.
- No se usa Locomotive Scroll.
- No se implementa todavia pasarela de pago real.
- Tienda e inscripcion aparecen como CTAs y bloques de preparacion funcional.

## Alcance de esta primera version

Incluido:

- Home estatica, usable y visualmente mas completa.
- Navegacion base con fallback si no hay menu configurado.
- CTA a equipos, inscripcion, tienda, calendario y contacto.
- Bloque de proximo partido con datos ficticios claramente sustituibles.
- Ticker de resultados/proximos partidos.
- Carrusel de sponsors con Swiper.
- Animaciones progresivas con respeto a reduccion de movimiento.
- Documentacion para agentes de IA.

No incluido:

- WooCommerce configurado.
- Stripe configurado.
- Formularios reales.
- CPT queries reales desde WordPress.
- ACF fields.
- Administracion completa de partidos/equipos.
- Checkout o pagos.

## Buenas practicas para Codex y Claude Code

Antes de tocar codigo:

1. Leer `README.md`, `docs/redesign/*`, `AGENTS.md` y, si se usa Claude Code, `CLAUDE.md`.
2. Comprobar `git status --short --branch`.
3. Trabajar por cambios pequenos y verificables.
4. No anadir dependencias sin aprobacion.
5. No copiar la web de referencia.
6. Ejecutar `npm run verify` antes de cerrar.
7. Verificar visualmente en navegador cuando haya cambios frontend.

Para tareas con IA:

- Usar prompts con objetivo, contexto, restricciones y criterio de acabado.
- Separar investigacion, plan, implementacion y QA.
- Mantener reglas persistentes en `AGENTS.md` para Codex.
- Mantener `CLAUDE.md` como entrada equivalente para Claude Code.
- Evitar dos agentes editando los mismos archivos a la vez.
- Usar GitHub PRs pequenos y revisables.
- Usar Browser/Playwright para QA visual.
- Usar Context7 o documentacion oficial para librerias cuando haya duda de version.

## Criterios de aceptacion

- La home se ve mayoritariamente blanca.
- El rojo tiene presencia fuerte pero controlada.
- El negro no domina salvo header/footer y texto.
- La pagina no copia Manchester.
- El hero tiene presencia deportiva clara.
- Los CTAs de inscripcion y tienda estan visibles.
- El contenido no se solapa en movil.
- El sitio respeta `prefers-reduced-motion`.
- `npm run verify` pasa.
