# Backlog

This backlog keeps the project incremental. Each cycle must be small, verifiable, and leave the repository in a stable state.

## MVP Urgente

Goal: a fast, presentable, mobile-first public website that the club can review even if final content is incomplete.

Included:

- Base technical structure.
- Main visual direction using 60% white, 30% red, 10% black.
- Navigation, header, footer.
- Home.
- Club page.
- Basic teams area.
- News/announcements.
- Sponsors.
- Contact.
- Basic registrations entry point.
- Shop structure.
- Payment structure in test mode only when provider data exists.
- Sports data structure.
- Basic SEO.
- Legal placeholder pages in staging if final legal copy is missing.

Not guaranteed for urgent MVP:

- Real production payments.
- Complete federation automation.
- Final product catalog.
- Final legal copy.
- Final player/minor public profiles.

## MVP Funcional Completo

Goal: an operational first version ready for controlled launch.

Included:

- WooCommerce with real products, sizes, prices, and stock.
- Payment gateway validated in test mode.
- Definitive registration forms and consents.
- Transactional emails.
- Basic order and registration management.
- Manual or CSV load of matches/results.
- Admin usage documentation.
- Staging ready.
- Production readiness checklist.

Blocked until confirmed:

- Production gateway keys.
- Legal/fiscal approval.
- Final privacy, purchase, returns, and registration texts.
- Club owner for orders, refunds, and reconciliation.
- SSL, backups, and staging validation.

## Cycle Overview

| ID  | Name                                | Objective                                                       | Module         | Dependencies       | Areas Affected                         | Acceptance Criteria                              | Minimum Test            | Risks                      | Status    |
| --- | ----------------------------------- | --------------------------------------------------------------- | -------------- | ------------------ | -------------------------------------- | ------------------------------------------------ | ----------------------- | -------------------------- | --------- |
| 0   | Base tecnica                        | Stabilize docs, env example, architecture, and workflow.        | Infrastructure | Existing repo      | Root docs, README, `.env.example`      | Required docs exist and verify passes.           | `npm run verify`        | Docs drift.                | Completed |
| 1   | Arquitectura visual y navegacion    | Public shell with header/footer/menu/mobile.                    | Public website | Cycle 0            | Theme templates/CSS/JS                 | Navigation works and mobile is readable.         | Browser desktop/mobile  | Visual regressions.        | In review |
| 2   | Home MVP                            | Presentable home connected to safe fallbacks/content.           | Public website | Cycle 1            | `front-page.php`, home content, CSS/JS | Home usable without ACF/data.                    | Browser desktop/mobile  | Placeholder content.       | In review |
| 3   | Contenido institucional             | Club, values, facilities, school/contact content pages.         | Public website | Cycle 1            | WP pages/templates/docs                | Pages have clear structure.                      | Manual WP check         | Missing final copy.        | Pending   |
| 4   | Equipos y datos deportivos manuales | Team archive/detail and basic match/result views.               | Sports data    | Cycle 0            | CPT templates, ACF fields, CSS         | Teams and matches render from CPTs or fallbacks. | Create test CPT content | Minor privacy.             | Pending   |
| 5   | Noticias y comunicados              | News list/detail and basic categories.                          | News           | Cycle 1            | WP posts/templates                     | News archive/detail usable.                      | Create test post        | Content migration.         | Pending   |
| 6   | Sponsors                            | Sponsors archive, tiers, ordering, home connection.             | Sponsors       | Cycle 0            | Sponsor CPT templates, CSS             | Sponsor logos/tier display works.                | Create test sponsor     | Logo rights/content.       | Pending   |
| 7   | Inscripciones basicas               | Registration info and first form/flow.                          | Registrations  | Legal fields draft | Page/form/plugin/custom code           | Submission is validated and stored/notified.     | Submit test form        | Sensitive data.            | Pending   |
| 8   | Tienda MVP                          | WooCommerce shop structure and first products.                  | Shop           | Product list       | WooCommerce config/theme docs          | Product/checkout structure exists in test.       | Add test product        | Stock/prices missing.      | Pending   |
| 9   | Pagos sandbox                       | Gateway test checkout and callbacks/webhooks.                   | Payments       | Gateway decision   | WooCommerce/Stripe/Redsys config/docs  | Test success/fail/cancel paths documented.       | Sandbox payment         | Misconfiguration.          | Pending   |
| 10  | Importacion deportiva inicial       | Manual/CSV import with validation and logs.                     | Sports data    | Source format      | Importer, docs                         | Import dry-run/upsert does not duplicate.        | Import sample file      | Unknown federation format. | Pending   |
| 11  | Administracion y gestion            | Roles and usage guide.                                          | Administration | Modules in place   | Roles/docs/admin config                | Club can operate common tasks.                   | Admin walkthrough       | Over-permission.           | Pending   |
| 12  | Hardening y entrega                 | Responsive, accessibility, SEO, performance, security, backups. | Infrastructure | MVP complete       | Docs/theme/config                      | Launch checklist complete.                       | Full QA                 | External hosting/DNS.      | Pending   |

