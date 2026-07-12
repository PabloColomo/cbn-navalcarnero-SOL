<?php
/**
 * Pista Viva match detail.
 *
 * Scores come exclusively from cbn_get_match_meta(), which only exposes
 * verified values. Missing official data is labelled as pending and never
 * replaced with fictional match information.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_matches_style_path = get_theme_file_path('assets/src/css/sol-matches.css');

add_action(
    'wp_enqueue_scripts',
    static function () use ($cbn_matches_style_path): void {
        wp_enqueue_style(
            'cbn-sol-matches',
            get_theme_file_uri('assets/src/css/sol-matches.css'),
            ['cbn-sol-experience'],
            file_exists($cbn_matches_style_path)
                ? (string) filemtime($cbn_matches_style_path)
                : CBN_THEME_VERSION
        );
    },
    20
);

$cbn_matches_archive_url = get_post_type_archive_link('cbn_match') ?: home_url('/partidos/');
$cbn_match_post = get_queried_object();

if (!$cbn_match_post instanceof WP_Post || 'cbn_match' !== $cbn_match_post->post_type) {
    wp_safe_redirect($cbn_matches_archive_url, 302);
    exit;
}

$cbn_match = cbn_get_match_meta($cbn_match_post->ID);
$cbn_has_score = null !== $cbn_match['home_score'] && null !== $cbn_match['away_score'];
$cbn_is_final_pending = 'final' === $cbn_match['status'] && !$cbn_has_score;
$cbn_status_label = cbn_get_match_status_label($cbn_match['status']);
$cbn_date_label = cbn_format_match_date($cbn_match['date'], $cbn_match['time']);
$cbn_date_is_pending = 'Fecha por confirmar' === $cbn_date_label;
$cbn_display_title = '' !== trim($cbn_match['display_title'])
    ? $cbn_match['display_title']
    : get_the_title($cbn_match_post);
$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_home_is_club = $cbn_match['club_team_id'] > 0 && $cbn_match['is_home_club'];
$cbn_away_is_club = $cbn_match['club_team_id'] > 0 && !$cbn_match['is_home_club'];
$cbn_team_url = $cbn_match['club_team_id'] > 0 && 'publish' === get_post_status($cbn_match['club_team_id'])
    ? get_permalink($cbn_match['club_team_id'])
    : '';
$cbn_has_content = '' !== trim((string) $cbn_match_post->post_content);

$cbn_public_team_label = static function (string $name, bool $is_club_side, int $club_team_id): string {
    $normalized = strtolower(remove_accents(trim($name)));

    if (in_array($normalized, ['', 'rival', 'por confirmar'], true)) {
        return 'Por confirmar';
    }

    if ('cbn' === $normalized && (!$is_club_side || 0 === $club_team_id)) {
        return 'Por confirmar';
    }

    return $name;
};

$cbn_home_label = $cbn_public_team_label(
    $cbn_match['home_name'],
    $cbn_home_is_club,
    $cbn_match['club_team_id']
);
$cbn_away_label = $cbn_public_team_label(
    $cbn_match['away_name'],
    $cbn_away_is_club,
    $cbn_match['club_team_id']
);

$cbn_match_facts = [
    ['label' => 'Estado', 'value' => $cbn_status_label, 'pending' => false],
    [
        'label' => 'Fecha y hora',
        'value' => $cbn_date_label,
        'pending' => $cbn_date_is_pending,
    ],
];

if ('' !== $cbn_match['competition_name']) {
    $cbn_match_facts[] = [
        'label' => 'Competición',
        'value' => $cbn_match['competition_name'],
        'pending' => false,
    ];
}

if ('' !== $cbn_match['round']) {
    $cbn_match_facts[] = [
        'label' => 'Jornada',
        'value' => $cbn_match['round'],
        'pending' => false,
    ];
}

if ('' !== $cbn_match['venue_name']) {
    $cbn_match_facts[] = [
        'label' => 'Pista',
        'value' => $cbn_match['venue_name'],
        'pending' => false,
    ];
}

$cbn_match_summary = [
    $cbn_status_label,
    'Equipo local: ' . $cbn_home_label,
    'Equipo visitante: ' . $cbn_away_label,
    $cbn_date_label,
];

if ($cbn_has_score) {
    $cbn_match_summary[] = sprintf(
        'Resultado verificado: %s, %d; %s, %d',
        $cbn_home_label,
        $cbn_match['home_score'],
        $cbn_away_label,
        $cbn_match['away_score']
    );
} elseif ($cbn_is_final_pending) {
    $cbn_match_summary[] = 'Marcador pendiente de verificación';
}

if ('' !== $cbn_match['venue_name']) {
    $cbn_match_summary[] = 'Pista: ' . $cbn_match['venue_name'];
}

$cbn_navigation_query = new WP_Query(
    [
        'post_type' => 'cbn_match',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'no_found_rows' => true,
        'orderby' => ['date' => 'ASC', 'title' => 'ASC'],
    ]
);
$cbn_navigation_matches = [];

foreach ($cbn_navigation_query->posts as $cbn_navigation_post) {
    $cbn_navigation_meta = cbn_get_match_meta($cbn_navigation_post->ID);
    $cbn_navigation_date = cbn_normalize_acf_date($cbn_navigation_meta['date']);
    $cbn_navigation_timestamp = '' !== $cbn_navigation_date
        ? strtotime($cbn_navigation_date)
        : false;

    $cbn_navigation_matches[] = [
        'post' => $cbn_navigation_post,
        'meta' => $cbn_navigation_meta,
        'timestamp' => false !== $cbn_navigation_timestamp ? $cbn_navigation_timestamp : PHP_INT_MAX,
    ];
}

usort(
    $cbn_navigation_matches,
    static function (array $a, array $b): int {
        if ($a['timestamp'] === $b['timestamp']) {
            return strcasecmp(get_the_title($a['post']), get_the_title($b['post']));
        }

        return $a['timestamp'] <=> $b['timestamp'];
    }
);

$cbn_match_position = null;

foreach ($cbn_navigation_matches as $cbn_position => $cbn_navigation_match) {
    if ($cbn_match_post->ID === $cbn_navigation_match['post']->ID) {
        $cbn_match_position = $cbn_position;
        break;
    }
}

$cbn_previous_match = null !== $cbn_match_position && $cbn_match_position > 0
    ? $cbn_navigation_matches[$cbn_match_position - 1]
    : null;
$cbn_next_match = null !== $cbn_match_position && isset($cbn_navigation_matches[$cbn_match_position + 1])
    ? $cbn_navigation_matches[$cbn_match_position + 1]
    : null;
$cbn_navigation_title = static function (array $navigation_match): string {
    return '' !== trim($navigation_match['meta']['display_title'])
        ? $navigation_match['meta']['display_title']
        : get_the_title($navigation_match['post']);
};

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-matches cbn-sol-match-detail" data-cbn-sol data-cbn-match-detail>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-matches__route cbn-sol-match-detail__route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 650" preserveAspectRatio="none" focusable="false">
      <path class="cbn-sol-route__ghost" d="M78 0 C18 62 18 127 69 169 S87 278 31 324 S16 436 70 479 S82 575 42 612 S35 640 50 650"></path>
      <path class="cbn-sol-route__active" data-cbn-route d="M78 0 C18 62 18 127 69 169 S87 278 31 324 S16 436 70 479 S82 575 42 612 S35 640 50 650"></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <?php while (have_posts()) : the_post(); ?>
    <section class="cbn-sol-match-detail-hero" aria-labelledby="cbn-match-detail-title">
      <div class="cbn-sol-match-detail-hero__court" aria-hidden="true">
        <svg viewBox="0 0 900 680" preserveAspectRatio="xMidYMid slice" focusable="false">
          <path d="M450 0V680M0 340H900"></path>
          <circle cx="450" cy="340" r="110"></circle>
          <path d="M0 130h178v420H0M900 130H722v420h178"></path>
          <path d="M178 236a110 110 0 0 1 0 208M722 236a110 110 0 0 0 0 208"></path>
        </svg>
      </div>

      <div class="cbn-sol-match-detail-hero__copy" data-sol-reveal>
        <a class="cbn-sol-match-detail-back" href="<?php echo esc_url($cbn_matches_archive_url); ?>" data-cbn-swish>
          <span aria-hidden="true">←</span> Volver a partidos
        </a>
        <p class="cbn-sol-kicker">
          <span><?php echo esc_html($cbn_status_label); ?></span>
          <?php if ('' !== $cbn_match['featured_label']) : ?>
            <span><?php echo esc_html($cbn_match['featured_label']); ?></span>
          <?php endif; ?>
        </p>
        <h1 id="cbn-match-detail-title"><?php echo esc_html($cbn_display_title); ?></h1>
        <p class="cbn-sol-match-detail-hero__date"><?php echo esc_html($cbn_date_label); ?></p>
        <div class="cbn-sol-match-detail-hero__actions">
          <a class="cbn-sol-button cbn-sol-button--shot" href="#ficha-partido" data-cbn-swish>
            <span>Ver ficha del partido</span>
            <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
          </a>
          <?php if ($cbn_team_url) : ?>
            <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_team_url); ?>" data-cbn-swish>
              Ver equipo CBN <span aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <article
        class="cbn-sol-match-scoreboard cbn-sol-match-scoreboard--<?php echo esc_attr($cbn_match['status']); ?><?php echo $cbn_is_final_pending ? ' is-score-pending' : ''; ?>"
        aria-label="Marcador del partido"
        aria-describedby="cbn-match-accessible-summary"
        data-cbn-parallax
      >
        <p id="cbn-match-accessible-summary" class="cbn-visually-hidden">
          <?php echo esc_html(implode('. ', $cbn_match_summary)); ?>.
        </p>
        <header class="cbn-sol-match-scoreboard__topline">
          <span class="cbn-sol-match-scoreboard__status">
            <i aria-hidden="true"></i><?php echo esc_html($cbn_status_label); ?>
          </span>
          <span><?php echo esc_html($cbn_date_label); ?></span>
        </header>

        <div class="cbn-sol-match-scoreboard__teams" aria-hidden="true">
          <div class="cbn-sol-match-scoreboard__team<?php echo $cbn_home_is_club ? ' is-club' : ''; ?>">
            <span class="cbn-sol-match-scoreboard__mark">
              <?php if ($cbn_home_is_club) : ?>
                <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
              <?php else : ?>
                <b>LOC</b>
              <?php endif; ?>
            </span>
            <small>Local</small>
            <strong><?php echo esc_html($cbn_home_label); ?></strong>
          </div>

          <div class="cbn-sol-match-scoreboard__center">
            <?php if ($cbn_has_score) : ?>
              <span class="cbn-sol-match-scoreboard__score">
                <b><?php echo esc_html((string) $cbn_match['home_score']); ?></b>
                <i>:</i>
                <b><?php echo esc_html((string) $cbn_match['away_score']); ?></b>
              </span>
              <small>Verificado</small>
            <?php elseif ($cbn_is_final_pending) : ?>
              <strong class="cbn-sol-match-scoreboard__pending">Por<br>verificar</strong>
              <small>Marcador</small>
            <?php elseif ('postponed' === $cbn_match['status']) : ?>
              <strong class="cbn-sol-match-scoreboard__state">Aplazado</strong>
            <?php elseif ('cancelled' === $cbn_match['status']) : ?>
              <strong class="cbn-sol-match-scoreboard__state">Cancelado</strong>
            <?php else : ?>
              <strong class="cbn-sol-match-scoreboard__versus">VS</strong>
            <?php endif; ?>
          </div>

          <div class="cbn-sol-match-scoreboard__team<?php echo $cbn_away_is_club ? ' is-club' : ''; ?>">
            <span class="cbn-sol-match-scoreboard__mark">
              <?php if ($cbn_away_is_club) : ?>
                <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
              <?php else : ?>
                <b>VIS</b>
              <?php endif; ?>
            </span>
            <small>Visitante</small>
            <strong><?php echo esc_html($cbn_away_label); ?></strong>
          </div>
        </div>

        <footer class="cbn-sol-match-scoreboard__footer">
          <?php if ($cbn_has_score) : ?>
            <span class="is-verified"><i aria-hidden="true">✓</i> Resultado verificado</span>
          <?php elseif ($cbn_is_final_pending) : ?>
            <span><i aria-hidden="true">!</i> Marcador pendiente de verificación</span>
          <?php else : ?>
            <span><i aria-hidden="true">•</i> <?php echo esc_html($cbn_status_label); ?></span>
          <?php endif; ?>
          <?php if ('' !== $cbn_match['round']) : ?>
            <strong><?php echo esc_html($cbn_match['round']); ?></strong>
          <?php endif; ?>
        </footer>
      </article>
    </section>

    <section class="cbn-sol-match-detail-facts" aria-label="Datos publicados del partido" data-sol-reveal>
      <?php foreach ($cbn_match_facts as $cbn_fact) : ?>
        <div<?php echo $cbn_fact['pending'] ? ' class="is-pending"' : ''; ?>>
          <small><?php echo esc_html($cbn_fact['label']); ?></small>
          <strong><?php echo esc_html($cbn_fact['value']); ?></strong>
        </div>
      <?php endforeach; ?>
    </section>

    <?php if (has_post_thumbnail()) : ?>
      <figure class="cbn-sol-match-detail-media" data-sol-reveal>
        <?php
        the_post_thumbnail(
            'large',
            [
                'class' => 'cbn-sol-match-detail-media__image',
                'loading' => 'eager',
                'decoding' => 'async',
            ]
        );
        ?>
        <figcaption>
          <span>Partido CBN</span>
          <strong><?php echo esc_html($cbn_display_title); ?></strong>
        </figcaption>
      </figure>
    <?php endif; ?>

    <section id="ficha-partido" class="cbn-sol-match-detail-story" aria-labelledby="cbn-match-story-title">
      <header class="cbn-sol-section-heading" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">01 / Ficha pública</p>
          <h2 id="cbn-match-story-title">Lo que sabemos<br>del partido.</h2>
        </div>
        <p>Solo se muestra información publicada por el club; los campos pendientes permanecen claramente identificados.</p>
      </header>

      <div class="cbn-sol-match-detail-story__grid">
        <div class="cbn-sol-match-detail-story__content" data-sol-reveal>
          <?php if ($cbn_has_content) : ?>
            <div class="cbn-sol-match-detail-prose"><?php the_content(); ?></div>
          <?php else : ?>
            <div class="cbn-sol-match-detail-empty" role="status">
              <span aria-hidden="true">24</span>
              <div>
                <h3>Sin crónica publicada.</h3>
                <p>La ficha conserva los datos deportivos disponibles aunque todavía no exista contenido editorial.</p>
              </div>
            </div>
          <?php endif; ?>

          <?php if ('' !== $cbn_match['public_notes']) : ?>
            <aside class="cbn-sol-match-detail-notice" aria-labelledby="cbn-match-notice-title">
              <small>Aviso del club</small>
              <h3 id="cbn-match-notice-title">Información pública</h3>
              <p><?php echo esc_html($cbn_match['public_notes']); ?></p>
            </aside>
          <?php endif; ?>
        </div>

        <aside class="cbn-sol-match-detail-summary" aria-labelledby="cbn-match-summary-title" data-sol-reveal>
          <p class="cbn-sol-section-index">Datos de jornada</p>
          <h3 id="cbn-match-summary-title"><?php echo esc_html($cbn_home_label); ?><br><em>vs</em><br><?php echo esc_html($cbn_away_label); ?></h3>
          <dl>
            <div>
              <dt>Fecha</dt>
              <dd><?php echo esc_html($cbn_date_label); ?></dd>
            </div>
            <?php if ('' !== $cbn_match['venue_name']) : ?>
              <div>
                <dt>Pista</dt>
                <dd><?php echo esc_html($cbn_match['venue_name']); ?></dd>
              </div>
            <?php else : ?>
              <div class="is-pending">
                <dt>Pista</dt>
                <dd>Pendiente de publicación</dd>
              </div>
            <?php endif; ?>
            <?php if ('' !== $cbn_match['competition_name']) : ?>
              <div>
                <dt>Competición</dt>
                <dd><?php echo esc_html($cbn_match['competition_name']); ?></dd>
              </div>
            <?php endif; ?>
          </dl>
          <?php if ($cbn_team_url) : ?>
            <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_team_url); ?>" data-cbn-swish>
              Abrir ficha del equipo <span aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>
        </aside>
      </div>
    </section>

    <section class="cbn-sol-match-detail-cta" aria-labelledby="cbn-match-detail-cta-title" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">02 / Toda la jornada</p>
        <h2 id="cbn-match-detail-cta-title">La pista<br>continúa.</h2>
      </div>
      <div>
        <p>Consulta el calendario y los resultados publicados del resto de equipos del club.</p>
        <a class="cbn-sol-button cbn-sol-button--light cbn-sol-button--shot" href="<?php echo esc_url($cbn_matches_archive_url); ?>" data-cbn-swish>
          <span>Ver todos los partidos</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
      </div>
    </section>

    <nav class="cbn-sol-match-switcher" aria-label="Navegar entre partidos">
      <?php if ($cbn_previous_match) : ?>
        <a href="<?php echo esc_url(get_permalink($cbn_previous_match['post'])); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Partido anterior</small>
          <strong><?php echo esc_html($cbn_navigation_title($cbn_previous_match)); ?></strong>
          <span><?php echo esc_html(cbn_format_match_date($cbn_previous_match['meta']['date'], $cbn_previous_match['meta']['time'])); ?></span>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url($cbn_matches_archive_url); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Volver</small>
          <strong>Todos los partidos</strong>
        </a>
      <?php endif; ?>
      <?php if ($cbn_next_match) : ?>
        <a href="<?php echo esc_url(get_permalink($cbn_next_match['post'])); ?>" data-cbn-swish>
          <small>Siguiente partido <span aria-hidden="true">→</span></small>
          <strong><?php echo esc_html($cbn_navigation_title($cbn_next_match)); ?></strong>
          <span><?php echo esc_html(cbn_format_match_date($cbn_next_match['meta']['date'], $cbn_next_match['meta']['time'])); ?></span>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url($cbn_matches_archive_url); ?>" data-cbn-swish>
          <small>Siguiente jugada <span aria-hidden="true">→</span></small>
          <strong>Volver al calendario</strong>
        </a>
      <?php endif; ?>
    </nav>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
