# Architecture

Project: Club Baloncesto Navalcarnero website redesign.

This document is the technical baseline for the MVP. It converts the product proposal into a maintainable WordPress architecture with clear module boundaries, security rules, and incremental delivery checkpoints.

## 1. Executive Summary

The project is a modern public website for Club Baloncesto Navalcarnero. It serves families, players, coaches, sponsors, people interested in the club, and internal club managers.

The site must solve five practical problems:

- Present the club with a modern, clean, sport-focused identity.
- Make core information easy to find on mobile.
- Let the club maintain news, teams, sponsors, documents, products, and basic sports data.
- Prepare online registrations, shop orders, and payments without unsafe custom payment handling.
- Prepare a future federation data integration without committing to an unknown source format.

Essential MVP capabilities:

- Home, navigation, footer, and core public pages.
- Teams, matches/results, news, sponsors, contact, and basic registration entry points.
- Shop structure with WooCommerce as the operational path.
- Payment architecture in test/sandbox first.
- Structured sports data model ready for manual entry, CSV import, or future federation sync.
- Privacy controls for minors and image/data permissions.

Desirable later capabilities:

- Federation automation.
- Advanced standings/statistics.
- Private family or member area.
- Advanced stock, refunds, and reconciliation reports.
- Visual regression, accessibility, and end-to-end test automation.

Prepared for later phases:

- External IDs on sports entities.
- ACF JSON fields.
- Module-specific documentation for payments, data import, and privacy.
- Docker local environment and CI quality checks.

## 2. Functional Requirements

### Public Website

- Home with hero, calls to action, teams, news, sponsors, shop/registration entry points, and match/result highlights.
- Club page with identity, values, facilities, school/base basketball, and institutional information.
- Teams archive and team details.
- Matches/results archive and basic match details.
- News and announcements.
- Sponsors page and home sponsor strip.
- Contact page with location, form entry point, and social/contact data.
- Documents and galleries when the club has enough approved content.

### Registrations

- Explain the registration, test, campus, or activity process.
- Collect participant data.
- Collect parent/guardian data when applicable.
- Capture legal consents and image consent separately.
- Optionally connect to a WooCommerce/Stripe payment when the product/activity is confirmed.
- Send confirmation emails and internal notifications when configured.
- Track states: `draft`, `submitted`, `pending_payment`, `paid`, `confirmed`, `cancelled`, `rejected`.

Candidate fields, pending final club/legal validation:

- Name and surname.
- DNI/NIE only if the club confirms it is necessary.
- Date of birth.
- Sex/category when needed for sports administration.
- Phone and email.
- Parent/guardian data for minors.
- Optional height/weight only if the club confirms a legitimate need.

### Shop

- Club products: shirts, shorts, training shirts, warm-up shirts, sweatshirts.
- Product variations for size and optional color/customization.
- Prices, stock, local pickup, and optional shipping.
- WooCommerce orders, transactional emails, changes, returns, and admin order management.

### Payments

- Shop checkout.
- Registration/campus/activity payments.
- Test/sandbox first.
- Provider to be confirmed: Stripe recommended, Redsys/TPV possible if the bank requires it.
- No card data stored by this project.
- Webhooks/notifications, payment logs, idempotency, and reconciliation are mandatory before real payments.

Payment states:

- `pending`
- `checkout_created`
- `paid`
- `failed`
- `cancelled`
- `refunded`
- `disputed`

### Sports Data / Federation

- Teams, players, matches, results, standings, competitions, seasons, and optional statistics.
- Priority order: official API, official CSV/Excel export, manual structured load, semi-automatic import, scraping only if permitted and documented.
- MVP must support manual entry and a future import path.

### Administration

- WordPress admin for content.
- WooCommerce admin for products, orders, emails, and payments when installed.
- CPTs for teams, players, matches, sponsors, and documents.
- Taxonomies for seasons, sports categories, competitions, venues, and sponsor tiers.
- Limited roles and a short usage guide before production.

## 3. Non-Functional Requirements

