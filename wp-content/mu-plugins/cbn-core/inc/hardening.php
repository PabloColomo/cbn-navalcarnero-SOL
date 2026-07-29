<?php
/**
 * Platform hardening for CBN.
 *
 * The site has exactly one authenticated surface (club administrators) and
 * one anonymous surface (visitors plus two admin-post form handlers). None
 * of the WordPress features disabled here are used by either. Each block is
 * independent and reversible through a filter so a future integration
 * (WooCommerce, a mobile app, a headless client) can re-enable just what it
 * needs instead of dropping the whole file.
 *
 * See docs/auditoria-seguridad-codigo-2026-07-25.md (finding M-1) and
 * docs/despliegue-produccion.md.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Blocks the theme/plugin file editors in wp-admin. Defined here rather than
// in wp-config.php so the protection travels with the repository and does not
// depend on how each environment is provisioned.
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

add_action('send_headers', 'cbn_send_security_headers');
add_filter('comments_open', '__return_false', 20);
add_filter('pings_open', '__return_false', 20);
add_filter('rest_endpoints', 'cbn_remove_rest_comment_endpoints');
add_action('init', 'cbn_disable_comment_support');
add_filter('comments_array', '__return_empty_array', 20);
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', 'cbn_remove_pingback_header');
add_filter('rest_endpoints', 'cbn_restrict_rest_user_endpoints');
add_filter('wp_is_application_passwords_available', 'cbn_disable_application_passwords');
add_action('template_redirect', 'cbn_block_author_enumeration');
add_filter('login_errors', 'cbn_generic_login_error');
add_action('init', 'cbn_remove_head_metadata');

/**
 * Adds baseline security headers to public responses.
 *
 * Content-Security-Policy is intentionally NOT set here. The theme ships
 * inline `<script>` and `wp_add_inline_script()` calls (header.php and
 * inc/assets.php), so a meaningful policy needs per-request nonces. Introduce
 * it as Content-Security-Policy-Report-Only first and only enforce it once
 * the report endpoint is quiet.
 */
function cbn_send_security_headers(): void
{
    if (is_admin() || headers_sent()) {
        return;
    }

    $headers = [
        // Stops browsers guessing a different MIME type than the one served.
        'X-Content-Type-Options' => 'nosniff',
        // Keeps full URLs (which can carry query args) off cross-origin referers.
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        // Anti-clickjacking. Relevant because the registration form is a
        // one-click submit that would otherwise be framable.
        'X-Frame-Options' => 'SAMEORIGIN',
        // The site requests none of these; deny them explicitly.
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=()',
        // Prevents Flash/PDF cross-domain policy files from being honoured.
        'X-Permitted-Cross-Domain-Policies' => 'none',
    ];

    // HSTS is opt-in through a constant and only ever sent over HTTPS.
    // Enabling it before the certificate covers the whole domain locks
    // visitors out, and browsers cache the policy, so it is painful to undo.
    // Turn it on in wp-config.php once HTTPS is verified:
    //     define('CBN_ENABLE_HSTS', true);
    // Start with a short max-age (600) and raise it to a year when confident.
    if (is_ssl() && defined('CBN_ENABLE_HSTS') && CBN_ENABLE_HSTS) {
        $max_age = defined('CBN_HSTS_MAX_AGE') ? (int) CBN_HSTS_MAX_AGE : 600;
        $headers['Strict-Transport-Security'] = 'max-age=' . max(0, $max_age);
    }

    /**
     * Filters the public security headers.
     *
     * WooCommerce checkout will need `payment=(self)` in Permissions-Policy
     * once a hosted Redsys form is embedded. Adjust here, not in the theme.
     *
     * @param array<string, string> $headers
     */
    $headers = (array) apply_filters('cbn_security_headers', $headers);

    foreach ($headers as $header => $value) {
        if (is_string($header) && is_string($value) && '' !== $value) {
            header($header . ': ' . $value);
        }
    }
}

