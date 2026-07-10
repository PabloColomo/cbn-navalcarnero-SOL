<?php
/**
 * Pista Viva template for the "El Club" page (slug: el-club).
 *
 * The content continues to come from cbn_get_club_content(), so ACF values
 * and the privacy-safe theme fallbacks remain the single source of truth.
 */

$cbn_club = cbn_get_club_content();
$cbn_club_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');

$cbn_enqueue_club_assets = static function (): void {
    $style_path = get_theme_file_path('assets/src/css/sol-club.css');
    $style_version = file_exists($style_path)
        ? (string) filemtime($style_path)
        : (string) wp_get_theme()->get('Version');

    wp_enqueue_style(
        'cbn-sol-club',
        get_theme_file_uri('assets/src/css/sol-club.css'),
        ['cbn-sol-experience'],
        $style_version
    );
};

if (did_action('wp_enqueue_scripts')) {
    $cbn_enqueue_club_assets();
} else {
    add_action('wp_enqueue_scripts', $cbn_enqueue_club_assets, 20);
}

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-club" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-club__route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 900" preserveAspectRatio="none" focusable="false">
      <path
        class="cbn-sol-route__ghost"
        d="M16 0 C16 82 84 92 84 176 S20 272 20 358 S80 454 80 540 S24 640 24 720 S72 812 54 900"
      ></path>
      <path
        class="cbn-sol-route__active"
        data-cbn-route
        d="M16 0 C16 82 84 92 84 176 S20 272 20 358 S80 454 80 540 S24 640 24 720 S72 812 54 900"
      ></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-club-hero" aria-labelledby="cbn-sol-club-title">
    <div class="cbn-sol-club-hero__court" aria-hidden="true">
      <svg viewBox="0 0 960 720" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M480 0v720M0 360h960"></path>
        <circle cx="480" cy="360" r="110"></circle>
        <path d="M0 142h178v436H0M960 142H782v436h178"></path>
        <path d="M178 250a112 112 0 0 1 0 220M782 250a112 112 0 0 0 0 220"></path>
        <path d="M0 62a300 300 0 0 1 0 596M960 62a300 300 0 0 0 0 596"></path>
      </svg>
    </div>

    <div class="cbn-sol-club-hero__copy" data-sol-reveal>
      <p class="cbn-sol-club-kicker">
        <span><?php echo esc_html($cbn_club['intro']['label']); ?></span>
        <span>Navalcarnero</span>
      </p>
      <h1 id="cbn-sol-club-title"><?php echo esc_html($cbn_club['intro']['title']); ?></h1>
      <p class="cbn-sol-club-hero__lead"><?php echo esc_html($cbn_club['intro']['lead']); ?></p>
      <p class="cbn-sol-club-hero__text"><?php echo esc_html($cbn_club['intro']['text']); ?></p>

      <div class="cbn-sol-club-hero__actions">
        <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url($cbn_club['intro']['primary_cta']['url']); ?>" data-cbn-swish>
          <span><?php echo esc_html($cbn_club['intro']['primary_cta']['label']); ?></span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_club['intro']['secondary_cta']['url']); ?>" data-cbn-swish>
          <?php echo esc_html($cbn_club['intro']['secondary_cta']['label']); ?> <span aria-hidden="true">&#8599;</span>
        </a>
      </div>

      <button class="cbn-sol-audio-invite" type="button" data-cbn-sound-secondary aria-pressed="false">
        <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span><strong>Escucha nuestra pista</strong><small>Activa el ambiente de parquet y red</small></span>
      </button>
    </div>

    <figure class="cbn-sol-club-hero__visual" data-sol-reveal data-cbn-parallax>
      <div class="cbn-sol-club-hero__image">
        <img
          src="<?php echo esc_url($cbn_club['intro']['image']['url']); ?>"
          alt="<?php echo esc_attr($cbn_club['intro']['image']['alt']); ?>"
          width="<?php echo esc_attr((string) $cbn_club['intro']['image']['width']); ?>"
          height="<?php echo esc_attr((string) $cbn_club['intro']['image']['height']); ?>"
          fetchpriority="high"
          decoding="async"
        >
      </div>
      <figcaption>
        <small>Club Baloncesto Navalcarnero</small>
        <strong>Cantera. Equipo. Comunidad.</strong>
      </figcaption>
      <img class="cbn-sol-club-hero__crest" src="<?php echo esc_url($cbn_club_logo_url); ?>" alt="" width="400" height="400">
      <span class="cbn-sol-club-hero__stamp" aria-hidden="true">Desde<br>la base</span>
    </figure>

    <div class="cbn-sol-club-hero__scroll" aria-hidden="true"><span></span>Conoce el club</div>
  </section>

  <section class="cbn-sol-club-values" aria-labelledby="cbn-sol-club-values-title">
    <header class="cbn-sol-club-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 &middot; Nuestro c&oacute;digo</p>
        <h2 id="cbn-sol-club-values-title"><?php echo esc_html($cbn_club['values_heading']); ?></h2>
      </div>
      <p>La forma de entrenar tambi&eacute;n es una forma de estar en el mundo.</p>
    </header>

    <div class="cbn-sol-club-values__court" aria-hidden="true">
      <span></span><i></i><b></b>
    </div>

    <div class="cbn-sol-club-values__grid">
      <?php foreach ($cbn_club['values'] as $cbn_value_index => $cbn_value) : ?>
        <article class="cbn-sol-club-value" data-sol-reveal data-cbn-tilt>
          <span class="cbn-sol-club-value__number"><?php echo esc_html(str_pad((string) ($cbn_value_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          <span class="cbn-sol-club-value__icon" aria-hidden="true">
            <?php echo cbn_get_home_icon($cbn_value['icon']); ?>
          </span>
          <h3><?php echo esc_html($cbn_value['title']); ?></h3>
          <p><?php echo esc_html($cbn_value['text']); ?></p>
          <span class="cbn-sol-club-value__line" aria-hidden="true"></span>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="cbn-sol-club-values__ticker" aria-hidden="true">
      <div>
        <?php foreach ($cbn_club['values'] as $cbn_value) : ?>
          <span><?php echo esc_html($cbn_value['title']); ?> <i>&bull;</i></span>
        <?php endforeach; ?>
        <?php foreach ($cbn_club['values'] as $cbn_value) : ?>
          <span><?php echo esc_html($cbn_value['title']); ?> <i>&bull;</i></span>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="cbn-sol-club-stats" aria-labelledby="cbn-sol-club-stats-title">
    <div class="cbn-sol-club-stats__intro" data-sol-reveal>
      <p class="cbn-sol-section-index">02 &middot; El equipo por dentro</p>
      <h2 id="cbn-sol-club-stats-title"><?php echo esc_html($cbn_club['stats_heading']); ?></h2>
    </div>
    <dl class="cbn-sol-club-stats__grid">
      <?php foreach ($cbn_club['stats'] as $cbn_stat_index => $cbn_stat) : ?>
        <div data-sol-reveal>
          <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($cbn_stat_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          <dt><?php echo esc_html($cbn_stat['value']); ?></dt>
          <dd><?php echo esc_html($cbn_stat['label']); ?></dd>
        </div>
      <?php endforeach; ?>
    </dl>
  </section>

  <section class="cbn-sol-club-facilities" aria-labelledby="cbn-sol-club-facilities-title">
    <header class="cbn-sol-club-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">03 &middot; Dónde sucede</p>
        <h2 id="cbn-sol-club-facilities-title"><?php echo esc_html($cbn_club['facilities_heading']); ?></h2>
      </div>
      <p>Dos espacios de Navalcarnero conectados por el mismo bal&oacute;n.</p>
    </header>

    <div class="cbn-sol-club-facilities__grid">
      <?php foreach ($cbn_club['facilities'] as $cbn_facility_index => $cbn_facility) : ?>
        <article class="cbn-sol-club-facility <?php echo 0 === $cbn_facility_index ? 'cbn-sol-club-facility--featured' : ''; ?>" data-sol-reveal data-cbn-tilt>
          <div class="cbn-sol-club-facility__media">
            <img
              src="<?php echo esc_url($cbn_facility['image']); ?>"
              alt=""
              width="1718"
              height="916"
              loading="lazy"
              decoding="async"
            >
            <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($cbn_facility_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          </div>
          <div class="cbn-sol-club-facility__copy">
            <small>Nuestra pista</small>
            <h3><?php echo esc_html($cbn_facility['title']); ?></h3>
            <p><?php echo esc_html($cbn_facility['text']); ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cbn-sol-club-school" aria-labelledby="cbn-sol-club-school-title">
    <div class="cbn-sol-club-school__media" data-sol-reveal data-cbn-parallax>
      <img
        src="<?php echo esc_url($cbn_club['school']['image']['url']); ?>"
        alt="<?php echo esc_attr($cbn_club['school']['image']['alt']); ?>"
        width="<?php echo esc_attr((string) $cbn_club['school']['image']['width']); ?>"
        height="<?php echo esc_attr((string) $cbn_club['school']['image']['height']); ?>"
        loading="lazy"
        decoding="async"
      >
      <span class="cbn-sol-club-school__word" aria-hidden="true">CRECER</span>
      <span class="cbn-sol-club-school__ball" aria-hidden="true"></span>
    </div>

    <div class="cbn-sol-club-school__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">04 &middot; <?php echo esc_html($cbn_club['school']['label']); ?></p>
      <h2 id="cbn-sol-club-school-title"><?php echo esc_html($cbn_club['school']['title']); ?></h2>
      <p class="cbn-sol-club-school__lead"><?php echo esc_html($cbn_club['school']['text']); ?></p>
      <ol class="cbn-sol-club-school__points">
        <?php foreach ($cbn_club['school']['points'] as $cbn_point_index => $cbn_point) : ?>
          <li>
            <span><?php echo esc_html(str_pad((string) ($cbn_point_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <?php echo esc_html($cbn_point); ?>
          </li>
        <?php endforeach; ?>
      </ol>
      <a class="cbn-sol-button cbn-sol-button--light" href="<?php echo esc_url($cbn_club['school']['cta_url']); ?>" data-cbn-swish>
        <?php echo esc_html($cbn_club['school']['cta_label']); ?> <span aria-hidden="true">&#8599;</span>
      </a>
    </div>
  </section>

  <section class="cbn-sol-club-cta" aria-labelledby="cbn-sol-club-cta-title">
    <div class="cbn-sol-club-cta__mark" aria-hidden="true">
      <span></span><i></i><b></b>
    </div>
    <div class="cbn-sol-club-cta__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">05 &middot; Tu siguiente jugada</p>
      <h2 id="cbn-sol-club-cta-title"><?php echo esc_html($cbn_club['cta']['title']); ?></h2>
      <p><?php echo esc_html($cbn_club['cta']['text']); ?></p>
      <div class="cbn-sol-club-cta__actions">
        <a class="cbn-sol-button cbn-sol-button--light" href="<?php echo esc_url($cbn_club['cta']['primary_url']); ?>" data-cbn-swish>
          <?php echo esc_html($cbn_club['cta']['primary_label']); ?> <span aria-hidden="true">&#8599;</span>
        </a>
        <a class="cbn-sol-club-cta__secondary" href="<?php echo esc_url($cbn_club['cta']['secondary_url']); ?>" data-cbn-swish>
          <?php echo esc_html($cbn_club['cta']['secondary_label']); ?> <span aria-hidden="true">&#8599;</span>
        </a>
      </div>
    </div>
    <img class="cbn-sol-club-cta__crest" src="<?php echo esc_url($cbn_club_logo_url); ?>" alt="" width="400" height="400" loading="lazy">
  </section>
</main>

<?php get_footer(); ?>