- Security: keep WordPress, plugins, PHP, and dependencies updated; use least-privilege roles.
- GDPR: collect only necessary data, document purpose, retention, access, and consent.
- Minors: no public sensitive data; default to minimal public profile data.
- Performance: keep frontend assets bundled with Vite, lazy-load media, avoid heavy animation.
- Responsive: mobile-first navigation, readable cards/tables/forms, no overlapping text.
- Accessibility: semantic markup, labels, focus states, contrast, keyboard navigation, and reduced motion.
- SEO: clean URLs, titles, metadata, sitemap, redirects, Open Graph, and sports/news schema where practical.
- Backups: external backups before production and before major plugin/payment changes.
- Logs: import and payment events must be traceable without exposing secrets or personal data.
- Deployment: staging before production; production requires SSL, backups, legal pages, and payment test evidence.
- Recovery: document rollback, backup restore, and provider disable steps.

## 4. Ambiguities And Decisions Pending

| Topic                      | Ambiguity                                                                    | Recommended Decision                                                       | Impact If Not Decided                             |
| -------------------------- | ---------------------------------------------------------------------------- | -------------------------------------------------------------------------- | ------------------------------------------------- |
| Target date                | No exact delivery date is confirmed.                                         | Set urgent MVP and complete MVP dates separately.                          | Scope pressure and unstable priorities.           |
| MVP scope                  | Payments, shop, and registrations are important but data may be missing.     | Ship public MVP first, then shop/payment sandbox when inputs arrive.       | Large unfinished features block launch.           |
| Payment gateway            | Stripe vs Redsys/TPV not fully confirmed.                                    | Use Stripe for speed unless the bank requires Redsys.                      | Rework in checkout, docs, and testing.            |
| Shop active or prepared    | Products, prices, stock, pickup/shipping not final.                          | Build WooCommerce structure; activate real products only after validation. | Inaccurate orders or customer confusion.          |
| Real payments or test only | Production payment readiness depends on legal/fiscal/provider setup.         | Test mode first; production only after sign-off.                           | Financial, legal, and support risk.               |
| Privacy of minors          | Which player data can be public is not fully specified per person/team.      | Default to minimal data and opt-in visibility.                             | Accidental exposure of personal data.             |
| Federation data            | API/export/source unknown.                                                   | Prepare model and manual/CSV fallback.                                     | Fragile scraper or duplicated data.               |
| WordPress vs custom        | Existing repo already uses WordPress.                                        | Continue WordPress with controlled custom theme and MU plugin.             | Rebuild cost and admin burden.                    |
| Order management           | Responsible person/process not confirmed.                                    | Assign club owner before production shop launch.                           | Missed orders, refunds, or reconciliation.        |
| Content updates            | Admin owners not confirmed.                                                  | Define content roles and guide.                                            | Stale website after launch.                       |
| Legal texts                | Privacy, purchase, returns, and inscription legal text missing.              | Use placeholders only in staging; require final legal text for production. | Checkout and registrations cannot safely go live. |
| Authorized photos          | Permission is generally confirmed, but per-asset governance is still needed. | Track approved image sources and avoid sensitive images by default.        | Image rights and minors privacy risk.             |

## 5. Risks

| Risk                                   | Probability | Impact | Mitigation                                                                      |
| -------------------------------------- | ----------: | -----: | ------------------------------------------------------------------------------- |
| Federation has no clear API/export.    |        High |   High | Build manual/CSV import first; audit source before automation.                  |
| Payment setup is not ready.            |      Medium |   High | Keep sandbox mode until provider, SSL, legal, fiscal, and emails are validated. |
| Accidental publication of minors data. |      Medium |   High | Minimal public data by default; separate admin/private data.                    |
| Real content is late.                  |        High | Medium | Use clear placeholders in staging; define minimum launch content.               |
| Shop lacks product/stock/prices.       |        High |   High | Do not activate public checkout until catalog is signed off.                    |
| Scope creep.                           |        High |   High | Deliver by small cycles with checkpoints.                                       |
| Excessive plugin dependency.           |      Medium |   High | Use plugins only for commodity areas; keep club data in `cbn-core`.             |
| Legal texts delay launch.              |        High |   High | Separate public content launch from payment/registration production launch.     |
| Payment reconciliation errors.         |      Medium |   High | Use WooCommerce order states, provider IDs, webhooks, and periodic checks.      |

