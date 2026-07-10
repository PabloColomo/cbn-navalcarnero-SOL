# Informe de skills, flujos, subagentes y logica del proyecto

Fecha: 2026-07-02

Proyecto: redisenyo WordPress del Club Baloncesto Navalcarnero.

Este informe resume como esta organizado el proyecto, que skills y subagentes
existen dentro del repositorio, que flujos de trabajo gobiernan los cambios y
cual es la logica tecnica y operativa que mantiene el proyecto alineado.

## 1. Resumen ejecutivo

El repositorio combina cuatro capas principales:

- Producto: una web publica moderna para el Club Baloncesto Navalcarnero,
  orientada a familias, jugadores, entrenadores, patrocinadores y gestores del
  club.
- WordPress: un tema custom para la presentacion publica y un MU plugin propio
  para el modelo estructurado del club.
- Documentacion operativa: arquitectura, backlog, pagos, privacidad,
  importacion de datos, dominio, federacion, QA y decisiones pendientes.
- Sistema de agentes: skills repo-locales, subagentes TOML, prompts fallback y
  changelog para trabajar con trazabilidad.

La decision base es conservar WordPress como CMS, mantener la identidad visual
60% blanco, 30% rojo y 10% negro, y avanzar por cambios pequenyos, revisables y
validados antes de commit, push, PR o merge.

## 2. Fuentes revisadas

- `AGENTS.md`
- `CLAUDE.md`
- `README.md`
- `ARCHITECTURE.md`
- `BACKLOG.md`
- `PAYMENTS.md`
- `DATA_IMPORT.md`
- `PRIVACY_NOTES.md`
- `AGENT_CHANGELOG.md`
- `docs/agent-system/README.md`
- `docs/agent-system/agent-workflow-safety-quality-2026-06-29.md`
- `docs/agent-prompts/*.md`
- `.codex/agents/*.toml`
- `.agents/skills/*`
- `docs/redesign/*.md`
- `wp-content/themes/cbn-theme/**`
- `wp-content/mu-plugins/cbn-core/**`
- `.github/workflows/ci.yml`

## 3. Reglas maestras del proyecto

### Identidad y diseno

- Manchester Basketball Club solo puede usarse como inspiracion conceptual de
  energia, ritmo, navegacion y jerarquia.
- No se puede copiar su codigo, layout exacto, textos, imagenes, assets,
  colores o identidad.
- La web debe mantener 60% blanco, 30% rojo y 10% negro.
- Blanco: superficie dominante.
- Rojo: marca, CTAs, estados activos, tickers y bandas clave.
- Negro: texto, header/footer, contraste y estructura limitada.
- Amarillo u oro solo cuando venga del escudo o de assets oficiales.

### Stack

- WordPress como CMS.
- Tema custom: `wp-content/themes/cbn-theme`.
- MU plugin: `wp-content/mu-plugins/cbn-core`.
- Vite para assets.
- GSAP + ScrollTrigger para animacion selectiva.
- Lenis para smooth scroll progresivo.
- Swiper para carruseles reales.
- WooCommerce + Stripe como direccion recomendada para pagos, siempre con
  alcance explicito y modo test antes de produccion.

### Seguridad y proceso

- Revisar `git status --short --branch` antes de editar.
- No instalar dependencias sin aprobacion.
- No commitear ni pushear sin peticion explicita.
- No guardar secretos, tokens, credenciales de dominio ni credenciales de
  federacion.
- No editar manualmente `assets/dist`.
- Usar `apply_patch` para ediciones manuales.
- Respetar `prefers-reduced-motion`.
- En cambios visuales: mostrar capturas desktop y mobile antes de commit, push,
  PR o merge.
- En cambios no visuales: mostrar resumen o diff antes de commit, push, PR o
  merge.

## 4. Logica de arquitectura

### Separacion principal

```text
WordPress CMS
  -> MU plugin cbn-core: tipos de contenido, taxonomias y datos del club
  -> Tema cbn-theme: presentacion, plantillas, assets, ACF y home
  -> WooCommerce futuro: tienda, pedidos y checkout
  -> Importador futuro: datos oficiales de federacion
```

La regla es que los datos criticos del club viven en el MU plugin, no en el
tema. El tema puede cambiar visualmente sin perder equipos, partidos, sponsors o
documentos.

### Modulos funcionales

