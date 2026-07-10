<?php
/**
 * Pista Viva single template for native club news and communications.
 * Comments remain intentionally absent from this editorial surface.
 */

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

<main id="primary" class="cbn-sol cbn-sol-news cbn-sol-news--single" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <?php
  while (have_posts()) :
      the_post();
      $cbn_news_categories = get_the_category();
      $cbn_previous_post = get_previous_post();
      $cbn_next_post = get_next_post();
      ?>
    <article <?php post_class('cbn-sol-news-article'); ?>>
      <header class="cbn-sol-news-article__hero">
        <div class="cbn-sol-news-article__headline" data-sol-reveal>
          <a class="cbn-sol-news-article__back" href="<?php echo esc_url(home_url('/noticias/')); ?>">
            <span aria-hidden="true">&#8592;</span> <?php esc_html_e('Archivo de noticias', 'cbn'); ?>
          </a>

          <?php if ($cbn_news_categories) : ?>
            <p class="cbn-sol-news-article__categories">
              <?php foreach ($cbn_news_categories as $cbn_news_category_index => $cbn_news_category) : ?>
                <?php if ($cbn_news_category_index > 0) : ?><span aria-hidden="true">&middot;</span><?php endif; ?>
                <a href="<?php echo esc_url(get_category_link($cbn_news_category->term_id)); ?>">
                  <?php echo esc_html($cbn_news_category->name); ?>
                </a>
              <?php endforeach; ?>
            </p>
          <?php endif; ?>

          <h1><?php the_title(); ?></h1>
          <p class="cbn-sol-news-article__meta">
            <span><?php esc_html_e('Publicado', 'cbn'); ?></span>
            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
          </p>
        </div>

        <div class="cbn-sol-news-article__press" data-sol-reveal aria-hidden="true">
          <span>CBN</span>
          <strong>Desde<br>la banda</strong>
          <small>Navalcarnero</small>
        </div>
      </header>

      <?php if (has_post_thumbnail()) : ?>
        <figure class="cbn-sol-news-article__image" data-sol-reveal data-cbn-parallax>
          <?php the_post_thumbnail('large', ['loading' => 'eager', 'decoding' => 'async']); ?>
          <span aria-hidden="true">Actualidad CBN</span>
        </figure>
      <?php endif; ?>

      <div class="cbn-sol-news-article__body">
        <aside class="cbn-sol-news-article__rail" data-sol-reveal aria-label="Datos del articulo">
          <span>CBN</span>
          <small><?php echo esc_html(get_the_date('Y')); ?></small>
          <i aria-hidden="true"></i>
        </aside>

        <div class="cbn-prose cbn-sol-news-prose" data-sol-reveal>
          <?php the_content(); ?>
          <?php
          wp_link_pages(
              [
                  'before' => '<nav class="cbn-sol-news-page-links" aria-label="' . esc_attr__('Paginas del articulo', 'cbn') . '">',
                  'after' => '</nav>',
              ]
          );
          ?>
        </div>
      </div>

      <footer class="cbn-sol-news-article__footer">
        <?php if ($cbn_previous_post || $cbn_next_post) : ?>
          <nav class="cbn-sol-news-post-nav" aria-label="<?php esc_attr_e('Navegacion entre noticias', 'cbn'); ?>">
            <?php if ($cbn_previous_post) : ?>
              <a href="<?php echo esc_url(get_permalink($cbn_previous_post)); ?>" data-sol-reveal data-cbn-swish>
                <small><span aria-hidden="true">&#8592;</span> <?php esc_html_e('Anterior', 'cbn'); ?></small>
                <strong><?php echo esc_html(get_the_title($cbn_previous_post)); ?></strong>
              </a>
            <?php endif; ?>
            <?php if ($cbn_next_post) : ?>
              <a class="cbn-sol-news-post-nav__next" href="<?php echo esc_url(get_permalink($cbn_next_post)); ?>" data-sol-reveal data-cbn-swish>
                <small><?php esc_html_e('Siguiente', 'cbn'); ?> <span aria-hidden="true">&#8594;</span></small>
                <strong><?php echo esc_html(get_the_title($cbn_next_post)); ?></strong>
              </a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>

        <a class="cbn-sol-button" href="<?php echo esc_url(home_url('/noticias/')); ?>" data-cbn-swish>
          <?php esc_html_e('Volver a noticias', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
        </a>
      </footer>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
