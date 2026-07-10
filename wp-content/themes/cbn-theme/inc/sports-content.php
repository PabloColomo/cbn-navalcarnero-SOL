<?php
/**
 * Sports data helpers for team and match templates.
 *
 * Reads the cbn_team/cbn_match meta registered by the cbn-core mu-plugin
 * ACF groups via get_post_meta so templates keep working if ACF is inactive.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_team_meta(int $team_id): array
{
    return [
        'federation_name' => (string) get_post_meta($team_id, 'cbn_team_federation_name', true),
        'category_label' => (string) get_post_meta($team_id, 'cbn_team_category_label', true),
        'season_name' => (string) get_post_meta($team_id, 'cbn_team_season_name', true),
        'competition_name' => (string) get_post_meta($team_id, 'cbn_team_competition_name', true),
        'home_venue_name' => (string) get_post_meta($team_id, 'cbn_team_home_venue_name', true),
        'coach' => (string) get_post_meta($team_id, 'cbn_team_coach', true),
        'staff' => (string) get_post_meta($team_id, 'cbn_team_staff', true),
        'training_schedule' => (string) get_post_meta($team_id, 'cbn_team_training_schedule', true),
        'public_roster' => (bool) get_post_meta($team_id, 'cbn_team_public_roster', true),
        'roster_notes' => (string) get_post_meta($team_id, 'cbn_team_roster_notes', true),
        'external_url' => (string) get_post_meta($team_id, 'cbn_team_external_url', true),
    ];
}

/**
 * Public roster names. Empty unless the team explicitly opted in
 * (minors privacy rule: rosters are opt-in, see PRIVACY_NOTES.md).
 */
function cbn_get_team_public_roster(int $team_id): array
{
    if (!get_post_meta($team_id, 'cbn_team_public_roster', true)) {
        return [];
    }

    $player_ids = get_post_meta($team_id, 'cbn_team_players', true);

    if (!is_array($player_ids) || !$player_ids) {
        return [];
    }

    $names = [];

    foreach ($player_ids as $player_id) {
        $player = get_post((int) $player_id);

        if ($player instanceof WP_Post && 'publish' === $player->post_status) {
            $names[] = get_the_title($player);
        }
    }

    return $names;
}

function cbn_query_teams(): array
{
    if (!post_type_exists('cbn_team')) {
        return [];
    }

    $query = new WP_Query(
        [
            'post_type' => 'cbn_team',
            'posts_per_page' => 50,
            'post_status' => 'publish',
            'no_found_rows' => true,
            'orderby' => ['title' => 'ASC'],
        ]
    );

    $teams = $query->posts;

    usort(
        $teams,
        static function (WP_Post $a, WP_Post $b): int {
            $order_a = (int) get_post_meta($a->ID, 'cbn_team_order', true);
            $order_b = (int) get_post_meta($b->ID, 'cbn_team_order', true);

            if ($order_a === $order_b) {
                return strcasecmp(get_the_title($a), get_the_title($b));
            }

            // Teams without explicit order (0) sink below ordered ones.
            if (0 === $order_a) {
                return 1;
            }

            if (0 === $order_b) {
                return -1;
            }

            return $order_a <=> $order_b;
        }
    );

    return $teams;
}

function cbn_get_match_meta(int $match_id): array
{
    $club_team_id = (int) get_post_meta($match_id, 'cbn_match_club_team', true);
    $is_home_club = (bool) get_post_meta($match_id, 'cbn_match_is_home_club', true);
    $home_name = (string) get_post_meta($match_id, 'cbn_match_home_team_name', true);
    $away_name = (string) get_post_meta($match_id, 'cbn_match_away_team_name', true);

    if ($club_team_id && '' === $home_name && $is_home_club) {
        $home_name = get_the_title($club_team_id);
    }

    if ($club_team_id && '' === $away_name && !$is_home_club) {
        $away_name = get_the_title($club_team_id);
    }

    $verified = (bool) get_post_meta($match_id, 'cbn_match_result_verified', true);
    $home_score = get_post_meta($match_id, 'cbn_match_home_score', true);
    $away_score = get_post_meta($match_id, 'cbn_match_away_score', true);

    return [
        'status' => (string) get_post_meta($match_id, 'cbn_match_status', true) ?: 'scheduled',
        'date' => (string) get_post_meta($match_id, 'cbn_match_date', true),
        'time' => (string) get_post_meta($match_id, 'cbn_match_time', true),
        'round' => (string) get_post_meta($match_id, 'cbn_match_round', true),
        'club_team_id' => $club_team_id,
        'is_home_club' => $is_home_club,
        'home_name' => $home_name ?: 'CBN',
        'away_name' => $away_name ?: 'Rival',
        'competition_name' => (string) get_post_meta($match_id, 'cbn_match_competition_name', true),
        'venue_name' => (string) get_post_meta($match_id, 'cbn_match_venue_name', true),
        // Scores are public only once the club marks the result verified.
        'home_score' => $verified && '' !== $home_score ? (int) $home_score : null,
        'away_score' => $verified && '' !== $away_score ? (int) $away_score : null,
        'display_title' => (string) get_post_meta($match_id, 'cbn_match_display_title', true),
        'featured_label' => (string) get_post_meta($match_id, 'cbn_match_featured_label', true),
        'public_notes' => (string) get_post_meta($match_id, 'cbn_match_public_notes', true),
    ];
}

function cbn_query_matches(string $mode, int $limit, int $team_id = 0): array
{
    if (!post_type_exists('cbn_match')) {
        return [];
    }

    $today = wp_date('Y-m-d');

    $args = [
        'post_type' => 'cbn_match',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'meta_key' => 'cbn_match_date',
        'orderby' => 'meta_value',
        // ACF stores date_picker as Ymd while imports may use Y-m-d;
        // casting to DATE keeps ordering correct across both.
        'meta_type' => 'DATE',
    ];

    if ('upcoming' === $mode) {
        $args['order'] = 'ASC';
        $args['meta_query'] = [
            [
                'key' => 'cbn_match_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATE',
            ],
            [
                'key' => 'cbn_match_status',
                'value' => ['scheduled', 'live'],
                'compare' => 'IN',
            ],
        ];
    } else {
        $args['order'] = 'DESC';
        $args['meta_query'] = [
            [
                'key' => 'cbn_match_status',
                'value' => 'final',
            ],
        ];
    }

    if ($team_id) {
        $args['meta_query'][] = [
            'key' => 'cbn_match_club_team',
            'value' => $team_id,
        ];
    }

    $query = new WP_Query($args);

    return $query->posts;
}

function cbn_format_match_date(string $date, string $time): string
{
    if ('' === $date) {
        return 'Fecha por confirmar';
    }

    $date = cbn_normalize_acf_date($date);

    $timestamp = strtotime($date);

    if (false === $timestamp) {
        return 'Fecha por confirmar';
    }

    $label = wp_date('j M Y', $timestamp);

    return '' !== $time ? $label . ' - ' . $time : $label;
}

function cbn_get_match_status_label(string $status): string
{
    $labels = [
        'scheduled' => 'Programado',
        'live' => 'En directo',
        'final' => 'Finalizado',
        'postponed' => 'Aplazado',
        'cancelled' => 'Cancelado',
    ];

    return $labels[$status] ?? $labels['scheduled'];
}
