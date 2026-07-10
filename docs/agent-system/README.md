# GitHub Multiagent Workflow

This repository contains a portable Codex multiagent workflow for GitHub work.

Use it when work involves branches, commits, pull requests, reviews, merge decisions, alignment checks, or operational changelog updates.

## Compatibility

- If Codex skills are available, use `.agents/skills/github-multiagent-workflow/SKILL.md`.
- If Codex custom agents are available, use `.codex/agents/*.toml`.
- If Claude Code is the active runtime, use the `github-multiagent-workflow` skill in `.claude/skills/` with the subagents in `.claude/agents/` (model routing and per-agent tool limits are enforced by the runtime; the main thread holds the coordinator role).
- If none of these surfaces is available, use the Markdown prompts in `docs/agent-prompts/` sequentially in the main thread.

The files are intentionally repo-local so the workflow can travel with the repository. `docs/agent-prompts/` is the single source of truth for role instructions across all runtimes.

## Required Context

Before taking GitHub-related action:

1. Capture the original user instruction exactly enough to preserve scope.
2. Read `AGENTS.md` if present.
3. Read this README if present.
4. Read `AGENT_CHANGELOG.md` if present.
5. Inspect repo status before proposing Git actions.

## Agent Routing

Use these logical agents:

- `coordinator`: decomposes the task, assigns work, reads history, prevents duplicate or contradictory actions.
- `github_operator`: prepares or executes branch, commit, PR, and merge actions within permission limits.
- `code_reviewer`: reviews code changes for correctness, regressions, scope control, and missing tests.
- `pull_request_agent`: prepares PR title, body, checklist, risks, and test evidence.
- `alignment_guard`: checks every relevant action against the original user request.
- `changelog_keeper`: maintains `AGENT_CHANGELOG.md`.
- `final_validator`: checks merge readiness and blocks unsafe merges.

## Model Routing Policy

When the current Codex account/runtime supports model selection, route simple and low-risk tasks to the smallest capable model. Keep stronger reasoning models for work where mistakes are expensive.

Use smaller models for:

- Changelog entries.
- PR body drafts.
- Status summaries.
- File inventories.
- Formatting-only documentation edits.
- Other clear, reversible, low-risk tasks.

Use stronger reasoning models for:

- Architecture decisions.
- Alignment checks.
- Code review.
- Security-sensitive work.
- Merge readiness.
- Payment, federation, deployment, or credentials decisions.
- Large refactors or unclear requirements.

If per-agent or per-task model selection is unavailable, use the active Codex model and state that model routing could not be enforced by the repo-local files alone.

Do not add `model` fields to `.codex/agents/*.toml` unless the target Codex version documents that syntax.

## Workflow

1. Coordinator: summarize the task, constraints, repo state, and blockers.
2. Alignment guard: classify the planned action as `aligned`, `partially_aligned`, `not_aligned`, or `needs_human_confirmation`.
3. GitHub operator: prepare only the necessary branch, commit, PR, or merge action.
4. Code reviewer: review diffs before commit and before PR.
5. Changelog keeper: record relevant actions, decisions, failures, assumptions, and blocked actions.
6. Pull request agent: prepare a complete PR package before opening or updating a PR.
7. Final validator: check merge readiness before any merge recommendation.

## Safety Rules

- Do not delete files, overwrite important work, force push, rewrite Git history, or merge without explicit human confirmation.
- Do not merge if tests fail, conflicts exist, alignment is uncertain, or the PR package is incomplete.
- Do not claim a GitHub action was performed unless the command or tool actually succeeded.
- Prefer recommendations over execution when permissions, authentication, or repository state are unclear.
- Make assumptions explicit and mark them in the changelog when they affect decisions.

## Expected Output

For each run, produce:

- Current task summary.
- Agent actions taken or recommended.
- Alignment status.
- Changelog update status.
- Tests or checks run.
- Next required human decision, if any.

Keep user-facing responses concise and action-oriented.
