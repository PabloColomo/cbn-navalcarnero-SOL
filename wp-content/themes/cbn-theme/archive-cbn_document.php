<?php
/**
 * Pista Viva archive for published cbn_document entries.
 */

global $wp_query;

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

$cbn_document_count = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-info cbn-sol-documents cbn-sol-documents--archive" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <section class="cbn-sol-documents-hero" aria-labelledby="cbn-sol-documents-title">
    <div class="cbn-sol-documents-hero__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Archivo p&uacute;blico &middot; CBN</p>
      <h1 id="cbn-sol-documents-title"><?php post_type_archive_title(); ?></h1>
      <p class="cbn-sol-documents-hero__content"><?php esc_html_e('Entradas documentales publicadas por el Club Baloncesto Navalcarnero.', 'cbn'); ?></p>
      <p class="cbn-sol-documents-hero__count">
        <strong><?php echo esc_html(number_format_i18n($cbn_document_count)); ?></strong>
        <?php echo 1 === $cbn_document_count ? esc_html__('documento publicado', 'cbn') : esc_html__('documentos publicados', 'cbn'); ?>
      </p>
      <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/documentacion/')); ?>" data-cbn-swish>
        <?php esc_html_e('Ir a documentacion', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
      </a>
    </div>

    <div class="cbn-sol-documents-hero__visual" data-sol-reveal>
      <div class="cbn-sol-documents-stack" aria-hidden="true">
        <span><i></i><i></i><i></i></span>
        <span><i></i><i></i><i></i></span>
        <span><b>CBN</b><i></i><i></i><i></i></span>
      </div>
      <small><?php esc_html_e('Archivo CBN', 'cbn'); ?></small>
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

    <?php if (have_posts()) : ?>
      <div class="cbn-sol-documents-grid">
        <?php
        while (have_posts()) :
            the_post();
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

      <div class="cbn-sol-info-pagination" data-sol-reveal>
        <?php
        the_posts_pagination(
            [
                'mid_size' => 1,
                'prev_text' => esc_html__('Anteriores', 'cbn'),
                'next_text' => esc_html__('Siguientes', 'cbn'),
                'screen_reader_text' => esc_html__('Paginacion de documentos', 'cbn'),
            ]
        );
        ?>
      </div>
    <?php else : ?>
      <article class="cbn-sol-info-empty" data-sol-reveal>
        <span aria-hidden="true">00</span>
        <div>
          <p class="cbn-sol-section-index"><?php esc_html_e('Archivo pendiente', 'cbn'); ?></p>
          <h2><?php esc_html_e('Todavia no hay documentos publicos', 'cbn'); ?></h2>
          <p><?php esc_html_e('El club publicara aqui los archivos cuando esten preparados para consulta general.', 'cbn'); ?></p>
          <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/contacto/')); ?>"><?php esc_html_e('Contactar', 'cbn'); ?> <span aria-hidden="true">&#8599;</span></a>
        </div>
      </article>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
