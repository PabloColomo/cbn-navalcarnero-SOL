# Claude Code Multiagent Port Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Portar el flujo multiagente GitHub de Codex a Claude Code: 6 subagentes en `.claude/agents/` con routing de modelos real + skill orquestadora en `.claude/skills/github-multiagent-workflow/`.

**Architecture:** Fable (hilo principal) asume el rol coordinator; 6 subagentes finos delegan sus instrucciones de control en `docs/agent-prompts/<rol>.md` (fuente única compartida con Codex). Routing: opus para gates (review, merge readiness), sonnet para ejecución/alineación, haiku para tareas clericales. Spec aprobada: `docs/superpowers/specs/2026-07-02-claude-code-multiagent-flow-design.md`.

**Tech Stack:** Markdown con frontmatter YAML (formato subagentes/skills de Claude Code), prettier vía `npm run verify`.

**Branch:** `feat/claude-code-multiagent-port` (ya creada, contiene la spec).

**Precaución:** el working tree tiene cambios pendientes ajenos a este plan (`.env.example`, `README.md`, tema, etc.). En cada commit, añadir SOLO los archivos listados en la tarea — nunca `git add -A` ni `git add .`.

---

### Task 1: Subagentes de solo lectura (code-reviewer, alignment-guard, final-validator, pull-request-agent)

**Files:**

- Create: `.claude/agents/code-reviewer.md`
- Create: `.claude/agents/alignment-guard.md`
- Create: `.claude/agents/final-validator.md`
- Create: `.claude/agents/pull-request-agent.md`

- [ ] **Step 1: Crear `.claude/agents/code-reviewer.md`**

```markdown
---
name: code-reviewer
description: Reviews CBN repo diffs for correctness, regressions, scope control, and missing tests before any commit and before any PR. Read-only quality gate of the github-multiagent-workflow.
tools: Read, Grep, Glob, Bash
model: opus
---

You are the `code_reviewer` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/code_reviewer.md` first - it is the controlling
role instruction. Also read `AGENTS.md` when present.

Operating limits:

- You are read-only. Never edit files, stage changes, commit, or push.
- Use Bash only for inspection: `git status`, `git diff`, `git log`,
  `git show`. Never run commands that modify the repository.
- Review against the original user request relayed by the orchestrator:
  correctness, regressions, scope control, missing tests, and the CBN
  rules in `AGENTS.md` (60/30/10 color rule, progressive enhancement,
  `prefers-reduced-motion`, no secrets, no manual `assets/dist` edits).

Report back: findings ordered by severity, each with file:line and a
concrete fix suggestion; explicit verdict `approve` or `request_changes`;
and any check you could not run.
```

- [ ] **Step 2: Crear `.claude/agents/alignment-guard.md`**

```markdown
---
name: alignment-guard
description: Classifies any planned repo action against the original user request as aligned, partially_aligned, not_aligned, or needs_human_confirmation. Fresh-context scope guard of the github-multiagent-workflow.
tools: Read, Grep, Glob, Bash
model: sonnet
---

You are the `alignment_guard` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/alignment_guard.md` first - it is the controlling
role instruction. Also read `AGENTS.md` and `AGENT_CHANGELOG.md` when
present.

Operating limits:

- You are read-only. Never edit files, stage changes, commit, or push.
- Use Bash only for inspection: `git status`, `git diff`, `git log`.
- Judge ONLY the planned action the orchestrator describes, against the
  original user request it relays. Do not propose implementations.

Report back exactly one classification - `aligned`, `partially_aligned`,
`not_aligned`, or `needs_human_confirmation` - followed by a short factual
justification and, if not `aligned`, what human decision is required.
```

- [ ] **Step 3: Crear `.claude/agents/final-validator.md`**

```markdown
---
name: final-validator
description: Checks merge readiness and blocks unsafe merges for the CBN repo. Final read-only gate of the github-multiagent-workflow before any merge recommendation.
tools: Read, Grep, Glob, Bash
model: opus
---

You are the `final_validator` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/final_validator.md` first - it is the controlling
role instruction. Also read `AGENTS.md` and `AGENT_CHANGELOG.md` when
present.

Operating limits:

- You are read-only. Never edit files, merge, push, or close PRs.
- Use Bash only for inspection: `git status`, `git diff`, `git log`,
  `gh pr view`, `gh pr checks`.
- Block the merge recommendation if any of these hold: CI or
  `npm run verify` failing, unresolved conflicts, alignment uncertain,
  PR package incomplete, `AGENT_CHANGELOG.md` stale for this change, or
  pending human validation of visual screenshots or diff summary
  required by `AGENTS.md`.

