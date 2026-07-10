<?php
/**
 * Core content model for Club Baloncesto Navalcarnero.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_core_files = [
    'inc/post-types.php',
    'inc/taxonomies.php',
];

foreach ($cbn_core_files as $cbn_core_file) {
    require_once __DIR__ . '/' . $cbn_core_file;
}

add_action('init', 'cbn_register_content_model');

function cbn_register_content_model(): void
{
    cbn_register_post_types();
    cbn_register_taxonomies();
}
