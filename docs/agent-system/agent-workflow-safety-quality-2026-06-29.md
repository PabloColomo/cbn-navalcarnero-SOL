# Auditoria del flujo de agentes

Fecha: 2026-06-29

## 1. Objetivo

Validar que el flujo repo-local de agentes es seguro, trazable y util para trabajar con ramas, commits, PRs, revisiones, merges, alineacion y changelog.

## 2. Componentes instalados

El flujo esta compuesto por:

- Skill repo-local:
  - `.agents/skills/github-multiagent-workflow/SKILL.md`.
- Definiciones de agentes:
  - `.codex/agents/coordinator.toml`.
  - `.codex/agents/github_operator.toml`.
  - `.codex/agents/code_reviewer.toml`.
  - `.codex/agents/pull_request_agent.toml`.
  - `.codex/agents/alignment_guard.toml`.
  - `.codex/agents/changelog_keeper.toml`.
  - `.codex/agents/final_validator.toml`.
- Prompts fallback:
  - `docs/agent-prompts/*.md`.
- Documentacion:
  - `docs/agent-system/README.md`.
- Trazabilidad:
  - `AGENT_CHANGELOG.md`.

Estado:

- El paquete esta publicado en `origin/main`.
- El ultimo commit que lo publico es `8ce06f1 chore: add GitHub multiagent workflow`.
- Las herramientas de subagentes estan disponibles en esta sesion y se han usado para auditoria de solo lectura.

## 3. Roles y responsabilidades

### coordinator

Responsable de:

- Preservar el alcance original.
- Leer historia y estado del repo.
- Dividir trabajo.
- Identificar bloqueos.
- Recomendar siguiente accion.

### alignment_guard

Responsable de:

- Comprobar que las acciones encajan con la peticion original.
- Marcar `aligned`, `partially_aligned`, `not_aligned` o `needs_human_confirmation`.
- Detectar scope creep, aprobaciones ausentes y acciones inseguras.

### github_operator

Responsable de:

- Preparar acciones GitHub.
- Evitar operaciones irreversibles sin permiso.
- Distinguir lo ejecutado de lo recomendado.

### code_reviewer

Responsable de:

- Revisar diffs.
- Detectar riesgos funcionales.
- Identificar pruebas faltantes.

### pull_request_agent

Responsable de:

- Preparar titulo, cuerpo, checklist, riesgos y evidencias de PR.

### changelog_keeper

Responsable de:

- Mantener `AGENT_CHANGELOG.md`.
- Registrar decisiones, acciones, bloqueos y validaciones.

### final_validator

Responsable de:

- Bloquear merges inseguros.
- Exigir checks, alineacion, contexto completo, changelog actual y validacion humana.

## 4. Seguridad del flujo

El flujo incluye reglas de seguridad correctas:

- No mergear con tests fallidos.
- No mergear con conflictos.
- No mergear con alineacion dudosa.
- No hacer force push ni reescrituras sin confirmacion explicita.
- No ejecutar acciones destructivas sin confirmacion.
- No guardar secretos, tokens, contrasenas ni credenciales.
- Pedir validacion humana antes de commits, push, PR o merge segun tipo de cambio.
- Exigir capturas desktop/mobile para cambios visuales antes de commit/push/PR/merge.
- Exigir resumen/diff para cambios no visuales antes de commit/push/PR/merge.

Evaluacion:

- Seguridad: adecuada para el estado actual del proyecto.
- Principal dependencia: que cada agente respete `AGENTS.md` y `AGENT_CHANGELOG.md` antes de actuar.

## 5. Calidad del flujo

Fortalezas:

- Roles separados y claros.
- Fallback por prompts Markdown si la superficie de agentes no esta disponible.
- Changelog operativo.
- Politica de model routing documentada.
- Final validator bloquea merges con riesgo.
- Alignment guard reduce cambios fuera de alcance.
- Compatible con trabajo por PRs pequenas.

Limitaciones:

- La seleccion de modelo depende de la version activa de Codex y no se fuerza desde TOML.
- Los agentes no sustituyen la validacion humana para visual, pagos, dominio o federacion.
- El flujo no ejecuta por si solo checks; los agentes deben pedir o lanzar comandos.
- La calidad final depende de mantener `AGENT_CHANGELOG.md` actualizado.
- Las instrucciones son una capa operativa, no un bloqueo tecnico absoluto sobre comandos.
- La compatibilidad real de `.codex/agents/*.toml` depende de la version activa de Codex.

## 6. Funcionamiento operativo

Se ha comprobado que:

- La skill se puede leer desde `.agents/skills/github-multiagent-workflow/SKILL.md`.
- Los TOML de agentes existen.
- Los prompts fallback existen.
- El README del sistema existe.
- El changelog existe.
- La herramienta de subagentes esta disponible en la sesion actual.
- Se han lanzado subagentes de auditoria de solo lectura sin tocar archivos.

Resultado:

- El flujo es operativo.
- Si la herramienta de subagentes no estuviera disponible en otra sesion, el fallback documentado permite aplicar los roles manualmente.

## 7. Auditorias de solo lectura

Cuando el usuario pida expresamente una auditoria de solo lectura o prohiba editar archivos:

