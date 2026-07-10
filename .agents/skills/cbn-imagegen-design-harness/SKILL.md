---
name: cbn-imagegen-design-harness
description: Use for Club Baloncesto Navalcarnero visual web design work that should be driven by Image Gen concepts before code, including page redesigns, home/teams/shop/registration visual directions, selected mockup implementation, browser QA, and coordination of frontend-app-builder, imagegen, Product Design, browser, and visual validation workflows.
---

# CBN ImageGen Design Harness

Use this skill as the repo-local harness for visual website design on Club Baloncesto Navalcarnero. It coordinates existing design/build skills; it does not replace them.

## Scope

Use for visual design only:

- Home, Club, Teams, Matches, News, Sponsors, Registration, Shop, Contact, and campaign pages.
- Image Gen concept creation.
- Selection and refinement of visual directions.
- Implementation of an approved visual target in the WordPress theme.
- Browser screenshots and visual QA.

Do not use this skill for payment logic, federation import logic, DNS, hosting, commits, PRs, or merges except to hand off to the appropriate workflow.

## Required CBN Constraints

Preserve these rules in every visual brief, concept, implementation, and QA pass:

- WordPress theme: `wp-content/themes/cbn-theme`.
- Structured content plugin: `wp-content/mu-plugins/cbn-core`.
- Visual proportion: 60% white, 30% red, 10% black.
- White must remain the dominant surface color.
- Red is for brand energy, CTAs, active states, tickers, and key bands.
- Black is for text, header/footer, structure, and limited emphasis.
- Yellow/gold only when it comes from official crest/assets.
- Manchester Basketball Club is conceptual inspiration only; never copy code, layout, text, images, colors, assets, or identity.
- Mobile-first, accessible, and progressive.
- Respect `prefers-reduced-motion`.
- Protect minors: avoid unnecessary personal data or identifiable imagery unless approved.

## Skill Routing

Prefer this route:

1. `product-design:get-context` when the brief is not already confirmed.
2. `product-design:ideate` when the user wants 3 visual options.
3. `build-web-apps:frontend-app-builder` when designing and implementing a selected visual direction in the real frontend.
4. `imagegen` for UI concepts, hero assets, section visuals, product imagery, or image refinements.
5. `product-design:image-to-code` only after a visual target is selected and a faithful image-to-code pass is appropriate.
6. `product-design:design-qa` for reference-vs-render comparison in Product Design workflows.
7. `build-web-apps:frontend-testing-debugging` for rendered frontend QA.
8. `browser:control-in-app-browser` or `browser-use:browser` for in-app browser validation; use Playwright only as fallback and state why.
9. `github-multiagent-workflow` only for branch, commit, PR, review, merge, or changelog-heavy Git work.

If a listed skill is unavailable, perform the same role manually and state the fallback.

## Workflow

### 1. Preflight

Before design or edits:

1. Read `AGENTS.md`.
2. Read `README.md` and relevant `docs/redesign/*`.
3. Run `git status --short --branch`.
4. Identify uncommitted work and avoid reverting user changes.
5. Confirm the target surface: full page, section, component, or asset.

### 2. Brief Gate

Confirm the design brief before Image Gen:

- Target page or section.
- Audience and primary task.
- Required navigation labels and CTAs.
- Required content modules.
- Visual references and what they are allowed to influence.
- Interactivity level: static visual concept, clickable prototype, or production implementation.
- Desktop/mobile expectations.

If the brief is missing a key decision, ask one concise question. Otherwise play the brief back and proceed.

### 3. Image Gen Concepting

Do not code from a written brief alone.

For broad or ambiguous design work:

- Generate exactly 3 independent concepts with `product-design:ideate` or `imagegen`.
- Show the images and wait for the user to choose or combine directions.

For a focused approved direction:

- Generate section-specific concepts when the page is long.
- Prefer one clear image per major section over one unreadable full-page mock.
- Include mobile concepts when mobile layout risk is high.

Every concept prompt must include the CBN constraints from this skill.

### 4. Visual Target Lock

Once the user selects a concept:

- Treat the selected image as the visual specification.
- Do not invent new major layout, colors, copy, sections, cards, or visual motifs.
- If feedback combines multiple concepts, generate a revised final concept before coding.

### 5. Design System Extraction

Before editing code, extract:

- Palette and 60/30/10 balance.
- Typography mood, scale, weights, and line heights.
- Section order and first viewport composition.
- Button and CTA treatments.
- Header/footer behavior.
- Card/list/table treatment.
- Image/media framing.
- Motion expectations.
- Required assets.
- Responsive rules and likely mobile changes.

Record any intentional deviation from the selected image before implementation.

### 6. WordPress Implementation

Edit only the smallest reasonable set of files.

Common paths:

- `wp-content/themes/cbn-theme/front-page.php`
- `wp-content/themes/cbn-theme/header.php`
- `wp-content/themes/cbn-theme/footer.php`
- `wp-content/themes/cbn-theme/inc/home-content.php`
- `wp-content/themes/cbn-theme/assets/src/css/main.css`
- `wp-content/themes/cbn-theme/assets/src/js/main.js`
- `wp-content/themes/cbn-theme/assets/src/images/`

Rules:

- Use `apply_patch` for manual edits outside permission-restricted skill metadata.
- Do not edit generated `assets/dist` manually.
- Keep text and controls code-native.
- Use generated bitmap assets for visual media, not CSS placeholders, when the concept relies on imagery.
- Move any project-bound generated image into the workspace; do not leave it only under Codex generated-image storage.
- Preserve ACF/fallback behavior where existing code already supports it.
- Do not install dependencies without approval.

### 7. Validation

Run:

```bash
npm run format
npm run verify
git diff --check
```

For visual changes, also run browser QA:

- Desktop viewport.
- Mobile viewport.
- Console errors/warnings.
- First viewport and scroll.
- Menu, CTAs, carousels, forms, and key interactions if present.
- Reduced-motion behavior when relevant.
- Image loading and no text overlap.

### 8. Visual Fidelity Gate

Before claiming completion:

1. Open the selected concept with `view_image`.
2. Capture the rendered implementation.
3. Open the rendered screenshot with `view_image`.
4. Compare at least:
   - copy/nav/CTA labels,
   - layout/section order,
   - color balance,
   - typography,
   - spacing/container model,
   - image treatment,
   - mobile behavior.
5. Fix P0/P1/P2 mismatches before handoff.

### 9. Checkpoint And Handoff

For visual changes:

- Show desktop and mobile screenshots to the user before any commit, push, PR, or merge.
- State validation commands and results.
- Update `AGENT_CHANGELOG.md` when the action is relevant.
- Do not commit unless explicitly asked.

Final response should include:

- Selected concept path or displayed concept reference.
- Files changed.
- Commands run.
- Browser/screenshot evidence.
- Known deviations.
- Next recommended design cycle.

## Subagent Use

Use subagents only when the user explicitly asks for subagents or parallel review.

Useful read-only subagent roles:

- `visual_researcher`: gather visual context and constraints.
- `concept_reviewer`: critique Image Gen options against CBN rules.
- `frontend_reviewer`: inspect implementation diff for visual and accessibility risks.
- `qa_validator`: verify browser screenshots and console health.
- `alignment_guard`: check the result against the selected concept and original user request.

Keep code edits local unless the subagent has a clearly disjoint write scope.
