<?php
/**
 * Pista Viva sponsors archive.
 *
 * Active, in-window sponsors remain grouped by their editorial tier.
 */

$cbn_sponsors_style_path = get_theme_file_path('assets/src/css/sol-sponsors.css');

wp_enqueue_style(
    'cbn-sol-sponsors',
    get_theme_file_uri('assets/src/css/sol-sponsors.css'),
    ['cbn-sol-experience'],
    file_exists($cbn_sponsors_style_path) ? (string) filemtime($cbn_sponsors_style_path) : CBN_THEME_VERSION
);

$cbn_sponsors = cbn_query_sponsors();
$cbn_sponsor_tiers = cbn_group_sponsors_by_tier($cbn_sponsors);
$cbn_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_logo_count = count(
    array_filter(
        $cbn_sponsors,
        static fn (array $cbn_sponsor): bool => '' !== $cbn_sponsor['logo']
    )
);
$cbn_sponsor_index = 0;

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-sponsors-page" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <section class="cbn-sol-sponsors-hero" aria-labelledby="cbn-sponsors-title">
    <div class="cbn-sol-sponsors-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker"><span>Alianzas CBN</span><span>Navalcarnero juega en equipo</span></p>
      <h1 id="cbn-sponsors-title">
        <span>Más que</span>
        <span>logos.</span>
        <span>Equipo.</span>
      </h1>
      <p class="cbn-sol-sponsors-hero__lead">
        Instituciones, empresas y colaboradores que el club mantiene activos forman parte de este muro de alianzas.
      </p>
      <?php if ($cbn_sponsors) : ?>
        <a class="cbn-sol-button cbn-sol-button--shot" href="#alianzas" data-cbn-swish>
          <span>Descubrir alianzas</span>
        </a>
      <?php else : ?>
        <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
          <span>Hablar con el club</span>
        </a>
      <?php endif; ?>
    </div>

    <div class="cbn-sol-sponsors-hero__identity" data-sol-reveal>
      <div class="cbn-sol-sponsors-hero__topline">
        <span>Support system / CBN</span>
        <span>Alianzas activas</span>
      </div>

      <div class="cbn-sol-sponsors-hero__crest">
        <img src="<?php echo esc_url($cbn_logo_url); ?>" alt="" width="400" height="400">
      </div>

      <dl class="cbn-sol-sponsors-hero__stats">
        <div>
          <dt>Apoyos publicados</dt>
          <dd><?php echo esc_html((string) count($cbn_sponsors)); ?></dd>
        </div>
        <div>
          <dt>Niveles editoriales</dt>
          <dd><?php echo esc_html((string) count($cbn_sponsor_tiers)); ?></dd>
        </div>
        <div>
          <dt>Logos disponibles</dt>
          <dd><?php echo esc_html((string) $cbn_logo_count); ?></dd>
        </div>
      </dl>
    </div>
  </section>

  <?php if ($cbn_sponsor_tiers) : ?>
    <section id="alianzas" class="cbn-sol-sponsors-wall" aria-labelledby="cbn-sponsors-wall-title">
      <header class="cbn-sol-section-heading" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">01 / Muro de alianzas</p>
          <h2 id="cbn-sponsors-wall-title">Quienes<br>también juegan.</h2>
        </div>
        <p>Cada entidad aparece en el nivel y orden editorial definidos por el club.</p>
      </header>

      <nav class="cbn-sol-sponsors-tier-nav" aria-label="Niveles de patrocinio" data-sol-reveal>
        <?php foreach ($cbn_sponsor_tiers as $cbn_tier_index => $cbn_tier) : ?>
          <?php $cbn_tier_anchor = 'alianza-' . ($cbn_tier_index + 1) . '-' . sanitize_title($cbn_tier['label']); ?>
          <a href="#<?php echo esc_attr($cbn_tier_anchor); ?>" data-cbn-swish>
            <span><?php echo esc_html(str_pad((string) ($cbn_tier_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <?php echo esc_html($cbn_tier['label']); ?>
            <small><?php echo esc_html((string) count($cbn_tier['sponsors'])); ?></small>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="cbn-sol-sponsors-tiers">
        <?php foreach ($cbn_sponsor_tiers as $cbn_tier_index => $cbn_tier) : ?>
          <?php
          $cbn_tier_anchor = 'alianza-' . ($cbn_tier_index + 1) . '-' . sanitize_title($cbn_tier['label']);
          $cbn_tier_count = count($cbn_tier['sponsors']);
          ?>
          <section
            id="<?php echo esc_attr($cbn_tier_anchor); ?>"
            class="cbn-sol-sponsors-tier"
            aria-labelledby="<?php echo esc_attr($cbn_tier_anchor . '-title'); ?>"
            data-tier-index="<?php echo esc_attr((string) ($cbn_tier_index + 1)); ?>"
            data-sol-reveal
          >
            <header class="cbn-sol-sponsors-tier__header">
              <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($cbn_tier_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <div>
                <p><?php echo esc_html(1 === $cbn_tier_count ? '1 alianza activa' : $cbn_tier_count . ' alianzas activas'); ?></p>
                <h2 id="<?php echo esc_attr($cbn_tier_anchor . '-title'); ?>"><?php echo esc_html($cbn_tier['label']); ?></h2>
              </div>
            </header>

            <div class="cbn-sol-sponsors-grid">
              <?php foreach ($cbn_tier['sponsors'] as $cbn_sponsor) : ?>
                <?php
                ++$cbn_sponsor_index;
                get_template_part(
                    'template-parts/sponsor-card',
                    null,
                    [
                        'sponsor' => $cbn_sponsor,
                        'index' => $cbn_sponsor_index,
                        'tier_label' => $cbn_tier['label'],
                    ]
                );
                ?>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endforeach; ?>
      </div>
    </section>
  <?php else : ?>
    <section id="alianzas" class="cbn-sol-sponsors-wall cbn-sol-sponsors-wall--empty" aria-labelledby="cbn-sponsors-empty-title">
      <article class="cbn-sol-sponsors-empty" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">Muro en actualización</p>
          <h2 id="cbn-sponsors-empty-title">Alianzas en preparación.</h2>
          <p role="status">El club todavía no ha publicado patrocinadores o colaboradores activos para este periodo.</p>
          <a class="cbn-sol-button" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>Contactar con el club</a>
        </div>
      </article>
    </section>
  <?php endif; ?>

  <?php cbn_render_club_photo_story('sponsors'); ?>

  <section class="cbn-sol-sponsors-cta" aria-labelledby="cbn-sponsors-cta-title" data-sol-reveal>
    <div>
      <p class="cbn-sol-section-index">02 / Jugar en equipo</p>
      <h2 id="cbn-sponsors-cta-title">Tu marca.<br>Nuestra pista.</h2>
    </div>
    <div class="cbn-sol-sponsors-cta__copy">
      <p>Si quieres conocer las opciones de colaboración con el Club Baloncesto Navalcarnero, habla directamente con el club.</p>
      <div>
        <a class="cbn-sol-button cbn-sol-button--light cbn-sol-button--shot" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
          <span>Empezar conversación</span>
        </a>
        <a class="cbn-sol-text-link" href="mailto:administracion@cbnavalcarnero.es" data-cbn-swish>
          Escribir al club <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
        </a>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
