<?php
/**
 * Generic Pista Viva page template.
 *
 * Slug-specific templates continue to take precedence in the WordPress
 * hierarchy. This surface intentionally adds no legal or factual copy.
 */

$cbn_enqueue_info_assets = static function (): void {
    $style_path = get_theme_file_path('assets/src/css/sol-info.css');
    $style_version = file_exists($style_path)
        ? (string) filemtime($style_path)
        : (string) wp_get_theme()->get('Version');

    wp_enqueue_style(
        'cbn-sol-info',
        get_theme_file_uri('assets/src/css/sol-info.css'),
        ['cbn-sol-experience'],
        $style_version
    );
};

if (did_action('wp_enqueue_scripts')) {
    $cbn_enqueue_info_assets();
} else {
    add_action('wp_enqueue_scripts', $cbn_enqueue_info_assets, 20);
}

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-info cbn-sol-info-page" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <?php if (have_posts()) : ?>
    <?php
    while (have_posts()) :
        the_post();
        $cbn_info_page_content = trim((string) get_the_content());
        ?>
      <article <?php post_class('cbn-sol-info-article'); ?>>
        <header class="cbn-sol-info-page__hero">
          <div data-sol-reveal>
            <p class="cbn-sol-section-index"><?php esc_html_e('Página informativa', 'cbn'); ?> &middot; CBN</p>
            <h1><?php the_title(); ?></h1>
          </div>
          <span class="cbn-sol-info-page__index" data-sol-reveal aria-hidden="true">CBN</span>
        </header>

        <?php if (has_post_thumbnail()) : ?>
          <figure class="cbn-sol-info-article__image" data-sol-reveal data-cbn-parallax>
            <?php the_post_thumbnail('large', ['loading' => 'eager', 'decoding' => 'async']); ?>
          </figure>
        <?php endif; ?>

        <div class="cbn-sol-info-page__body">
          <?php if ($cbn_info_page_content) : ?>
            <div class="cbn-prose cbn-sol-info-prose" data-sol-reveal>
              <?php the_content(); ?>
              <?php
              wp_link_pages(
                  [
                      'before' => '<nav class="cbn-sol-info-page-links" aria-label="' . esc_attr__('Paginas del contenido', 'cbn') . '">',
                      'after' => '</nav>',
                  ]
              );
              ?>
            </div>
          <?php else : ?>
            <div class="cbn-sol-info-inline-empty" data-sol-reveal>
              <p class="cbn-sol-section-index"><?php esc_html_e('Contenido pendiente', 'cbn'); ?></p>
              <h2><?php esc_html_e('Información pendiente de publicación', 'cbn'); ?></h2>
              <p><?php esc_html_e('Esta página todavía no contiene información publicada por el club.', 'cbn'); ?></p>
              <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/contacto/')); ?>">
                <?php esc_html_e('Contactar', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <section class="cbn-sol-info-missing" aria-labelledby="cbn-sol-info-missing-title">
      <p class="cbn-sol-section-index">CBN</p>
      <h1 id="cbn-sol-info-missing-title"><?php esc_html_e('Contenido no encontrado', 'cbn'); ?></h1>
    </section>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
