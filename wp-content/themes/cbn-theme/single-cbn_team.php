<?php
/**
 * Pista Viva team detail.
 *
 * Displays club-owned identity, staff, opt-in roster data and related matches.
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

$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_archive_url = get_post_type_archive_link('cbn_team') ?: home_url('/equipos/');
$cbn_matches_archive_url = get_post_type_archive_link('cbn_match') ?: home_url('/partidos/');

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-teams-page cbn-sol-team-detail" data-cbn-sol data-cbn-teams-page>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-teams-route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 650" preserveAspectRatio="none" focusable="false">
      <path class="cbn-sol-route__ghost" d="M78 0 C15 63 18 132 70 173 S86 284 30 326 S14 438 72 478 S81 579 42 616 S34 642 50 650"></path>
      <path class="cbn-sol-route__active" data-cbn-route d="M78 0 C15 63 18 132 70 173 S86 284 30 326 S14 438 72 478 S81 579 42 616 S34 642 50 650"></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <?php while (have_posts()) : the_post(); ?>
    <?php
    $cbn_team_id = get_the_ID();
    $cbn_meta = cbn_get_team_meta($cbn_team_id);
    $cbn_roster = cbn_get_team_public_roster($cbn_team_id);
    $cbn_upcoming = cbn_query_matches('upcoming', 3, $cbn_team_id);
    $cbn_results = cbn_query_matches('results', 3, $cbn_team_id);
    $cbn_content = trim((string) get_the_content());
    $cbn_terms = get_the_terms($cbn_team_id, 'cbn_sport_category');
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

    $cbn_all_teams = cbn_query_teams();
    $cbn_team_position = null;

    foreach ($cbn_all_teams as $cbn_position => $cbn_ordered_team) {
        if ($cbn_team_id === $cbn_ordered_team->ID) {
            $cbn_team_position = $cbn_position;
            break;
        }
    }

    $cbn_previous_team = null !== $cbn_team_position && $cbn_team_position > 0
        ? $cbn_all_teams[$cbn_team_position - 1]
        : null;
    $cbn_next_team = null !== $cbn_team_position && isset($cbn_all_teams[$cbn_team_position + 1])
        ? $cbn_all_teams[$cbn_team_position + 1]
        : null;
    $cbn_team_number = null !== $cbn_team_position
        ? str_pad((string) ($cbn_team_position + 1), 2, '0', STR_PAD_LEFT)
        : 'CBN';
    $cbn_identity_facts = [
        ['label' => 'Temporada', 'value' => $cbn_meta['season_name']],
        ['label' => 'Competición', 'value' => $cbn_meta['competition_name']],
        ['label' => 'Pista local', 'value' => $cbn_meta['home_venue_name']],
    ];
    $cbn_team_data = [
        ['label' => 'Entrenador/a', 'value' => $cbn_meta['coach']],
        ['label' => 'Cuerpo técnico', 'value' => $cbn_meta['staff']],
        ['label' => 'Entrenamientos', 'value' => $cbn_meta['training_schedule']],
    ];
    $cbn_has_team_data = array_filter(
        $cbn_team_data,
        static fn (array $cbn_fact): bool => '' !== trim((string) $cbn_fact['value'])
    );
    ?>

    <section class="cbn-sol-team-detail-hero" aria-labelledby="cbn-team-title">
      <div class="cbn-sol-team-detail-hero__copy" data-sol-reveal>
        <a class="cbn-sol-team-back" href="<?php echo esc_url($cbn_archive_url); ?>" data-cbn-swish>
          <span aria-hidden="true">←</span> Todos los equipos
        </a>
        <p class="cbn-sol-kicker"><span><?php echo esc_html($cbn_category); ?></span><span>Club Baloncesto Navalcarnero</span></p>
        <h1 id="cbn-team-title"><?php the_title(); ?></h1>
        <?php if ($cbn_meta['federation_name']) : ?>
          <p class="cbn-sol-team-detail-hero__federation">
            <small>Nombre federativo</small>
            <?php echo esc_html($cbn_meta['federation_name']); ?>
          </p>
        <?php endif; ?>
        <div class="cbn-sol-team-detail-hero__actions">
          <a class="cbn-sol-button cbn-sol-button--shot" href="#ficha-equipo" data-cbn-swish>
            <span>Conocer el equipo</span>
            <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
          </a>
          <a class="cbn-sol-text-link" href="#partidos-equipo" data-cbn-swish>Ver partidos <span aria-hidden="true">↓</span></a>
        </div>
      </div>

      <figure class="cbn-sol-team-detail-hero__media" data-sol-reveal data-cbn-parallax>
        <?php if (has_post_thumbnail()) : ?>
          <?php
          the_post_thumbnail(
              'large',
              [
                  'class' => 'cbn-sol-team-detail-hero__image',
                  'fetchpriority' => 'high',
                  'decoding' => 'async',
              ]
          );
          ?>
        <?php else : ?>
          <span class="cbn-sol-team-detail-hero__fallback" aria-hidden="true">
            <span class="cbn-sol-team-detail-hero__court">
              <i></i><i></i><i></i>
            </span>
            <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
          </span>
        <?php endif; ?>
        <figcaption aria-hidden="true">
          <strong class="cbn-photo-credit">&copy; CBN</strong>
        </figcaption>
      </figure>
    </section>

    <section class="cbn-sol-team-fact-strip" aria-label="Datos principales del equipo" data-sol-reveal>
      <?php foreach ($cbn_identity_facts as $cbn_fact) : ?>
        <?php if ('' === trim((string) $cbn_fact['value'])) : continue; endif; ?>
        <div>
          <small><?php echo esc_html($cbn_fact['label']); ?></small>
          <strong><?php echo esc_html($cbn_fact['value']); ?></strong>
        </div>
      <?php endforeach; ?>
      <div class="cbn-sol-team-fact-strip__identity">
        <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
        <span>Somos CBN</span>
      </div>
    </section>

    <section id="ficha-equipo" class="cbn-sol-team-profile" aria-labelledby="cbn-team-profile-title">
      <header class="cbn-sol-section-heading" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">01 / Dentro del equipo</p>
          <h2 id="cbn-team-profile-title">La ficha<br>táctica.</h2>
        </div>
        <p>Información deportiva publicada y mantenida por el club para esta temporada.</p>
      </header>

      <div class="cbn-sol-team-profile__grid">
        <div class="cbn-sol-team-profile__story" data-sol-reveal>
          <?php if ('' !== $cbn_content) : ?>
            <div class="cbn-sol-team-prose"><?php the_content(); ?></div>
          <?php else : ?>
            <div class="cbn-sol-team-inline-empty" role="status">
              <span aria-hidden="true">01</span>
              <p>La historia y los objetivos de este equipo se están preparando para su publicación.</p>
            </div>
          <?php endif; ?>
        </div>

        <aside class="cbn-sol-team-profile__data" aria-labelledby="cbn-team-data-title" data-sol-reveal>
          <div class="cbn-sol-team-profile__data-head">
            <p class="cbn-sol-section-index">Datos de pista</p>
            <span aria-hidden="true"><?php echo esc_html($cbn_team_number); ?></span>
          </div>
          <h3 id="cbn-team-data-title">Cuerpo técnico y horarios</h3>
          <?php if ($cbn_has_team_data) : ?>
            <dl>
              <?php foreach ($cbn_team_data as $cbn_fact) : ?>
                <?php if ('' === trim((string) $cbn_fact['value'])) : continue; endif; ?>
                <div>
                  <dt><?php echo esc_html($cbn_fact['label']); ?></dt>
                  <dd><?php echo esc_html($cbn_fact['value']); ?></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          <?php else : ?>
            <p class="cbn-sol-team-profile__data-empty" role="status">El club está actualizando el cuerpo técnico y los horarios públicos de este equipo.</p>
          <?php endif; ?>
          <?php if ($cbn_meta['external_url']) : ?>
            <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_meta['external_url']); ?>" rel="external noopener" data-cbn-swish>
              Ficha oficial en la federación <span aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>
        </aside>
      </div>
    </section>

    <section class="cbn-sol-team-roster-zone" aria-labelledby="cbn-team-roster-title">
      <div class="cbn-sol-team-roster-zone__intro" data-sol-reveal>
        <p class="cbn-sol-section-index">02 / Plantilla</p>
        <h2 id="cbn-team-roster-title">Las personas<br>en la pista.</h2>
        <p>La información de jugadores solo aparece cuando el club ha activado expresamente su publicación.</p>
      </div>

      <div class="cbn-sol-team-roster-panel" data-sol-reveal>
        <?php if ($cbn_meta['public_roster'] && $cbn_roster) : ?>
          <div class="cbn-sol-team-roster-panel__head">
            <span>Plantilla pública</span>
            <strong><?php echo esc_html((string) count($cbn_roster)); ?></strong>
          </div>
          <ol class="cbn-sol-team-roster">
            <?php foreach ($cbn_roster as $cbn_player_index => $cbn_player_name) : ?>
              <li>
                <span><?php echo esc_html(str_pad((string) ($cbn_player_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                <strong><?php echo esc_html($cbn_player_name); ?></strong>
              </li>
            <?php endforeach; ?>
          </ol>
          <?php if ($cbn_meta['roster_notes']) : ?>
            <p class="cbn-sol-team-roster-panel__notes"><?php echo esc_html($cbn_meta['roster_notes']); ?></p>
          <?php endif; ?>
        <?php elseif ($cbn_meta['public_roster']) : ?>
          <div class="cbn-sol-team-private-state" role="status">
            <span class="cbn-sol-team-private-state__mark" aria-hidden="true">+</span>
            <div>
              <small>Publicación activada</small>
              <h3>Plantilla pendiente de carga.</h3>
              <p>El club ha autorizado esta sección, pero todavía no hay integrantes publicados.</p>
            </div>
          </div>
        <?php else : ?>
          <div class="cbn-sol-team-private-state" role="status">
            <span class="cbn-sol-team-private-state__mark" aria-hidden="true">
              <i></i>
            </span>
            <div>
              <small>Privacidad activa</small>
              <h3>Plantilla no publicada.</h3>
              <p>Por protección de datos —especialmente cuando hay menores— este equipo no muestra su plantilla online.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section id="partidos-equipo" class="cbn-sol-team-matches" aria-labelledby="cbn-team-matches-title">
      <header class="cbn-sol-section-heading cbn-sol-section-heading--inverse" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">03 / Jornada</p>
          <h2 id="cbn-team-matches-title">El balón<br>no para.</h2>
        </div>
        <p>Próximos encuentros y últimos resultados verificados de este equipo.</p>
      </header>

      <?php if ($cbn_upcoming || $cbn_results) : ?>
        <div class="cbn-sol-team-matches__grid">
          <?php if ($cbn_upcoming) : ?>
            <section aria-labelledby="cbn-team-upcoming-title" data-sol-reveal>
              <div class="cbn-sol-team-matches__heading">
                <h3 id="cbn-team-upcoming-title">Próximos</h3>
                <span><?php echo esc_html((string) count($cbn_upcoming)); ?></span>
              </div>
              <?php foreach ($cbn_upcoming as $cbn_match) : ?>
                <?php get_template_part('template-parts/match-row', null, ['match' => $cbn_match]); ?>
              <?php endforeach; ?>
            </section>
          <?php endif; ?>
          <?php if ($cbn_results) : ?>
            <section aria-labelledby="cbn-team-results-title" data-sol-reveal>
              <div class="cbn-sol-team-matches__heading">
                <h3 id="cbn-team-results-title">Resultados</h3>
                <span><?php echo esc_html((string) count($cbn_results)); ?></span>
              </div>
              <?php foreach ($cbn_results as $cbn_match) : ?>
                <?php get_template_part('template-parts/match-row', null, ['match' => $cbn_match]); ?>
              <?php endforeach; ?>
            </section>
          <?php endif; ?>
        </div>
      <?php else : ?>
        <div class="cbn-sol-team-matches__empty" role="status" data-sol-reveal>
          <span aria-hidden="true">24</span>
          <div>
            <h3>Sin partidos publicados.</h3>
            <p>Cuando el club publique la próxima jornada o valide resultados, aparecerán aquí.</p>
          </div>
        </div>
      <?php endif; ?>

      <a class="cbn-sol-button cbn-sol-button--light" href="<?php echo esc_url($cbn_matches_archive_url); ?>" data-cbn-swish>
        Calendario completo <span aria-hidden="true">→</span>
      </a>
    </section>

    <nav class="cbn-sol-team-switcher" aria-label="Navegar entre equipos">
      <?php if ($cbn_previous_team) : ?>
        <a href="<?php echo esc_url(get_permalink($cbn_previous_team)); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Equipo anterior</small>
          <strong><?php echo esc_html(get_the_title($cbn_previous_team)); ?></strong>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url($cbn_archive_url); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Volver</small>
          <strong>Todos los equipos</strong>
        </a>
      <?php endif; ?>
      <?php if ($cbn_next_team) : ?>
        <a href="<?php echo esc_url(get_permalink($cbn_next_team)); ?>" data-cbn-swish>
          <small>Equipo siguiente <span aria-hidden="true">→</span></small>
          <strong><?php echo esc_html(get_the_title($cbn_next_team)); ?></strong>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url(home_url('/inscripcion/')); ?>" data-cbn-swish>
          <small>Siguiente jugada <span aria-hidden="true">→</span></small>
          <strong>Quiero jugar</strong>
        </a>
      <?php endif; ?>
    </nav>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
