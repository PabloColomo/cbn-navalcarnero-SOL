# Privacy Notes

This document records privacy and minors-protection rules for the Club Baloncesto Navalcarnero website.

## 1. Core Rule

Do not publish personal data of minors unless the club has explicit legal authorization and the data is necessary for the public purpose.

Default public approach:

- Minimal player data.
- No DNI/NIE.
- No phone.
- No email.
- No family data.
- No private notes.
- No sensitive documents.
- Images only when approved.

## 2. Data Categories

### Public Data

Allowed only when approved:

- Team name.
- Category.
- Competition.
- Match dates/results.
- Sponsor data.
- News and club announcements.
- Player display name or limited roster data only if authorized.
- Approved images.

### Administrative Data

Must stay private:

- Registration form data.
- Parent/guardian data.
- Contact details.
- DNI/NIE or identity documents when required.
- Payment/order metadata beyond public order status.
- Consent records.
- Medical, insurance, or special category data unless explicitly scoped and legally reviewed.

### Payment Data

Never store:

- Card number.
- CVC.
- Raw card details.

Allowed operational references:

- WooCommerce order ID.
- Provider session/payment ID.
- Payment state.
- Amount and currency.
- Timestamp.

## 3. Minors Rules

- No full public profiles by default.
- Public rosters must be opt-in and controlled.
- Photos of minors require approved image consent.
- Avoid combining image, full name, birth date, and team when not necessary.
- Allow hiding a player, photo, or roster entry.
- Keep registration and family data out of theme templates.

## 4. Consents

Registrations should separate:

- Registration terms acceptance.
- Privacy policy acceptance.
- Image use consent.
- Commercial/marketing communication consent, only if needed and optional.

Consent records should capture:

- Version of text accepted.
- Date/time.
- Form/activity.
- Person giving consent.
- Related participant when applicable.

Final legal wording must come from the club or legal advisor before production.

## 5. Public vs Private Storage

Public data should live in posts/CPTs intended for publication.

Private form data should live in:

- A controlled form plugin store, WooCommerce order metadata, or custom private storage.
- Admin-only screens.
- Export flows with access control.

Private data must not be embedded in:

- Public HTML.
- Public JSON.
- JS bundles.
- Theme templates.
- Screenshots.
- Logs.

## 6. Development Data

- Do not use real minors data in local development unless unavoidable and approved.
- Prefer anonymized sample data.
- Do not commit exports from WordPress, WooCommerce, forms, or federation systems if they include personal data.
- Do not commit uploads containing private documents or unapproved photos.

## 7. Images

Image governance:

- Use official/approved club assets.
- Track source and approval outside Git if it contains personal details.
- Avoid hero/campaign images where a minor is identifiable unless approved.
- Provide alt text that describes the image without exposing unnecessary personal identity.

## 8. Cookies And Analytics

Analytics must be decided together with legal/cookie policy.

- Avoid unnecessary trackers before legal review.
- If GA4 or marketing pixels are used, document consent requirements.
- Prefer minimal analytics if the club wants lower privacy overhead.

## 9. Retention

The club must define:

- How long registration data is kept.
- How long order/payment metadata is kept.
- Who can export data.
- When old activity/campus registration data is deleted or archived.

Until defined, keep collection minimal and avoid sensitive fields.

## 10. Incident Response

If personal data is accidentally exposed:

1. Remove public exposure immediately.
2. Preserve internal evidence without spreading data further.
3. Identify affected data and users.
4. Notify the responsible club contact.
5. Follow legal/GDPR notification obligations.
6. Document the fix and prevention step.

## 11. Production Privacy Checklist

- Final privacy policy published.
- Legal notice published.
- Cookie policy/consent configured if needed.
- Purchase and return terms published before payments.
- Registration terms and consents published.
- Minor/image visibility rules confirmed.
- Admin roles limited.
- No real secrets in Git.
- No private data in public templates.
- No personal data in logs or screenshots.
