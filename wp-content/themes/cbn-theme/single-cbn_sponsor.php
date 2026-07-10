<?php
/**
 * Pista Viva sponsor detail.
 *
 * Only sponsors that are active and inside their editorial date window are
 * exposed. Hidden or expired records return to the active archive.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_sponsors_archive_url = get_post_type_archive_link('cbn_sponsor') ?: home_url('/sponsors/');
$cbn_sponsor_post = get_queried_object();

if (!$cbn_sponsor_post instanceof WP_Post || 'cbn_sponsor' !== $cbn_sponsor_post->post_type) {
    wp_safe_redirect($cbn_sponsors_archive_url, 302);
    exit;
}

$cbn_sponsor = cbn_get_sponsor_card($cbn_sponsor_post);

if (!$cbn_sponsor['active'] || !cbn_sponsor_in_date_window($cbn_sponsor)) {
    wp_safe_redirect($cbn_sponsors_archive_url, 302);
    exit;
}

$cbn_sponsors_style_path = get_theme_file_path('assets/src/css/sol-sponsors.css');

wp_enqueue_style(
    'cbn-sol-sponsors',
    get_theme_file_uri('assets/src/css/sol-sponsors.css'),
    ['cbn-sol-experience'],
    file_exists($cbn_sponsors_style_path) ? (string) filemtime($cbn_sponsors_style_path) : CBN_THEME_VERSION
);

$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_all_sponsors = cbn_query_sponsors();
$cbn_sponsor_position = null;

foreach ($cbn_all_sponsors as $cbn_position => $cbn_ordered_sponsor) {
    if ((int) $cbn_sponsor['id'] === (int) $cbn_ordered_sponsor['id']) {
        $cbn_sponsor_position = $cbn_position;
        break;
    }
}

$cbn_previous_sponsor = null !== $cbn_sponsor_position && $cbn_sponsor_position > 0
    ? $cbn_all_sponsors[$cbn_sponsor_position - 1]
    : null;
$cbn_next_sponsor = null !== $cbn_sponsor_position && isset($cbn_all_sponsors[$cbn_sponsor_position + 1])
    ? $cbn_all_sponsors[$cbn_sponsor_position + 1]
    : null;
$cbn_sponsor_number = null !== $cbn_sponsor_position
    ? str_pad((string) ($cbn_sponsor_position + 1), 2, '0', STR_PAD_LEFT)
    : 'CBN';
$cbn_sponsor_tier = '' !== trim($cbn_sponsor['tier_name'])
    ? $cbn_sponsor['tier_name']
    : __('Colaboradores', 'cbn');
$cbn_has_content = '' !== trim((string) $cbn_sponsor_post->post_content);

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-sponsors-page cbn-sol-sponsor-detail" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-sponsors-route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 610" preserveAspectRatio="none" focusable="false">
      <path class="cbn-sol-route__ghost" d="M80 0 C18 58 15 119 65 163 S87 270 34 315 S15 425 70 470 S82 560 47 595 S40 604 50 610"></path>
      <path class="cbn-sol-route__active" data-cbn-route d="M80 0 C18 58 15 119 65 163 S87 270 34 315 S15 425 70 470 S82 560 47 595 S40 604 50 610"></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <?php while (have_posts()) : the_post(); ?>
    <section class="cbn-sol-sponsor-detail-hero" aria-labelledby="cbn-sponsor-title">
      <div class="cbn-sol-sponsor-detail-hero__copy" data-sol-reveal>
        <a class="cbn-sol-sponsor-back" href="<?php echo esc_url($cbn_sponsors_archive_url); ?>" data-cbn-swish>
          <span aria-hidden="true">←</span> Todas las alianzas
        </a>
        <p class="cbn-sol-kicker"><span><?php echo esc_html($cbn_sponsor_tier); ?></span><span>Alianza CBN</span></p>
        <h1 id="cbn-sponsor-title"><?php echo esc_html($cbn_sponsor['display_name']); ?></h1>
        <?php if ('' !== $cbn_sponsor['description']) : ?>
          <p class="cbn-sol-sponsor-detail-hero__lead"><?php echo esc_html($cbn_sponsor['description']); ?></p>
        <?php else : ?>
          <p class="cbn-sol-sponsor-detail-hero__lead">Entidad colaboradora publicada por el Club Baloncesto Navalcarnero.</p>
        <?php endif; ?>
        <div class="cbn-sol-sponsor-detail-hero__actions">
          <?php if ('' !== $cbn_sponsor['url']) : ?>
            <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url($cbn_sponsor['url']); ?>" rel="external noopener" data-cbn-swish>
              <span>Visitar sitio web</span>
              <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
            </a>
          <?php endif; ?>
          <a class="cbn-sol-text-link" href="#historia-alianza" data-cbn-swish>Conocer la alianza <span aria-hidden="true">↓</span></a>
        </div>
      </div>

      <figure class="cbn-sol-sponsor-detail-hero__identity" data-cbn-parallax>
        <div class="cbn-sol-sponsor-detail-hero__topline">
          <span>Partner / <?php echo esc_html($cbn_sponsor_number); ?></span>
          <span>Navalcarnero</span>
        </div>
        <span class="cbn-sol-sponsor-detail-hero__court" aria-hidden="true"><i></i><i></i></span>
        <div class="cbn-sol-sponsor-detail-hero__logo">
          <?php if ('' !== $cbn_sponsor['logo']) : ?>
            <img
              src="<?php echo esc_url($cbn_sponsor['logo']); ?>"
              alt="<?php echo esc_attr($cbn_sponsor['logo_alt'] ?: $cbn_sponsor['display_name']); ?>"
              decoding="async"
            >
          <?php else : ?>
            <span aria-hidden="true"><?php echo esc_html(cbn_get_sponsor_initials($cbn_sponsor['display_name'])); ?></span>
          <?php endif; ?>
        </div>
        <figcaption>
          <span><?php echo esc_html($cbn_sponsor_tier); ?></span>
          <strong>Alianza activa</strong>
        </figcaption>
      </figure>
    </section>

    <section class="cbn-sol-sponsor-detail-strip" aria-label="Estado de la alianza" data-sol-reveal>
      <div>
        <small>Entidad</small>
        <strong><?php echo esc_html($cbn_sponsor['display_name']); ?></strong>
      </div>
      <div>
        <small>Nivel editorial</small>
        <strong><?php echo esc_html($cbn_sponsor_tier); ?></strong>
      </div>
      <div>
        <small>Estado público</small>
        <strong>Activa</strong>
      </div>
      <div class="cbn-sol-sponsor-detail-strip__crest">
        <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
        <span>Juega CBN</span>
      </div>
    </section>

    <section id="historia-alianza" class="cbn-sol-sponsor-story" aria-labelledby="cbn-sponsor-story-title">
      <header class="cbn-sol-section-heading" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">01 / Dentro de la alianza</p>
          <h2 id="cbn-sponsor-story-title">Sumar también<br>es jugar.</h2>
        </div>
        <p>Información pública aprobada para esta entidad dentro de la red de colaboradores del club.</p>
      </header>

      <div class="cbn-sol-sponsor-story__grid">
        <div class="cbn-sol-sponsor-story__content" data-sol-reveal>
          <?php if ($cbn_has_content) : ?>
            <div class="cbn-sol-sponsor-prose"><?php the_content(); ?></div>
          <?php else : ?>
            <div class="cbn-sol-sponsor-story__empty" role="status">
              <span aria-hidden="true"><?php echo esc_html($cbn_sponsor_number); ?></span>
              <div>
                <h3>Información ampliada en preparación.</h3>
                <p>El club todavía no ha publicado una presentación adicional para esta alianza.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <aside class="cbn-sol-sponsor-story__aside" aria-labelledby="cbn-sponsor-impact-title" data-sol-reveal>
          <p class="cbn-sol-section-index">Apoyo compartido</p>
          <h3 id="cbn-sponsor-impact-title">Club, cantera y comunidad.</h3>
          <p>El apoyo de patrocinadores y colaboradores acompaña la actividad del club y su compromiso con el baloncesto en Navalcarnero.</p>
          <?php if ('' !== $cbn_sponsor['url']) : ?>
            <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_sponsor['url']); ?>" rel="external noopener" data-cbn-swish>
              Ir a su sitio web <span aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>
        </aside>
      </div>
    </section>

    <section class="cbn-sol-sponsor-detail-cta" aria-labelledby="cbn-sponsor-detail-cta-title" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">02 / Próxima alianza</p>
        <h2 id="cbn-sponsor-detail-cta-title">¿Quieres sumar<br>al proyecto?</h2>
      </div>
      <div>
        <p>Contacta con el club para conocer las opciones de colaboración disponibles.</p>
        <a class="cbn-sol-button cbn-sol-button--light cbn-sol-button--shot" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
          <span>Hablar con el CBN</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
      </div>
    </section>

    <nav class="cbn-sol-sponsor-switcher" aria-label="Navegar entre patrocinadores">
      <?php if ($cbn_previous_sponsor) : ?>
        <a href="<?php echo esc_url(get_permalink((int) $cbn_previous_sponsor['id'])); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Alianza anterior</small>
          <strong><?php echo esc_html($cbn_previous_sponsor['display_name']); ?></strong>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url($cbn_sponsors_archive_url); ?>" data-cbn-swish>
          <small><span aria-hidden="true">←</span> Volver</small>
          <strong>Todas las alianzas</strong>
        </a>
      <?php endif; ?>
      <?php if ($cbn_next_sponsor) : ?>
        <a href="<?php echo esc_url(get_permalink((int) $cbn_next_sponsor['id'])); ?>" data-cbn-swish>
          <small>Siguiente alianza <span aria-hidden="true">→</span></small>
          <strong><?php echo esc_html($cbn_next_sponsor['display_name']); ?></strong>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
          <small>Siguiente jugada <span aria-hidden="true">→</span></small>
          <strong>Contactar con el club</strong>
        </a>
      <?php endif; ?>
    </nav>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