## Slices de identidad visual (pendientes de entregas del cliente o decision)

Cada uno es un slice corto con su propio QA visual antes de commit. Registrados 2026-07-03.

| Slice | Descripcion                                                                                                                 | Bloqueado por                       |
| ----- | --------------------------------------------------------------------------------------------------------------------------- | ----------------------------------- |
| ID-1  | Tipografia de marca: elegir display condensada y texto, sustituir pilas de sistema en `--cbn-font-display`/`--cbn-font-ui`. | Decision de diseno (no cliente).    |
| ID-2  | Sustituir imagenes generadas del Home por fotos autorizadas del club.                                                       | Fotos + autorizaciones del cliente. |
| ID-3  | Logo definitivo en alta calidad (header, footer, favicon).                                                                  | Entrega del cliente.                |
| ID-4  | Textos reales del Home y claims aprobados por el club.                                                                      | Textos del cliente.                 |
| ID-5  | Titulo/tagline del hero definitivo.                                                                                         | Validacion del cliente.             |
| ID-6  | Enlaces reales de redes sociales en el footer.                                                                              | URLs del cliente.                   |

## Ciclo 0 - Base tecnica

### MVP objetivo

Alcanzar una base documental y operativa estable para que los siguientes ciclos se ejecuten sin deriva: stack elegido, modulos, riesgos, pagos, datos federativos, privacidad, variables de entorno y backlog.

### Feature atomica

Crear y conectar la documentacion raiz obligatoria del proyecto MVP.

### Alcance

Incluye:

- `ARCHITECTURE.md`
- `BACKLOG.md`
- `PAYMENTS.md`
- `DATA_IMPORT.md`
- `PRIVACY_NOTES.md`
- Revision de `.env.example`
- Enlaces documentales desde `README.md`
- Checkpoint en `AGENT_CHANGELOG.md`

No incluye:

- Nuevas pantallas publicas.
- Instalacion de plugins.
- Configuracion real de WooCommerce o Stripe.
- Importacion federativa.
- Capturas visuales.
- Commit, push, PR o merge.

### Archivos o modulos previstos

- `ARCHITECTURE.md`: decision de stack, requisitos, matrices, modulos y mapa del repo.
- `BACKLOG.md`: MVP urgente, MVP completo y ciclos incrementales.
- `PAYMENTS.md`: arquitectura de pago, sandbox, estados, pruebas y paso a produccion.
- `DATA_IMPORT.md`: estrategia federativa, formatos, validacion, deduplicacion y logs.
- `PRIVACY_NOTES.md`: menores, consentimientos, datos publicos/privados e imagenes.
- `.env.example`: variables locales y marcadores seguros para futuras integraciones.
- `README.md`: indice hacia documentos operativos.
- `AGENT_CHANGELOG.md`: trazabilidad del ciclo.

### Plan tecnico

1. Leer documentacion base y estado Git.
2. Inventariar stack y archivos existentes.
3. Crear documentos raiz con decisiones vigentes.
4. Actualizar README y `.env.example` si falta trazabilidad operativa.
5. Ejecutar formato y build mediante `npm run verify`.
6. Documentar checkpoint y riesgos vivos.

### Implementacion realizada

Se creo la capa documental raiz del MVP y no se cambio codigo runtime. La base queda orientada a WordPress profesional con ruta hibrida futura para importacion federativa.

### Comandos ejecutados o recomendados

```bash
git status --short --branch
npm run verify
```

### Validacion

Typecheck: no aplica en este stack.

Lint: `npm run format:check` paso dentro de `npm run verify`.

Tests: no hay suite automatizada de tests en este ciclo.

Build: `npm run build` paso dentro de `npm run verify`.

Prueba manual: revision documental y diff no visual; no se requieren capturas porque el ciclo no cambia interfaz.

### Debug

Errores encontrados:

- No se encontraron errores de formato ni build.

Causa:

- No aplica.

Solucion:

- No aplica.

Verificacion:

- `npm run verify` paso correctamente.

### Checkpoint

Estado estable alcanzado:

- Documentacion base creada, README enlazado, variables de ejemplo ampliadas sin secretos y verificacion local completada.

Pendiente:

- Revision humana del resumen/diff antes de cualquier commit, push, PR o merge.
- Decisiones externas sobre pasarela, productos, textos legales, hosting/dominio y fuente federativa.

Riesgos:

- Los documentos pueden quedar obsoletos si pagos, hosting, federation o legal se deciden fuera del repo sin registrar el cambio.

Decisiones tomadas:

- Continuar con WordPress profesional y ruta hibrida para importacion federativa.
- No implementar pagos reales ni importacion en Ciclo 0.

### Siguiente MVP incremental

Ciclo 1: arquitectura visual y navegacion publica, con verificacion en navegador y capturas desktop/mobile antes de cualquier commit o PR.
