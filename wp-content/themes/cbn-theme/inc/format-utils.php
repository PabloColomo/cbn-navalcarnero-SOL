<?php
/**
 * Shared formatting helpers used across theme content files.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ACF date_picker fields persist as Ymd (8 digits) regardless of the
 * field's configured return_format when read through get_post_meta().
 * Normalizes that raw value to Y-m-d so it can be parsed with strtotime()
 * or compared lexicographically. Anything that is not an 8-digit Ymd
 * string (including an already Y-m-d value, an empty string, or free
 * text) is returned unchanged, matching the behavior previously inlined
 * in cbn_format_match_date() and cbn_sponsor_in_date_window().
 */
function cbn_normalize_acf_date(string $raw): string
{
    return preg_match('/^\d{8}$/', $raw)
        ? substr($raw, 0, 4) . '-' . substr($raw, 4, 2) . '-' . substr($raw, 6, 2)
        : $raw;
}
