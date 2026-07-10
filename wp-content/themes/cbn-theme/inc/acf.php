<?php
/**
 * ACF integration.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('acf/settings/save_json', 'cbn_acf_json_save_path');
add_filter('acf/settings/load_json', 'cbn_acf_json_load_paths');

function cbn_acf_json_save_path(string $path): string
{
    unset($path);

    return CBN_THEME_DIR . '/acf-json';
}

function cbn_acf_json_load_paths(array $paths): array
{
    $paths[] = CBN_THEME_DIR . '/acf-json';

    return array_values(array_unique($paths));
}
