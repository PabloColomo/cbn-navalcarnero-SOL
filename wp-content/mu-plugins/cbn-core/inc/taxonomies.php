<?php
/**
 * Taxonomies for sport structure and sponsor grouping.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_register_taxonomies(): void
{
    cbn_register_taxonomy('cbn_season', 'Temporada', 'Temporadas', ['cbn_team', 'cbn_player', 'cbn_match', 'post']);
    cbn_register_taxonomy('cbn_sport_category', 'Categoría deportiva', 'Categorías deportivas', ['cbn_team', 'cbn_player', 'cbn_match']);
    cbn_register_taxonomy('cbn_competition', 'Competición', 'Competiciones', ['cbn_team', 'cbn_match']);
    cbn_register_taxonomy('cbn_venue', 'Instalación', 'Instalaciones', ['cbn_team', 'cbn_match']);
    cbn_register_taxonomy('cbn_sponsor_tier', 'Nivel de sponsor', 'Niveles de sponsor', ['cbn_sponsor']);
}

function cbn_register_taxonomy(string $taxonomy, string $singular, string $plural, array $post_types): void
{
    register_taxonomy(
        $taxonomy,
        $post_types,
        [
            'labels' => [
                'name' => $plural,
                'singular_name' => $singular,
                'search_items' => 'Buscar ' . strtolower($plural),
                'all_items' => 'Todas',
                'edit_item' => 'Editar ' . strtolower($singular),
                'update_item' => 'Actualizar ' . strtolower($singular),
                'add_new_item' => 'Añadir ' . strtolower($singular),
                'new_item_name' => 'Nueva ' . strtolower($singular),
            ],
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => str_replace('cbn_', '', $taxonomy)],
            'capabilities' => [
                'manage_terms' => 'manage_cbn_content',
                'edit_terms' => 'manage_cbn_content',
                'delete_terms' => 'manage_cbn_content',
                'assign_terms' => 'manage_cbn_content',
            ],
        ]
    );
}
