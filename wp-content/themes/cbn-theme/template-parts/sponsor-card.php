<?php
/**
 * Pista Viva sponsor card for the tier-grouped sponsor archive.
 *
 * Expects $args['sponsor'] as built by cbn_get_sponsor_card(). Optional
 * $args['index'] and $args['tier_label'] add editorial context only.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_sponsor = $args['sponsor'] ?? null;

if (!is_array($cbn_sponsor) || empty($cbn_sponsor['id'])) {
    return;
}

$cbn_sponsor_index = isset($args['index']) ? (int) $args['index'] : 0;
$cbn_sponsor_number = $cbn_sponsor_index > 0
    ? str_pad((string) $cbn_sponsor_index, 2, '0', STR_PAD_LEFT)
    : 'CBN';
$cbn_tier_label = trim((string) ($args['tier_label'] ?? $cbn_sponsor['tier_name'] ?? ''));
$cbn_sponsor_permalink = get_permalink((int) $cbn_sponsor['id']);
?>
<article class="cbn-sol-sponsor-card" data-cbn-tilt>
  <div class="cbn-sol-sponsor-card__topline">
    <span><?php echo esc_html($cbn_sponsor_number); ?></span>
    <?php if ('' !== $cbn_tier_label) : ?>
      <small><?php echo esc_html($cbn_tier_label); ?></small>
    <?php endif; ?>
  </div>

  <div class="cbn-sol-sponsor-card__logo">
    <?php if ('' !== $cbn_sponsor['logo']) : ?>
      <img
        src="<?php echo esc_url($cbn_sponsor['logo']); ?>"
        alt="<?php echo esc_attr($cbn_sponsor['logo_alt'] ?: $cbn_sponsor['display_name']); ?>"
        loading="lazy"
        decoding="async"
      >
    <?php else : ?>
      <span class="cbn-sol-sponsor-card__fallback" aria-hidden="true">
        <?php echo esc_html(cbn_get_sponsor_initials($cbn_sponsor['display_name'])); ?>
      </span>
    <?php endif; ?>
  </div>

  <div class="cbn-sol-sponsor-card__body">
    <h3>
      <a href="<?php echo esc_url($cbn_sponsor_permalink); ?>" data-cbn-swish>
        <?php echo esc_html($cbn_sponsor['display_name']); ?>
      </a>
    </h3>
    <?php if ('' !== $cbn_sponsor['description']) : ?>
      <p><?php echo esc_html($cbn_sponsor['description']); ?></p>
    <?php endif; ?>
  </div>

  <div class="cbn-sol-sponsor-card__actions">
    <a href="<?php echo esc_url($cbn_sponsor_permalink); ?>" data-cbn-swish>
      Conocer la alianza <span aria-hidden="true">→</span>
    </a>
    <?php if ('' !== $cbn_sponsor['url']) : ?>
      <a href="<?php echo esc_url($cbn_sponsor['url']); ?>" rel="external noopener" data-cbn-swish>
        Visitar web <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
      </a>
    <?php endif; ?>
  </div>
</article>
