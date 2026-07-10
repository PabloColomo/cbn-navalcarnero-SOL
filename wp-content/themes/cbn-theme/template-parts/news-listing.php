<?php
/**
 * Shared native WordPress news loop, pagination and empty state.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (have_posts()) :
    $cbn_news_card_index = 0;
    ?>
  <div class="cbn-sol-news-grid">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part(
            'template-parts/news-card',
            null,
            ['index' => $cbn_news_card_index]
        );
        $cbn_news_card_index++;
    endwhile;
    ?>
  </div>

  <div class="cbn-sol-news-pagination" data-sol-reveal>
    <?php
    the_posts_pagination(
        [
            'mid_size' => 1,
            'prev_text' => esc_html__('Anteriores', 'cbn'),
            'next_text' => esc_html__('Siguientes', 'cbn'),
            'screen_reader_text' => esc_html__('Paginacion de noticias', 'cbn'),
            'class' => 'cbn-pagination',
        ]
    );
    ?>
  </div>
<?php else : ?>
  <article class="cbn-sol-news-empty" data-sol-reveal>
    <span aria-hidden="true">00</span>
    <div>
      <p class="cbn-sol-section-index"><?php esc_html_e('Tiempo muerto', 'cbn'); ?></p>
      <h2><?php esc_html_e('Todavía no hay noticias', 'cbn'); ?></h2>
      <p><?php esc_html_e('Estamos preparando comunicados y noticias del club. Vuelve pronto.', 'cbn'); ?></p>
    </div>
  </article>
<?php endif; ?>
