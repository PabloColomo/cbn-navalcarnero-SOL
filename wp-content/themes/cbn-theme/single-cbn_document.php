<?php
/**
 * Pista Viva single view for a published cbn_document entry.
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

<main id="primary" class="cbn-sol cbn-sol-info cbn-sol-document-single" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <?php
  while (have_posts()) :
      the_post();
      $cbn_document_content = trim((string) get_the_content());
      $cbn_previous_document = get_previous_post();
      $cbn_next_document = get_next_post();
      ?>
    <article <?php post_class('cbn-sol-info-article'); ?>>
      <header class="cbn-sol-info-article__hero">
        <div data-sol-reveal>
          <a class="cbn-sol-info-back" href="<?php echo esc_url(get_post_type_archive_link('cbn_document')); ?>">
            <span aria-hidden="true">&#8592;</span> <?php esc_html_e('Archivo de documentos', 'cbn'); ?>
          </a>
          <p class="cbn-sol-section-index">Documento p&uacute;blico &middot; CBN</p>
          <h1><?php the_title(); ?></h1>
          <p class="cbn-sol-info-article__date">
            <span><?php esc_html_e('Publicado', 'cbn'); ?></span>
            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
          </p>
        </div>
        <div class="cbn-sol-info-article__mark" data-sol-reveal aria-hidden="true"><span>DOC</span><strong>CBN</strong><small>Archivo</small></div>
      </header>

      <?php if (has_post_thumbnail()) : ?>
        <figure class="cbn-sol-info-article__image" data-sol-reveal data-cbn-parallax>
          <?php the_post_thumbnail('large', ['loading' => 'eager', 'decoding' => 'async']); ?>
        </figure>
      <?php endif; ?>

      <div class="cbn-sol-info-article__body">
        <aside data-sol-reveal aria-hidden="true"><span>CBN</span><i></i></aside>
        <?php if ($cbn_document_content) : ?>
          <div class="cbn-prose cbn-sol-info-prose" data-sol-reveal>
            <?php the_content(); ?>
            <?php
            wp_link_pages(
                [
                    'before' => '<nav class="cbn-sol-info-page-links" aria-label="' . esc_attr__('Paginas del documento', 'cbn') . '">',
                    'after' => '</nav>',
                ]
            );
            ?>
          </div>
        <?php else : ?>
          <div class="cbn-sol-info-inline-empty" data-sol-reveal>
            <p class="cbn-sol-section-index"><?php esc_html_e('Contenido pendiente', 'cbn'); ?></p>
            <h2><?php esc_html_e('Este documento no tiene contenido publicado', 'cbn'); ?></h2>
            <p><?php esc_html_e('Consulta el archivo general o contacta con el club si necesitas informacion.', 'cbn'); ?></p>
          </div>
        <?php endif; ?>
      </div>

      <footer class="cbn-sol-info-article__footer">
        <?php if ($cbn_previous_document || $cbn_next_document) : ?>
          <nav class="cbn-sol-info-post-nav" aria-label="<?php esc_attr_e('Navegacion entre documentos', 'cbn'); ?>">
            <?php if ($cbn_previous_document) : ?>
              <a href="<?php echo esc_url(get_permalink($cbn_previous_document)); ?>" data-sol-reveal>
                <small><span aria-hidden="true">&#8592;</span> <?php esc_html_e('Anterior', 'cbn'); ?></small>
                <strong><?php echo esc_html(get_the_title($cbn_previous_document)); ?></strong>
              </a>
            <?php endif; ?>
            <?php if ($cbn_next_document) : ?>
              <a class="cbn-sol-info-post-nav__next" href="<?php echo esc_url(get_permalink($cbn_next_document)); ?>" data-sol-reveal>
                <small><?php esc_html_e('Siguiente', 'cbn'); ?> <span aria-hidden="true">&#8594;</span></small>
                <strong><?php echo esc_html(get_the_title($cbn_next_document)); ?></strong>
              </a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
        <a class="cbn-sol-button" href="<?php echo esc_url(home_url('/documentacion/')); ?>" data-cbn-swish>
          <?php esc_html_e('Volver a documentacion', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
        </a>
      </footer>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
