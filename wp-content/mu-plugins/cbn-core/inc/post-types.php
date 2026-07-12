<?php
/**
 * Custom post types for club-owned content.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_register_post_types(): void
{
    cbn_register_post_type('cbn_team', 'Equipo', 'Equipos', 'equipos', 'dashicons-groups');
    // Minors' data must never be publicly listed until an authorization
    // workflow exists: cbn_player is fully non-public (no front-end archive
    // or single view, excluded from search) and show_in_rest stays false
    // because the REST posts controller serves published posts of any
    // show_in_rest type to anonymous readers regardless of public flags.
    // Players are managed with the classic editor + ACF fields in wp-admin.
    cbn_register_post_type('cbn_player', 'Jugador', 'Jugadores', 'jugadores', 'dashicons-id', [
        'public' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'show_ui' => true,
        'show_in_rest' => false,
    ]);
    cbn_register_post_type('cbn_match', 'Partido', 'Partidos', 'partidos', 'dashicons-calendar-alt');
    cbn_register_post_type('cbn_sponsor', 'Sponsor', 'Sponsors', 'sponsors', 'dashicons-star-filled');
    cbn_register_post_type('cbn_document', 'Documento', 'Documentos', 'documentos', 'dashicons-media-document');
}

/**
 * Singular and plural capability bases used by the CBN content types.
 *
 * Keeping these capabilities separate from the default post capabilities
 * means only explicitly authorised club administrators can mutate sporting
 * data, even through REST or another authenticated WordPress surface.
 *
 * @return array<string, array{0:string, 1:string}>
 */
function cbn_get_post_type_capability_bases(): array
{
    return [
        'cbn_team' => ['cbn_team', 'cbn_teams'],
        'cbn_player' => ['cbn_player', 'cbn_players'],
        'cbn_match' => ['cbn_match', 'cbn_matches'],
        'cbn_sponsor' => ['cbn_sponsor', 'cbn_sponsors'],
        'cbn_document' => ['cbn_document', 'cbn_documents'],
    ];
}

function cbn_register_post_type(string $post_type, string $singular, string $plural, string $slug, string $icon, array $overrides = []): void
{
    $capability_bases = cbn_get_post_type_capability_bases();
    $capability_type = $capability_bases[$post_type] ?? [$post_type, $post_type . 's'];

    $args = [
        'labels' => [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new_item' => 'Añadir ' . strtolower($singular),
            'edit_item' => 'Editar ' . strtolower($singular),
            'new_item' => 'Nuevo ' . strtolower($singular),
            'view_item' => 'Ver ' . strtolower($singular),
            'search_items' => 'Buscar ' . strtolower($plural),
            'not_found' => 'No se encontraron ' . strtolower($plural),
        ],
        'public' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'menu_icon' => $icon,
        'rewrite' => ['slug' => $slug],
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'capability_type' => $capability_type,
        'map_meta_cap' => true,
    ];

    register_post_type($post_type, array_merge($args, $overrides));
}
