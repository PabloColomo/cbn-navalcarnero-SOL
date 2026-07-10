# Home content foundation

Fecha: 2026-06-27

## Objetivo

Convertir la home MVP en una base mantenible desde WordPress sin romper el tema cuando ACF, contenido real o plugins comerciales aun no esten instalados.

## Resultado

La plantilla `front-page.php` deja de depender de textos rigidos y consume datos desde:

```text
wp-content/themes/cbn-theme/inc/home-content.php
```

La integracion de ACF queda preparada en:

```text
wp-content/themes/cbn-theme/inc/acf.php
wp-content/themes/cbn-theme/acf-json/group_cbn_home_content.json
```

## Principios

- La home debe renderizar siempre, incluso sin ACF.
- Los datos por defecto viven en codigo, con textos seguros y revisables.
- Si ACF esta activo, los campos de la pagina marcada como portada pueden sobrescribir hero, CTAs, partido destacado, encabezados y bloques comerciales.
- Los listados dinamicos usan CPTs o posts nativos cuando existen contenidos publicados.
- Si no hay contenidos, se mantienen los fallbacks del MVP.

## Campos editables con ACF

El grupo `CBN Home Content` se muestra en la pagina marcada como portada.

Campos incluidos:

- Hero title line 1.
- Hero title line 2.
- Hero title line 3.
- Hero lead.
- Hero primary CTA label.
- Hero primary CTA URL.
- Hero secondary CTA label.
- Hero secondary CTA URL.
- Hero image.
- Match label.
- Home team.
- Away team.
- Match date.
- Match venue.
- Match CTA URL.
- Teams heading.
- Teams link label.
- Teams link URL.
- News heading.
- Sponsors heading.
- Shop title.
- Shop text.
- Shop CTA label.
- Shop CTA URL.
- Registration title.
- Registration text.
- Registration CTA label.
- Registration CTA URL.

No se usan repeaters para evitar depender de ACF Pro en esta fase.

## Contenido conectado automaticamente

Cuando existan contenidos publicados:

- `cbn_team`: alimenta las tarjetas de equipos.
- `post`: alimenta las noticias.
- `cbn_sponsor`: alimenta el carrusel de patrocinadores.
- `cbn_match`: alimenta el ticker de partidos de forma basica.

El partido destacado sigue usando campos simples de ACF porque aun no hay modelo de campos deportivos para equipos, rival, fecha, hora, resultado y pabellon.

## Fallbacks

Si no hay ACF o contenido publicado:

- El hero usa el asset `home-hero-basketball.jpg`.
- La portada usa textos por defecto.
- Equipos, noticias, ticker y sponsors mantienen contenido de muestra.

## Decisiones

- ACF JSON se versiona dentro del tema en `acf-json/`.
- `inc/acf.php` registra rutas de carga y guardado de ACF JSON.
- `front-page.php` solo compone markup y escapa datos.
- `inc/home-content.php` concentra defaults, lectura ACF y queries.
- La tienda y las inscripciones siguen siendo CTAs; WooCommerce/Stripe quedan para una fase separada.

## Pendiente

- Definir campos reales para `cbn_match`.
- Definir campos reales para `cbn_team`.
- Definir campos reales para `cbn_sponsor`.
- Crear campos de opciones globales para redes, contacto, logos y footer.
- Conectar WooCommerce y Stripe en modo test.
- Validar todo en WordPress real con ACF instalado.
