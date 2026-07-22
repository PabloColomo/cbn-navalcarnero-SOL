<?php
/**
 * Pista Viva search results.
 */

global $wp_query;

$cbn_system_style_path = get_theme_file_path('assets/src/css/sol-system.css');

add_action(
    'wp_enqueue_scripts',
    static function () use ($cbn_system_style_path): void {
        wp_enqueue_style(
            'cbn-sol-system',
            get_theme_file_uri('assets/src/css/sol-system.css'),
            ['cbn-sol-experience'],
            file_exists($cbn_system_style_path)
                ? (string) filemtime($cbn_system_style_path)
                : CBN_THEME_VERSION
        );
    },
    20
);

$cbn_search_query = get_search_query(false);
$cbn_search_page = max(1, (int) get_query_var('paged'));
$cbn_search_results = [];

if (isset($wp_query->posts) && is_array($wp_query->posts)) {
    $cbn_search_results = array_values(
        array_filter(
            $wp_query->posts,
            static fn ($post): bool => $post instanceof WP_Post && 'cbn_player' !== $post->post_type
        )
    );
}

$cbn_search_result_count = count($cbn_search_results);
$cbn_search_teams_url = get_post_type_archive_link('cbn_team') ?: home_url('/equipos/');

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-system cbn-sol-system--search" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <section class="cbn-sol-system-search-hero" aria-labelledby="cbn-system-search-title">
    <div class="cbn-sol-system-search-hero__court" aria-hidden="true">
      <span></span><i></i><b></b>
    </div>

    <div class="cbn-sol-system-search-hero__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Buscador · CBN Navalcarnero</p>
      <h1 id="cbn-system-search-title">
        <?php if ('' !== trim($cbn_search_query)) : ?>
          <span>Resultados para</span>
          <em>“<?php echo esc_html($cbn_search_query); ?>”</em>
        <?php else : ?>
          <span>Buscar en</span>
          <em>el club.</em>
        <?php endif; ?>
      </h1>
    </div>

    <form class="cbn-sol-system-search cbn-sol-system-search--hero" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" data-sol-reveal>
      <label for="cbn-main-search">Buscar en la web</label>
      <div>
        <input
          id="cbn-main-search"
          type="search"
          name="s"
          value="<?php echo esc_attr($cbn_search_query); ?>"
          placeholder="Noticias, equipos, partidos…"
        >
        <button type="submit" data-cbn-swish>
          <span>Buscar</span><span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
        </button>
      </div>
    </form>
  </section>

  <section class="cbn-sol-system-results" aria-labelledby="cbn-system-results-title">
    <header class="cbn-sol-system-results__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 · Resultado de la jugada</p>
        <h2 id="cbn-system-results-title">
          <?php echo $cbn_search_results ? esc_html__('En la pista', 'cbn') : esc_html__('Sin coincidencias', 'cbn'); ?>
        </h2>
      </div>
      <div class="cbn-sol-system-results__summary">
        <span><?php echo esc_html(str_pad((string) $cbn_search_result_count, 2, '0', STR_PAD_LEFT)); ?></span>
        <p>
          <?php
          echo esc_html(
              1 === $cbn_search_result_count
                  ? 'resultado visible en esta página'
                  : 'resultados visibles en esta página'
          );
          ?>
          <?php if ($cbn_search_page > 1) : ?>
            <small><?php echo esc_html('Página ' . $cbn_search_page); ?></small>
          <?php endif; ?>
        </p>
      </div>
    </header>

    <?php if ($cbn_search_results) : ?>
      <ol class="cbn-sol-system-results__list" role="list">
        <?php foreach ($cbn_search_results as $cbn_search_index => $post) : ?>
          <?php
          setup_postdata($post);

          $cbn_search_post_type = get_post_type_object(get_post_type());
          $cbn_search_type_label = $cbn_search_post_type && isset($cbn_search_post_type->labels->singular_name)
              ? (string) $cbn_search_post_type->labels->singular_name
              : 'Contenido';
          $cbn_search_excerpt = wp_trim_words(
              wp_strip_all_tags(strip_shortcodes((string) get_the_excerpt())),
              28,
              '…'
          );
          $cbn_search_image = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
          ?>
          <li data-sol-reveal data-cbn-tilt>
            <article <?php post_class('cbn-sol-system-result-card'); ?>>
              <a href="<?php the_permalink(); ?>" data-cbn-swish>
                <span class="cbn-sol-system-result-card__index" aria-hidden="true">
                  <?php echo esc_html(str_pad((string) ($cbn_search_index + 1), 2, '0', STR_PAD_LEFT)); ?>
                </span>
                <?php if ($cbn_search_image) : ?>
                  <span class="cbn-sol-system-result-card__image" aria-hidden="true">
                    <img src="<?php echo esc_url($cbn_search_image); ?>" alt="" width="768" height="512" loading="lazy" decoding="async">
                  </span>
                <?php else : ?>
                  <span class="cbn-sol-system-result-card__placeholder" aria-hidden="true">CBN</span>
                <?php endif; ?>
                <span class="cbn-sol-system-result-card__copy">
                  <small><?php echo esc_html($cbn_search_type_label); ?></small>
                  <h3><?php echo esc_html(get_the_title()); ?></h3>
                  <?php if ('' !== $cbn_search_excerpt) : ?>
                    <span class="cbn-sol-system-result-card__excerpt"><?php echo esc_html($cbn_search_excerpt); ?></span>
                  <?php endif; ?>
                  <span class="cbn-sol-system-result-card__arrow" aria-hidden="true">Abrir <i class="cbn-sol-arrow-up-right"></i></span>
                </span>
              </a>
            </article>
          </li>
        <?php endforeach; ?>
      </ol>
      <?php wp_reset_postdata(); ?>

      <nav class="cbn-sol-system-pagination" aria-label="Paginación de resultados" data-sol-reveal>
        <?php
        the_posts_pagination(
            [
                'mid_size' => 1,
                'prev_text' => 'Anteriores',
                'next_text' => 'Siguientes',
                'screen_reader_text' => 'Paginación de resultados de búsqueda',
            ]
        );
        ?>
      </nav>
    <?php else : ?>
      <article class="cbn-sol-system-empty" aria-labelledby="cbn-system-empty-title" data-sol-reveal>
        <div>
          <p class="cbn-sol-section-index">Tiempo muerto</p>
          <h3 id="cbn-system-empty-title">No hemos encontrado resultados</h3>
          <p>Prueba con otro término o vuelve a una de las secciones principales.</p>
        </div>
        <nav aria-label="Secciones principales">
          <a href="<?php echo esc_url(home_url('/')); ?>" data-cbn-swish>Inicio <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span></a>
          <a href="<?php echo esc_url($cbn_search_teams_url); ?>" data-cbn-swish>Equipos <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span></a>
          <a href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>Contacto <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span></a>
        </nav>
      </article>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
