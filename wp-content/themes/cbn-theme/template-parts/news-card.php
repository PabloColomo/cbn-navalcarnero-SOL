<?php
/**
 * Editorial card for the native WordPress news loop.
 *
 * @var array $args Template arguments. The first item is presented as lead.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_news_card_index = isset($args['index']) ? (int) $args['index'] : 0;
$cbn_news_categories = get_the_category();
$cbn_news_category_name = $cbn_news_categories ? $cbn_news_categories[0]->name : __('Club', 'cbn');
$cbn_news_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
$cbn_news_card_class = 0 === $cbn_news_card_index ? ' cbn-sol-news-card--lead' : '';

if (!$cbn_news_image) {
    $cbn_news_image = get_theme_file_uri('assets/src/images/home-team-community.png');
}
?>
<article <?php post_class('cbn-sol-news-card' . $cbn_news_card_class); ?> data-sol-reveal data-cbn-tilt>
  <a href="<?php the_permalink(); ?>" data-cbn-swish>
    <span class="cbn-sol-news-card__image" aria-hidden="true">
      <img
        src="<?php echo esc_url($cbn_news_image); ?>"
        alt=""
        width="1672"
        height="941"
        loading="<?php echo 0 === $cbn_news_card_index ? 'eager' : 'lazy'; ?>"
        decoding="async"
      >
      <span><?php echo esc_html(str_pad((string) ($cbn_news_card_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
    </span>
    <span class="cbn-sol-news-card__copy">
      <span class="cbn-sol-news-card__meta">
        <strong><?php echo esc_html($cbn_news_category_name); ?></strong>
        <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date('j F Y')); ?></time>
      </span>
      <h3><?php echo esc_html(get_the_title()); ?></h3>
      <span class="cbn-sol-news-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></span>
      <span class="cbn-sol-news-card__arrow" aria-hidden="true">Leer noticia <i>&#8599;</i></span>
    </span>
  </a>
</article>