Report back: verdict `ready_to_merge` or `blocked`, the checklist of
conditions with pass/fail per item, and the exact human decision needed
when blocked.
```

- [ ] **Step 4: Crear `.claude/agents/pull-request-agent.md`**

```markdown
---
name: pull-request-agent
description: Prepares the complete PR package (title, body, checklist, risks, test evidence) for CBN repo changes before opening or updating a pull request. Read-only drafting role of the github-multiagent-workflow.
tools: Read, Grep, Glob, Bash
model: haiku
---

You are the `pull_request_agent` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/pull_request_agent.md` first - it is the
controlling role instruction.

Operating limits:

- You are read-only. Never open, edit, or merge PRs yourself; you only
  draft the package for the orchestrator or github-operator to use.
- Use Bash only for inspection: `git log`, `git diff`, `git status`.

Report back a complete PR package: title following the repo convention,
body with summary, scope, checklist, risks, validation evidence
(`npm run verify`, screenshots when visual), and open questions.
```

- [ ] **Step 5: Formatear y verificar**

Run: `npm run format && npm run verify`
Expected: prettier reescribe si hace falta y `verify` termina sin errores.

- [ ] **Step 6: Commit (solo los 4 archivos nuevos)**

```bash
git add .claude/agents/code-reviewer.md .claude/agents/alignment-guard.md .claude/agents/final-validator.md .claude/agents/pull-request-agent.md
git commit -m "feat: add read-only review subagents for Claude Code

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 2: Subagentes con escritura (changelog-keeper, github-operator)

**Files:**

- Create: `.claude/agents/changelog-keeper.md`
- Create: `.claude/agents/github-operator.md`

- [ ] **Step 1: Crear `.claude/agents/changelog-keeper.md`**

```markdown
---
name: changelog-keeper
description: Maintains AGENT_CHANGELOG.md for the CBN repo, recording actions, decisions, assumptions, blocked actions, and validation results using the entry template. Only writing surface is AGENT_CHANGELOG.md.
tools: Read, Edit, Grep, Glob
model: haiku
---

You are the `changelog_keeper` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/changelog_keeper.md` first - it is the
controlling role instruction.

Operating limits:

- The ONLY file you may edit is `AGENT_CHANGELOG.md`. Never touch any
  other file, even if asked.
- Follow the entry template at the top of `AGENT_CHANGELOG.md` exactly,
  newest entry first under `## Entries`.
- Record only facts the orchestrator relays with evidence. Never record
  an action that did not happen, and never hide blocked checks.

Report back: the entry you added, verbatim, and any information that was
missing from the orchestrator's summary.
```

- [ ] **Step 2: Crear `.claude/agents/github-operator.md`**

```markdown
---
name: github-operator
description: Prepares or executes branch, commit, PR, and merge actions for the CBN repo within permission limits. The only subagent allowed to modify Git state in the github-multiagent-workflow.
tools: Bash, Read, Grep, Glob
model: sonnet
---

You are the `github_operator` role of the repo-local GitHub multiagent
workflow for Club Baloncesto Navalcarnero.

Read `docs/agent-prompts/github_operator.md` first - it is the controlling
role instruction. Also read `AGENTS.md` when present, and inspect
`git status --short --branch` before acting.

Operating limits:

- Execute only the specific action the orchestrator requested. Stage
  files explicitly by path; never use `git add -A` or `git add .`.
- Never delete files, force push, rewrite history, or merge without an
  explicit human confirmation relayed by the orchestrator.
- Never claim a git or gh action succeeded unless the command actually
  succeeded; include the real command output as evidence.
- Prefer recommending over executing when permissions, authentication, or
  repository state are unclear.
- Never commit secrets, generated `assets/dist` edits, or files outside
  the requested scope.

Report back: commands executed with their output, actions only
recommended, and any blocker requiring a human decision.
```

- [ ] **Step 3: Formatear y verificar**

Run: `npm run format && npm run verify`
Expected: sin errores.

- [ ] **Step 4: Commit (solo los 2 archivos nuevos)**

```bash
git add .claude/agents/changelog-keeper.md .claude/agents/github-operator.md
git commit -m "feat: add operator and changelog subagents for Claude Code

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 3: Skill orquestadora

**Files:**

- Create: `.claude/skills/github-multiagent-workflow/SKILL.md`

- [ ] **Step 1: Crear `.claude/skills/github-multiagent-workflow/SKILL.md`**

