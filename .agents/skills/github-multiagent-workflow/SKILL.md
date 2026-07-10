---
name: github-multiagent-workflow
description: Use for GitHub workflows that need coordinated agents for branch, commit, PR, review, merge readiness, alignment, and changelog traceability.
---

# GitHub Multiagent Workflow

Use this skill when the user asks Codex to manage or prepare GitHub work involving branches, commits, pull requests, reviews, merge decisions, alignment checks, or operational changelog updates.

## Required Context

Before taking action:

1. Capture the original user instruction exactly enough to preserve scope.
2. Read `AGENTS.md` if present.
3. Read `docs/agent-system/README.md` if present.
4. Read `AGENT_CHANGELOG.md` if present.
5. Inspect repo status before proposing Git actions.

## Agent Routing

If custom subagents are available, spawn or invoke these agent names when useful:

- `coordinator`
- `github_operator`
- `code_reviewer`
- `pull_request_agent`
- `alignment_guard`
- `changelog_keeper`
- `final_validator`

If subagents are unavailable, perform the same roles sequentially in the main thread using the prompts in `docs/agent-prompts/`.

## Model Routing

Use the smallest capable model for simple, low-risk tasks when the current Codex account/runtime supports model selection. Examples include changelog entries, PR body drafts, status summaries, file inventories, formatting-only documentation edits, and other clear reversible work.

Use a stronger reasoning model for architecture decisions, alignment checks, code review, security-sensitive work, merge readiness, payment/federation/deployment decisions, large refactors, unclear requirements, or any task with irreversible risk.

If per-agent or per-task model selection is unavailable, use the active Codex model and state that model routing is only documented guidance in this repository. Do not add unsupported `model` fields to `.codex/agents/*.toml`.

## Workflow

1. Coordinator: summarize the task, constraints, current repo state, and blockers.
2. Alignment guard: classify the planned action as `aligned`, `partially_aligned`, `not_aligned`, or `needs_human_confirmation`.
3. Implementation or GitHub operator: prepare only the necessary branch, commit, PR, or merge action.
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

## Outputs

For each run, produce:

- Current task summary.
- Agent actions taken or recommended.
- Alignment status.
- Changelog update status.
- Tests or checks run.
- Next required human decision, if any.

Keep final user-facing responses concise and action-oriented.