| Modulo          | Responsabilidad                                   | Estado actual                                 |
| --------------- | ------------------------------------------------- | --------------------------------------------- |
| Web publica     | Home, header, footer, secciones principales, CTAs | Base implementada en tema                     |
| Equipos         | CPT, campos ACF, futuras plantillas               | Modelo listo, plantillas completas pendientes |
| Partidos        | CPT, resultados, calendario, trazabilidad         | Modelo listo, vistas completas pendientes     |
| Noticias        | Posts nativos y categorias                        | Base WordPress disponible                     |
| Sponsors        | CPT, niveles, visibilidad, home                   | Modelo listo, carrusel home basico            |
| Inscripciones   | Formularios, consentimientos, pagos si aplica     | Disenyado, no implementado en produccion      |
| Tienda          | WooCommerce, productos, stock, pedidos            | Disenyado, no configurado en produccion       |
| Pagos           | Stripe/WooCommerce, webhooks, estados             | Documentado, requiere test mode               |
| Federacion      | Fuente oficial, importador, logs, upsert          | Preparado a nivel de modelo, sin acceso real  |
| Privacidad      | Menores, permisos, datos publicos/privados        | Documentado como regla transversal            |
| Infraestructura | Docker, CI, staging, dominio, backups             | Local validado; staging/dominio pendientes    |

## 5. Logica del codigo actual

### Tema `cbn-theme`

- `functions.php` define constantes del tema y carga `inc/setup.php`,
  `inc/assets.php`, `inc/acf.php` y `inc/home-content.php`.
- `inc/setup.php` activa soportes de tema: title tag, thumbnails, HTML5,
  align-wide y menus `primary` y `footer`.
- `inc/assets.php` intenta cargar el manifest de Vite desde
  `assets/dist/.vite/manifest.json`; si existe, encola CSS y JS compilados. Si
  no existe, usa el CSS fuente como fallback.
- `inc/acf.php` configura las rutas de guardado y carga de ACF JSON dentro del
  tema.
- `inc/home-content.php` centraliza defaults, lectura ACF y queries para la
  portada.
- `front-page.php` solo compone markup, escapa datos y consume
  `cbn_get_home_content()`.
- `header.php` da navegacion con menu WordPress si existe y fallback si no hay
  menu configurado.
- `footer.php` define enlaces estructurales, marca y redes placeholder.

### Home

El flujo de datos de la home es:

```text
Defaults del tema
  -> campos ACF de la portada si existen
  -> queries a CPTs/posts publicados si existen
  -> relleno con fallbacks si faltan contenidos
  -> front-page.php renderiza HTML escapado
```

Este patron permite que la home cargue aunque WordPress no tenga ACF, equipos,
partidos, noticias o sponsors reales.

### Frontend

- `assets/src/js/main.js` importa CSS, Lenis, GSAP, ScrollTrigger y Swiper.
- Lenis se activa solo si no hay `prefers-reduced-motion` y el dispositivo tiene
  puntero fino.
- GSAP anima hero y elementos `data-cbn-reveal` solo si no hay reduccion de
  movimiento.
- Swiper se aplica a carruseles marcados con `data-cbn-swiper` y tiene mensajes
  A11y para controles.

### MU plugin `cbn-core`

El MU plugin carga `post-types.php` y `taxonomies.php`, y registra todo en
`init`.

CPTs:

- `cbn_team`
- `cbn_player`
- `cbn_match`
- `cbn_sponsor`
- `cbn_document`

Taxonomias:

- `cbn_season`
- `cbn_sport_category`
- `cbn_competition`
- `cbn_venue`
- `cbn_sponsor_tier`

La logica es mantener contenido deportivo, sponsors y documentos como datos
estructurados y versionables desde WordPress.

### ACF JSON

Grupos actuales:

- `CBN Home Content`: campos editables de portada.
- `CBN Team Details`: identidad, staff, plantilla, portada y trazabilidad.
- `CBN Match Details`: datos oficiales, resultado, editorial y trazabilidad.
- `CBN Sponsor Details`: datos publicos, visibilidad y trazabilidad.

Los campos evitan depender de ACF Pro y separan:

- datos oficiales,
- datos editoriales,
- trazabilidad externa,
- fallbacks visibles cuando las taxonomias aun no estan normalizadas.

## 6. Skills repo-locales

### `github-multiagent-workflow`

Ruta:

```text
.agents/skills/github-multiagent-workflow/SKILL.md
```

Uso:

- Branches.
- Commits.
- Pull requests.
- Revisiones.
- Merge readiness.
- Alineacion.
- Changelog operativo.

Logica:

