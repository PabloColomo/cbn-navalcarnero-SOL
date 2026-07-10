<?php
/**
 * Home page content helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_home_content(): array
{
    $defaults = cbn_get_home_defaults();

    $content = [
        'hero' => [
            'title_lines' => [
                cbn_get_home_acf_field('cbn_home_hero_line_1', $defaults['hero']['title_lines'][0]),
                cbn_get_home_acf_field('cbn_home_hero_line_2', $defaults['hero']['title_lines'][1]),
                cbn_get_home_acf_field('cbn_home_hero_line_3', $defaults['hero']['title_lines'][2]),
            ],
            'lead' => cbn_get_home_acf_field('cbn_home_hero_lead', $defaults['hero']['lead']),
            'primary_cta' => [
                'label' => cbn_get_home_acf_field('cbn_home_hero_primary_label', $defaults['hero']['primary_cta']['label']),
                'url' => cbn_get_home_acf_field('cbn_home_hero_primary_url', $defaults['hero']['primary_cta']['url']),
            ],
            'secondary_cta' => [
                'label' => cbn_get_home_acf_field('cbn_home_hero_secondary_label', $defaults['hero']['secondary_cta']['label']),
                'url' => cbn_get_home_acf_field('cbn_home_hero_secondary_url', $defaults['hero']['secondary_cta']['url']),
            ],
            'summary' => $defaults['hero']['summary'],
            'image' => cbn_get_home_hero_image($defaults['hero']['image']),
        ],
        'match' => [
            'label' => cbn_get_home_acf_field('cbn_home_match_label', $defaults['match']['label']),
            'home' => cbn_get_home_acf_field('cbn_home_match_home', $defaults['match']['home']),
            'away' => cbn_get_home_acf_field('cbn_home_match_away', $defaults['match']['away']),
            'date' => cbn_get_home_acf_field('cbn_home_match_date', $defaults['match']['date']),
            'venue' => cbn_get_home_acf_field('cbn_home_match_venue', $defaults['match']['venue']),
            'url' => cbn_get_home_acf_field('cbn_home_match_url', $defaults['match']['url']),
        ],
        'quick_links' => $defaults['quick_links'],
        'ticker' => cbn_get_home_ticker_items($defaults['ticker']),
        'teams_heading' => cbn_get_home_acf_field('cbn_home_teams_heading', $defaults['teams_heading']),
        'teams_link_label' => cbn_get_home_acf_field('cbn_home_teams_link_label', $defaults['teams_link_label']),
        'teams_link_url' => cbn_get_home_acf_field('cbn_home_teams_link_url', $defaults['teams_link_url']),
        'teams' => cbn_get_home_team_cards($defaults['teams']),
        'news_heading' => cbn_get_home_acf_field('cbn_home_news_heading', $defaults['news_heading']),
        'news' => cbn_get_home_news_items($defaults['news']),
        'shop' => [
            'title' => cbn_get_home_acf_field('cbn_home_shop_title', $defaults['shop']['title']),
            'text' => cbn_get_home_acf_field('cbn_home_shop_text', $defaults['shop']['text']),
            'label' => cbn_get_home_acf_field('cbn_home_shop_label', $defaults['shop']['label']),
            'url' => cbn_get_home_acf_field('cbn_home_shop_url', $defaults['shop']['url']),
            'image' => $defaults['shop']['image'],
        ],
        'registration' => [
            'title' => cbn_get_home_acf_field('cbn_home_registration_title', $defaults['registration']['title']),
            'text' => cbn_get_home_acf_field('cbn_home_registration_text', $defaults['registration']['text']),
            'label' => cbn_get_home_acf_field('cbn_home_registration_label', $defaults['registration']['label']),
            'url' => cbn_get_home_acf_field('cbn_home_registration_url', $defaults['registration']['url']),
            'image' => $defaults['registration']['image'],
        ],
        'sponsors_heading' => cbn_get_home_acf_field('cbn_home_sponsors_heading', $defaults['sponsors_heading']),
        'sponsors' => cbn_get_home_sponsor_items($defaults['sponsors']),
    ];

    return apply_filters('cbn_home_content', $content);
}

function cbn_get_home_defaults(): array
{
    return [
        'hero' => [
            'title_lines' => ['La cantera', 'que mueve', 'Navalcarnero'],
            'lead' => 'Formamos personas, entrenamos equipos y construimos comunidad. Baloncesto de base, cantera y valores.',
            'primary_cta' => [
                'label' => 'Quiero jugar',
                'url' => home_url('/inscripcion/'),
            ],
            'secondary_cta' => [
                'label' => 'Ver equipos',
                'url' => home_url('/equipos/'),
            ],
            'summary' => [
                [
                    'term' => 'Formación integral',
                    'description' => 'Deportiva y personal',
                ],
                [
                    'term' => 'Entrenadores',
                    'description' => 'Cualificados',
                ],
                [
                    'term' => 'Compromiso local',
                    'description' => 'Somos Navalcarnero',
                ],
            ],
            'image' => [
                'url' => get_theme_file_uri('assets/src/images/home-hero-training.png'),
                'alt' => 'Entrenamiento genérico de baloncesto con jugadores no identificables.',
                'width' => 1718,
                'height' => 916,
            ],
        ],
        'match' => [
            'label' => 'Próximo partido',
            'home' => 'CBN',
            'away' => 'Por confirmar',
            'date' => 'Fecha por confirmar',
            'venue' => 'Pabellón Municipal La Estación',
            'url' => home_url('/partidos/'),
        ],
        'quick_links' => [
            [
                'title' => 'Escuela CBN',
                'text' => 'Descubre nuestra escuela y empieza a jugar',
                'url' => home_url('/equipos/'),
                'icon' => 'school',
            ],
            [
                'title' => 'Partidos',
                'text' => 'Calendario, resultados y clasificaciones',
                'url' => home_url('/partidos/'),
                'icon' => 'calendar',
            ],
            [
                'title' => 'Inscripciones',
                'text' => 'Únete a nuestra familia CBN',
                'url' => home_url('/inscripcion/'),
                'icon' => 'form',
            ],
        ],
        'ticker' => [
            ['type' => 'label', 'text' => 'En pista'],
            ['type' => 'strong', 'text' => 'Calendario'],
            ['type' => 'label', 'text' => 'Consulta los próximos partidos'],
            ['type' => 'strong', 'text' => 'Resultados'],
            ['type' => 'label', 'text' => 'Marcadores verificados por el club'],
            ['type' => 'strong', 'text' => 'Temporada 2026/2027'],
            ['type' => 'label', 'text' => 'Solicitudes de inscripción abiertas'],
        ],
        'teams_heading' => 'Una pista. Todas las edades.',
        'teams_link_label' => 'Ver todos los equipos',
        'teams_link_url' => home_url('/equipos/'),
        'teams' => [
            [
                'label' => 'Escuela CBN',
                'title' => 'Babybasket mixto',
                'url' => home_url('/equipos/'),
                'image' => get_theme_file_uri('assets/src/images/home-team-community.png'),
            ],
            [
                'label' => 'Cantera masculina',
                'title' => 'De benjamín a Sub-22',
                'url' => home_url('/equipos/'),
                'image' => get_theme_file_uri('assets/src/images/home-hero-training.png'),
            ],
            [
                'label' => 'Cantera femenina',
                'title' => 'De alevín a Sub-22',
                'url' => home_url('/equipos/'),
                'image' => get_theme_file_uri('assets/src/images/home-team-community.png'),
            ],
            [
                'label' => 'Senior',
                'title' => 'Competición autonómica',
                'url' => home_url('/equipos/'),
                'image' => get_theme_file_uri('assets/src/images/home-hero-basketball.jpg'),
            ],
        ],
        'news_heading' => 'Desde la banda',
        'news' => [
            [
                'category' => 'Información',
                'title' => 'Horarios y calendario de la jornada',
                'excerpt' => 'Consulta los próximos partidos y las novedades de cada equipo.',
                'date' => 'Actualidad CBN',
                'url' => home_url('/noticias/'),
                'image' => get_theme_file_uri('assets/src/images/home-team-community.png'),
            ],
            [
                'category' => 'Inscripciones',
                'title' => 'Matrícula y altas del club',
                'excerpt' => 'Información para solicitar plaza y entrar en la familia CBN.',
                'date' => 'Temporada 2026/2027',
                'url' => home_url('/noticias/'),
                'image' => get_theme_file_uri('assets/src/images/home-hero-training.png'),
            ],
            [
                'category' => 'Cantera',
                'title' => 'Talento y formación dentro y fuera de la pista',
                'excerpt' => 'Deporte de base, aprendizaje y valores para crecer en equipo.',
                'date' => 'Comunidad CBN',
                'url' => home_url('/noticias/'),
                'image' => get_theme_file_uri('assets/src/images/home-hero-basketball.jpg'),
            ],
            [
                'category' => 'Club',
                'title' => 'Entrenadores que comparten nuestros valores',
                'excerpt' => 'El club sigue construyendo un proyecto de formación cercano.',
                'date' => 'Actualidad CBN',
                'url' => home_url('/noticias/'),
                'image' => get_theme_file_uri('assets/src/images/home-team-community.png'),
            ],
        ],
        'shop' => [
            'title' => 'Viste el equipo',
            'text' => 'El catálogo oficial del club está en preparación. Consulta la tienda para conocer su estado.',
            'label' => 'Explorar tienda',
            'url' => home_url('/tienda/'),
            'image' => [
                'url' => get_theme_file_uri('assets/src/images/home-shop-merch.png'),
                'alt' => 'Imagen conceptual de ropa deportiva roja y negra; no representa el catálogo definitivo.',
                'width' => 1672,
                'height' => 941,
            ],
        ],
        'registration' => [
            'title' => 'Tu sitio está en la pista',
            'text' => 'Temporada 2026/2027 para escuela, cantera femenina, cantera masculina y equipos senior.',
            'label' => 'Solicitar plaza',
            'url' => home_url('/inscripcion/'),
            'image' => [
                'url' => get_theme_file_uri('assets/src/images/home-hero-basketball.jpg'),
                'alt' => 'Jugador genérico de baloncesto con trazo rojo.',
                'width' => 1717,
                'height' => 916,
            ],
        ],
        'sponsors_heading' => 'Comunidad que impulsa',
        'sponsors' => [
            'Ayuntamiento de Navalcarnero',
            'ELEVA Planificación y Entrenamiento',
            "Domino's Pizza Navalcarnero",
            'Multiópticas Navalcarnero',
        ],
    ];
}

function cbn_get_home_acf_field(string $field_name, mixed $default): mixed
{
    if (!function_exists('get_field')) {
        return $default;
    }

    $front_page_id = (int) get_option('page_on_front');
    $source_id = $front_page_id > 0 ? $front_page_id : get_queried_object_id();
    $value = get_field($field_name, $source_id ?: false);

    if ($value === null || $value === false || $value === '') {
        return $default;
    }

    return $value;
}

function cbn_get_home_hero_image(array $default): array
{
    $image = cbn_get_home_acf_field('cbn_home_hero_image', null);

    if (is_array($image) && !empty($image['url'])) {
        return [
            'url' => $image['url'],
            'alt' => $image['alt'] ?? $default['alt'],
            'width' => isset($image['width']) ? (int) $image['width'] : $default['width'],
            'height' => isset($image['height']) ? (int) $image['height'] : $default['height'],
        ];
    }

    return $default;
}

function cbn_get_home_ticker_items(array $fallback): array
{
    if (!post_type_exists('cbn_match')) {
        return $fallback;
    }

    $matches = cbn_query_home_posts('cbn_match', 5);

    if (!$matches) {
        return $fallback;
    }

    $items = [
        ['type' => 'label', 'text' => 'Partidos'],
    ];

    foreach ($matches as $match) {
        $items[] = ['type' => 'strong', 'text' => get_the_title($match)];
        $items[] = ['type' => 'label', 'text' => get_the_date('j M Y', $match)];
    }

    $fallback_pairs = array_slice($fallback, 1);

    while (count($items) < count($fallback) && $fallback_pairs) {
        $items[] = array_shift($fallback_pairs);
    }

    return array_slice($items, 0, count($fallback));
}

function cbn_get_home_team_cards(array $fallback): array
{
    if (!post_type_exists('cbn_team')) {
        return $fallback;
    }

    $teams = cbn_query_home_posts('cbn_team', 4);

    if (!$teams) {
        return $fallback;
    }

    $real_teams = array_map(
        static function (WP_Post $team, int $index) use ($fallback): array {
            $terms = get_the_terms($team, 'cbn_sport_category');
            $label = is_array($terms) && isset($terms[0]) ? $terms[0]->name : 'Equipo';
            $fallback_image = $fallback[$index]['image'] ?? get_theme_file_uri('assets/src/images/home-team-community.png');

            return [
                'label' => $label,
                'title' => get_the_title($team),
                'url' => get_permalink($team),
                'image' => get_the_post_thumbnail_url($team, 'large') ?: $fallback_image,
            ];
        },
        $teams,
        array_keys($teams)
    );

    return cbn_fill_home_items($real_teams, $fallback, 4);
}

function cbn_get_home_news_items(array $fallback): array
{
    $posts = cbn_query_home_posts('post', 4);

    if (!$posts) {
        return $fallback;
    }

    $real_news = array_map(
        static function (WP_Post $post, int $index) use ($fallback): array {
            $categories = get_the_category($post->ID);
            $fallback_item = $fallback[$index] ?? ($fallback[0] ?? []);

            return [
                'category' => isset($categories[0]) ? $categories[0]->name : ($fallback_item['category'] ?? 'Club'),
                'title' => get_the_title($post),
                'excerpt' => get_the_excerpt($post) ?: ($fallback_item['excerpt'] ?? ''),
                'date' => get_the_date('j F Y', $post),
                'url' => get_permalink($post),
                'image' => get_the_post_thumbnail_url($post, 'large') ?: ($fallback_item['image'] ?? get_theme_file_uri('assets/src/images/home-team-community.png')),
            ];
        },
        $posts,
        array_keys($posts)
    );

    return cbn_fill_home_items($real_news, $fallback, 4);
}

function cbn_get_home_sponsor_items(array $fallback): array
{
    $sponsors = array_values(
        array_filter(
            cbn_query_sponsors(),
            static fn (array $sponsor): bool => $sponsor['show_on_home']
        )
    );

    if (!$sponsors) {
        return $fallback;
    }

    $sponsors = array_slice($sponsors, 0, 8);

    return array_map(
        static fn (array $sponsor): string => $sponsor['display_name'],
        $sponsors
    );
}

function cbn_query_home_posts(string $post_type, int $limit): array
{
    $query = new WP_Query(
        [
            'post_type' => $post_type,
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ]
    );

    return $query->posts;
}

function cbn_fill_home_items(array $real_items, array $fallback, int $limit): array
{
    $items = array_values($real_items);

    if (count($items) >= $limit) {
        return array_slice($items, 0, $limit);
    }

    $fallback_slice = array_slice($fallback, count($items));

    return array_slice(array_merge($items, $fallback_slice), 0, $limit);
}

function cbn_get_home_icon(string $icon): string
{
    $icons = [
        'school' => '<svg viewBox="0 0 32 32" role="img" focusable="false" aria-hidden="true"><path d="M16 4 4 10l12 6 12-6-12-6Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 13v7c0 2.4 3.1 4 7 4s7-1.6 7-4v-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M28 10v8" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'calendar' => '<svg viewBox="0 0 32 32" role="img" focusable="false" aria-hidden="true"><rect x="5" y="7" width="22" height="20" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M10 4v6M22 4v6M5 13h22M11 18h3M18 18h3M11 23h3M18 23h3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'form' => '<svg viewBox="0 0 32 32" role="img" focusable="false" aria-hidden="true"><path d="M9 5h11l5 5v17H9V5Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M20 5v6h6M13 16h8M13 21h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="m21 25 5-5 2 2-5 5h-2v-2Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
        'shield' => '<svg viewBox="0 0 32 32" role="img" focusable="false" aria-hidden="true"><path d="M16 4 6 8v7c0 6.4 4.1 10.5 10 13 5.9-2.5 10-6.6 10-13V8L16 4Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="m11 16 3 3 7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];

    return $icons[$icon] ?? $icons['shield'];
}
