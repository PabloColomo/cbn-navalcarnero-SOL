<?php
/**
 * Single match row shared by the matches archive and team detail.
 *
 * Expects $args['match'] as a WP_Post of type cbn_match.
 */

if (!defined('ABSPATH')) {
    exit;
}

$cbn_match = $args['match'] ?? null;

if (!$cbn_match instanceof WP_Post) {
    return;
}

$cbn_m = cbn_get_match_meta($cbn_match->ID);
$cbn_has_score = null !== $cbn_m['home_score'] && null !== $cbn_m['away_score'];
?>
<article class="cbn-match-row">
  <div class="cbn-match-row__meta">
    <span class="cbn-match-row__status cbn-match-row__status--<?php echo esc_attr($cbn_m['status']); ?>">
      <?php echo esc_html(cbn_get_match_status_label($cbn_m['status'])); ?>
    </span>
    <span><?php echo esc_html(cbn_format_match_date($cbn_m['date'], $cbn_m['time'])); ?></span>
    <?php if ($cbn_m['round']) : ?>
      <span><?php echo esc_html($cbn_m['round']); ?></span>
    <?php endif; ?>
  </div>

  <p class="cbn-match-row__teams">
    <span><?php echo esc_html($cbn_m['home_name']); ?></span>
    <?php if ($cbn_has_score) : ?>
      <strong class="cbn-match-row__score">
        <?php echo esc_html($cbn_m['home_score'] . ' - ' . $cbn_m['away_score']); ?>
      </strong>
    <?php else : ?>
      <span class="cbn-match-row__vs" aria-hidden="true">vs</span>
      <span class="cbn-visually-hidden">contra</span>
    <?php endif; ?>
    <span><?php echo esc_html($cbn_m['away_name']); ?></span>
  </p>

  <div class="cbn-match-row__details">
    <?php if ($cbn_m['competition_name']) : ?>
      <span><?php echo esc_html($cbn_m['competition_name']); ?></span>
    <?php endif; ?>
    <?php if ($cbn_m['venue_name']) : ?>
      <span><?php echo esc_html($cbn_m['venue_name']); ?></span>
    <?php endif; ?>
  </div>

  <?php if ($cbn_m['public_notes']) : ?>
    <p class="cbn-sports-muted"><?php echo esc_html($cbn_m['public_notes']); ?></p>
  <?php endif; ?>
</article>
