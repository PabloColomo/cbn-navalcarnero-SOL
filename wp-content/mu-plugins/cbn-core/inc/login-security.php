<?php
/**
 * Brute-force protection for wp-login.php.
 *
 * Finding N-2 of docs/auditoria-seguridad-codigo-2026-07-25.md. Closing
 * XML-RPC removed the amplified vector (system.multicall could test hundreds
 * of passwords per request), and the generic login error removed username
 * enumeration, but a plain dictionary attack against the form was still
 * unlimited.
 *
 * Implemented in-house rather than with a plugin: the project rule is not to
 * add dependencies without approval, and the whole requirement here is a
 * counter plus a lockout. The trade-off is that this is per-site state in the
 * options table, not a distributed reputation service — appropriate for a
 * club website with a handful of administrators, not for a large multi-site.
 */

if (!defined('ABSPATH')) {
    exit;
}

const CBN_LOGIN_MAX_ATTEMPTS = 5;
const CBN_LOGIN_LOCKOUT = 15 * MINUTE_IN_SECONDS;
const CBN_LOGIN_LONG_LOCKOUT = HOUR_IN_SECONDS;
const CBN_LOGIN_LONG_THRESHOLD = 10;

add_filter('authenticate', 'cbn_login_block_locked_out', 30, 1);
add_action('wp_login_failed', 'cbn_login_record_failure');
add_action('wp_login', 'cbn_login_clear_failures', 10, 2);

/**
 * Returns the IP used to key login attempts.
 *
 * Reuses the form module's resolver when the theme is active so both share
 * the same "do not trust proxy headers unless told to" policy. Falls back to
 * REMOTE_ADDR so this mu-plugin keeps working if the theme is switched.
 */
function cbn_login_client_ip(): string
{
    if (function_exists('cbn_form_client_ip')) {
        return cbn_form_client_ip();
    }

    $raw = isset($_SERVER['REMOTE_ADDR'])
        ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']))
        : '';
    $ip = filter_var($raw, FILTER_VALIDATE_IP);

    return is_string($ip) ? $ip : 'unknown';
}

function cbn_login_transient_key(string $suffix): string
{
    return 'cbn_login_' . $suffix . '_' . substr(md5(cbn_login_client_ip() . wp_salt('auth')), 0, 20);
}

/**
 * Whether the current IP is exempt from the limiter.
 *
 * Escape hatch for a fixed office address, and the hook a future allow list
 * would use. Empty by default: an exemption is a hole, so it must be opted in.
 */
function cbn_login_is_exempt(): bool
{
    return (bool) apply_filters('cbn_login_exempt', false, cbn_login_client_ip());
}

/**
 * Rejects authentication while the IP is locked out.
 *
 * Hooked at priority 30, after WordPress has already assembled the user or
 * error at priority 20, so this replaces the result rather than racing it.
 *
 * @param WP_User|WP_Error|null $user
 * @return WP_User|WP_Error|null
 */
function cbn_login_block_locked_out($user)
{
    if (cbn_login_is_exempt()) {
        return $user;
    }

    $locked_until = (int) get_transient(cbn_login_transient_key('lock'));

    if ($locked_until <= time()) {
        return $user;
    }

    $minutes = max(1, (int) ceil(($locked_until - time()) / MINUTE_IN_SECONDS));

    return new WP_Error(
        'cbn_login_locked',
        sprintf(
            'Demasiados intentos fallidos. Vuelve a probar dentro de %d minutos.',
            $minutes
        )
    );
}

/**
 * Counts a failed attempt and locks the IP once the threshold is reached.
 *
 * The lockout escalates: repeated waves from the same address get an hour
 * instead of fifteen minutes.
 */
function cbn_login_record_failure(string $username): void
{
    unset($username);

    if (cbn_login_is_exempt()) {
        return;
    }

    $attempts_key = cbn_login_transient_key('fails');
    $attempts = (int) get_transient($attempts_key) + 1;

    set_transient($attempts_key, $attempts, DAY_IN_SECONDS);

    if ($attempts < CBN_LOGIN_MAX_ATTEMPTS) {
        return;
    }

    $lockout = $attempts >= CBN_LOGIN_LONG_THRESHOLD
        ? CBN_LOGIN_LONG_LOCKOUT
        : CBN_LOGIN_LOCKOUT;

    set_transient(cbn_login_transient_key('lock'), time() + $lockout, $lockout);

    error_log(
        sprintf(
            '[CBN] Bloqueo de acceso tras %d intentos fallidos (IP: %s, %d minutos)',
            $attempts,
            cbn_login_client_ip(),
            (int) ($lockout / MINUTE_IN_SECONDS)
        )
    );
}

/**
 * Clears the counters after a successful login.
 *
 * @param string  $user_login
 * @param WP_User $user
 */
function cbn_login_clear_failures($user_login, $user): void
{
    unset($user_login, $user);

    delete_transient(cbn_login_transient_key('fails'));
    delete_transient(cbn_login_transient_key('lock'));
}
