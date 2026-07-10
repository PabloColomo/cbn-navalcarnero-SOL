<?php
/**
 * Pista Viva teams archive.
 *
 * Keeps the club-controlled editorial ordering while presenting every
 * published team as an accessible, progressively filterable tactical card.
 */

$cbn_teams_style_path = get_theme_file_path('assets/src/css/sol-teams.css');
$cbn_teams_script_path = get_theme_file_path('assets/src/js/sol-teams.js');

wp_enqueue_style(
    'cbn-sol-teams',
    get_theme_file_uri('assets/src/css/sol-teams.css'),
    ['cbn-sol-experience'],
    file_exists($cbn_teams_style_path) ? (string) filemtime($cbn_teams_style_path) : CBN_THEME_VERSION
);
wp_enqueue_script(
    'cbn-sol-teams',
    get_theme_file_uri('assets/src/js/sol-teams.js'),
    ['cbn-sol-experience'],
    file_exists($cbn_teams_script_path) ? (string) filemtime($cbn_teams_script_path) : CBN_THEME_VERSION,
    true
);

$cbn_teams = cbn_query_teams();
$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_group_labels = [
    'escuela' => 'Escuela',
    'femenino' => 'Femenino',
    'masculino' => 'Masculino',
    'senior' => 'Senior',
    'cantera' => 'Cantera',
];
$cbn_group_counts = array_fill_keys(array_keys($cbn_group_labels), 0);
$cbn_team_cards = [];
$cbn_venue_names = [];
$cbn_category_names = [];

foreach ($cbn_teams as $cbn_team) {
    $cbn_meta = cbn_get_team_meta($cbn_team->ID);
    $cbn_terms = get_the_terms($cbn_team->ID, 'cbn_sport_category');
    $cbn_term_names = !is_wp_error($cbn_terms) && $cbn_terms
        ? wp_list_pluck($cbn_terms, 'name')
        : [];
    $cbn_category = trim($cbn_meta['category_label']);

    if ('' === $cbn_category && $cbn_term_names) {
        $cbn_category = implode(' · ', $cbn_term_names);
    }

    if ('' === $cbn_category) {
        $cbn_category = 'Equipo CBN';
    }

    $cbn_search = strtolower(
        remove_accents(
            implode(
                ' ',
                array_merge(
                    [get_the_title($cbn_team), $cbn_category, $cbn_meta['competition_name']],
                    $cbn_term_names
                )
            )
        )
    );
    $cbn_groups = [];

    if (str_contains($cbn_search, 'escuela') || str_contains($cbn_search, 'baby')) {
        $cbn_groups[] = 'escuela';
    }

    if (str_contains($cbn_search, 'femen')) {
        $cbn_groups[] = 'femenino';
    }

    if (str_contains($cbn_search, 'mascul')) {
        $cbn_groups[] = 'masculino';
    }

    if (
        str_contains($cbn_search, 'senior')
        || str_contains($cbn_search, 'sub-22')
        || str_contains($cbn_search, 'sub 22')
        || str_contains($cbn_search, 'sub22')
        || str_contains($cbn_search, 'autonom')
    ) {
        $cbn_groups[] = 'senior';
    }

    if (!in_array('senior', $cbn_groups, true) && !in_array('escuela', $cbn_groups, true)) {
        $cbn_groups[] = 'cantera';
    }

    $cbn_groups = array_values(array_unique($cbn_groups));

    foreach ($cbn_groups as $cbn_group) {
        ++$cbn_group_counts[$cbn_group];
    }

    if ('' !== $cbn_meta['home_venue_name']) {
        $cbn_venue_names[] = $cbn_meta['home_venue_name'];
    }

    $cbn_category_names[] = $cbn_category;
    $cbn_team_cards[] = [
        'post' => $cbn_team,
        'meta' => $cbn_meta,
        'category' => $cbn_category,
        'groups' => $cbn_groups,
    ];
}

