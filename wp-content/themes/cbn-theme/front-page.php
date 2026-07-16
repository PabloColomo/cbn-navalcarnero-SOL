<?php
$cbn_home = cbn_get_home_content();
$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');

$cbn_match_away = trim((string) $cbn_home['match']['away']);
$cbn_match_is_placeholder = in_array(strtolower($cbn_match_away), ['', 'rival', 'por confirmar'], true);
$cbn_match_away_label = $cbn_match_is_placeholder ? 'Por confirmar' : $cbn_match_away;
$cbn_match_date_label = $cbn_match_is_placeholder ? 'Fecha por confirmar' : $cbn_home['match']['date'];

$cbn_quick_plays = [
    [
        'number' => '01',
        'label' => 'Escuela',
        'title' => 'Empieza a jugar',
        'text' => 'Babybasket mixto y formación desde la base.',
        'url' => home_url('/equipos/'),
    ],
    [
        'number' => '02',
        'label' => 'Equipos',
        'title' => 'Encuentra tu pista',
        'text' => 'Categorías femeninas y masculinas de cantera y senior.',
        'url' => home_url('/equipos/'),
    ],
    [
        'number' => '03',
        'label' => 'Partidos',
        'title' => 'Sigue la jornada',
        'text' => 'Calendario, resultados verificados y clasificaciones.',
        'url' => home_url('/partidos/'),
    ],
    [
        'number' => '04',
        'label' => 'Inscripción',
        'title' => 'Entra en el equipo',
        'text' => 'Solicita plaza para la temporada 2026/2027.',
        'url' => home_url('/inscripcion/'),
    ],
];

$cbn_team_descriptions = [
    'escuela' => 'Primer contacto con el balón, el equipo y la pista.',
    'femenino' => 'Alevín, infantil, cadete, senior y Sub-22 femenina.',
    'masculino' => 'Benjamín, alevín, infantil, cadete, junior y Sub-22.',
    'senior' => 'Senior femenina y 1.ª Autonómica Masculina Plata.',
];
$cbn_team_groups = [];

foreach (array_slice($cbn_home['teams'], 0, 4) as $cbn_team_index => $cbn_team) {
    $cbn_team_search = strtolower(remove_accents(($cbn_team['label'] ?? '') . ' ' . ($cbn_team['title'] ?? '')));
    $cbn_team_filter = 'senior';

    if (str_contains($cbn_team_search, 'escuela') || str_contains($cbn_team_search, 'baby')) {
        $cbn_team_filter = 'escuela';
    } elseif (str_contains($cbn_team_search, 'femen')) {
        $cbn_team_filter = 'femenino';
    } elseif (str_contains($cbn_team_search, 'mascul')) {
        $cbn_team_filter = 'masculino';
    }

    $cbn_team_groups[] = [
        'filter' => $cbn_team_filter,
        'eyebrow' => $cbn_team['label'] ?? 'Equipo CBN',
        'title' => $cbn_team['title'] ?? 'Club Baloncesto Navalcarnero',
        'text' => $cbn_team_descriptions[$cbn_team_filter],
        'image' => $cbn_team['image'] ?? cbn_get_club_photo_url('club-057'),
        'photo_id' => $cbn_team['photo_id'] ?? null,
        'url' => $cbn_team['url'] ?? home_url('/equipos/'),
    ];
}

$cbn_verified_sponsors = array_slice($cbn_home['sponsors'], 0, 8);
$cbn_news_items = array_slice($cbn_home['news'], 0, 3);

get_header();
?>

