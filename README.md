# Club Baloncesto Navalcarnero

Repositorio inicial para la nueva web del Club Baloncesto Navalcarnero.

El proyecto parte de una direccion clara: crear una web nueva, original y mantenible para el club, usando Manchester Basketball Club solo como inspiracion conceptual de energia deportiva, estructura, animacion y presencia de marca. No se copiara codigo, diseno exacto, textos, imagenes ni identidad visual.

## Repositorio correcto

La version completa **CBN Sol / Pista Viva** vive en:

```text
https://github.com/PabloColomo/cbn-navalcarnero-SOL
```

El proyecto se ejecuta desde la raiz de ese repositorio. No uses copias antiguas
o carpetas anidadas llamadas `club-baloncesto-navalcarnero`.

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

### Windows: preparacion automatica recomendada

Requisitos: Git, Docker Desktop y Node.js 22 o superior.

En una copia nueva:

```powershell
git clone https://github.com/PabloColomo/cbn-navalcarnero-SOL.git
cd cbn-navalcarnero-SOL
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1
```

El bootstrap es idempotente: se puede volver a ejecutar. Compila los assets,
levanta Docker, instala WordPress si falta, activa `CBN Theme`, crea las paginas
esenciales y configura Inicio/Noticias/permalinks. Si crea WordPress, muestra al
final unas credenciales de administrador exclusivamente locales.

En una copia que ya existia:

```powershell
git remote -v
git pull --ff-only
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1
```

`git remote -v` debe apuntar a `PabloColomo/cbn-navalcarnero-SOL`. Si apunta a
otro repositorio, no estas ejecutando CBN Sol.

La URL se obtiene de `WORDPRESS_PORT` en `.env` y se muestra al terminar. El
valor predeterminado es `http://localhost:8080`. El acceso administrador esta en
`/wp-login.php` y tambien aparece en el pie de la web.

### Preparacion manual

1. Copia variables de entorno:

   ```bash
   cp .env.example .env
   ```

2. Instala y compila las dependencias exactas del lockfile:

   ```bash
   npm ci
   npm run build
   ```

3. Levanta WordPress:

   ```bash
   docker compose up -d
   ```

4. Si necesitas phpMyAdmin o WP-CLI, usa el perfil de herramientas:

   ```bash
   docker compose --profile tools up -d
   ```

5. Abre WordPress, completa el instalador si la base de datos es nueva y activa
   `CBN Theme`:

   ```text
   http://localhost:8080
   ```

La base de datos, los usuarios y los uploads son datos de entorno y no se
publican en Git. El diseño, los estilos, el logo, las imagenes del tema, los
sonidos sintetizados y el panel CBN si estan versionados. En entorno local el
tema usa los CSS fuente con version `filemtime`, por lo que un `assets/dist`
antiguo no puede sustituir el diseño actual. El bootstrap genera un manifest
nuevo para cargar tambien el bundle de animaciones; si el manifest queda por
detras de los fuentes, el tema vuelve automaticamente al CSS versionado.

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
- Los assets compilados en `assets/dist` se generan con `npm ci && npm run build` y no se versionan.
- El manifest de Vite solo se usa cuando es mas reciente que los fuentes; en caso contrario se carga el CSS fuente versionado.
- Si el navegador ya estaba abierto durante una actualizacion, usa `Ctrl+F5` una vez.
- Las decisiones de diseno visual final deben respetar el sistema 60% blanco, 30% rojo y 10% negro.
- La pasarela de pago debe validarse en modo test antes de publicarse.