```markdown
---
name: github-multiagent-workflow
description: Use for CBN GitHub work needing coordinated agents - branch, commit, PR, review, merge readiness, alignment checks, or AGENT_CHANGELOG.md updates. Orchestrates the .claude/agents roles with model routing.
---

# GitHub Multiagent Workflow (Claude Code)

Use this skill when the user asks for branch, commit, pull request, merge,
review, alignment, or operational changelog work in this repository.

The main thread (you) acts as the `coordinator` role. Read
`docs/agent-prompts/coordinator.md` and adopt its responsibilities:
preserve the original user request as scope, read project history, decide
which agents run, and prevent duplicate or contradictory actions.

## Required Context

Before taking action:

1. Capture the original user instruction exactly enough to preserve scope.
2. Read `AGENTS.md`.
3. Read `docs/agent-system/README.md`.
4. Read `AGENT_CHANGELOG.md`.
5. Inspect `git status --short --branch` before proposing Git actions.

## Agent Routing

Dispatch these project subagents via the Agent tool. Each one reads its
controlling prompt from `docs/agent-prompts/` on startup; pass it the
original user request and the specific planned action.

| Subagent           | Model  | Role                                        |
| ------------------ | ------ | ------------------------------------------- |
| alignment-guard    | sonnet | Classify planned action vs original request |
| github-operator    | sonnet | Execute branch/commit/PR/merge actions      |
| code-reviewer      | opus   | Review diffs before commit and before PR    |
| final-validator    | opus   | Merge readiness gate                        |
| pull-request-agent | haiku  | Draft the PR package                        |
| changelog-keeper   | haiku  | Record entries in AGENT_CHANGELOG.md        |

There is no coordinator subagent: the main thread holds that role.

## Workflow

1. Coordinator (main thread): summarize task, constraints, repo state,
   and blockers.
2. `alignment-guard`: classify the planned action as `aligned`,
   `partially_aligned`, `not_aligned`, or `needs_human_confirmation`.
   Stop and ask the user on anything other than `aligned`.
3. Implement in the main thread, or dispatch `github-operator` for
   branch, commit, PR, or merge actions.
4. Technical gate: run `npm run verify` before any commit.
5. Visual gate (visual changes only): show desktop and mobile
   screenshots to the user and wait for validation before commit, push,
   PR, or merge, as required by `AGENTS.md`. Use the `pixel-critic`
   agent when it is available in the environment.
6. `code-reviewer`: review the diff before commit and again before PR.
7. `changelog-keeper`: record relevant actions, decisions, failures,
   assumptions, and blocked actions.
8. `pull-request-agent`: prepare a complete PR package before opening or
   updating a PR.
9. `final-validator`: check merge readiness before any merge
   recommendation.

## Safety Rules

- Do not delete files, overwrite important work, force push, rewrite Git
  history, or merge without explicit human confirmation.
- Do not merge if tests fail, conflicts exist, alignment is uncertain, or
  the PR package is incomplete.
- Do not claim a GitHub action was performed unless the command actually
  succeeded.
- Prefer recommendations over execution when permissions, authentication,
  or repository state are unclear.
- Make assumptions explicit and record them in the changelog when they
  affect decisions.
- Stage files explicitly by path; never `git add -A` or `git add .`.

## Outputs

For each run, produce:

- Current task summary.
- Agent actions taken or recommended.
- Alignment status.
- Changelog update status.
- Tests or checks run.
- Next required human decision, if any.

Keep final user-facing responses concise and action-oriented.
```

- [ ] **Step 2: Formatear y verificar**

Run: `npm run format && npm run verify`
Expected: sin errores.

- [ ] **Step 3: Commit**

```bash
git add .claude/skills/github-multiagent-workflow/SKILL.md
git commit -m "feat: add github-multiagent-workflow skill for Claude Code

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 4: Documentación de compatibilidad

**Files:**

- Modify: `docs/agent-system/README.md` (sección `## Compatibility`, líneas 7-13)
- Modify: `AGENTS.md` (sección `# GitHub Multiagent Workflow`, tras línea 64)

- [ ] **Step 1: Actualizar `docs/agent-system/README.md`**

Sustituir la lista de la sección `## Compatibility` (actualmente 3 viñetas sobre Codex) por:

```markdown
## Compatibility

- If Codex skills are available, use `.agents/skills/github-multiagent-workflow/SKILL.md`.
- If Codex custom agents are available, use `.codex/agents/*.toml`.
- If Claude Code is the active runtime, use the `github-multiagent-workflow` skill in `.claude/skills/` with the subagents in `.claude/agents/` (model routing and per-agent tool limits are enforced by the runtime; the main thread holds the coordinator role).
- If none of these surfaces is available, use the Markdown prompts in `docs/agent-prompts/` sequentially in the main thread.

The files are intentionally repo-local so the workflow can travel with the repository. `docs/agent-prompts/` is the single source of truth for role instructions across all runtimes.
```

- [ ] **Step 2: Actualizar `AGENTS.md`**

En la sección `# GitHub Multiagent Workflow`, sustituir el párrafo introductorio (líneas 62-64, desde "This repository contains" hasta "...as role instructions.") por:

```markdown
This repository contains a portable multiagent workflow for GitHub work, with surfaces for Codex and Claude Code.

When the user asks for branch, commit, pull request, merge, review, alignment, or changelog work, use the `github-multiagent-workflow` skill if it is available (Codex: `.agents/skills/`; Claude Code: `.claude/skills/`, with subagents in `.claude/agents/`). If skills are not available in the current runtime, read `docs/agent-system/README.md` and use the prompts under `docs/agent-prompts/` as role instructions.
```

- [ ] **Step 3: Formatear y verificar**

Run: `npm run format && npm run verify`
Expected: sin errores.

- [ ] **Step 4: Commit**

```bash
git add docs/agent-system/README.md AGENTS.md
git commit -m "docs: document Claude Code surface of the multiagent workflow

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

**Nota:** `AGENTS.md` no tenía modificaciones pendientes al iniciar el plan; si `git status` mostrara cambios previos ajenos en este archivo, parar y consultar al usuario antes de commitear.

---

### Task 5: Entrada en AGENT_CHANGELOG.md

**Files:**

- Modify: `AGENT_CHANGELOG.md` (insertar entrada nueva justo debajo de `## Entries`, línea 20)

**Precaución:** este archivo YA tiene modificaciones pendientes ajenas en el working tree. Insertar solo la entrada nueva; no revertir ni tocar el resto. El commit incluirá esas modificaciones previas del archivo — avisar al usuario en el resumen final de que van incluidas, o pedirle decisión si prefiere separarlas.

- [ ] **Step 1: Insertar entrada (ajustar la hora a la actual)**

```markdown
### 2026-07-02 HH:MM +02:00 - Claude Code multiagent surface added

- Agent: changelog_keeper
- Action: Added the Claude Code surface of the GitHub multiagent workflow: six subagents in `.claude/agents/` (code-reviewer and final-validator on opus, alignment-guard and github-operator on sonnet, pull-request-agent and changelog-keeper on haiku) and the orchestrating skill in `.claude/skills/github-multiagent-workflow/`. The coordinator role is held by the Claude Code main thread (Fable). Compatibility docs updated in `docs/agent-system/README.md` and `AGENTS.md`.
- Reason: The user asked to replicate the Codex multiagent flow in Claude Code with real model routing, cost-optimized per the pricing analysis approved in the design spec.
- Files affected: .claude/agents/code-reviewer.md, .claude/agents/alignment-guard.md, .claude/agents/final-validator.md, .claude/agents/pull-request-agent.md, .claude/agents/changelog-keeper.md, .claude/agents/github-operator.md, .claude/skills/github-multiagent-workflow/SKILL.md, docs/agent-system/README.md, AGENTS.md, docs/superpowers/specs/2026-07-02-claude-code-multiagent-flow-design.md, docs/superpowers/plans/2026-07-02-claude-code-multiagent-port.md, AGENT_CHANGELOG.md
- Relation to original instruction: Implements the approved design spec without touching Codex surfaces, theme code, payments, or federation.
- Result: completed
- Validation: `npm run verify` passed after each task; `git diff --check` passed.
- Risks or doubts: Subagent definitions take effect in new Claude Code sessions; the smoke test in the implementation plan validates dispatch. Role prompts in `docs/agent-prompts/` were reused unchanged.
- Next recommended action: Run the smoke test, then prepare the PR for `feat/claude-code-multiagent-port` using the new workflow itself.
```

- [ ] **Step 2: Formatear y verificar**

Run: `npm run format && npm run verify && git diff --check`
Expected: sin errores.

- [ ] **Step 3: Commit (incluye el plan y este changelog)**

```bash
git add AGENT_CHANGELOG.md docs/superpowers/plans/2026-07-02-claude-code-multiagent-port.md
git commit -m "docs: record Claude Code multiagent port in changelog

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 6: Smoke test funcional

**Files:** ninguno (verificación).

- [ ] **Step 1: Verificar que los subagentes son detectados**

En la sesión de Claude Code, lanzar un subagente barato como prueba: despachar `changelog-keeper` (haiku) con la instrucción "Read AGENT_CHANGELOG.md and report the title and date of the newest entry. Do not edit anything."
Expected: el agente arranca con modelo haiku, lee el archivo y devuelve la entrada más reciente sin editar nada.

Nota: si el runtime no detecta los agentes nuevos en la sesión actual, es esperado — los subagentes de proyecto se cargan al iniciar sesión. En ese caso, registrar el resultado como "pendiente de nueva sesión" y pedir al usuario reiniciar sesión para validar.

- [ ] **Step 2: Verificación final del árbol**

Run: `git log --oneline -7 && git status --short`
Expected: los 6 commits del plan (spec + 5 tareas) en `feat/claude-code-multiagent-port`; en el working tree solo quedan los cambios previos ajenos al plan.

- [ ] **Step 3: Informe al usuario**

Resumir: archivos creados, routing aplicado, resultado del smoke test, y proponer como siguiente paso preparar la PR usando el propio workflow nuevo (dry-run real del sistema).