1. Preservar la peticion original como alcance.
2. Leer `AGENTS.md`, `docs/agent-system/README.md`,
   `AGENT_CHANGELOG.md` y estado Git.
3. Usar subagentes o, si no estan disponibles, prompts Markdown fallback.
4. Registrar acciones relevantes en `AGENT_CHANGELOG.md`.
5. Bloquear merges con tests fallidos, conflictos, alineacion dudosa, changelog
   obsoleto o validacion humana pendiente.

### `cbn-imagegen-design-harness`

Ruta:

```text
.agents/skills/cbn-imagegen-design-harness/SKILL.md
```

Uso:

- Diseno visual CBN.
- Conceptos con Image Gen.
- Redisenyo de Home, Club, Teams, Matches, News, Sponsors, Registration, Shop,
  Contact y campanyas.
- Implementacion de una direccion visual aprobada.
- QA visual en navegador.

Logica:

1. Preflight: leer reglas, docs y estado Git.
2. Gate de brief: confirmar objetivo, audiencia, CTAs, modulos e
   interactividad.
3. Concepting: generar 3 conceptos cuando el diseno sea amplio o ambiguo.
4. Bloqueo de target visual: no codificar hasta que el usuario seleccione o
   valide una direccion.
5. Extraccion de sistema visual: paleta, tipografia, layout, CTAs, assets y
   responsive.
6. Implementacion WordPress con cambios acotados.
7. Validacion: formato, build, diff check, navegador, desktop/mobile, consola,
   reduced motion e imagenes.
8. Handoff: capturas y validacion humana antes de Git.

### Skill interface YAML

Ruta:

```text
.agents/skills/cbn-imagegen-design-harness/agents/openai.yaml
```

Define nombre visible, descripcion corta, prompt por defecto y permite
invocacion implicita del harness visual.

## 7. Skills externos referenciados por el harness visual

Estos no viven en el repositorio, pero el harness los cita como capacidades a
usar cuando esten disponibles:

| Skill o capacidad                           | Funcion dentro del flujo CBN       | Nota                                                                |
| ------------------------------------------- | ---------------------------------- | ------------------------------------------------------------------- |
| `product-design:get-context`                | Cerrar brief antes de idear        | Usar cuando el brief no esta confirmado                             |
| `product-design:ideate`                     | Generar alternativas visuales      | Preferido para 3 opciones de diseno                                 |
| `build-web-apps:frontend-app-builder`       | Implementar frontend real          | Aplicar respetando WordPress existente                              |
| `imagegen`                                  | Crear assets o conceptos raster    | Usar para visuales bitmap, no para copiar referencias               |
| `product-design:image-to-code`              | Traducir mockup aprobado a codigo  | Solo despues de target visual seleccionado                          |
| `product-design:design-qa`                  | Comparar referencia vs render      | Referenciado por el harness; si no esta disponible, hacer QA manual |
| `build-web-apps:frontend-testing-debugging` | QA de frontend renderizado         | Para navegador, responsive y errores                                |
| `browser:control-in-app-browser`            | Validacion en navegador integrado  | Preferido frente a Playwright cuando aplica                         |
| `browser-use:browser`                       | Navegacion/inspeccion alternativa  | Fallback de navegador                                               |
| `github-multiagent-workflow`                | Git, PR, review, merge y changelog | Solo cuando el trabajo pasa a GitHub                                |

## 8. Subagentes Codex

Los subagentes estan definidos en:

```text
.codex/agents/*.toml
```

Cada TOML es intencionadamente corto y delega en un prompt Markdown de
`docs/agent-prompts/`.

| Subagente            | Responsabilidad                                                         | Prompt fallback                            |
| -------------------- | ----------------------------------------------------------------------- | ------------------------------------------ |
| `coordinator`        | Descompone tareas, conserva alcance, lee historial, identifica bloqueos | `docs/agent-prompts/coordinator.md`        |
| `github_operator`    | Prepara o ejecuta branch, commit, PR y merge dentro de permisos         | `docs/agent-prompts/github_operator.md`    |
| `code_reviewer`      | Revisa diffs, regresiones, seguridad, scope y tests faltantes           | `docs/agent-prompts/code_reviewer.md`      |
| `pull_request_agent` | Prepara titulo, cuerpo, checklist, riesgos y evidencias de PR           | `docs/agent-prompts/pull_request_agent.md` |
| `alignment_guard`    | Clasifica acciones contra la peticion original y docs del proyecto      | `docs/agent-prompts/alignment_guard.md`    |
| `changelog_keeper`   | Mantiene `AGENT_CHANGELOG.md` con acciones y decisiones trazables       | `docs/agent-prompts/changelog_keeper.md`   |
| `final_validator`    | Bloquea merges inseguros y valida preparacion final                     | `docs/agent-prompts/final_validator.md`    |

