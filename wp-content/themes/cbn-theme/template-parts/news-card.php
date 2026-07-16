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
$cbn_news_thumbnail_id = get_post_thumbnail_id();
$cbn_news_fallback_ids = cbn_get_club_photo_features('news_fallbacks', ['club-002']);

if (!$cbn_news_fallback_ids) {
    $cbn_news_fallback_ids = ['club-002'];
}

$cbn_news_photo_id = $cbn_news_thumbnail_id
    ? null
    : $cbn_news_fallback_ids[$cbn_news_card_index % count($cbn_news_fallback_ids)];
$cbn_news_card_class = 0 === $cbn_news_card_index ? ' cbn-sol-news-card--lead' : '';
?>
<article <?php post_class('cbn-sol-news-card' . $cbn_news_card_class); ?> data-sol-reveal data-cbn-tilt>
  <a href="<?php the_permalink(); ?>" data-cbn-swish>
    <span class="cbn-sol-news-card__image" aria-hidden="true">
      <?php if ($cbn_news_thumbnail_id) : ?>
        <?php
        echo wp_get_attachment_image(
            $cbn_news_thumbnail_id,
            'large',
            false,
            [
                'alt' => '',
                'loading' => 'lazy',
                'decoding' => 'async',
                'sizes' => '(max-width: 760px) 100vw, 44vw',
            ]
        );
        ?>
      <?php else : ?>
        <?php cbn_render_club_photo($cbn_news_photo_id, ['decorative' => true, 'sizes' => '(max-width: 760px) 100vw, 44vw']); ?>
      <?php endif; ?>
      <span class="cbn-photo-credit">&copy; CBN</span>
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