## 6. Stack Decision

### Option A - Professional WordPress

Recommended for this project.

- WordPress as CMS.
- Custom theme: `wp-content/themes/cbn-theme`.
- MU plugin: `wp-content/mu-plugins/cbn-core`.
- WooCommerce for shop/orders/products/checkout.
- Stripe official WooCommerce extension as preferred gateway.
- Redsys/TPV plugin only if the bank requires it.
- ACF JSON for structured editable fields.
- WP-Cron or real cron for imports after source validation.
- WP-CLI for maintenance and scripted checks.
- WordPress roles for limited admin access.

Why it fits: it supports club-managed content, fast MVP delivery, news, sponsors, shop, payments, and conventional hosting.

How we avoid a fragile plugin-heavy site:

- Keep club-specific data models in `cbn-core`, not in visual builder plugins.
- Keep visual implementation in the theme.
- Use WooCommerce for commerce because it is a mature domain tool.
- Use ACF for field editing, not for business logic.
- Add plugins only when the feature is commodity and maintained.
- Document every plugin decision and test it in staging.

### Option B - Custom Development

Not recommended as the main route now.

Possible stack: Next.js, React, TypeScript, PostgreSQL, Prisma, Zod, Stripe/Redsys, custom admin, Playwright, Vitest/Jest.

It would be justified if federation integration, admin workflows, or scaling requirements become too specific for WordPress. The cost is building and maintaining a custom CMS/admin for the club.

### Option C - Hybrid

Recommended as an evolution path, not a reset.

- WordPress for public content, admin, shop, and editorial flow.
- WooCommerce for shop and payment operations.
- Custom import module or service for federation data if needed.
- Optional API layer for sports data after the source is known.

## 7. Stack Matrix

| Criterion            | WordPress | Custom Development | Hybrid | Winner           |
| -------------------- | --------: | -----------------: | -----: | ---------------- |
| MVP speed            |         5 |                  2 |      4 | WordPress        |
| Club maintainability |         5 |                  2 |      4 | WordPress        |
| Shop                 |         5 |                  3 |      5 | WordPress/Hybrid |
| Payments             |         4 |                  4 |      4 | Tie              |
| Registrations        |         4 |                  4 |      4 | Tie              |
| Federation data      |         3 |                  5 |      5 | Hybrid           |
| Security             |         4 |                  4 |      4 | Tie              |
| Cost                 |         5 |                  2 |      4 | WordPress        |
| Scalability          |         3 |                  5 |      4 | Custom           |
| Complexity           |         4 |                  2 |      3 | WordPress        |
| Risk                 |         4 |                  2 |      3 | WordPress        |

Primary decision: continue with professional WordPress and keep a hybrid path for federation import.

## 8. Area Matrix