$cbn_venue_count = count(array_unique($cbn_venue_names));
$cbn_category_count = count(array_unique($cbn_category_names));

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-teams-page cbn-sol-team-archive" data-cbn-sol data-cbn-teams-page>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-teams-route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 500" preserveAspectRatio="none" focusable="false">
      <path class="cbn-sol-route__ghost" d="M15 0 C85 55 84 112 42 151 S12 245 68 279 S92 388 35 430 S20 476 50 500"></path>
      <path class="cbn-sol-route__active" data-cbn-route d="M15 0 C85 55 84 112 42 151 S12 245 68 279 S92 388 35 430 S20 476 50 500"></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-teams-hero" aria-labelledby="cbn-teams-title">
    <div class="cbn-sol-teams-hero__court" aria-hidden="true">
      <svg viewBox="0 0 900 620" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M450 0V620M0 310H900"></path>
        <circle cx="450" cy="310" r="108"></circle>
        <path d="M0 114h180v392H0M900 114H720v392h180"></path>
        <path d="M180 208a108 108 0 0 1 0 204M720 208a108 108 0 0 0 0 204"></path>
        <path d="M0 38a292 292 0 0 1 0 544M900 38a292 292 0 0 0 0 544"></path>
      </svg>
    </div>

    <div class="cbn-sol-teams-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker"><span>La cantera del CBN</span><span>Navalcarnero</span></p>
      <h1 id="cbn-teams-title">
        <span>Una pista.</span>
        <span>Todos los</span>
        <span>equipos.</span>
      </h1>
      <p class="cbn-sol-teams-hero__lead">
        Desde los primeros botes hasta la competición senior: encuentra tu categoría y entra en el juego.
      </p>
      <a class="cbn-sol-button cbn-sol-button--shot" href="#mapa-equipos" data-cbn-swish>
        <span>Ver el mapa de equipos</span>
        <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
      </a>
    </div>

    <div class="cbn-sol-teams-hero__board" data-sol-reveal>
      <div class="cbn-sol-teams-hero__board-top">
        <span>Roster / CBN</span>
        <span>Temporada en juego</span>
      </div>
      <div class="cbn-sol-teams-hero__crest">
        <span class="cbn-sol-teams-hero__orbit" aria-hidden="true"></span>
        <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
      </div>
      <dl class="cbn-sol-teams-hero__stats">
        <div>
          <dt>Equipos publicados</dt>
          <dd><?php echo esc_html((string) count($cbn_team_cards)); ?></dd>
        </div>
        <div>
          <dt>Categorías</dt>
          <dd><?php echo esc_html((string) $cbn_category_count); ?></dd>
        </div>
        <div>
          <dt>Pistas publicadas</dt>
          <dd><?php echo esc_html((string) $cbn_venue_count); ?></dd>
        </div>
      </dl>
      <span class="cbn-sol-teams-hero__board-label">La misma camiseta. Distintas historias.</span>
    </div>
  </section>

  <section id="mapa-equipos" class="cbn-sol-teams-list" aria-labelledby="cbn-teams-list-title">
    <header class="cbn-sol-section-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 / Mapa de equipos</p>
        <h2 id="cbn-teams-list-title">Elige tu zona<br>de juego.</h2>
      </div>
      <p>Explora las categorías publicadas por el club y abre la ficha de cada equipo para consultar sus datos y partidos.</p>
    </header>

    <?php if ($cbn_team_cards) : ?>
      <div class="cbn-sol-teams-filter-shell" data-sol-reveal>
        <div class="cbn-sol-teams-filters" role="group" aria-label="Filtrar equipos">
          <button class="is-active" type="button" aria-pressed="true" aria-controls="cbn-team-card-grid" data-cbn-team-archive-filter="all">
            <span>Todos</span><small><?php echo esc_html((string) count($cbn_team_cards)); ?></small>
          </button>
          <?php foreach ($cbn_group_labels as $cbn_group => $cbn_group_label) : ?>
            <?php if (0 === $cbn_group_counts[$cbn_group]) : continue; endif; ?>
            <button type="button" aria-pressed="false" aria-controls="cbn-team-card-grid" data-cbn-team-archive-filter="<?php echo esc_attr($cbn_group); ?>">
              <span><?php echo esc_html($cbn_group_label); ?></span><small><?php echo esc_html((string) $cbn_group_counts[$cbn_group]); ?></small>
            </button>
          <?php endforeach; ?>
        </div>
        <p class="cbn-sol-teams-filter-status" role="status" aria-live="polite" data-cbn-team-filter-status>
          <?php
          echo esc_html(
              sprintf(
                  '%d %s en pista',
                  count($cbn_team_cards),
                  1 === count($cbn_team_cards) ? 'equipo' : 'equipos'
              )
          );
          ?>
        </p>
      </div>

      <div id="cbn-team-card-grid" class="cbn-sol-teams-grid">
        <?php foreach ($cbn_team_cards as $cbn_index => $cbn_card) : ?>
          <?php
          $cbn_team = $cbn_card['post'];
          $cbn_meta = $cbn_card['meta'];
          $cbn_permalink = get_permalink($cbn_team);
          $cbn_card_number = str_pad((string) ($cbn_index + 1), 2, '0', STR_PAD_LEFT);
          ?>
          <article
            class="cbn-sol-teams-card"
            data-cbn-team-archive-card
            data-cbn-team-groups="<?php echo esc_attr(implode(' ', $cbn_card['groups'])); ?>"
            data-sol-reveal
          >
            <a href="<?php echo esc_url($cbn_permalink); ?>" aria-label="Ver equipo <?php echo esc_attr(get_the_title($cbn_team)); ?>" data-cbn-swish>
              <span class="cbn-sol-teams-card__media">
                <?php if (has_post_thumbnail($cbn_team)) : ?>
                  <?php
                  echo get_the_post_thumbnail(
                      $cbn_team,
                      'large',
                      [
                          'class' => 'cbn-sol-teams-card__image',
                          'loading' => 'lazy',
                          'decoding' => 'async',
                      ]
                  );
                  ?>
                <?php else : ?>
                  <span class="cbn-sol-teams-card__fallback" aria-hidden="true">
                    <span class="cbn-sol-teams-card__half-court"></span>
                    <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400" loading="lazy">
                  </span>
                <?php endif; ?>
                <span class="cbn-sol-teams-card__number" aria-hidden="true"><?php echo esc_html($cbn_card_number); ?></span>
                <span class="cbn-sol-teams-card__category"><?php echo esc_html($cbn_card['category']); ?></span>
              </span>

              <span class="cbn-sol-teams-card__body">
                <span class="cbn-sol-teams-card__topline">
                  <small>Equipo CBN</small>
                  <i aria-hidden="true">↗</i>
                </span>
                <strong><?php echo esc_html(get_the_title($cbn_team)); ?></strong>
                <span class="cbn-sol-teams-card__facts">
                  <?php if ($cbn_meta['competition_name']) : ?>
                    <span><small>Competición</small><?php echo esc_html($cbn_meta['competition_name']); ?></span>
                  <?php endif; ?>
                  <?php if ($cbn_meta['coach']) : ?>
                    <span><small>Entrenador/a</small><?php echo esc_html($cbn_meta['coach']); ?></span>
                  <?php endif; ?>
                  <?php if ($cbn_meta['home_venue_name']) : ?>
                    <span><small>Pista</small><?php echo esc_html($cbn_meta['home_venue_name']); ?></span>
                  <?php endif; ?>
                </span>
                <span class="cbn-sol-teams-card__action">Abrir ficha <span aria-hidden="true">→</span></span>
              </span>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <article class="cbn-sol-teams-empty" role="status" data-sol-reveal>
        <span class="cbn-sol-teams-empty__ball" aria-hidden="true"></span>
        <div>
          <p class="cbn-sol-section-index">El vestuario se está preparando</p>
          <h2>Equipos en actualización.</h2>
          <p>El club todavía no ha publicado los equipos de la temporada. Puedes volver pronto o escribirnos para consultar una categoría.</p>
          <a class="cbn-sol-button" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>Contactar con el club</a>
        </div>
      </article>
    <?php endif; ?>
  </section>

  <section class="cbn-sol-teams-join" aria-labelledby="cbn-teams-join-title" data-sol-reveal>
    <div>
      <p class="cbn-sol-section-index">02 / Siguiente jugada</p>
      <h2 id="cbn-teams-join-title">¿Aún no tienes<br>equipo?</h2>
    </div>
    <div class="cbn-sol-teams-join__copy">
      <p>Cuéntanos tu edad y experiencia. El club te orientará hacia la categoría adecuada.</p>
      <div>
        <a class="cbn-sol-button cbn-sol-button--light cbn-sol-button--shot" href="<?php echo esc_url(home_url('/inscripcion/')); ?>" data-cbn-swish>
          <span>Quiero jugar</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>Resolver una duda <span aria-hidden="true">↗</span></a>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