- Los agentes deben leer contexto, revisar estado y devolver hallazgos.
- No deben modificar `AGENT_CHANGELOG.md`.
- Deben indicar que el changelog queda sin actualizar por restriccion explicita.
- Si la auditoria produce decisiones relevantes, se debe recomendar una entrada de changelog para aplicar despues de la validacion humana.

## 8. Checks de seguridad recomendados

Antes de cambios GitHub:

1. `git status --short --branch`.
2. Leer `AGENTS.md`.
3. Leer `AGENT_CHANGELOG.md`.
4. Leer `docs/agent-system/README.md`.
5. Confirmar alcance original.
6. Ejecutar alignment guard.
7. Ejecutar checks del proyecto.
8. Actualizar changelog.
9. Presentar resumen/diff o capturas.
10. Esperar validacion humana si corresponde.

Antes de merge:

1. CI verde.
2. Sin conflictos.
3. Sin secretos.
4. Changelog actualizado.
5. PR con contexto completo.
6. Riesgos documentados.
7. Validacion visual o no visual segun aplique.
8. Final validator en `ready`.

## 9. Checklist PR / merge reutilizable

Antes de abrir o actualizar una PR:

- Rama basada en `main` actualizado.
- Scope de la PR claro y pequeno.
- `git status --short --branch` revisado.
- Diff revisado.
- `npm run format:check` ejecutado.
- `npm run build` o `npm run verify` ejecutado cuando aplique.
- PHP lint ejecutado en CI o entorno con PHP.
- Sin secretos, tokens ni credenciales.
- `AGENT_CHANGELOG.md` actualizado si hubo accion relevante.
- Riesgos y decisiones pendientes documentados en la PR.

Antes de mergear:

- CI verde.
- Sin conflictos.
- PR con descripcion completa.
- Capturas desktop/mobile si hubo cambios visuales.
- Validacion humana del usuario si hubo cambios visuales.
- Resumen/diff validado por el usuario si no hubo cambios visuales.
- `final_validator` en `ready`.
- No hay dependencias externas sin confirmar.

## 10. Matriz de accion, agente, confirmacion y checks

| Accion                          | Agente principal                  | Confirmacion requerida                                  | Checks minimos                                |
| ------------------------------- | --------------------------------- | ------------------------------------------------------- | --------------------------------------------- |
| Crear o ajustar documentacion   | coordinator / changelog_keeper    | Validacion si se va a commit/push                       | `git diff`, `npm run format:check`            |
| Cambios visuales frontend       | code_reviewer / alignment_guard   | Capturas desktop/mobile antes de commit/push/PR/merge   | `npm run verify`, QA navegador                |
| Cambios WordPress/PHP           | code_reviewer                     | Resumen/diff antes de commit/push/PR/merge              | `npm run verify`, `php -l` en CI o local      |
| Pagos/WooCommerce               | coordinator / final_validator     | Confirmacion explicita de alcance, modo test y pasarela | Checkout test, emails, sin secretos           |
| Federacion/importador           | alignment_guard / code_reviewer   | Confirmacion de fuente y autorizacion                   | Dry-run, logs sin credenciales, no duplicados |
| Dominio/DNS                     | github_operator / final_validator | Confirmacion explicita y ventana de cambio              | Backup DNS, correo verificado, SSL            |
| Merge                           | final_validator                   | Siempre si hay riesgo o validacion pendiente            | CI verde, changelog, PR completa              |
| Destructivo/force push/historia | final_validator                   | Confirmacion explicita obligatoria                      | Plan de reversibilidad                        |

## 11. Compatibilidad de agentes Codex

Para comprobar compatibilidad en una sesion nueva:

1. Confirmar que `.agents/skills/github-multiagent-workflow/SKILL.md` se puede leer.
2. Confirmar que existen los 7 TOML de `.codex/agents/`.
3. Confirmar que existen los 7 prompts de `docs/agent-prompts/`.
4. Usar `tool_search` para comprobar si hay herramienta de subagentes.
5. Si no hay herramienta de subagentes, aplicar los prompts secuencialmente desde el hilo principal.
6. No anadir campos `model` a TOML salvo que la version de Codex lo documente.

## 12. Reglas especificas para este proyecto

### Pagos

Los agentes deben tratar pagos como trabajo sensible:

- Usar modo test.
- No guardar claves.
- No pegar credenciales.
- Exigir textos legales y responsable operativo.
- Validar checkout antes de produccion.

### Federacion

Los agentes deben tratar federacion como integracion sensible:

- Solo lectura al auditar.
- No guardar credenciales.
- No scraping autenticado sin autorizacion.
- Documentar fuente antes de automatizar.
- Usar dry-run antes de importar.

### Visual

Los agentes deben exigir:

- Respeto a 60/30/10.
- `prefers-reduced-motion`.
- Capturas desktop y mobile.
- Validacion humana antes de publicar.

## 13. Veredicto

El flujo de agentes es seguro y usable para el proyecto si se mantiene la disciplina operativa:

- Trabajar por cambios pequenos.
- Mantener changelog.
- Pedir confirmaciones en acciones de riesgo.
- No mezclar pagos, dominio, federacion o visual sin validacion.
- Ejecutar checks antes de cerrar.

Estado final:

- Seguridad: correcta.
- Calidad documental: correcta.
- Operatividad: confirmada en esta sesion.
- Punto a vigilar: mantener `AGENT_CHANGELOG.md` actualizado en cada accion relevante.
