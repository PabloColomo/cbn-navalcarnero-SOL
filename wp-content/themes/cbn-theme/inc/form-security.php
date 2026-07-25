<?php
/**
 * Shared abuse protection for the two public admin-post form handlers.
 *
 * Context (see docs/auditoria-seguridad-codigo-2026-07-25.md, finding A-1):
 * a WordPress nonce is NOT a rate limit. For logged-out visitors the nonce is
 * derived from user ID 0 with an empty session token, so every anonymous
 * visitor receives the same value and it stays valid for roughly 12-24 hours.
 * A bot fetches /contacto/ once, extracts the nonce and can then replay
 * unlimited POSTs for a day. The honeypot only stops naive crawlers.
 *
 * This module adds the missing layers, with no external dependency:
 *
 *  - a per-IP and per-email counter backed by transients,
 *  - a signed time trap that rejects submissions faster than a human,
 *  - explicit maximum lengths, so a single POST cannot generate a huge email,
 *  - safe mail headers, including a From address on the site's own domain.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_mail_failed', 'cbn_form_log_mail_failure');
add_action('admin_notices', 'cbn_form_mail_failure_notice');

/**
 * Returns the client IP used for rate limiting.
 *
 * REMOTE_ADDR only, on purpose. X-Forwarded-For and friends are attacker
 * controlled: honouring them by default would let a single bot bypass the
 * limit entirely just by rotating a header value. Sites behind a reverse
 * proxy or CDN must opt in explicitly, and only after confirming the proxy
 * overwrites (not appends to) the header it is told to trust.
 *
 * define('CBN_TRUSTED_PROXY_HEADER', 'HTTP_CF_CONNECTING_IP'); // example
 */
function cbn_form_client_ip(): string
{
    $header = 'REMOTE_ADDR';

    if (defined('CBN_TRUSTED_PROXY_HEADER') && is_string(CBN_TRUSTED_PROXY_HEADER)) {
        $candidate = (string) CBN_TRUSTED_PROXY_HEADER;

        if (isset($_SERVER[$candidate])) {
            $header = $candidate;
        }
    }

    $raw = isset($_SERVER[$header]) ? sanitize_text_field(wp_unslash($_SERVER[$header])) : '';

    // A trusted proxy header may still carry a list; the client is the first.
    if (str_contains($raw, ',')) {
        $parts = explode(',', $raw);
        $raw = trim($parts[0]);
    }

    $ip = filter_var($raw, FILTER_VALIDATE_IP);

    return is_string($ip) ? $ip : 'unknown';
}

/**
 * Builds an opaque transient key for a rate limit bucket.
 *
 * The identifier (an IP or an email address) is hashed with the site's auth
 * salt so the options table never stores a readable list of who submitted
 * what, and so the key stays within the transient name length limit.
 */
function cbn_form_rate_limit_key(string $action, string $scope, string $identifier): string
{
    return 'cbn_rl_' . substr(md5($action . '|' . $scope . '|' . strtolower($identifier) . '|' . wp_salt('auth')), 0, 24);
}

/**
 * Returns the configured limits for an action.
 *
 * Registration is deliberately stricter than contact: each submission emails
 * a minor's personal data, so a flood is worse than an inbox nuisance.
 *
 * @return array{ip:int, email:int, window:int}
 */
function cbn_form_rate_limits(string $action): array
{
    $defaults = [
        'cbn_contact' => ['ip' => 5, 'email' => 3, 'window' => HOUR_IN_SECONDS],
        'cbn_registration' => ['ip' => 3, 'email' => 2, 'window' => HOUR_IN_SECONDS],
    ];

    $limits = $defaults[$action] ?? ['ip' => 5, 'email' => 3, 'window' => HOUR_IN_SECONDS];

    /**
     * Filters the rate limit for a public form action.
     *
     * @param array{ip:int, email:int, window:int} $limits
     * @param string                               $action
     */
    return (array) apply_filters('cbn_form_rate_limits', $limits, $action);
}

/**
 * Whether the given scope has already exhausted its allowance.
 */