| Area               | Recommendation                            | Alternatives       | Reason                                               | Risks                         | Decision                |
| ------------------ | ----------------------------------------- | ------------------ | ---------------------------------------------------- | ----------------------------- | ----------------------- |
| CMS/framework      | WordPress                                 | Next.js custom CMS | Club can manage content.                             | Plugin/security upkeep.       | WordPress               |
| Frontend           | Custom theme + Vite                       | Page builder       | Controlled performance and design.                   | More code ownership.          | Custom theme            |
| Backend            | WordPress + MU plugin                     | Custom API         | Native admin and CPTs.                               | WordPress constraints.        | MU plugin               |
| Database           | WordPress/MariaDB                         | PostgreSQL         | Fits WordPress.                                      | Less relational strictness.   | MariaDB                 |
| Content admin      | WP admin + ACF                            | Custom admin       | Fast and familiar.                                   | Requires field discipline.    | WP admin                |
| Shop               | WooCommerce                               | Shopify, custom    | Mature WordPress commerce.                           | Plugin configuration.         | WooCommerce             |
| Payments           | Stripe first                              | Redsys/TPV         | Fast test mode and hosted checkout.                  | Bank may require Redsys.      | Stripe unless changed   |
| Registration forms | WooCommerce product flow or form plugin   | Fully custom       | Connects to payment/order states.                    | Legal fields must be correct. | Decide per activity     |
| Federation import  | Manual/CSV first, automated later         | Scraper            | Unknown source.                                      | Duplicates or brittle import. | Source-first design     |
| Validation         | Server-side PHP/WP + provider validation  | Zod custom         | Keep validation close to submission.                 | Plugin forms can be weak.     | Custom where critical   |
| Testing            | npm verify + CI + WP validation           | Full e2e now       | Current repo already supports build/format/PHP lint. | No e2e yet.                   | Incremental             |
| Security           | Least privilege, updates, WAF, no secrets | Custom auth        | Practical for WordPress.                             | Admin/plugin risk.            | WordPress hardening     |
| Backups            | Hosting/external backups                  | Manual export      | Needed before production.                            | Restore untested.             | External backups        |
| Hosting            | Managed WordPress with staging            | VPS                | Lower ops burden.                                    | Provider lock-in.             | Managed WP preferred    |
| CI/CD              | GitHub Actions + build                    | Manual upload only | Existing CI.                                         | Deploy process not final.     | Keep CI                 |
| Observability/logs | WP/debug logs, payment/import event logs  | External APM       | Enough for MVP.                                      | PII in logs if careless.      | Minimal safe logs       |
| Documentation      | Root docs + redesign docs                 | Ad hoc notes       | Needed for agent continuity.                         | Docs drift.                   | Maintain docs per cycle |

## 9. Libraries, Plugins, And Services

| Need              | Library/plugin/service                                              | Purpose                                                            | Alternatives                            | Risks                             | Recommendation                                |
| ----------------- | ------------------------------------------------------------------- | ------------------------------------------------------------------ | --------------------------------------- | --------------------------------- | --------------------------------------------- |
| Shop              | WooCommerce                                                         | Products, cart, checkout, orders, emails.                          | Shopify, custom checkout.               | Misconfiguration, plugin updates. | Use WooCommerce.                              |
| Stripe payments   | WooPayments or official Stripe for WooCommerce                      | Card payments in test/production with provider handling card data. | Custom Stripe Checkout.                 | Webhook/config errors.            | Use official maintained extension.            |
| Redsys/TPV        | Bank-supported Redsys plugin                                        | Spanish bank TPV when required.                                    | Stripe.                                 | Plugin quality varies.            | Only if bank requires it.                     |
| Forms             | Gravity Forms or Fluent Forms                                       | Contact/registration forms.                                        | WooCommerce product fields, custom CPT. | Sensitive data and payment sync.  | Use only if WooCommerce flow is insufficient. |
| Validation        | WordPress server-side validation + form/plugin validation           | Required fields, consents, data sanity.                            | Zod in custom app.                      | Client-only validation is unsafe. | Server-side mandatory.                        |
| Emails            | WooCommerce emails + SMTP plugin/service                            | Transactional delivery.                                            | Hosting mail only, Mailgun, Brevo.      | Deliverability.                   | Use SMTP/service in production.               |
| Roles             | WordPress roles/capabilities                                        | Limited admin access.                                              | Members/User Role Editor.               | Over-permission.                  | Native first; plugin only if needed.          |
| Custom fields     | ACF with Local JSON                                                 | Admin fields for home, teams, matches, sponsors.                   | Carbon Fields, Meta Box.                | License/features if Pro needed.   | Keep ACF-free-compatible where possible.      |
| CSV/Excel import  | WP All Import or custom WP-CLI/admin import                         | Manual structured sports data import.                              | Spreadsheet manual entry.               | Duplicate records.                | Start custom/simple when format is known.     |
| Scraping/parsing  | Custom parser only after permission                                 | Last-resort source ingestion.                                      | API/export/manual CSV.                  | Legal and fragility risk.         | Avoid unless approved.                        |
| Backups           | Hosting backups + external backup plugin/service                    | Restore points.                                                    | Manual DB/file export.                  | Untested restore.                 | Configure before production.                  |
| Security          | Cloudflare/WAF, 2FA, updates                                        | Basic WordPress hardening.                                         | VPS firewall only.                      | False sense of security.          | Use managed layers.                           |
| SEO               | Rank Math or Yoast                                                  | Metadata, sitemap.                                                 | Native WP only.                         | Plugin bloat.                     | Pick one maintained plugin.                   |
| Analytics         | GA4 or privacy-first analytics                                      | Usage insight.                                                     | Server logs.                            | Consent/cookies.                  | Decide with privacy policy.                   |
| Cache/performance | Hosting cache, LiteSpeed Cache, WP Rocket, Cloudflare               | Page speed.                                                        | No cache.                               | Cache checkout/admin incorrectly. | Configure carefully after WooCommerce.        |
| Testing           | GitHub Actions, npm verify, PHP lint, Browser/Playwright for visual | Quality gates.                                                     | Manual only.                            | Gaps without e2e.                 | Keep CI, add visual/e2e later.                |
| Deployment        | Managed WordPress staging + Git/build process                       | Safe release.                                                      | FTP manual.                             | Assets not built.                 | Require staging and build step.               |

