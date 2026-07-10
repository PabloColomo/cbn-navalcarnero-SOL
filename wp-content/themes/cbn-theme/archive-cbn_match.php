<?php
/**
 * Matches archive: Pista Viva calendar and verified results.
 */

$cbn_matches_style_path = get_theme_file_path('assets/src/css/sol-matches.css');

// Run after the shared theme loader so this page-scoped layer stays last.
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

$cbn_upcoming = cbn_query_matches('upcoming', 12);
$cbn_results = cbn_query_matches('results', 12);
$cbn_featured_match = $cbn_upcoming[0] ?? $cbn_results[0] ?? null;
$cbn_featured_meta = $cbn_featured_match instanceof WP_Post
    ? cbn_get_match_meta($cbn_featured_match->ID)
    : null;
$cbn_featured_has_score = $cbn_featured_meta
    && null !== $cbn_featured_meta['home_score']
    && null !== $cbn_featured_meta['away_score'];
$cbn_featured_is_result = $cbn_featured_meta && 'final' === $cbn_featured_meta['status'];
$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_teams_url = get_post_type_archive_link('cbn_team') ?: home_url('/equipos/');

$cbn_public_team_label = static function (string $name): string {
    $normalized = strtolower(remove_accents(trim($name)));

    return in_array($normalized, ['', 'rival', 'por confirmar'], true)
        ? 'Por confirmar'
        : $name;
};

