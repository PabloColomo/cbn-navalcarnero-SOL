<?php
/**
 * Theme asset loading.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'cbn_enqueue_assets');
add_filter('script_loader_tag', 'cbn_module_script_tag', 10, 3);

function cbn_enqueue_assets(): void
{
    $manifest = cbn_get_vite_manifest();

    if ($manifest && isset($manifest['wp-content/themes/cbn-theme/assets/src/js/main.js'])) {
        $entry = $manifest['wp-content/themes/cbn-theme/assets/src/js/main.js'];

        if (!empty($entry['css'])) {
            foreach ($entry['css'] as $index => $css_file) {
                wp_enqueue_style(
                    'cbn-theme-' . $index,
                    CBN_THEME_URI . '/assets/dist/' . $css_file,
                    [],
                    CBN_THEME_VERSION
                );
            }
        }

        wp_enqueue_script(
            'cbn-theme',
            CBN_THEME_URI . '/assets/dist/' . $entry['file'],
            [],
            CBN_THEME_VERSION,
            true
        );

        cbn_enqueue_experience_assets(true);

        return;
    }

    wp_enqueue_style(
        'cbn-theme-source',
        CBN_THEME_URI . '/assets/src/css/main.css',
        [],
        CBN_THEME_VERSION
    );

    cbn_enqueue_experience_assets(false);
}

/**
 * Loads the dependency-free Pista Viva layer after the theme bundle.
 *
 * Keeping this layer independent means the cursor, sound controls and the
 * new Home stay usable in a fresh WordPress checkout before npm dependencies
 * have been installed. The regular Vite bundle remains the primary source for
 * the rest of the theme.
 */
function cbn_enqueue_experience_assets(bool $has_main_bundle): void
{
    $style_path = CBN_THEME_DIR . '/assets/src/css/sol.css';
    $script_path = CBN_THEME_DIR . '/assets/src/js/sol.js';
    $style_version = file_exists($style_path) ? (string) filemtime($style_path) : CBN_THEME_VERSION;
    $script_version = file_exists($script_path) ? (string) filemtime($script_path) : CBN_THEME_VERSION;

    wp_enqueue_style(
        'cbn-sol-experience',
        CBN_THEME_URI . '/assets/src/css/sol.css',
        [],
        $style_version
    );

    wp_enqueue_script(
        'cbn-sol-experience',
        CBN_THEME_URI . '/assets/src/js/sol.js',
        [],
        $script_version,
        true
    );

    wp_add_inline_script(
        'cbn-sol-experience',
        'window.cbnSolConfig = ' . wp_json_encode(
            [
                'mainBundle' => $has_main_bundle,
                'logoUrl' => get_theme_file_uri('assets/src/images/cbn-logo.png'),
            ]
        ) . ';',
        'before'
    );

    cbn_enqueue_pista_viva_surface_assets();
}

/**
 * Enqueues isolated Pista Viva assets for each public surface.
 *
 * Page agents own their template and matching sol-{surface}.css/js files;
 * this shared registry keeps loading deterministic and prevents page styles
 * from leaking into unrelated templates.
 */
function cbn_enqueue_pista_viva_surface_assets(): void
{
    $surfaces = [
        'club' => is_page('el-club') || is_page_template('page-el-club.php'),
        'teams' => is_post_type_archive('cbn_team') || is_singular('cbn_team'),
        'matches' => is_post_type_archive('cbn_match') || is_singular('cbn_match'),
        'news' => is_home() || is_category() || is_singular('post'),
        'sponsors' => is_post_type_archive('cbn_sponsor') || is_singular('cbn_sponsor'),
        'contact' => is_page('contacto') || is_page_template('page-contacto.php'),
        'registration' => is_page('inscripcion') || is_page_template('page-inscripcion.php'),
        'shop' => is_page('tienda') || is_page_template('page-tienda.php'),
        'info' => is_page(['documentacion', 'privacidad', 'aviso-legal'])
            || is_post_type_archive('cbn_document')
            || is_singular('cbn_document'),
        'system' => is_404() || is_search(),
    ];

    foreach ($surfaces as $surface => $active) {
        if (!$active) {
            continue;
        }

        $style_relative = '/assets/src/css/sol-' . $surface . '.css';
        $style_path = CBN_THEME_DIR . $style_relative;

        if (file_exists($style_path)) {
            wp_enqueue_style(
                'cbn-sol-' . $surface,
                CBN_THEME_URI . $style_relative,
                ['cbn-sol-experience'],
                (string) filemtime($style_path)
            );
        }

        $script_relative = '/assets/src/js/sol-' . $surface . '.js';
        $script_path = CBN_THEME_DIR . $script_relative;

        if (file_exists($script_path)) {
            wp_enqueue_script(
                'cbn-sol-' . $surface,
                CBN_THEME_URI . $script_relative,
                ['cbn-sol-experience'],
                (string) filemtime($script_path),
                true
            );
        }
    }
}

function cbn_get_vite_manifest(): ?array
{
    $manifest_path = CBN_THEME_DIR . '/assets/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        return null;
    }

    $manifest = json_decode((string) file_get_contents($manifest_path), true);

    return is_array($manifest) ? $manifest : null;
}

function cbn_module_script_tag(string $tag, string $handle, string $src): string
{
    if ($handle !== 'cbn-theme') {
        return $tag;
    }

    return '<script type="module" src="' . esc_url($src) . '" id="cbn-theme-js"></script>';
}
