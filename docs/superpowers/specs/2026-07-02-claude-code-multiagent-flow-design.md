# Diseño: flujo multiagente interno para Claude Code (CBN)

Fecha: 2026-07-02
Estado: aprobado por el usuario (diseño v2)

## Objetivo

Portar el flujo multiagente GitHub de Codex (documentado en `AGENTS.md`,
`.codex/agents/*.toml`, `.agents/skills/github-multiagent-workflow/` y
`docs/agent-prompts/`) a Claude Code, aprovechando las capacidades nativas que
Codex no tiene: routing de modelos ejecutable por subagente, restricción de
herramientas por rol, y Fable como orquestador de largo horizonte en el hilo
principal.

## Decisiones tomadas

1. **Port nativo completo**: `.claude/agents/` + `.claude/skills/`, sin tocar
   las superficies de Codex (`.codex/`, `.agents/skills/`).
2. **Alcance**: solo el flujo GitHub multiagente. El harness visual
   `cbn-imagegen-design-harness` queda fuera (iteración futura).
3. **Fable no es un subagente**: es el hilo principal de la sesión de Claude
   Code y asume el rol `coordinator` (descomponer, custodiar scope, leer
   historial, decidir agentes, prevenir acciones contradictorias). El rol
   coordinator desaparece como subagente en la superficie Claude Code.
4. **Routing de modelos** (optimización de coste; política "smallest capable
   model" de `AGENTS.md` aplicada con datos de pricing):

   | Subagente          | Modelo | Justificación                                 |
   | ------------------ | ------ | --------------------------------------------- |
   | alignment-guard    | sonnet | Juicio independiente, input pequeño           |
   | github-operator    | sonnet | Ejecuta git/gh dentro de límites ya decididos |
   | code-reviewer      | opus   | Gate de calidad pre-commit/pre-PR             |
   | final-validator    | opus   | Gate irreversible: merge readiness            |
   | pull-request-agent | haiku  | Redacción con inputs claros, reversible       |
   | changelog-keeper   | haiku  | Entradas de changelog, reversible             |

   Coste estimado: ~$0.60 por ciclo completo (vs ~$1.40 todo-Opus).

## Estructura de archivos (nueva)

```
.claude/
├── agents/
│   ├── github-operator.md     (model: sonnet, Bash git/gh)
│   ├── code-reviewer.md       (model: opus, solo lectura)
│   ├── alignment-guard.md     (model: sonnet, solo lectura)
│   ├── pull-request-agent.md  (model: haiku, solo lectura)
│   ├── changelog-keeper.md    (model: haiku, Read + Edit)
│   └── final-validator.md     (model: opus, solo lectura)
└── skills/
    └── github-multiagent-workflow/
        └── SKILL.md
```

## Fuente única de verdad

Cada `.claude/agents/<rol>.md` es un adaptador fino (frontmatter + una
referencia) que declara `docs/agent-prompts/<rol>.md` como instrucciones de
control — el mismo patrón que los TOML de Codex. El prompt
`docs/agent-prompts/coordinator.md` lo consume la skill para guiar al hilo
principal (Fable), no un subagente. Editar un rol en `docs/agent-prompts/`
actualiza ambos IDEs.

## Permisos por subagente

- **Solo lectura** (Read, Grep, Glob, Bash limitado a `git status/diff/log`):
  code-reviewer, alignment-guard, final-validator, pull-request-agent.
- **Read + Edit**: changelog-keeper (restringido por prompt a
  `AGENT_CHANGELOG.md`).
- **Bash completo (git/gh)**: github-operator — único rol con capacidad de
  escritura en Git, manteniendo las reglas de confirmación humana para
  acciones destructivas.

## Workflow de la skill

`SKILL.md` en `.claude/skills/github-multiagent-workflow/` define este ciclo:

1. **Hilo principal (Fable)**: resumir tarea, restricciones, estado del repo,
   bloqueos (responsabilidades de `docs/agent-prompts/coordinator.md`).
2. **alignment-guard**: clasificar la acción planeada como `aligned`,
   `partially_aligned`, `not_aligned` o `needs_human_confirmation`.
3. **Implementación** en el hilo principal, o **github-operator** para
   acciones de rama/commit/PR/merge.
4. **Gate técnico**: `npm run verify` obligatorio antes de commit.
5. **Gate visual** (solo cambios visuales): capturas desktop/mobile mostradas
   al usuario antes de commit/push/PR/merge, usando el agente `pixel-critic`
   disponible en el entorno del usuario cuando exista.
6. **code-reviewer**: revisar diffs antes de commit y antes de PR.
7. **changelog-keeper**: registrar acciones, decisiones, supuestos, bloqueos y
   validaciones en `AGENT_CHANGELOG.md`.
8. **pull-request-agent**: preparar título, body, checklist, riesgos y
   evidencia de tests antes de abrir/actualizar PR.
9. **final-validator**: verificar merge readiness; bloquear si CI falla, hay
   conflictos, la alineación es dudosa o el changelog está obsoleto.

### Safety rules (idénticas a la versión Codex)

- No borrar archivos, sobrescribir trabajo importante, forzar push, reescribir
  historial ni mergear sin confirmación humana explícita.
- No mergear si fallan tests, hay conflictos, la alineación es incierta o el
  paquete de PR está incompleto.
- No afirmar que una acción GitHub ocurrió salvo que el comando haya tenido
  éxito real.
- Preferir recomendación sobre ejecución cuando permisos, autenticación o
  estado del repo no estén claros.
- Hacer explícitos los supuestos y registrarlos en el changelog cuando afecten
  decisiones.

### Output por ejecución

Resumen de tarea, acciones de agentes (ejecutadas o recomendadas), estado de
alineación, estado del changelog, checks ejecutados, y próxima decisión humana
requerida.

## Documentación a actualizar

- `docs/agent-system/README.md`: añadir Claude Code a la sección
  Compatibility.
- `AGENTS.md`: una línea en la sección del workflow indicando la superficie
  `.claude/` para Claude Code.
- `AGENT_CHANGELOG.md`: entrada registrando decisión, routing y archivos.

## Verificación

- `npm run verify` (format check cubre los `.md` nuevos + build).
- Prueba funcional: invocar la skill en una tarea pequeña de rama/PR y
  comprobar que los subagentes se lanzan con el modelo y permisos declarados.

## Fuera de alcance

- Harness visual imagegen para Claude Code.
- Cambios en los archivos de Codex (`.codex/`, `.agents/skills/`).
- Cambios en los prompts de rol de `docs/agent-prompts/` (se reutilizan tal
  cual; si un rol necesita ajuste, es un cambio aparte).