## 10. Modular Architecture

### Public Website Module

Owns Home, Club, Teams, Matches/Results, News, Sponsors, Contact, and legal footer. Implemented primarily in the theme.

### Registrations Module

Owns registration forms, validation, consents, storage, email notifications, payment association, and registration state. It must not expose private/minor data publicly.

### Shop Module

Owns products, variations, stock, cart, checkout, pickup/shipping, orders, emails, returns, and admin workflows. Implemented with WooCommerce when activated.

### Payments Module

Owns provider configuration, checkout, webhooks/notifications, idempotency, state changes, payment event logs, test mode, production switch checklist, and reconciliation.

### Sports Data Module

Owns teams, categories, seasons, matches, results, standings, statistics, official source mapping, manual/CSV imports, and future automation.

### Sponsors Module

Owns sponsor CPT, logo, tier, link, description, ordering, home visibility, and sponsors page.

### News Module

Owns posts, categories, announcements, events, campus notices, registration notices, and related content.

### Administration Module

Owns roles, permissions, editing workflows, content guides, order management, registration management, and sports data management.

### Legal And Privacy Module

Owns privacy, cookies, purchase terms, returns, minor consent, image consent, and legal notices.

### Infrastructure Module

Owns local Docker, staging, production, env vars, backups, deployment, logs, monitoring, and rollback.

## 11. Current Repository Mapping

| Area                  | Current Path                                   |
| --------------------- | ---------------------------------------------- |
| Theme                 | `wp-content/themes/cbn-theme`                  |
| Theme setup           | `wp-content/themes/cbn-theme/inc`              |
| Frontend source       | `wp-content/themes/cbn-theme/assets/src`       |
| ACF JSON              | `wp-content/themes/cbn-theme/acf-json`         |
| MU plugin loader      | `wp-content/mu-plugins/cbn-core.php`           |
| MU plugin core        | `wp-content/mu-plugins/cbn-core`               |
| Docker local          | `compose.yaml`, `docker/wordpress/uploads.ini` |
| CI                    | `.github/workflows/ci.yml`                     |
| Product/redesign docs | `docs/redesign`                                |

## 12. Implementation Loop

Every cycle follows:

1. MVP target.
2. One atomic feature.
3. Debug and verification.
4. Checkpoint documentation.
5. Next incremental MVP.

Do not advance when build fails, critical flows are broken, payments are not validated in test mode, import errors are uncontrolled, secrets are exposed, or there is no clear test path.