/**
 * Drops the X-Pingback header advertised on every page.
 *
 * @param array<string, string> $headers
 * @return array<string, string>
 */
function cbn_remove_pingback_header(array $headers): array
{
    unset($headers['X-Pingback']);

    return $headers;
}

/**
 * Removes the REST user routes for readers who cannot list users.
 *
 * /wp-json/wp/v2/users is readable anonymously by default and returns the
 * `slug` of every user who has authored a published post, which is the
 * account's login name in a default installation. Combined with a reachable
 * wp-login.php that hands an attacker half of the credential pair.
 *
 * @param array<string, mixed> $endpoints
 * @return array<string, mixed>
 */
function cbn_restrict_rest_user_endpoints(array $endpoints): array
{
    if (current_user_can('list_users')) {
        return $endpoints;
    }

    unset(
        $endpoints['/wp/v2/users'],
        $endpoints['/wp/v2/users/(?P<id>[\d]+)'],
        $endpoints['/wp/v2/users/me']
    );

    return $endpoints;
}

/**
 * Turns off application passwords.
 *
 * Nothing in this project authenticates over the REST API. Leaving them
 * enabled keeps a password-equivalent credential path open on an account
 * that can do everything.
 */
function cbn_disable_application_passwords(): bool
{
    return false;
}

/**
 * Blocks ?author=N and /author/<slug>/ user enumeration on the front end.
 *
 * The theme has no author.php template, so these URLs serve no purpose here
 * beyond disclosing account names.
 */
function cbn_block_author_enumeration(): void
{
    // WordPress resolves both ?author=N and /author/<slug>/ to an author
    // query, so is_author() covers the two entry points.
    if (is_admin() || !is_author()) {
        return;
    }

    wp_safe_redirect(home_url('/'), 301);
    exit;
}

/**
 * Returns a single generic message for every login failure.
 *
 * The default messages distinguish "unknown username" from "wrong password",
 * which confirms which accounts exist.
 */
function cbn_generic_login_error(string $error): string
{
    global $errors;

    // The lockout notice from login-security.php must survive this filter:
    // replacing it with "wrong credentials" would push a locked-out user to
    // keep retrying and extend their own lockout. The lockout is not an
    // information leak — the attacker caused it and already knows.
    if ($errors instanceof WP_Error
        && in_array('cbn_login_locked', $errors->get_error_codes(), true)
    ) {
        return $error;
    }

    unset($error);

    return 'Las credenciales no son correctas.';
}

/**
 * Removes comment support entirely.
 *
 * The theme has no comments.php and never calls comments_template(), so
 * comments are invisible on the front end. They were NOT disabled, though:
 * wp-comments-post.php still accepted POSTs and /wp/v2/comments still
 * accepted anonymous ones. The practical result is spam accumulating in the
 * database, unmoderated and unseen, with stored outbound links — exactly the
 * kind of thing an external security review flags. Akismet is installed but
 * inert without an API key.
 *
 * Closing them at the data layer is safer than relying on the theme simply
 * not rendering them.
 */
function cbn_disable_comment_support(): void
{
    foreach (get_post_types(['public' => true], 'names') as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}

/**
 * Removes the REST comment routes.
 *
 * /wp/v2/comments accepts anonymous POST when comments are open, and its GET
 * discloses commenter names. Neither is used by this project.
 *
 * @param array<string, mixed> $endpoints
 * @return array<string, mixed>
 */
function cbn_remove_rest_comment_endpoints(array $endpoints): array
{
    unset(
        $endpoints['/wp/v2/comments'],
        $endpoints['/wp/v2/comments/(?P<id>[\d]+)']
    );

    return $endpoints;
}

/**
 * Strips version and legacy discovery metadata from <head>.
 *
 * wp_generator publishes the exact WordPress version, which lets an attacker
 * match the install against a public vulnerability list without probing.
 */
function cbn_remove_head_metadata(): void
{
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');

    add_filter('the_generator', '__return_empty_string');
}
