<?php
/**
 * Link-based category filters for native WordPress news.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_news_cats = get_categories(['hide_empty' => true]);

if (!$cbn_news_cats) {
    return;
}
?>
<nav class="cbn-sol-news-filters" aria-label="<?php esc_attr_e('Filtrar noticias por categoría', 'cbn'); ?>" data-sol-reveal>
  <ul>
    <li>
      <a
        class="cbn-sol-news-filters__item<?php echo !is_category() ? ' is-active' : ''; ?>"
        href="<?php echo esc_url(home_url('/noticias/')); ?>"
        <?php echo !is_category() ? 'aria-current="page"' : ''; ?>
      >
        <span><?php esc_html_e('Todas', 'cbn'); ?></span><i aria-hidden="true">&#8599;</i>
      </a>
    </li>
    <?php foreach ($cbn_news_cats as $cbn_news_cat) : ?>
      <li>
        <a
          class="cbn-sol-news-filters__item<?php echo is_category($cbn_news_cat->term_id) ? ' is-active' : ''; ?>"
          href="<?php echo esc_url(get_category_link($cbn_news_cat->term_id)); ?>"
          <?php echo is_category($cbn_news_cat->term_id) ? 'aria-current="page"' : ''; ?>
        >
          <span><?php echo esc_html($cbn_news_cat->name); ?></span>
          <small aria-label="<?php echo esc_attr(sprintf(_n('%s publicacion', '%s publicaciones', $cbn_news_cat->count, 'cbn'), number_format_i18n($cbn_news_cat->count))); ?>">
            <?php echo esc_html(number_format_i18n($cbn_news_cat->count)); ?>
          </small>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