## 9. Logica de los prompts fallback

Los prompts fallback permiten ejecutar el sistema manualmente si Codex no expone
subagentes custom en una sesion.

- `coordinator`: resume tarea, restricciones, repo state, modelo recomendado,
  agentes necesarios, bloqueos y siguiente accion.
- `github_operator`: inspecciona estado Git, evita operaciones irreversibles y
  nunca afirma acciones no ejecutadas.
- `code_reviewer`: lidera con hallazgos por severidad y cita archivos/lineas
  cuando sea posible.
- `pull_request_agent`: genera PR body con resumen, motivo, validacion, riesgos
  y notas.
- `alignment_guard`: devuelve `aligned`, `partially_aligned`, `not_aligned` o
  `needs_human_confirmation`.
- `changelog_keeper`: registra acciones relevantes, decisiones, bloqueos,
  supuestos y validacion.
- `final_validator`: decide `ready`, `blocked` o `needs_human_confirmation` para
  merge.

## 10. Flujos de trabajo

### Flujo general de cualquier cambio

```text
Leer reglas y docs base
  -> git status
  -> delimitar alcance
  -> editar el minimo necesario
  -> validar
  -> actualizar changelog si aplica
  -> entregar resumen o capturas
  -> esperar validacion humana antes de Git
```

### Flujo GitHub multiagente

```text
coordinator
  -> alignment_guard
  -> github_operator si hay accion Git
  -> code_reviewer
  -> changelog_keeper
  -> pull_request_agent si hay PR
  -> final_validator antes de merge
```

Bloqueos obligatorios:

- tests fallidos,
- conflictos,
- secretos,
- scope dudoso,
- PR incompleta,
- changelog obsoleto,
- validacion humana pendiente,
- acciones destructivas sin confirmacion explicita.

### Flujo visual con Image Gen

```text
Preflight
  -> brief
  -> 3 conceptos si el diseno es amplio
  -> seleccion del usuario
  -> extraccion de sistema visual
  -> implementacion WordPress
  -> npm run verify
  -> QA navegador desktop/mobile
  -> capturas al usuario
  -> solo despues, Git si se pide
```

Regla clave: no codificar una direccion visual amplia sin concepto aprobado.

### Flujo de contenido Home

```text
ACF si existe
  -> CPTs/posts si existen
  -> defaults seguros
  -> render progresivo
```

El objetivo es que la portada nunca quede rota por falta de contenido real.

### Flujo de datos de federacion

```text
Federacion
  -> auditoria solo lectura
  -> documentar fuente y permisos
  -> parser/normalizador
  -> dry-run
  -> upsert por external_id
  -> logs sin credenciales
  -> WordPress como cache publicable
```

No se permite scraping autenticado sin autorizacion. No se guardan credenciales
en Git, docs, logs o chats.

### Flujo de pagos

```text
WooCommerce
  -> Stripe o Redsys en modo test
  -> productos o inscripciones
  -> checkout proveedor
  -> webhooks validados
  -> estados de pedido/pago
  -> reconciliacion
  -> produccion solo con sign-off
```

Estados de pago documentados:

- `pending`
- `checkout_created`
- `paid`
- `failed`
- `cancelled`
- `refunded`
- `disputed`

La produccion queda bloqueada hasta tener SSL, textos legales, test mode
validado, emails, responsable operativo, backups y pasarela aprobada.

### Flujo de importacion deportiva

Prioridad de fuente:

1. API oficial.
2. CSV/Excel/XML/JSON oficial.
3. CSV/Excel manual estructurado.
4. Importacion semiautomatica.
5. Scraping solo si esta permitido y documentado.

Regla de upsert:

```text
external_source + external_id
  -> si falta, comparar temporada + competicion + fecha + local + visitante
  -> si hay ambiguedad, revision manual
```

### Flujo de validacion

Local:

- `npm run format:check`
- `npm run build`
- `npm run verify`
- `git diff --check`
- Browser QA para cambios visuales cuando haya entorno disponible.

CI:

- `npm ci`
- `npm run format:check`
- `npm run build`
- `php -l` en `wp-content`

## 11. Changelog operativo

`AGENT_CHANGELOG.md` es el registro factual del trabajo de agentes.

