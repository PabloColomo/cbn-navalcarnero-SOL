<?php
/**
 * Pista Viva category archive for native WordPress news.
 */

global $wp_query;

$cbn_news_category_title = single_cat_title('', false);
$cbn_news_category_description = category_description();
$cbn_news_total = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;

$cbn_enqueue_news_assets = static function (): void {
    $style_path = get_theme_file_path('assets/src/css/sol-news.css');
    $style_version = file_exists($style_path)
        ? (string) filemtime($style_path)
        : (string) wp_get_theme()->get('Version');

    wp_enqueue_style(
        'cbn-sol-news',
        get_theme_file_uri('assets/src/css/sol-news.css'),
        ['cbn-sol-experience'],
        $style_version
    );
};

if (did_action('wp_enqueue_scripts')) {
    $cbn_enqueue_news_assets();
} else {
    add_action('wp_enqueue_scripts', $cbn_enqueue_news_assets, 20);
}

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-news cbn-sol-news--archive cbn-sol-news--category" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>
  <div class="cbn-sol-news__route" aria-hidden="true"><span></span><i></i><b></b></div>

  <section class="cbn-sol-news-hero" aria-labelledby="cbn-sol-news-title">
    <div class="cbn-sol-news-hero__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Categor&iacute;a &middot; Archivo CBN</p>
      <h1 id="cbn-sol-news-title"><?php echo esc_html($cbn_news_category_title); ?></h1>
      <?php if ($cbn_news_category_description) : ?>
        <div class="cbn-sol-news-hero__description"><?php echo wp_kses_post($cbn_news_category_description); ?></div>
      <?php else : ?>
        <p><?php esc_html_e('Una seleccion de publicaciones del Club Baloncesto Navalcarnero.', 'cbn'); ?></p>
      <?php endif; ?>
      <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/noticias/')); ?>" data-cbn-swish>
        <?php esc_html_e('Todas las noticias', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
      </a>
    </div>
    <div class="cbn-sol-news-hero__edition" data-sol-reveal aria-label="Resumen de la categoría">
      <span>CBN</span>
      <div>
        <small>Canal seleccionado</small>
        <strong><?php echo esc_html($cbn_news_category_title); ?></strong>
      </div>
      <p><b><?php echo esc_html(number_format_i18n($cbn_news_total)); ?></b> <?php echo 1 === $cbn_news_total ? esc_html__('publicacion', 'cbn') : esc_html__('publicaciones', 'cbn'); ?></p>
    </div>
  </section>

  <section class="cbn-sol-news-feed" aria-labelledby="cbn-sol-news-feed-title">
    <header class="cbn-sol-news-feed__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 &middot; Selecci&oacute;n</p>
        <h2 id="cbn-sol-news-feed-title"><?php echo esc_html($cbn_news_category_title); ?></h2>
      </div>
      <p><?php esc_html_e('Explora esta categoría o cambia de canal.', 'cbn'); ?></p>
    </header>

    <?php get_template_part('template-parts/news-filters'); ?>
    <?php get_template_part('template-parts/news-listing'); ?>
  </section>

  <?php cbn_render_club_photo_story('news'); ?>
</main>

<?php get_footer(); ?>
