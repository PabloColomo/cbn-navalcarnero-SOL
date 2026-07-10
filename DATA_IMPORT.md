# Data Import

This document defines the safe path for importing or synchronizing sports data from federation sources into WordPress.

## 1. Principle

Federation data should be treated as the official sports source when access is legal, stable, and technically viable.

Flow:

```text
Federation source -> Importer/normalizer -> WordPress -> Public website
```

WordPress acts as:

- Public cache.
- Editorial layer.
- Visibility control.
- Relationship layer for news, photos, sponsors, and match reports.

## 2. Source Priority

Use this order:

1. Official API.
2. Official CSV/Excel/XML/JSON export.
3. Manual structured CSV/Excel import.
4. Semi-automatic import.
5. Scraping only if explicitly permitted and documented.

Do not build authenticated scraping before confirming permission and terms of use.

## 3. Questions Before Automation

Before implementing an automated import, confirm:

- Is there an official API?
- Is there an official export file?
- Is there a private panel with downloads?
- Are public pages enough for the desired data?
- Is scraping allowed?
- Are there rate limits?
- Is authentication required?
- Can the club legally store the data?
- What fields are mandatory and optional?
- What update frequency is useful?
- What teams, competitions, and seasons are in scope?

## 4. MVP Import Scope

For the MVP:

- Prepare data structures.
- Allow manual content entry.
- Prefer CSV/Excel import only after format is known.
- Display teams, matches, and results clearly.
- Leave automatic synchronization for a later cycle if source access is unclear.

## 5. Current WordPress Entities

Custom post types:

- `cbn_team`
- `cbn_player`
- `cbn_match`
- `cbn_sponsor`
- `cbn_document`

Taxonomies:

- `cbn_season`
- `cbn_sport_category`
- `cbn_competition`
- `cbn_venue`
- `cbn_sponsor_tier`

ACF fields already prepare external traceability on teams, matches, and sponsors.

## 6. External Traceability

Imported entities should store:

- `external_source`
- `external_id`
- `external_url`
- `external_updated_at`
- `external_hash` when useful
- `external_sync_status`

The pair `external_source` + `external_id` must be stable enough to support upsert without duplicates.

## 7. Suggested Match Fields

Minimum match import fields:

- External ID.
- Season.
- Competition.
- Round/jornada.
- Match date.
- Match time.
- Home team.
- Away team.
- CBN team relation when known.
- Venue.
- Status.
- Home score.
- Away score.
- Federation URL.
- External updated timestamp.

Optional fields:

- Standings group.
- Phase.
- Referee data only if legally/publicly appropriate.
- Statistics if source is reliable.
- Match report relation.

## 8. Validation Rules

Each import must validate:

- Required fields exist.
- Dates and times parse correctly.
- Scores are numeric when present.
- Status is one of the accepted values.
- Season and competition values are normalized or stored as visible fallback text.
- External ID is present for automatic upsert.
- No private credentials or raw sensitive payloads are logged.

## 9. Deduplication

Deduplication order:

1. `external_source` + `external_id`.
2. If missing, normalized season + competition + date + home team + away team.
3. Manual review for ambiguous rows.

Never silently merge ambiguous matches.

## 10. Import Logs

Each import run should record:

- Date and time.
- Source.
- Mode: dry-run or write.
- File/API reference, without credentials.
- Rows read.
- Created records.
- Updated records.
- Skipped records.
- Errors.
- Duration.
- User/admin who ran it when applicable.

Logs must not include passwords, tokens, session cookies, authorization headers, or private personal data.

## 11. Error Handling

Import must fail safely:

- A bad row should not break the public site.
- Dry-run should be available before write.
- Partial failures should be reviewable.
- Public views should show last update date where data comes from federation.
- If synchronization fails, keep last known good public data and surface an admin warning.

## 12. Legal And Privacy Constraints

- Do not import private player data unless legal basis and club approval are explicit.
- Do not publish full names or sensitive data of minors by default.
- Keep public data separate from administrative data.
- Do not store federation credentials in the repository.
- Do not paste federation credentials in chat or logs.

## 13. Future Implementation Path

1. Audit federation source in read-only mode.
2. Document available endpoints/exports and fields.
3. Create sample source files without secrets.
4. Build parser/normalizer.
5. Implement dry-run.
6. Implement upsert.
7. Add logs and admin/CLI controls.
8. Validate in staging.
9. Automate only after manual import is reliable.