Debe registrar:

- acciones relevantes,
- decisiones,
- supuestos,
- bloqueos,
- archivos afectados,
- validaciones,
- riesgos,
- siguiente accion recomendada.

No debe registrar acciones que no ocurrieron ni ocultar checks bloqueados.

## 12. Politica de modelos

La politica documentada dice:

- Usar el modelo mas pequenyo capaz para tareas simples y reversibles si la
  version de Codex lo permite.
- Usar modelo fuerte para arquitectura, revision, seguridad, merge readiness,
  pagos, federacion, despliegue, credenciales, refactors grandes o requisitos
  ambiguos.
- No anyadir campos `model` a `.codex/agents/*.toml` salvo soporte documentado
  por la version activa de Codex.

En la practica, el repositorio documenta la politica, pero no puede forzarla por
si solo si el runtime no expone seleccion de modelo.

## 13. Estado actual del proyecto

### Ya existe

- Base WordPress documentada.
- Tema custom.
- MU plugin con CPTs y taxonomias.
- Home MVP con fallbacks.
- ACF JSON para home, equipos, partidos y sponsors.
- Assets y Vite configurados.
- GSAP, Lenis y Swiper declarados.
- CI GitHub.
- Docker local documentado y validado previamente.
- Sistema multiagente repo-local.
- Harness visual CBN con Image Gen.
- Documentos de arquitectura, backlog, pagos, importacion y privacidad.

### Pendiente

- Plantillas publicas completas para equipos, partidos, sponsors y documentos.
- WooCommerce configurado con productos reales.
- Stripe o Redsys en modo test.
- Formularios definitivos de inscripcion.
- Textos legales finales.
- Staging real.
- Dominio/DNS con correo verificado.
- Auditoria de fuente federativa.
- Importador real.
- QA visual final en navegador con capturas desktop/mobile.
- Validacion del club antes de produccion.

## 14. Riesgos principales

- Scope creep por mezclar home, tienda, pagos, federacion y dominio en un unico
  cambio.
- Exponer datos de menores si no se mantienen perfiles publicos minimos.
- Activar pagos sin modo test, textos legales, SSL, emails y responsable
  operativo.
- Construir scraping federativo antes de confirmar permisos.
- Romper correo del club al cambiar DNS sin revisar MX, SPF, DKIM y DMARC.
- Dejar `AGENT_CHANGELOG.md` obsoleto y perder trazabilidad.
- Publicar assets sin ejecutar build en el entorno de despliegue.

## 15. Mapa rapido de archivos

| Area                | Ruta                                      |
| ------------------- | ----------------------------------------- |
| Reglas de agente    | `AGENTS.md`                               |
| Guia Claude         | `CLAUDE.md`                               |
| Changelog operativo | `AGENT_CHANGELOG.md`                      |
| Arquitectura MVP    | `ARCHITECTURE.md`                         |
| Backlog incremental | `BACKLOG.md`                              |
| Pagos               | `PAYMENTS.md`                             |
| Importacion         | `DATA_IMPORT.md`                          |
| Privacidad          | `PRIVACY_NOTES.md`                        |
| Sistema de agentes  | `docs/agent-system/`                      |
| Prompts fallback    | `docs/agent-prompts/`                     |
| Subagentes Codex    | `.codex/agents/`                          |
| Skills repo-locales | `.agents/skills/`                         |
| Tema                | `wp-content/themes/cbn-theme/`            |
| Helpers del tema    | `wp-content/themes/cbn-theme/inc/`        |
| ACF JSON            | `wp-content/themes/cbn-theme/acf-json/`   |
| Assets fuente       | `wp-content/themes/cbn-theme/assets/src/` |
| MU plugin           | `wp-content/mu-plugins/cbn-core/`         |
| CI                  | `.github/workflows/ci.yml`                |

## 16. Recomendacion operativa

Para la siguiente fase conviene mantener este orden:

1. Cerrar una rama pequenya para plantillas publicas de equipos y partidos.
2. Validar visualmente desktop/mobile.
3. Preparar tienda e inscripciones solo cuando esten confirmados productos,
   precios, campos legales y pasarela.
4. Auditar federacion en solo lectura antes de cualquier automatizacion.
5. Mantener `AGENT_CHANGELOG.md` actualizado en cada decision relevante.

La logica general del proyecto es conservar una base WordPress simple,
estructurada y trazable, anadiendo complejidad solo cuando haya fuente,
validacion y responsable operativo claros.
