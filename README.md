# Club Baloncesto Navalcarnero

Repositorio inicial para la nueva web del Club Baloncesto Navalcarnero.

El proyecto parte de una direccion clara: crear una web nueva, original y mantenible para el club, usando Manchester Basketball Club solo como inspiracion conceptual de energia deportiva, estructura, animacion y presencia de marca. No se copiara codigo, diseno exacto, textos, imagenes ni identidad visual.

## Requisitos actualizados

La documentacion de alcance incluye una actualizacion posterior al plan inicial:

- Proporcion cromatica obligatoria para toda la web: 60% blanco, 30% rojo y 10% negro.
- Permisos de imagen y datos confirmados por el responsable del proyecto, manteniendo controles de privacidad en la implementacion.
- Pasarela de pago incluida para tienda de ropa del club e inscripciones.
- WooCommerce + Stripe queda como enfoque recomendado para pagos dentro de WordPress.

Documento de referencia: `docs/redesign/navalcarnero-requirements-update-2026-06-27.md`.

## Stack inicial

- WordPress como CMS.
- Tema custom: `wp-content/themes/cbn-theme`.
- Plugin MU propio para contenido estructurado: `wp-content/mu-plugins/cbn-core`.
- WooCommerce para tienda, pedidos, productos de ropa e inscripciones con pago.
- Stripe como pasarela principal recomendada.
- Vite para assets frontend.
- GSAP + ScrollTrigger para animaciones selectivas.
- Lenis para scroll suave.
- Swiper para carruseles.
- Docker Compose para entorno local WordPress + MariaDB.
- WP-CLI local para validar plugins, tema y ACF JSON.

## Puesta en marcha local

1. Copia variables de entorno:

   ```bash
   cp .env.example .env
   ```

2. Levanta WordPress:

   ```bash
   docker compose up -d
   ```

3. Si necesitas phpMyAdmin o WP-CLI, levanta el perfil de herramientas:

   ```bash
   docker compose --profile tools up -d
   ```

4. Instala dependencias frontend:

   ```bash
   npm install
   ```

5. Compila assets del tema:

   ```bash
   npm run build
   ```

6. Abre WordPress:

   ```text
   http://localhost:8080
   ```

## Estructura

```text
docs/
  redesign/                  Plan, resumen y actualizaciones del rediseno
wp-content/
  mu-plugins/
    cbn-core.php             Loader del plugin MU
    cbn-core/                Tipos de contenido y taxonomias del club
  themes/
    cbn-theme/               Tema custom inicial
compose.yaml                 Entorno local WordPress
docker/                      Configuracion local de PHP/WordPress para Docker
package.json                 Frontend tooling
vite.config.js               Build de assets del tema
```

## Contenido estructurado inicial

El plugin `cbn-core` registra:

- Equipos.
- Jugadores.
- Partidos.
- Sponsors.
- Documentos.
- Temporadas.
- Categorias deportivas.
- Competiciones.
- Instalaciones.
- Niveles de sponsor.

## Documentacion del plan

- `docs/redesign/navalcarnero-redesign-plan.md`
- `docs/redesign/navalcarnero-redesign-summary.txt`
- `docs/redesign/navalcarnero-requirements-update-2026-06-27.md`
- `docs/redesign/homepage-mvp-2026-06-27.md`
- `docs/redesign/home-content-foundation-2026-06-27.md`
- `docs/redesign/federation-domain-architecture-2026-06-27.md`
- `docs/redesign/next-steps-plan-2026-06-27.md`
- `docs/redesign/sports-data-model-2026-06-27.md`
- `docs/redesign/local-docker-validation-2026-06-27.md`
- `docs/redesign/home-reference-alignment-2026-06-27.md`
- `docs/LOCAL_DOCKER.md`
- `docs/PROJECT_SETUP.md`
- `AGENTS.md`
- `CLAUDE.md`

## Documentacion operativa MVP

- `ARCHITECTURE.md`: decision de stack, modulos, riesgos y arquitectura.
- `BACKLOG.md`: MVP urgente, MVP funcional completo y ciclos incrementales.
- `PAYMENTS.md`: pasarela, sandbox, estados, webhooks, pruebas y produccion.
- `DATA_IMPORT.md`: federacion, CSV/API/manual, validacion, deduplicacion y logs.
- `PRIVACY_NOTES.md`: menores, consentimientos, datos publicos/privados e imagenes.

## Notas

- El repositorio no incluye WordPress core.
- Los uploads y plugins instalados localmente no se versionan.
- Los assets compilados en `assets/dist` se generan con `npm run build`.
- Las decisiones de diseno visual final deben respetar el sistema 60% blanco, 30% rojo y 10% negro.
- La pasarela de pago debe validarse en modo test antes de publicarse.