<main id="primary" class="cbn-home cbn-sol" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 1200" preserveAspectRatio="none" focusable="false">
      <path
        class="cbn-sol-route__ghost"
        d="M12 0 C12 90 88 105 88 190 S18 290 18 385 S82 485 82 585 S22 690 22 790 S84 905 84 1000 S48 1110 48 1200"
      ></path>
      <path
        class="cbn-sol-route__active"
        data-cbn-route
        d="M12 0 C12 90 88 105 88 190 S18 290 18 385 S82 485 82 585 S22 690 22 790 S84 905 84 1000 S48 1110 48 1200"
      ></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-hero" aria-labelledby="cbn-sol-title">
    <div class="cbn-sol-hero__court" aria-hidden="true">
      <svg viewBox="0 0 900 720" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M450 0V720M0 360H900"></path>
        <circle cx="450" cy="360" r="112"></circle>
        <path d="M0 145h168v430H0M900 145H732v430h168"></path>
        <path d="M168 252a112 112 0 0 1 0 216M732 252a112 112 0 0 0 0 216"></path>
        <path d="M0 70a290 290 0 0 1 0 580M900 70a290 290 0 0 0 0 580"></path>
      </svg>
    </div>

    <div class="cbn-sol-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker">
        <span>Navalcarnero · Madrid</span>
        <span>Temporada 26/27</span>
      </p>
      <h1 id="cbn-sol-title">
        <?php foreach ($cbn_home['hero']['title_lines'] as $cbn_hero_title_line) : ?>
          <span><?php echo esc_html($cbn_hero_title_line); ?></span>
        <?php endforeach; ?>
      </h1>
      <p class="cbn-sol-hero__lead"><?php echo esc_html($cbn_home['hero']['lead']); ?></p>
      <div class="cbn-sol-hero__actions">
        <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url($cbn_home['hero']['primary_cta']['url']); ?>" data-cbn-swish>
          <span><?php echo esc_html($cbn_home['hero']['primary_cta']['label']); ?></span>
          <span class="cbn-sol-button__arc" aria-hidden="true">
            <span></span>
          </span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_home['hero']['secondary_cta']['url']); ?>" data-cbn-swish>
          <?php echo esc_html($cbn_home['hero']['secondary_cta']['label']); ?> <span aria-hidden="true">↗</span>
        </a>
      </div>

      <button class="cbn-sol-audio-invite" type="button" data-cbn-sound-secondary aria-pressed="false">
        <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span><strong>Escucha la pista</strong><small>Activa pisadas de parqué y sonido de red</small></span>
      </button>
    </div>

    <figure class="cbn-sol-hero__visual" data-sol-reveal data-cbn-parallax>
      <div class="cbn-sol-hero__image-wrap <?php echo empty($cbn_home['hero']['image']['photo_ids']) ? '' : 'cbn-sol-photo-collage'; ?>">
        <?php if (!empty($cbn_home['hero']['image']['attachment_id'])) : ?>
          <?php
          echo wp_get_attachment_image(
              (int) $cbn_home['hero']['image']['attachment_id'],
              'full',
              false,
              [
                  'alt' => $cbn_home['hero']['image']['alt'],
                  'loading' => 'eager',
                  'fetchpriority' => 'high',
                  'decoding' => 'async',
                  'sizes' => '(max-width: 760px) 100vw, 46vw',
              ]
          );
          ?>
        <?php elseif (!empty($cbn_home['hero']['image']['photo_ids'])) : ?>
          <?php foreach ($cbn_home['hero']['image']['photo_ids'] as $cbn_hero_photo_index => $cbn_hero_photo_id) : ?>
            <?php
            cbn_render_club_photo(
                $cbn_hero_photo_id,
                [
                    'sizes' => '(max-width: 760px) 32vw, 18vw',
                    'loading' => 'eager',
                    'fetchpriority' => 0 === $cbn_hero_photo_index ? 'high' : '',
                ]
            );
            ?>
          <?php endforeach; ?>
        <?php else : ?>
          <img
            src="<?php echo esc_url($cbn_home['hero']['image']['url']); ?>"
            alt="<?php echo esc_attr($cbn_home['hero']['image']['alt']); ?>"
            width="<?php echo esc_attr((string) $cbn_home['hero']['image']['width']); ?>"
            height="<?php echo esc_attr((string) $cbn_home['hero']['image']['height']); ?>"
            fetchpriority="high"
            decoding="async"
          >
        <?php endif; ?>
      </div>
      <figcaption aria-hidden="true">
        <strong class="cbn-photo-credit">&copy; CBN</strong>
      </figcaption>
      <img class="cbn-sol-hero__crest" src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
      <span class="cbn-sol-shot-clock" aria-hidden="true">
        <small>Shot clock</small>
        <strong data-cbn-shot-clock>24</strong>
      </span>
    </figure>

    <div class="cbn-sol-scroll-cue" aria-hidden="true">
      <span></span>
      Baja a pista
    </div>
  </section>

  <section class="cbn-sol-scoreboard" aria-labelledby="cbn-scoreboard-title" data-sol-reveal>
    <header class="cbn-sol-scoreboard__intro">
      <p class="cbn-sol-section-index">01 · En juego</p>
      <h2 id="cbn-scoreboard-title">El pulso<br>del club</h2>
      <p>La jornada, los equipos y la información que importa, sin perder un segundo.</p>
    </header>

    <article class="cbn-sol-match-card">
      <div class="cbn-sol-match-card__topline">
        <span><i></i><?php echo esc_html($cbn_home['match']['label']); ?></span>
        <a href="<?php echo esc_url(home_url('/partidos/')); ?>">Calendario completo ↗</a>
      </div>
      <div class="cbn-sol-match-card__score">
        <div>
          <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
          <strong><?php echo esc_html($cbn_home['match']['home']); ?></strong>
          <small>Navalcarnero</small>
        </div>
        <span class="cbn-sol-match-card__versus"><small>Jornada</small><strong>VS</strong></span>
        <div>
          <span class="cbn-sol-rival-mark" aria-hidden="true">?</span>
          <strong><?php echo esc_html($cbn_match_away_label); ?></strong>
          <small>Próximo rival</small>
        </div>
      </div>
      <div class="cbn-sol-match-card__meta">
        <span><small>Cuándo</small><strong><?php echo esc_html($cbn_match_date_label); ?></strong></span>
        <span><small>Dónde</small><strong><?php echo esc_html($cbn_home['match']['venue']); ?></strong></span>
      </div>
    </article>

    <nav class="cbn-sol-scoreboard__links" aria-label="Información deportiva">
      <a href="<?php echo esc_url(home_url('/partidos/')); ?>" data-cbn-swish><span>01</span><strong>Próximos partidos</strong><i>↗</i></a>
      <a href="<?php echo esc_url(home_url('/partidos/')); ?>" data-cbn-swish><span>02</span><strong>Resultados verificados</strong><i>↗</i></a>
      <a href="<?php echo esc_url(home_url('/equipos/')); ?>" data-cbn-swish><span>03</span><strong>Temporada 26/27</strong><i>↗</i></a>
    </nav>
  </section>

  <section class="cbn-sol-quick" aria-labelledby="cbn-quick-title">
    <header class="cbn-sol-section-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">02 · Accesos</p>
        <h2 id="cbn-quick-title">Jugadas rápidas</h2>
      </div>
      <p>Todo lo que necesitas del club, a un pase de distancia.</p>
    </header>
    <div class="cbn-sol-quick__grid">
      <?php foreach ($cbn_quick_plays as $cbn_quick_play) : ?>
        <a class="cbn-sol-quick-card" href="<?php echo esc_url($cbn_quick_play['url']); ?>" data-sol-reveal data-cbn-tilt data-cbn-swish>
          <span class="cbn-sol-quick-card__number"><?php echo esc_html($cbn_quick_play['number']); ?></span>
          <small><?php echo esc_html($cbn_quick_play['label']); ?></small>
          <strong><?php echo esc_html($cbn_quick_play['title']); ?></strong>
          <p><?php echo esc_html($cbn_quick_play['text']); ?></p>
          <span class="cbn-sol-quick-card__arrow" aria-hidden="true">↗</span>
          <span class="cbn-sol-quick-card__line" aria-hidden="true"></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cbn-sol-teams" aria-labelledby="cbn-teams-title">
    <header class="cbn-sol-section-heading cbn-sol-section-heading--inverse" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">03 · Categorías publicadas 25/26</p>
        <h2 id="cbn-teams-title"><?php echo esc_html($cbn_home['teams_heading']); ?></h2>
      </div>
      <p>Equipos femeninos y masculinos desde Babybasket hasta competición senior.</p>
    </header>

    <div class="cbn-sol-team-filters" role="group" aria-label="Filtrar grupos de equipos" data-sol-reveal>
      <button type="button" class="is-active" data-cbn-team-filter="all" aria-pressed="true">Todos</button>
      <button type="button" data-cbn-team-filter="escuela" aria-pressed="false">Escuela</button>
      <button type="button" data-cbn-team-filter="femenino" aria-pressed="false">Femenino</button>
      <button type="button" data-cbn-team-filter="masculino" aria-pressed="false">Masculino</button>
      <button type="button" data-cbn-team-filter="senior" aria-pressed="false">Senior</button>
    </div>

    <div class="cbn-sol-team-grid">
      <?php foreach ($cbn_team_groups as $cbn_team_group) : ?>
        <article class="cbn-sol-team-card" data-cbn-team="<?php echo esc_attr($cbn_team_group['filter']); ?>" data-sol-reveal data-cbn-tilt>
          <a href="<?php echo esc_url($cbn_team_group['url']); ?>" data-cbn-swish>
            <span class="cbn-sol-team-card__media">
              <?php if (!empty($cbn_team_group['attachment_id'])) : ?>
                <?php
                echo wp_get_attachment_image(
                    (int) $cbn_team_group['attachment_id'],
                    'large',
                    false,
                    [
                        'alt' => '',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                        'sizes' => '(max-width: 760px) 100vw, 42vw',
                    ]
                );
                ?>
              <?php elseif (!empty($cbn_team_group['photo_id'])) : ?>
                <?php cbn_render_club_photo($cbn_team_group['photo_id'], ['decorative' => true, 'sizes' => '(max-width: 760px) 100vw, 42vw']); ?>
              <?php else : ?>
                <img src="<?php echo esc_url($cbn_team_group['image']); ?>" alt="" width="960" height="640" loading="lazy" decoding="async">
              <?php endif; ?>
              <span class="cbn-photo-credit" aria-hidden="true">&copy; CBN</span>
            </span>
            <span class="cbn-sol-team-card__copy">
              <small><?php echo esc_html($cbn_team_group['eyebrow']); ?></small>
              <strong><?php echo esc_html($cbn_team_group['title']); ?></strong>
              <p><?php echo esc_html($cbn_team_group['text']); ?></p>
              <i aria-hidden="true">Ver equipos ↗</i>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cbn-sol-manifesto" aria-labelledby="cbn-manifesto-title">
    <div class="cbn-sol-manifesto__media" data-sol-reveal data-cbn-parallax>
      <?php cbn_render_club_photo(cbn_get_club_photo_feature('home_manifesto') ?: 'club-031', ['sizes' => '(max-width: 760px) 100vw, 54vw']); ?>
      <span aria-hidden="true">CBN</span>
    </div>
    <div class="cbn-sol-manifesto__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">04 · Nuestro porqué</p>
      <h2 id="cbn-manifesto-title">Aquí nadie<br>se queda<br><em>sin jugar.</em></h2>
      <p>Promovemos el baloncesto en Navalcarnero con una idea sencilla: formar personas, crear equipo y abrir la pista a cada niño y cada niña que quiera disfrutar del juego.</p>
      <dl>
        <div><dt>Formación integral</dt><dd>Deporte y valores dentro y fuera de la pista.</dd></div>
        <div><dt>Cantera primero</dt><dd>Una estructura que acompaña cada etapa.</dd></div>
        <div><dt>Comunidad local</dt><dd>Navalcarnero juega unido.</dd></div>
      </dl>
      <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/el-club/')); ?>" data-cbn-swish>Conoce el club <span aria-hidden="true">↗</span></a>
    </div>
  </section>

  <?php cbn_render_club_photo_story('home'); ?>

  <section class="cbn-sol-news" aria-labelledby="cbn-news-title">
    <header class="cbn-sol-section-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">05 · Actualidad</p>
        <h2 id="cbn-news-title"><?php echo esc_html($cbn_home['news_heading']); ?></h2>
      </div>
      <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/noticias/')); ?>">Todas las noticias <span aria-hidden="true">↗</span></a>
    </header>

    <div class="cbn-sol-news__grid">
      <?php foreach ($cbn_news_items as $cbn_news_index => $cbn_news_item) : ?>
        <article class="cbn-sol-news-card <?php echo 0 === $cbn_news_index ? 'cbn-sol-news-card--lead' : ''; ?>" data-sol-reveal data-cbn-tilt>
          <a href="<?php echo esc_url($cbn_news_item['url']); ?>" data-cbn-swish>
            <span class="cbn-sol-news-card__media">
              <?php if (!empty($cbn_news_item['attachment_id'])) : ?>
                <?php
                echo wp_get_attachment_image(
                    (int) $cbn_news_item['attachment_id'],
                    'large',
                    false,
                    [
                        'alt' => '',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                        'sizes' => '(max-width: 760px) 100vw, 38vw',
                    ]
                );
                ?>
              <?php elseif (!empty($cbn_news_item['photo_id'])) : ?>
                <?php cbn_render_club_photo($cbn_news_item['photo_id'], ['decorative' => true, 'sizes' => '(max-width: 760px) 100vw, 38vw']); ?>
              <?php else : ?>
                <img src="<?php echo esc_url($cbn_news_item['image']); ?>" alt="" width="960" height="640" loading="lazy" decoding="async">
              <?php endif; ?>
              <small class="cbn-photo-credit" aria-hidden="true">&copy; CBN</small>
            </span>
            <span class="cbn-sol-news-card__copy">
              <small><?php echo esc_html($cbn_news_item['date']); ?></small>
              <strong><?php echo esc_html($cbn_news_item['title']); ?></strong>
              <p><?php echo esc_html($cbn_news_item['excerpt']); ?></p>
              <i aria-hidden="true">Leer ↗</i>
            </span>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cbn-sol-commerce" aria-label="Inscripciones y tienda">
    <article class="cbn-sol-commerce__card cbn-sol-commerce__card--join" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">06 · Siguiente jugada</p>
        <h2><?php echo esc_html($cbn_home['registration']['title']); ?></h2>
        <p><?php echo esc_html($cbn_home['registration']['text']); ?></p>
        <a class="cbn-sol-button cbn-sol-button--light" href="<?php echo esc_url($cbn_home['registration']['url']); ?>" data-cbn-swish><?php echo esc_html($cbn_home['registration']['label']); ?> <span aria-hidden="true">↗</span></a>
      </div>
      <?php cbn_render_club_photo($cbn_home['registration']['image']['photo_id'] ?? 'club-064', ['sizes' => '(max-width: 760px) 100vw, 28vw']); ?>
    </article>

    <article class="cbn-sol-commerce__card cbn-sol-commerce__card--shop" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">Tienda CBN</p>
        <h2><?php echo esc_html($cbn_home['shop']['title']); ?></h2>
        <p><?php echo esc_html($cbn_home['shop']['text']); ?></p>
        <a class="cbn-sol-button cbn-sol-button--dark" href="<?php echo esc_url($cbn_home['shop']['url']); ?>" data-cbn-swish><?php echo esc_html($cbn_home['shop']['label']); ?> <span aria-hidden="true">↗</span></a>
      </div>
      <?php cbn_render_club_photo($cbn_home['shop']['image']['photo_id'] ?? 'club-025', ['sizes' => '(max-width: 760px) 100vw, 28vw']); ?>
    </article>
  </section>

  <section class="cbn-sol-places" aria-labelledby="cbn-places-title">
    <header class="cbn-sol-section-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">07 · Nuestra casa</p>
        <h2 id="cbn-places-title">Dos pabellones.<br>Un mismo latido.</h2>
      </div>
      <p>Entrenamos cada día en Navalcarnero. La Estación es nuestra sede y el centro de la actividad del club.</p>
    </header>

    <div class="cbn-sol-places__grid">
      <article class="cbn-sol-place-card cbn-sol-place-card--main" data-sol-reveal>
        <span class="cbn-sol-place-card__court" aria-hidden="true"></span>
        <small>Sede principal · Oficina 1.ª planta</small>
        <h3>Pabellón Municipal<br>La Estación</h3>
        <p>C/ Río Ebro, s/n<br>28600 Navalcarnero, Madrid</p>
        <a href="https://www.google.com/maps/search/?api=1&amp;query=Pabellon+Municipal+La+Estacion+Navalcarnero" target="_blank" rel="noreferrer">Abrir en Maps ↗</a>
      </article>
      <article class="cbn-sol-place-card" data-sol-reveal>
        <small>Instalación complementaria</small>
        <h3>Colegio<br>María Martín</h3>
        <p>C/ Víctimas del Terrorismo, s/n<br>Navalcarnero, Madrid</p>
        <a href="https://www.google.com/maps/search/?api=1&amp;query=Colegio+Maria+Martin+Navalcarnero" target="_blank" rel="noreferrer">Abrir en Maps ↗</a>
      </article>
      <aside class="cbn-sol-contact-card" data-sol-reveal>
        <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400" loading="lazy">
        <p>¿Tienes una pregunta?</p>
        <a href="tel:+34696849235">(+34) 696 849 235</a>
        <a href="mailto:administracion@cbnavalcarnero.es">administracion@cbnavalcarnero.es</a>
        <a class="cbn-sol-contact-card__cta" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>Hablemos ↗</a>
      </aside>
    </div>
  </section>

  <section class="cbn-sol-sponsors" aria-labelledby="cbn-sponsors-title">
    <header data-sol-reveal>
      <p class="cbn-sol-section-index">08 · Juegan con nosotros</p>
      <h2 id="cbn-sponsors-title"><?php echo esc_html($cbn_home['sponsors_heading']); ?>.</h2>
    </header>
    <div class="cbn-sol-sponsors__rail" data-sol-reveal>
      <?php foreach ($cbn_verified_sponsors as $cbn_sponsor) : ?>
        <span><?php echo esc_html($cbn_sponsor); ?></span>
      <?php endforeach; ?>
    </div>
    <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/sponsors/')); ?>">Patrocina al CBN <span aria-hidden="true">↗</span></a>
  </section>
</main>

<?php get_footer(); ?>
