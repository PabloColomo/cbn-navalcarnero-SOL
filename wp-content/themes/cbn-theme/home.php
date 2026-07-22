<?php
/**
 * Pista Viva posts index for the club's native WordPress news.
 */

global $wp_query;

$cbn_news_page_id = (int) get_option('page_for_posts');
$cbn_news_title = $cbn_news_page_id ? get_the_title($cbn_news_page_id) : '';
$cbn_news_title = $cbn_news_title ?: __('Noticias', 'cbn');
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

<main id="primary" class="cbn-sol cbn-sol-news cbn-sol-news--archive" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>
  <section class="cbn-sol-news-hero" aria-labelledby="cbn-sol-news-title">
    <div class="cbn-sol-news-hero__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Actualidad &middot; Club Baloncesto Navalcarnero</p>
      <h1 id="cbn-sol-news-title"><?php echo esc_html($cbn_news_title); ?></h1>
      <p>Noticias, comunicados y cr&oacute;nicas para seguir el pulso del club dentro y fuera de la pista.</p>
    </div>
    <div class="cbn-sol-news-hero__edition" data-sol-reveal aria-label="Resumen del archivo">
      <span>CBN</span>
      <div>
        <small>Edici&oacute;n digital</small>
        <strong>Desde<br>la banda</strong>
      </div>
      <p><b><?php echo esc_html(number_format_i18n($cbn_news_total)); ?></b> <?php echo 1 === $cbn_news_total ? esc_html__('publicacion', 'cbn') : esc_html__('publicaciones', 'cbn'); ?></p>
    </div>
  </section>

  <section class="cbn-sol-news-feed" aria-labelledby="cbn-sol-news-feed-title">
    <header class="cbn-sol-news-feed__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 &middot; Archivo</p>
        <h2 id="cbn-sol-news-feed-title"><?php esc_html_e('Últimas noticias', 'cbn'); ?></h2>
      </div>
      <p>Selecciona una categor&iacute;a o recorre todas las publicaciones.</p>
    </header>

    <?php get_template_part('template-parts/news-filters'); ?>
    <?php get_template_part('template-parts/news-listing'); ?>
  </section>

  <?php cbn_render_club_photo_story('news'); ?>
</main>

<?php get_footer(); ?>
