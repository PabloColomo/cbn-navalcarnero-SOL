<?php
/**
 * Theme setup for Club Baloncesto Navalcarnero.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CBN_THEME_VERSION', '0.1.0');
define('CBN_THEME_DIR', get_template_directory());
define('CBN_THEME_URI', get_template_directory_uri());

$cbn_theme_files = [
    'inc/setup.php',
    'inc/assets.php',
    'inc/acf.php',
    'inc/format-utils.php',
    'inc/home-content.php',
    'inc/club-content.php',
    'inc/sports-content.php',
    'inc/sponsors-content.php',
    'inc/contact-content.php',
    'inc/registration-content.php',
];

foreach ($cbn_theme_files as $cbn_theme_file) {
    require_once CBN_THEME_DIR . '/' . $cbn_theme_file;
}
