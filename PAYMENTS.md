# Payments

This document defines the payment architecture for the Club Baloncesto Navalcarnero MVP. It is intentionally conservative: no real payments until provider, legal, fiscal, SSL, emails, and club operations are validated.

## 1. Scope

Payment use cases:

- Shop orders for club clothing.
- Registrations, campus, trials, activities, fees, or events when the club confirms them.

Out of scope for the first safe payment cycle:

- Storing card data.
- Real production charges without final sign-off.
- Custom card forms.
- Subscription/recurring payments unless explicitly scoped.
- Federation or membership automation tied directly to a paid order.

## 2. Provider Decision

Decision (2026-07-02): the club chose a bank virtual TPV, which means **WooCommerce + Redsys** as the primary path. The bank (Banco Sabadell or Ibercaja) is pending confirmation of costs and setup; both use Redsys, so the technical integration does not depend on which bank is chosen.

Why WooCommerce:

- WooCommerce covers products, variations, stock, cart, orders, emails, taxes/shipping hooks, coupons, and admin management.
- Card data stays with the provider, not this project.

Implementation rule for Redsys:

- Select a maintained Redsys plugin with test mode, documentation, and support.
- Build and test the full checkout with the standard Redsys test environment (test merchant data and test cards) without waiting for the bank.
- When the bank delivers the real merchant data (FUC merchant code, terminal, secret key), swap credentials in configuration only.
- Fallback if the bank TPV activation is late for launch: shop visible with bank transfer / local pickup, card payments activated once the TPV is ready.

Previous recommendation (superseded): Stripe was the recommended default while the gateway was undecided. Keep Stripe as a documented alternative only if the bank TPV falls through.

- Do not run both gateways in production unless the operational owner can reconcile both.

## 3. Payment States

Project-level states:

- `pending`
- `checkout_created`
- `paid`
- `failed`
- `cancelled`
- `refunded`
- `disputed`

WooCommerce order states will remain the operational source of truth for shop payments. Custom registration states must map to provider/order states when payments are attached.

Registration states:

- `draft`
- `submitted`
- `pending_payment`
- `paid`
- `confirmed`
- `cancelled`
- `rejected`

## 4. Required Payment Record Data

Every paid transaction must clearly track:

- What is being paid.
- Amount.
- Currency.
- Buyer or registrant.
- Parent/guardian reference when applicable.
- State.
- Provider.
- Provider payment/session/charge ID.
- Related WooCommerce order or registration ID.
- Created/updated dates.
- Refund/dispute references if applicable.

Never store card number, CVC, or raw card data.

## 5. Sandbox First

Minimum sandbox tests before any production switch:

- Successful shop payment.
- Failed shop payment.
- Cancelled checkout.
- Refund flow.
- Order email to buyer.
- Internal notification to club.
- Registration payment success.
- Registration payment failure/cancel.
- Webhook/notification validation.
- Duplicate webhook/idempotency behavior.
- Reconciliation between provider dashboard and WooCommerce/admin data.

## 6. Webhooks And Notifications

Requirements:

- Validate webhook signatures or provider notification secrets.
- Use provider IDs to avoid duplicate processing.
- Log event type, provider ID, related object, state change, and timestamp.
- Do not log secrets, full payloads with sensitive personal data, card data, or authorization headers.
- Webhooks must fail safely and be retryable.

## 7. Production Preconditions

Production payments are blocked until all items are true:

- Gateway selected and approved.
- Test mode validated.
- SSL active.
- Domain and hosting stable.
- Legal pages final: privacy, cookies if needed, purchase terms, returns, inscriptions.
- Fiscal/invoice responsibility confirmed by the club/advisor.
- Product catalog confirmed: products, sizes, prices, stock, pickup/shipping.
- Registration activities and prices confirmed.
- Transactional emails reviewed.
- Club owner assigned for orders, refunds, and reconciliation.
- Backups configured.
- Admin roles limited.

## 8. Operational Responsibilities

Club owner must be assigned for:

- Reviewing new orders.
- Managing pickup/shipping.
- Handling size changes and returns.
- Confirming registration payment status.
- Reconciling provider payouts against orders.
- Responding to failed or disputed payments.

Technical owner must be assigned for:

- Gateway setup.
- Webhook configuration.
- Plugin updates.
- Staging checks.
- Logs and incident response.

## 9. Security Rules

- No real keys in Git, docs, screenshots, chats, or logs.
- No card data in the WordPress database.
- Use provider-hosted or official provider components.
- Keep production and test keys separate.
- Do not activate live mode from local development.
- Do not paste provider secrets into issue descriptions or changelogs.

## 10. Recommended Implementation Cycles

1. WooCommerce installed/configured in staging.
2. Product catalog MVP with test products.
3. Redsys test mode configured.
4. Shop checkout test matrix.
5. Registration payment flow design.
6. Registration payment test matrix.
7. Legal and operational review.
8. Production switch checklist.

## 11. Documentation To Update Per Payment Cycle

- Gateway selected.
- Plugin/service version.
- Test card scenarios used.
- Webhook endpoint location.
- State mapping.
- Known limitations.
- Production blockers.
- Reconciliation instructions.