$cbn_render_match_list = static function (array $matches, string $context): void {
    ?>
    <ol class="cbn-sol-match-list" role="list">
      <?php foreach ($matches as $cbn_match_index => $cbn_match) : ?>
        <?php
        $cbn_match_meta = cbn_get_match_meta($cbn_match->ID);
        $cbn_match_has_score = null !== $cbn_match_meta['home_score']
            && null !== $cbn_match_meta['away_score'];
        $cbn_match_is_unverified_final = 'final' === $cbn_match_meta['status'] && !$cbn_match_has_score;
        $cbn_team_url = $cbn_match_meta['club_team_id']
            ? get_permalink($cbn_match_meta['club_team_id'])
            : '';
        ?>
        <li
          class="cbn-sol-match-entry<?php echo $cbn_match_is_unverified_final ? ' is-score-pending' : ''; ?>"
          data-match-index="<?php echo esc_attr(str_pad((string) ($cbn_match_index + 1), 2, '0', STR_PAD_LEFT)); ?>"
          data-match-context="<?php echo esc_attr($context); ?>"
          data-sol-reveal
          data-cbn-tilt
        >
          <?php get_template_part('template-parts/match-row', null, ['match' => $cbn_match]); ?>

          <footer class="cbn-sol-match-entry__footer">
            <?php if ('results' === $context) : ?>
              <?php if ($cbn_match_has_score) : ?>
                <span class="cbn-sol-match-entry__trust cbn-sol-match-entry__trust--verified">
                  <i aria-hidden="true"></i> Resultado verificado
                </span>
              <?php else : ?>
                <span class="cbn-sol-match-entry__trust">
                  <i aria-hidden="true"></i> Marcador pendiente de verificación
                </span>
              <?php endif; ?>
            <?php else : ?>
              <span class="cbn-sol-match-entry__trust">
                <i aria-hidden="true"></i> <?php echo esc_html(cbn_get_match_status_label($cbn_match_meta['status'])); ?>
              </span>
            <?php endif; ?>

            <?php if ($cbn_team_url) : ?>
              <a href="<?php echo esc_url($cbn_team_url); ?>" data-cbn-swish>
                Ver equipo <span aria-hidden="true">↗</span>
              </a>
            <?php endif; ?>
          </footer>
        </li>
      <?php endforeach; ?>
    </ol>
    <?php
};

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-matches" data-cbn-sol data-cbn-matches>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-matches__route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 1000" preserveAspectRatio="none" focusable="false">
      <path
        class="cbn-sol-route__ghost"
        d="M15 0 C15 80 85 95 85 180 S20 285 20 370 S82 475 82 560 S18 665 18 750 S80 855 80 930 S52 975 52 1000"
      ></path>
      <path
        class="cbn-sol-route__active"
        data-cbn-route
        d="M15 0 C15 80 85 95 85 180 S20 285 20 370 S82 475 82 560 S18 665 18 750 S80 855 80 930 S52 975 52 1000"
      ></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-matches-hero" aria-labelledby="cbn-matches-title">
    <div class="cbn-sol-matches-hero__court" aria-hidden="true">
      <svg viewBox="0 0 760 920" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M0 460h760M380 0v920"></path>
        <circle cx="380" cy="460" r="108"></circle>
        <path d="M210 0v208h340V0M210 920V712h340v208"></path>
        <path d="M276 208a104 104 0 0 0 208 0M276 712a104 104 0 0 1 208 0"></path>
        <path d="M94 0a286 286 0 0 0 572 0M94 920a286 286 0 0 1 572 0"></path>
      </svg>
    </div>

    <div class="cbn-sol-matches-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker">
        <span>Partidos · CBN Navalcarnero</span>
        <span>Calendario del club</span>
      </p>
      <h1 id="cbn-matches-title">
        <span>La jornada</span>
        <em>late aquí.</em>
      </h1>
      <p class="cbn-sol-matches-hero__lead">
        Próximos cruces, pabellones y resultados del club en una cancha que se
        actualiza con la información publicada en WordPress.
      </p>

      <div class="cbn-sol-matches-hero__actions">
        <a class="cbn-sol-button cbn-sol-button--shot" href="#proximos-partidos" data-cbn-swish>
          <span>Ver la jornada</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_teams_url); ?>" data-cbn-swish>
          Explorar equipos <span aria-hidden="true">↗</span>
        </a>
      </div>

      <button
        class="cbn-sol-audio-invite cbn-sol-matches-hero__audio"
        type="button"
        data-cbn-sound-secondary
        aria-pressed="false"
      >
        <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span><strong>Escucha la pista</strong><small>Sonido opcional, apagado por defecto</small></span>
      </button>
    </div>

    <article class="cbn-sol-featured-match<?php echo $cbn_featured_match ? '' : ' is-empty'; ?>" data-sol-reveal data-cbn-parallax>
      <header class="cbn-sol-featured-match__topline">
        <?php if ($cbn_featured_meta) : ?>
          <span class="cbn-sol-featured-match__status cbn-sol-featured-match__status--<?php echo esc_attr($cbn_featured_meta['status']); ?>">
            <i aria-hidden="true"></i>
            <?php echo esc_html(cbn_get_match_status_label($cbn_featured_meta['status'])); ?>
          </span>
          <span><?php echo esc_html(cbn_format_match_date($cbn_featured_meta['date'], $cbn_featured_meta['time'])); ?></span>
        <?php else : ?>
          <span class="cbn-sol-featured-match__status">
            <i aria-hidden="true"></i> Próxima posesión
          </span>
          <span>Datos por confirmar</span>
        <?php endif; ?>
      </header>

      <?php if ($cbn_featured_meta) : ?>
        <div class="cbn-sol-featured-match__board">
          <div class="cbn-sol-featured-match__team<?php echo $cbn_featured_meta['is_home_club'] ? ' is-club' : ''; ?>">
            <span class="cbn-sol-featured-match__mark" aria-hidden="true">
              <?php if ($cbn_featured_meta['is_home_club']) : ?>
                <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
              <?php else : ?>
                <b>LOC</b>
              <?php endif; ?>
            </span>
            <small>Local</small>
            <strong><?php echo esc_html($cbn_public_team_label($cbn_featured_meta['home_name'])); ?></strong>
          </div>

          <div class="cbn-sol-featured-match__result">
            <?php if ($cbn_featured_has_score) : ?>
              <span aria-hidden="true">
                <b><?php echo esc_html((string) $cbn_featured_meta['home_score']); ?></b>
                <i>:</i>
                <b><?php echo esc_html((string) $cbn_featured_meta['away_score']); ?></b>
              </span>
              <span class="cbn-visually-hidden">
                <?php echo esc_html($cbn_featured_meta['home_score'] . ' a ' . $cbn_featured_meta['away_score']); ?>
              </span>
              <small>Verificado</small>
            <?php elseif ($cbn_featured_is_result) : ?>
              <strong class="cbn-sol-featured-match__pending">Por<br>verificar</strong>
              <small>Marcador</small>
            <?php else : ?>
              <strong class="cbn-sol-featured-match__versus" aria-hidden="true">VS</strong>
              <span class="cbn-visually-hidden">contra</span>
              <small>En pista</small>
            <?php endif; ?>
          </div>

          <div class="cbn-sol-featured-match__team<?php echo !$cbn_featured_meta['is_home_club'] ? ' is-club' : ''; ?>">
            <span class="cbn-sol-featured-match__mark" aria-hidden="true">
              <?php if (!$cbn_featured_meta['is_home_club']) : ?>
                <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
              <?php else : ?>
                <b>VIS</b>
              <?php endif; ?>
            </span>
            <small>Visitante</small>
            <strong><?php echo esc_html($cbn_public_team_label($cbn_featured_meta['away_name'])); ?></strong>
          </div>
        </div>

        <footer class="cbn-sol-featured-match__meta">
          <span>
            <small>Competición</small>
            <strong><?php echo esc_html($cbn_featured_meta['competition_name'] ?: 'Por confirmar'); ?></strong>
          </span>
          <span>
            <small>Pista</small>
            <strong><?php echo esc_html($cbn_featured_meta['venue_name'] ?: 'Por confirmar'); ?></strong>
          </span>
          <?php if ($cbn_featured_meta['round']) : ?>
            <span>
              <small>Jornada</small>
              <strong><?php echo esc_html($cbn_featured_meta['round']); ?></strong>
            </span>
          <?php endif; ?>
        </footer>
      <?php else : ?>
        <div class="cbn-sol-featured-match__empty">
          <span aria-hidden="true"><i></i></span>
          <div>
            <h2>Calendario en preparación</h2>
            <p>Publicaremos aquí el siguiente encuentro cuando sus datos estén confirmados.</p>
          </div>
        </div>
      <?php endif; ?>

      <p class="cbn-sol-featured-match__verification">
        <span aria-hidden="true">✓</span>
        Los marcadores solo se muestran tras su verificación.
      </p>
    </article>

    <div class="cbn-sol-matches-hero__shot-clock" aria-hidden="true">
      <small>Posesión</small>
      <strong data-cbn-shot-clock>24</strong>
    </div>
  </section>

  <section class="cbn-sol-matches-ticker" aria-label="Información de la página">
    <p class="cbn-visually-hidden">
      Calendario dinámico, resultados verificados e información deportiva del club.
    </p>
    <div aria-hidden="true">
      <span>Calendario dinámico</span><i>●</i>
      <span>Resultados verificados</span><i>●</i>
      <span>La jornada en una pista</span><i>●</i>
      <span>Calendario dinámico</span><i>●</i>
      <span>Resultados verificados</span><i>●</i>
      <span>La jornada en una pista</span><i>●</i>
    </div>
  </section>

  <section id="proximos-partidos" class="cbn-sol-match-zone cbn-sol-match-zone--upcoming" aria-labelledby="cbn-upcoming-heading">
    <header class="cbn-sol-match-zone__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 · Siguiente jugada</p>
        <h2 id="cbn-upcoming-heading">Próximos<br><em>partidos.</em></h2>
      </div>
      <div class="cbn-sol-match-zone__intro">
        <span class="cbn-sol-match-zone__count" aria-label="<?php echo esc_attr(count($cbn_upcoming) . ' partidos mostrados'); ?>">
          <?php echo esc_html(str_pad((string) count($cbn_upcoming), 2, '0', STR_PAD_LEFT)); ?>
        </span>
        <p>Fecha, hora y pabellón de los encuentros que ya están publicados.</p>
      </div>
    </header>

    <?php if ($cbn_upcoming) : ?>
      <?php $cbn_render_match_list($cbn_upcoming, 'upcoming'); ?>
    <?php else : ?>
      <div class="cbn-sol-match-empty" role="status" data-sol-reveal>
        <span class="cbn-sol-match-empty__ball" aria-hidden="true"></span>
        <div>
          <p class="cbn-sol-section-index">Tiempo muerto</p>
          <h3>No hay partidos programados</h3>
          <p>Esta zona se activará automáticamente cuando el club publique el siguiente encuentro.</p>
        </div>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_teams_url); ?>" data-cbn-swish>
          Ver equipos <span aria-hidden="true">↗</span>
        </a>
      </div>
    <?php endif; ?>
  </section>

  <section id="ultimos-resultados" class="cbn-sol-match-zone cbn-sol-match-zone--results" aria-labelledby="cbn-results-heading">
    <div class="cbn-sol-match-zone__court-number" aria-hidden="true">02</div>
    <header class="cbn-sol-match-zone__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">02 · Marcador final</p>
        <h2 id="cbn-results-heading">Últimos<br><em>resultados.</em></h2>
      </div>
      <div class="cbn-sol-match-zone__intro">
        <span class="cbn-sol-match-zone__count" aria-label="<?php echo esc_attr(count($cbn_results) . ' resultados mostrados'); ?>">
          <?php echo esc_html(str_pad((string) count($cbn_results), 2, '0', STR_PAD_LEFT)); ?>
        </span>
        <p>Un tanteo aparece únicamente cuando el club lo ha marcado como verificado.</p>
      </div>
    </header>

    <?php if ($cbn_results) : ?>
      <?php $cbn_render_match_list($cbn_results, 'results'); ?>
    <?php else : ?>
      <div class="cbn-sol-match-empty cbn-sol-match-empty--inverse" role="status" data-sol-reveal>
        <span class="cbn-sol-match-empty__ball" aria-hidden="true"></span>
        <div>
          <p class="cbn-sol-section-index">Acta pendiente</p>
          <h3>Todavía no hay resultados</h3>
          <p>Los marcadores verificados aparecerán aquí sin necesidad de cambiar la página.</p>
        </div>
        <a class="cbn-sol-text-link" href="#proximos-partidos" data-cbn-swish>
          Ver calendario <span aria-hidden="true">↑</span>
        </a>
      </div>
    <?php endif; ?>
  </section>

  <section class="cbn-sol-matches-cta" aria-labelledby="cbn-matches-cta-title">
    <div class="cbn-sol-matches-cta__court" aria-hidden="true">
      <span></span><span></span><span></span>
    </div>
    <div data-sol-reveal>
      <p class="cbn-sol-section-index">La próxima jugada</p>
      <h2 id="cbn-matches-cta-title">No mires la pista.<br><em>Entra en ella.</em></h2>
    </div>
    <div class="cbn-sol-matches-cta__action" data-sol-reveal>
      <p>Conoce los equipos del CBN o solicita información para formar parte del club.</p>
      <a class="cbn-sol-button cbn-sol-button--light cbn-sol-button--shot" href="<?php echo esc_url(home_url('/inscripcion/')); ?>" data-cbn-swish>
        <span>Quiero jugar</span>
        <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
      </a>
    </div>
  </section>
</main>

<?php get_footer(); ?>
