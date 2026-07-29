<?php
/**
 * Two-surface access model for CBN.
 *
 * Public visitors do not need a WordPress account. Content mutation is
 * reserved for administrators, while the native public admin-post handlers
 * used by contact and registration forms remain available.
 */

if (!defined('ABSPATH')) {
    exit;
}

const CBN_ROLE_SCHEMA_VERSION = '1.0.0';

add_action('init', 'cbn_install_administrator_capabilities', 5);
add_action('admin_init', 'cbn_restrict_non_admin_dashboard', 1);
add_filter('pre_option_users_can_register', 'cbn_disable_public_user_registration', 10, 0);
add_filter('user_has_cap', 'cbn_enforce_administrator_only_writes', 10, 4);
add_filter('show_admin_bar', 'cbn_filter_admin_bar_visibility');
add_filter('body_class', 'cbn_add_frontend_role_class');
add_filter('login_redirect', 'cbn_filter_login_redirect', 10, 3);
add_filter('logout_redirect', 'cbn_filter_logout_redirect', 10, 3);

/**
 * Returns every primitive capability required by a CBN custom post type.
 *
 * @return string[]
 */
function cbn_get_custom_content_capabilities(): array
{
    $capabilities = ['manage_cbn_content'];

    foreach (cbn_get_post_type_capability_bases() as [$singular, $plural]) {
        $capabilities = array_merge(
            $capabilities,
            [
                'edit_' . $singular,
                'read_' . $singular,
                'delete_' . $singular,
                'edit_' . $plural,
                'edit_others_' . $plural,
                'publish_' . $plural,
                'read_private_' . $plural,
                'delete_' . $plural,
                'delete_private_' . $plural,
                'delete_published_' . $plural,
                'delete_others_' . $plural,
                'edit_private_' . $plural,
                'edit_published_' . $plural,
            ]
        );
    }

    return array_values(array_unique($capabilities));
}

/**
 * Adds the versioned CBN capabilities to WordPress administrators.
 *
 * The version flag avoids writing the roles option on every request while
 * still allowing future capability migrations.
 */
function cbn_install_administrator_capabilities(): void
{
    $administrator = get_role('administrator');

    if (!$administrator instanceof WP_Role) {
        return;
    }

    $capabilities = cbn_get_custom_content_capabilities();
    $capabilities_complete = array_reduce(
        $capabilities,
        static fn (bool $complete, string $capability): bool => $complete && $administrator->has_cap($capability),
        true
    );

    if (CBN_ROLE_SCHEMA_VERSION === get_option('cbn_role_schema_version') && $capabilities_complete) {
        return;
    }

    foreach ($capabilities as $capability) {
        $administrator->add_cap($capability);
    }

    update_option('cbn_role_schema_version', CBN_ROLE_SCHEMA_VERSION, false);
}

/**
 * Prevents self-service accounts: the public experience is anonymous.
 */
function cbn_disable_public_user_registration(): string
{
    return '0';
}

/**
 * Capabilities a non-administrator account may keep.
 *
 * Everything outside this list is denied. See
 * cbn_enforce_administrator_only_writes() for why this is an allow list.
 *
 * @return array<string, true>
 */
function cbn_get_non_admin_allowed_capabilities(): array
{
    /**
     * Filters the minimal capability set kept for non-administrator accounts.
     *
     * Intended for future roles (for example a coach who may only upload
     * files). Keep any addition as narrow as possible.
     *
     * @param array<string, true> $allowed
     */
    return (array) apply_filters(
        'cbn_non_admin_allowed_capabilities',
        [
            'read' => true,
            'level_0' => true,
        ]
    );
}

/**
 * Ensures non-administrator accounts cannot mutate the site.
 *
 * We do not delete WordPress roles or user records. Instead, every account
 * without `manage_options` is reduced at runtime to a read-only capability
 * set. This also covers authenticated REST requests without breaking the
 * public admin-post handlers, which run unauthenticated.
 *
 * This is an ALLOW list on purpose. The previous implementation enumerated
 * the capabilities to deny, which left `promote_users`, `create_users`,
 * `install_plugins`, `activate_plugins`, `switch_themes`, `export` and
 * `unfiltered_html` reachable: an account holding `promote_users` without
 * `manage_options` could have escalated itself to administrator and bypassed
 * the whole model. Denying by default also means capabilities introduced
 * later by a plugin (WooCommerce, for instance) are covered automatically
 * instead of silently slipping through.
 *
 * @param array<string, bool> $allcaps
 * @param string[]            $caps
 * @param array<int, mixed>   $args
 * @return array<string, bool>
 */
function cbn_enforce_administrator_only_writes(array $allcaps, array $caps, array $args, WP_User $user): array
{
    if (!empty($allcaps['manage_options'])) {
        return $allcaps;
    }

    $allowed = cbn_get_non_admin_allowed_capabilities();

    foreach ($allcaps as $capability => $granted) {
        if (!isset($allowed[$capability])) {
            $allcaps[$capability] = false;
        }
    }

    return $allcaps;
}

/**
 * Keeps logged-in non-admin users out of wp-admin while allowing the public
 * admin-post.php and admin-ajax.php endpoints required by the site.
 */
function cbn_restrict_non_admin_dashboard(): void
{
    if (!is_user_logged_in() || current_user_can('manage_options')) {
        return;
    }

    global $pagenow;

    // profile.php is allowed so a non-administrator account can still change
    // its own password. Blocking it would leave such a user unable to rotate
    // a compromised credential without an administrator's help.
    $allowed = ['admin-post.php', 'admin-ajax.php', 'profile.php'];

    if (wp_doing_ajax() || in_array($pagenow, $allowed, true)) {
        return;
    }

    wp_safe_redirect(home_url('/'));
    exit;
}

function cbn_filter_admin_bar_visibility(bool $show): bool
{
    return current_user_can('manage_options') ? $show : false;
}

/**
 * Adds a stable role class for small front-end differences such as the CTA.
 *
 * @param string[] $classes
 * @return string[]
 */
function cbn_add_frontend_role_class(array $classes): array
{
    $classes[] = current_user_can('manage_options') ? 'cbn-role-admin' : 'cbn-role-public';
    return $classes;
}

/**
 * Sends administrators directly to the simplified CBN control panel.
 *
 * @param string          $redirect_to
 * @param string          $requested_redirect_to
 * @param WP_User|WP_Error $user
 */
function cbn_filter_login_redirect(string $redirect_to, string $requested_redirect_to, $user): string
{
    if (!$user instanceof WP_User) {
        return $redirect_to;
    }

    return user_can($user, 'manage_options')
        ? admin_url('admin.php?page=cbn-panel')
        : home_url('/');
}

/**
 * @param string  $redirect_to
 * @param string  $requested_redirect_to
 * @param WP_User $user
 */
function cbn_filter_logout_redirect(string $redirect_to, string $requested_redirect_to, WP_User $user): string
{
    return home_url('/');
}
