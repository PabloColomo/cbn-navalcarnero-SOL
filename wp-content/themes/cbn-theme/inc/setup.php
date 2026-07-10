<?php
/**
 * Theme support and menus.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', 'cbn_theme_setup');

function cbn_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('align-wide');
    add_theme_support('editor-styles');

    register_nav_menus(
        [
            'primary' => __('Menu principal', 'cbn'),
            'footer' => __('Menu pie', 'cbn'),
        ]
    );
}