function cbn_form_rate_limit_exceeded(string $action, string $scope, string $identifier): bool
{
    if ('' === $identifier || 'unknown' === $identifier) {
        return false;
    }

    $limits = cbn_form_rate_limits($action);
    $max = (int) ($limits[$scope] ?? 0);

    if ($max <= 0) {
        return false;
    }

    $hits = (int) get_transient(cbn_form_rate_limit_key($action, $scope, $identifier));

    return $hits >= $max;
}

/**
 * Records one attempt against a bucket.
 *
 * The window is not extended on each hit: the transient keeps the expiry set
 * by the first attempt, so a blocked sender is released a fixed time after
 * their first submission rather than being locked out indefinitely by their
 * own retries.
 */
function cbn_form_register_attempt(string $action, string $scope, string $identifier): void
{
    if ('' === $identifier || 'unknown' === $identifier) {
        return;
    }

    $limits = cbn_form_rate_limits($action);
    $key = cbn_form_rate_limit_key($action, $scope, $identifier);
    $hits = (int) get_transient($key);

    set_transient($key, $hits + 1, (int) ($limits['window'] ?? HOUR_IN_SECONDS));
}

/**
 * Prints the hidden, signed timestamp used by the time trap.
 *
 * The value is signed with wp_hash() so it cannot be forged or rewound; a bot
 * that strips or fakes it fails the check in cbn_form_time_trap_passed().
 */
function cbn_form_time_trap_field(string $action): void
{
    $issued_at = time();
    $token = $issued_at . '|' . wp_hash($action . '|' . $issued_at);

    printf(
        '<input type="hidden" name="cbn_form_ts" value="%s">',
        esc_attr($token)
    );
}

/**
 * Validates the time trap.
 *
 * Only a MINIMUM elapsed time is enforced. There is deliberately no maximum:
 * a full-page cache (or a visitor who leaves the tab open) would otherwise
 * serve a stale timestamp and reject a perfectly legitimate submission.
 * Rewinding the clock is already prevented by the signature.
 */
function cbn_form_time_trap_passed(string $action, string $raw_token, int $minimum_seconds = 3): bool
{
    if ('' === $raw_token || !str_contains($raw_token, '|')) {
        return false;
    }

    [$issued_at, $signature] = explode('|', $raw_token, 2);

    if (!ctype_digit($issued_at)) {
        return false;
    }

    if (!hash_equals(wp_hash($action . '|' . $issued_at), $signature)) {
        return false;
    }

    return (time() - (int) $issued_at) >= $minimum_seconds;
}

/**
 * Trims a submitted value to a maximum length.
 *
 * Without this, sanitize_textarea_field() happily accepts a multi-megabyte
 * message and turns it into a multi-megabyte email.
 */
function cbn_form_limit_length(string $value, int $max_length): string
{
    return mb_substr($value, 0, $max_length);
}

/**
 * Maximum accepted length per submitted field.
 */
function cbn_form_max_lengths(): array
{
    return (array) apply_filters(
        'cbn_form_max_lengths',
        [
            'name' => 120,
            'email' => 254, // RFC 5321 maximum path length.
            'phone' => 30,
            'dni' => 12,
            'short' => 60,
            'message' => 5000,
        ]
    );
}

/**
 * Default From address for club notifications.
 *
 * WordPress otherwise sends as wordpress@<server hostname>, which almost never
 * passes SPF/DKIM/DMARC. The result is silent delivery failure, and because
 * these forms deliberately store nothing in the database, a lost email is a
 * lost registration. This only fixes the address: the DNS records and an
 * authenticated SMTP transport still have to be configured on the host. See
 * docs/despliegue-produccion.md section 5.
 *
 * @return array{address:string, name:string}
 */
function cbn_form_notification_sender(): array
{
    $host = wp_parse_url(home_url(), PHP_URL_HOST);
    $host = is_string($host) ? preg_replace('/^www\./i', '', $host) : '';

    // PHPMailer rejects addresses whose domain has no dot ("localhost"), and
    // then the mail dies at header validation before any transport attempt.
    // Fall back to admin_email so local and single-label hosts still send.
    if (!is_string($host) || !str_contains($host, '.')) {
        $host = '';
    }

    $sender = [
        'address' => $host ? 'no-reply@' . $host : (string) get_option('admin_email'),
        'name' => wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES),
    ];

    /**
     * Filters the From address used for club form notifications.
     *
     * @param array{address:string, name:string} $sender
     */
    return (array) apply_filters('cbn_form_notification_sender', $sender);
}

