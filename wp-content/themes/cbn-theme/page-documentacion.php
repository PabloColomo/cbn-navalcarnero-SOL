<?php
/**
 * Pista Viva landing page for public club documentation.
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

$cbn_document_page_id = 0;
$cbn_document_page_title = __('Documentacion', 'cbn');
$cbn_document_page_content = '';
$cbn_document_page_image_id = 0;

if (have_posts()) {
    the_post();
    $cbn_document_page_id = get_the_ID();
    $cbn_document_page_title = get_the_title() ?: $cbn_document_page_title;
    $cbn_document_page_content = trim((string) get_the_content());
    $cbn_document_page_image_id = get_post_thumbnail_id();
}
$cbn_document_paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
$cbn_documents = new WP_Query(
    [
        'post_type' => 'cbn_document',
        'post_status' => 'publish',
        'posts_per_page' => 9,
        'paged' => $cbn_document_paged,
    ]
);

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-info cbn-sol-documents" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <section class="cbn-sol-documents-hero" aria-labelledby="cbn-sol-documents-title">
    <div class="cbn-sol-documents-hero__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Archivo p&uacute;blico &middot; CBN</p>
      <h1 id="cbn-sol-documents-title"><?php echo esc_html($cbn_document_page_title); ?></h1>
      <?php if ($cbn_document_page_content) : ?>
        <div class="cbn-sol-documents-hero__content">
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
        <p class="cbn-sol-documents-hero__content"><?php esc_html_e('Consulta aqui los documentos que el club publique para acceso general.', 'cbn'); ?></p>
      <?php endif; ?>
      <p class="cbn-sol-documents-hero__count">
        <strong><?php echo esc_html(number_format_i18n($cbn_documents->found_posts)); ?></strong>
        <?php echo 1 === (int) $cbn_documents->found_posts ? esc_html__('documento publicado', 'cbn') : esc_html__('documentos publicados', 'cbn'); ?>
      </p>
    </div>

    <div class="cbn-sol-documents-hero__visual" data-sol-reveal<?php echo $cbn_document_page_image_id ? ' data-cbn-parallax' : ''; ?>>
      <?php if ($cbn_document_page_image_id) : ?>
        <?php echo wp_get_attachment_image($cbn_document_page_image_id, 'large', false, ['loading' => 'eager', 'decoding' => 'async']); ?>
      <?php else : ?>
        <div class="cbn-sol-documents-stack" aria-hidden="true">
          <span><i></i><i></i><i></i></span>
          <span><i></i><i></i><i></i></span>
          <span><b>CBN</b><i></i><i></i><i></i></span>
        </div>
      <?php endif; ?>
      <small><?php echo $cbn_document_page_image_id ? esc_html__('Imagen editorial', 'cbn') : esc_html__('Archivo CBN', 'cbn'); ?></small>
    </div>
  </section>

  <section class="cbn-sol-documents-feed" aria-labelledby="cbn-sol-documents-feed-title">
    <header class="cbn-sol-info-heading" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 &middot; Publicaciones</p>
        <h2 id="cbn-sol-documents-feed-title"><?php esc_html_e('Documentos disponibles', 'cbn'); ?></h2>
      </div>
      <p><?php esc_html_e('Solo se muestran entradas publicadas por el club.', 'cbn'); ?></p>
    </header>

    <?php if ($cbn_documents->have_posts()) : ?>
      <div class="cbn-sol-documents-grid">
        <?php
        while ($cbn_documents->have_posts()) :
            $cbn_documents->the_post();
            $cbn_document_summary = has_excerpt()
                ? get_the_excerpt()
                : wp_trim_words(wp_strip_all_tags(get_the_content()), 26);
            ?>
          <article <?php post_class('cbn-sol-document-card'); ?> data-sol-reveal data-cbn-tilt>
            <a href="<?php the_permalink(); ?>" data-cbn-swish>
              <span class="cbn-sol-document-card__media" aria-hidden="true">
                <?php if (has_post_thumbnail()) : ?>
                  <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'decoding' => 'async', 'alt' => '']); ?>
                <?php else : ?>
                  <span class="cbn-sol-document-card__paper"><i></i><i></i><i></i></span>
                <?php endif; ?>
                <strong>DOC</strong>
              </span>
              <span class="cbn-sol-document-card__copy">
                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
                <h3><?php echo esc_html(get_the_title()); ?></h3>
                <?php if ($cbn_document_summary) : ?><span><?php echo esc_html($cbn_document_summary); ?></span><?php endif; ?>
                <b>Ver documento <i aria-hidden="true">&#8599;</i></b>
              </span>
            </a>
          </article>
        <?php endwhile; ?>
      </div>

      <?php
      $cbn_document_pagination = paginate_links(
          [
              'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
              'current' => $cbn_document_paged,
              'total' => (int) $cbn_documents->max_num_pages,
              'mid_size' => 1,
              'prev_text' => esc_html__('Anteriores', 'cbn'),
              'next_text' => esc_html__('Siguientes', 'cbn'),
              'type' => 'list',
          ]
      );
      ?>
      <?php if ($cbn_document_pagination) : ?>
        <nav class="cbn-sol-info-pagination" aria-label="<?php esc_attr_e('Paginacion de documentos', 'cbn'); ?>" data-sol-reveal>
          <?php echo wp_kses_post($cbn_document_pagination); ?>
        </nav>
      <?php endif; ?>
    <?php else : ?>
      <article class="cbn-sol-info-empty" data-sol-reveal>
        <span aria-hidden="true">00</span>
        <div>
          <p class="cbn-sol-section-index"><?php esc_html_e('Archivo pendiente', 'cbn'); ?></p>
          <h2><?php esc_html_e('Todavía no hay documentos públicos', 'cbn'); ?></h2>
          <p><?php esc_html_e('El club publicara aqui los archivos cuando esten preparados para consulta general.', 'cbn'); ?></p>
          <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/contacto/')); ?>"><?php esc_html_e('Contactar', 'cbn'); ?> <span aria-hidden="true">&#8599;</span></a>
        </div>
      </article>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </section>
</main>

<?php get_footer(); ?>
