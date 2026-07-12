<?php
/**
 * "El Club" page content helpers.
 *
 * Follows the same placeholder-content pattern as inc/home-content.php:
 * ACF fields (when present) override defaults, otherwise the theme falls
 * back to editable placeholder copy. No real minors data or real names.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_club_content(): array
{
    $defaults = cbn_get_club_defaults();

    $content = [
        'intro' => [
            'label' => cbn_get_club_acf_field('cbn_club_intro_label', $defaults['intro']['label']),
            'title' => cbn_get_club_acf_field('cbn_club_intro_title', $defaults['intro']['title']),
            'lead' => cbn_get_club_acf_field('cbn_club_intro_lead', $defaults['intro']['lead']),
            'text' => cbn_get_club_acf_field('cbn_club_intro_text', $defaults['intro']['text']),
            'primary_cta' => [
                'label' => cbn_get_club_acf_field('cbn_club_intro_primary_label', $defaults['intro']['primary_cta']['label']),
                'url' => cbn_get_club_acf_field('cbn_club_intro_primary_url', $defaults['intro']['primary_cta']['url']),
            ],
            'secondary_cta' => [
                'label' => cbn_get_club_acf_field('cbn_club_intro_secondary_label', $defaults['intro']['secondary_cta']['label']),
                'url' => cbn_get_club_acf_field('cbn_club_intro_secondary_url', $defaults['intro']['secondary_cta']['url']),
            ],
            'image' => $defaults['intro']['image'],
        ],
        'values_heading' => cbn_get_club_acf_field('cbn_club_values_heading', $defaults['values_heading']),
        'values' => $defaults['values'],
        'facilities_heading' => cbn_get_club_acf_field('cbn_club_facilities_heading', $defaults['facilities_heading']),
        'facilities' => $defaults['facilities'],
        'school' => [
            'label' => cbn_get_club_acf_field('cbn_club_school_label', $defaults['school']['label']),
            'title' => cbn_get_club_acf_field('cbn_club_school_title', $defaults['school']['title']),
            'text' => cbn_get_club_acf_field('cbn_club_school_text', $defaults['school']['text']),
            'points' => $defaults['school']['points'],
            'cta_label' => cbn_get_club_acf_field('cbn_club_school_cta_label', $defaults['school']['cta_label']),
            'cta_url' => cbn_get_club_acf_field('cbn_club_school_cta_url', $defaults['school']['cta_url']),
            'image' => $defaults['school']['image'],
        ],
        'stats_heading' => cbn_get_club_acf_field('cbn_club_stats_heading', $defaults['stats_heading']),
        'stats' => $defaults['stats'],
        'cta' => [
            'title' => cbn_get_club_acf_field('cbn_club_cta_title', $defaults['cta']['title']),
            'text' => cbn_get_club_acf_field('cbn_club_cta_text', $defaults['cta']['text']),
            'primary_label' => cbn_get_club_acf_field('cbn_club_cta_primary_label', $defaults['cta']['primary_label']),
            'primary_url' => cbn_get_club_acf_field('cbn_club_cta_primary_url', $defaults['cta']['primary_url']),
            'secondary_label' => cbn_get_club_acf_field('cbn_club_cta_secondary_label', $defaults['cta']['secondary_label']),
            'secondary_url' => cbn_get_club_acf_field('cbn_club_cta_secondary_url', $defaults['cta']['secondary_url']),
        ],
    ];

    return apply_filters('cbn_club_content', $content);
}

function cbn_get_club_defaults(): array
{
    return [
        'intro' => [
            'label' => 'El club',
            'title' => 'Baloncesto de Navalcarnero, para Navalcarnero',
            'lead' => 'Promovemos y divulgamos el baloncesto en Navalcarnero, con el foco puesto en el deporte de base.',
            'text' => 'Trabajamos cada temporada para que niños, niñas y jóvenes de la localidad tengan un lugar donde aprender, competir y crecer a través del baloncesto, desde la escuela hasta los equipos sénior.',
            'primary_cta' => [
                'label' => 'Inscribirse',
                'url' => home_url('/inscripcion/'),
            ],
            'secondary_cta' => [
                'label' => 'Contactar',
                'url' => home_url('/contacto/'),
            ],
            'image' => [
                'url' => get_theme_file_uri('assets/src/images/home-team-community.png'),
                'alt' => 'Grupo genérico de jugadoras y jugadores de baloncesto no identificables.',
                'width' => 1672,
                'height' => 941,
            ],
        ],
        'values_heading' => 'Nuestros valores',
        'values' => [
            [
                'title' => 'Formación integral',
                'text' => 'Cuidamos el desarrollo deportivo y personal en cada categoría.',
                'icon' => 'shield',
            ],
            [
                'title' => 'Esfuerzo y compromiso',
                'text' => 'El trabajo diario en los entrenamientos como base del progreso.',
                'icon' => 'school',
            ],
            [
                'title' => 'Respeto y deportividad',
                'text' => 'Dentro y fuera de la pista, con rivales, árbitros y afición.',
                'icon' => 'form',
            ],
            [
                'title' => 'Comunidad local',
                'text' => 'Un club de Navalcarnero y para Navalcarnero, abierto a las familias.',
                'icon' => 'calendar',
            ],
        ],
        'facilities_heading' => 'Instalaciones',
        'facilities' => [
            [
                'title' => 'Pabellón Municipal La Estación',
                'text' => 'Sede principal de entrenamientos y partidos del club.',
                'image' => get_theme_file_uri('assets/src/images/home-hero-training.png'),
            ],
            [
                'title' => 'Pabellones del Colegio María Martín',
                'text' => 'Instalaciones complementarias para escuela y categorías inferiores.',
                'image' => get_theme_file_uri('assets/src/images/home-hero-basketball.jpg'),
            ],
        ],
        'school' => [
            'label' => 'Escuela y cantera',
            'title' => 'La cantera, nuestra prioridad',
            'text' => 'La Escuela CBN es la puerta de entrada al club: desde los primeros botes hasta la competición federada, acompañamos a cada jugador y jugadora en su progreso.',
            'points' => [
                'Grupos por edad, desde los más pequeños hasta cadete y júnior.',
                'Entrenadores cualificados y metodología propia del club.',
                'Convivencia, valores y disfrute del baloncesto como base del aprendizaje.',
            ],
            'cta_label' => 'Ver equipos',
            'cta_url' => home_url('/equipos/'),
            'image' => [
                'url' => get_theme_file_uri('assets/src/images/home-hero-basketball.jpg'),
                'alt' => 'Jugador genérico de baloncesto entrenando, sin identificar.',
                'width' => 1717,
                'height' => 916,
            ],
        ],
        'stats_heading' => 'El club en datos',
        'stats' => [
            ['value' => 'Base', 'label' => 'Deporte de formación'],
            ['value' => '2', 'label' => 'Instalaciones publicadas'],
            ['value' => '25/26', 'label' => 'Categorías publicadas'],
            ['value' => 'Local', 'label' => 'Identidad de Navalcarnero'],
        ],
        'cta' => [
            'title' => '¿Quieres formar parte del club?',
            'text' => 'Escríbenos si tienes dudas o inscríbete directamente en la temporada actual.',
            'primary_label' => 'Inscribirse',
            'primary_url' => home_url('/inscripcion/'),
            'secondary_label' => 'Contactar',
            'secondary_url' => home_url('/contacto/'),
        ],
    ];
}

function cbn_get_club_acf_field(string $field_name, mixed $default): mixed
{
    $source_id = get_queried_object_id();
    $value = function_exists('get_field')
        ? get_field($field_name, $source_id ?: false)
        : ($source_id ? get_post_meta($source_id, $field_name, true) : null);

    if ($value === null || $value === false || $value === '') {
        return $default;
    }

    return $value;
}
