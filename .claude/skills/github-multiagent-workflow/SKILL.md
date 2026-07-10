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
5. Read `docs/redesign/fase1-plan-2026-07-02.md` for current scope,
   deadline, and client decisions.
6. Inspect `git status --short --branch` before proposing Git actions.

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
