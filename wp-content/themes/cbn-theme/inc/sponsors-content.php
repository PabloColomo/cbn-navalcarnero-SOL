<?php
/**
 * Sponsor data helpers for the sponsors archive and the home block.
 *
 * Reads the cbn_sponsor meta registered by the cbn-core mu-plugin ACF
 * group via get_post_meta so templates keep working if ACF is inactive,
 * matching the convention in inc/sports-content.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_sponsor_card(WP_Post $sponsor): array
{
    $sponsor_id = $sponsor->ID;
    $display_name = (string) get_post_meta($sponsor_id, 'cbn_sponsor_display_name', true);
    $tier_label = (string) get_post_meta($sponsor_id, 'cbn_sponsor_tier_label', true);
    $terms = get_the_terms($sponsor_id, 'cbn_sponsor_tier');
    $term = (is_array($terms) && isset($terms[0])) ? $terms[0] : null;

    return [
        'id' => $sponsor_id,
        'display_name' => $display_name !== '' ? $display_name : get_the_title($sponsor_id),
        'url' => (string) get_post_meta($sponsor_id, 'cbn_sponsor_url', true),
        'logo' => get_the_post_thumbnail_url($sponsor_id, 'medium') ?: '',
        'logo_alt' => (string) get_post_meta($sponsor_id, 'cbn_sponsor_logo_alt', true),
        'description' => (string) get_post_meta($sponsor_id, 'cbn_sponsor_description', true),
        'tier_id' => $term instanceof WP_Term ? $term->term_id : 0,
        'tier_slug' => $term instanceof WP_Term ? $term->slug : '',
        'tier_name' => $term instanceof WP_Term ? $term->name : $tier_label,
        'active' => cbn_get_sponsor_bool_meta($sponsor_id, 'cbn_sponsor_active', true),
        'show_on_home' => cbn_get_sponsor_bool_meta($sponsor_id, 'cbn_sponsor_show_on_home', false),
        'order' => (int) get_post_meta($sponsor_id, 'cbn_sponsor_order', true),
        'start_date' => (string) get_post_meta($sponsor_id, 'cbn_sponsor_start_date', true),
        'end_date' => (string) get_post_meta($sponsor_id, 'cbn_sponsor_end_date', true),
    ];
}

/**
 * ACF true_false fields are stored as '1'/'0' once saved. A truly missing
 * meta key (never saved) returns '' from get_post_meta(); fall back to the
 * field's own default_value from the acf-json group in that case.
 */
function cbn_get_sponsor_bool_meta(int $sponsor_id, string $key, bool $default_when_missing): bool
{
    $raw = get_post_meta($sponsor_id, $key, true);

    if ('' === $raw) {
        return $default_when_missing;
    }

    return (bool) $raw;
}

function cbn_sponsor_in_date_window(array $card): bool
{
    // ACF date_picker persists as Ymd regardless of return_format when
    // read through get_post_meta; normalize before comparing (see
    // cbn_normalize_acf_date() in inc/format-utils.php).
    $today = wp_date('Y-m-d');
    $start_date = $card['start_date'] ? cbn_normalize_acf_date($card['start_date']) : '';
    $end_date = $card['end_date'] ? cbn_normalize_acf_date($card['end_date']) : '';

    if ($start_date && $start_date > $today) {
        return false;
    }

    if ($end_date && $end_date < $today) {
        return false;
    }

    return true;
}

/**
 * Active, in-window sponsors ordered like cbn_query_teams(): explicit
 * cbn_sponsor_order first (ascending), unordered sponsors (0) sink to the
 * bottom sorted by name.
 */
function cbn_query_sponsors(): array
{
    if (!post_type_exists('cbn_sponsor')) {
        return [];
    }

    $query = new WP_Query(
        [
            'post_type' => 'cbn_sponsor',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'no_found_rows' => true,
            'orderby' => 'title',
            'order' => 'ASC',
        ]
    );

    $sponsors = [];

    foreach ($query->posts as $sponsor_post) {
        $card = cbn_get_sponsor_card($sponsor_post);

        if ($card['active'] && cbn_sponsor_in_date_window($card)) {
            $sponsors[] = $card;
        }
    }

    usort(
        $sponsors,
        static function (array $a, array $b): int {
            if ($a['order'] === $b['order']) {
                return strcasecmp($a['display_name'], $b['display_name']);
            }

            if (0 === $a['order']) {
                return 1;
            }

            if (0 === $b['order']) {
                return -1;
            }

            return $a['order'] <=> $b['order'];
        }
    );

    return $sponsors;
}

/**
 * Groups active sponsors by their cbn_sponsor_tier term. The taxonomy has
 * no term-order convention (no term meta registered in
 * mu-plugins/cbn-core/inc/taxonomies.php), so tier groups are ordered by
 * a presentation-only priority list of known term slugs (filterable via
 * cbn_sponsor_tier_priority so the club/theme can adjust it later without
 * touching the data model), then any remaining taxonomy-backed tiers
 * alphabetically. Sponsors without a taxonomy term use their per-post
 * cbn_sponsor_tier_label as a fallback group label, and those label-only
 * groups always sink below the taxonomy-backed groups.
 */
function cbn_group_sponsors_by_tier(array $sponsors): array
{
    $groups = [];

    foreach ($sponsors as $sponsor) {
        $has_term = $sponsor['tier_id'] > 0;
        $label = $sponsor['tier_name'] !== '' ? $sponsor['tier_name'] : __('Colaboradores', 'cbn');
        $slug = $has_term ? $sponsor['tier_slug'] : '';
        $key = $has_term ? 'term-' . $sponsor['tier_id'] : 'label-' . sanitize_title($label);

        if (!isset($groups[$key])) {
            $groups[$key] = [
                'label' => $label,
                'slug' => $slug,
                'has_term' => $has_term,
                'sponsors' => [],
            ];
        }

        $groups[$key]['sponsors'][] = $sponsor;
    }

    $cbn_sponsor_tier_priority = apply_filters('cbn_sponsor_tier_priority', ['institucional', 'principal']);
    $cbn_sponsor_tier_priority = array_values(array_map('sanitize_title', (array) $cbn_sponsor_tier_priority));

    uasort(
        $groups,
        static function (array $a, array $b) use ($cbn_sponsor_tier_priority): int {
            if ($a['has_term'] !== $b['has_term']) {
                return $a['has_term'] ? -1 : 1;
            }

            if ($a['has_term']) {
                $a_priority = array_search($a['slug'], $cbn_sponsor_tier_priority, true);
                $b_priority = array_search($b['slug'], $cbn_sponsor_tier_priority, true);

                if (false !== $a_priority || false !== $b_priority) {
                    if (false === $a_priority) {
                        return 1;
                    }

                    if (false === $b_priority) {
                        return -1;
                    }

                    return $a_priority <=> $b_priority;
                }
            }

            return strcasecmp($a['label'], $b['label']);
        }
    );

    return array_values($groups);
}

function cbn_get_sponsor_initials(string $name): string
{
    $name = trim($name);

    if ('' === $name) {
        return '?';
    }

    $words = preg_split('/\s+/', $name);
    $initials = '';

    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    }

    return '' !== $initials ? $initials : mb_strtoupper(mb_substr($name, 0, 2));
}