/**
 * Builds the headers for a club notification email.
 *
 * The Reply-To display name is rebuilt from a restricted character set rather
 * than trusting sanitize_text_field() to have removed everything dangerous.
 * That function does strip CR/LF (which is what blocks header injection) and
 * does strip angle-bracket sequences, but relying on a side effect of a
 * general-purpose sanitiser for a header boundary is the kind of assumption
 * that breaks quietly when the sanitiser changes.
 *
 * @return string[]
 */
function cbn_form_mail_headers(string $reply_to_email, string $reply_to_name = ''): array
{
    $sender = cbn_form_notification_sender();
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('From: %s <%s>', cbn_form_header_safe_name($sender['name']), $sender['address']),
    ];

    if (is_email($reply_to_email)) {
        $name = cbn_form_header_safe_name($reply_to_name);
        $headers[] = '' !== $name
            ? sprintf('Reply-To: %s <%s>', $name, $reply_to_email)
            : sprintf('Reply-To: %s', $reply_to_email);
    }

    return $headers;
}

/**
 * Reduces a display name to characters that cannot break a mail header.
 */
function cbn_form_header_safe_name(string $name): string
{
    $name = preg_replace('/[^\p{L}\p{N} .\'-]/u', '', $name);

    return trim(mb_substr((string) $name, 0, 80));
}

/**
 * Records a failed delivery in the PHP error log.
 *
 * Deliberately uses error_log() and not a file under wp-content: with
 * WP_DEBUG_LOG disabled in production (as the deployment runbook requires),
 * this reaches the host's error log instead of a web-readable file.
 */
function cbn_form_log_mail_failure(WP_Error $error): void
{
    error_log(
        sprintf(
            '[CBN] Fallo al enviar un correo del formulario: %s',
            $error->get_error_message()
        )
    );

    // Also surfaced in wp-admin: nobody reads the server error log, and a
    // registration that never arrives leaves no other trace because these
    // forms deliberately store nothing in the database.
    update_option(
        'cbn_last_mail_failure',
        [
            'time' => time(),
            'message' => mb_substr($error->get_error_message(), 0, 300),
        ],
        false
    );
}

/**
 * Warns administrators that outgoing mail is failing.
 *
 * Without this the club would keep believing the forms work while every
 * enquiry and every registration silently disappears.
 */
function cbn_form_mail_failure_notice(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $failure = get_option('cbn_last_mail_failure');

    if (!is_array($failure) || empty($failure['time'])) {
        return;
    }

    // Dismiss the warning automatically once it is a week old, so a single
    // historical blip does not nag forever.
    if ((time() - (int) $failure['time']) > WEEK_IN_SECONDS) {
        delete_option('cbn_last_mail_failure');
        return;
    }

    printf(
        '<div class="notice notice-error"><p><strong>%s</strong> %s</p><p>%s</p><p><em>%s</em></p></div>',
        esc_html__('El envío de correo está fallando.', 'cbn'),
        esc_html(
            sprintf(
                'Último fallo: %s.',
                wp_date('j \d\e F \a \l\a\s H:i', (int) $failure['time'])
            )
        ),
        esc_html__(
            'Los formularios de contacto e inscripción no guardan nada en la web: si el correo no sale, la solicitud se pierde. Revisa la configuración SMTP del hosting.',
            'cbn'
        ),
        esc_html((string) ($failure['message'] ?? ''))
    );
}

/**
 * Records a rejected submission so repeated abuse is visible in the host log.
 *
 * Throttled to one line per IP, reason and hour. Logging every rejection would
 * turn a submission flood into a disk-filling flood, which just converts one
 * denial of service into another.
 */
function cbn_form_log_rejection(string $action, string $reason): void
{
    $ip = cbn_form_client_ip();
    $throttle_key = cbn_form_rate_limit_key($action, 'log_' . $reason, $ip);

    if (get_transient($throttle_key)) {
        return;
    }

    set_transient($throttle_key, 1, HOUR_IN_SECONDS);

    error_log(
        sprintf(
            '[CBN] Envío rechazado en %s (motivo: %s, IP: %s)',
            $action,
            $reason,
            $ip
        )
    );
}
