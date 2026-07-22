<?php
/**
 * Responsive, privacy-safe access to the approved CBN photo library.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_club_photo_manifest(): array
{
    static $manifest = null;

    if (is_array($manifest)) {
        return $manifest;
    }

    $manifest_path = get_theme_file_path('assets/src/images/club/manifest.json');

    if (!file_exists($manifest_path)) {
        $manifest = [];
        return $manifest;
    }

    $decoded = json_decode((string) file_get_contents($manifest_path), true);
    $manifest = is_array($decoded) ? $decoded : [];

    return $manifest;
}

function cbn_get_club_photo(string $photo_id): ?array
{
    $manifest = cbn_get_club_photo_manifest();
    $photo = $manifest['photos'][$photo_id] ?? null;

    return is_array($photo) ? $photo : null;
}

function cbn_get_club_photo_feature(string $placement, int $index = 0): ?string
{
    $photos = cbn_get_club_photo_features($placement);
    $photo_id = $photos[$index] ?? null;

    return is_string($photo_id) ? $photo_id : null;
}

function cbn_get_club_photo_features(string $placement, array $fallback = []): array
{
    $manifest = cbn_get_club_photo_manifest();
    $photos = $manifest['featured_placements'][$placement] ?? [];
    $photos = is_array($photos) ? $photos : [];
    $photos = array_values(
        array_filter(
            $photos,
            static fn (mixed $photo_id): bool => is_string($photo_id) && null !== cbn_get_club_photo($photo_id)
        )
    );

    foreach ($fallback as $index => $fallback_id) {
        if (!isset($photos[$index]) && is_string($fallback_id) && cbn_get_club_photo($fallback_id)) {
            $photos[$index] = $fallback_id;
        }
    }

    ksort($photos);

    return array_values($photos);
}

function cbn_get_club_photo_url(string $photo_id, int $width = 960, string $format = 'jpeg'): string
{
    $photo = cbn_get_club_photo($photo_id);

    if (!$photo || empty($photo['derivatives'][$format])) {
        return '';
    }

    $variants = $photo['derivatives'][$format];
    $selected = end($variants);

    foreach ($variants as $variant) {
        if ((int) ($variant['width'] ?? 0) >= $width) {
            $selected = $variant;
            break;
        }
    }

    if (!is_array($selected) || empty($selected['path'])) {
        return '';
    }

    return get_theme_file_uri('assets/src/images/club/' . ltrim((string) $selected['path'], '/'));
}

function cbn_get_club_photo_srcset(array $photo, string $format): string
{
    $variants = $photo['derivatives'][$format] ?? [];
    $sources = [];

    foreach ($variants as $variant) {
        if (empty($variant['path']) || empty($variant['width'])) {
            continue;
        }

        $sources[] = sprintf(
            '%s %dw',
            get_theme_file_uri('assets/src/images/club/' . ltrim((string) $variant['path'], '/')),
            (int) $variant['width']
        );
    }

    return implode(', ', $sources);
}

function cbn_render_club_photo(string $photo_id, array $args = []): void
{
    $photo = cbn_get_club_photo($photo_id);

    if (!$photo) {
        return;
    }

    $defaults = [
        'alt' => $photo['alt'] ?? '',
        'class' => '',
        'picture_class' => '',
        'sizes' => '(max-width: 760px) 78vw, 24vw',
        'loading' => 'lazy',
        'fetchpriority' => '',
        'decorative' => false,
        'focal_position' => $photo['focal_position'] ?? '50% 50%',
    ];
    $args = wp_parse_args($args, $defaults);
    $args['alt'] = $args['decorative'] ? '' : (string) $args['alt'];

    $jpeg_variants = $photo['derivatives']['jpeg'] ?? [];
    $fallback = $jpeg_variants ? end($jpeg_variants) : null;

    if (!is_array($fallback) || empty($fallback['path'])) {
        return;
    }

    $picture_classes = trim('cbn-club-photo ' . (string) $args['picture_class']);
    $fetchpriority = in_array($args['fetchpriority'], ['high', 'low', 'auto'], true)
        ? (string) $args['fetchpriority']
        : '';
    ?>
    <picture
      class="<?php echo esc_attr($picture_classes); ?>"
      data-cbn-photo-id="<?php echo esc_attr($photo_id); ?>"
      style="--cbn-photo-position: <?php echo esc_attr((string) $args['focal_position']); ?>"
    >
      <source
        type="image/avif"
        srcset="<?php echo esc_attr(cbn_get_club_photo_srcset($photo, 'avif')); ?>"
        sizes="<?php echo esc_attr((string) $args['sizes']); ?>"
      >
      <source
        type="image/webp"
        srcset="<?php echo esc_attr(cbn_get_club_photo_srcset($photo, 'webp')); ?>"
        sizes="<?php echo esc_attr((string) $args['sizes']); ?>"
      >
      <img
        class="<?php echo esc_attr((string) $args['class']); ?>"
        src="<?php echo esc_url(get_theme_file_uri('assets/src/images/club/' . $fallback['path'])); ?>"
        srcset="<?php echo esc_attr(cbn_get_club_photo_srcset($photo, 'jpeg')); ?>"
        sizes="<?php echo esc_attr((string) $args['sizes']); ?>"
        alt="<?php echo esc_attr((string) $args['alt']); ?>"
        width="<?php echo esc_attr((string) (int) $fallback['width']); ?>"
        height="<?php echo esc_attr((string) (int) $fallback['height']); ?>"
        loading="<?php echo esc_attr((string) $args['loading']); ?>"
        decoding="async"
        <?php if ($fetchpriority) : ?>fetchpriority="<?php echo esc_attr($fetchpriority); ?>"<?php endif; ?>
      >
    </picture>
    <?php
}

function cbn_render_club_photo_story(string $story_name): void
{
    $manifest = cbn_get_club_photo_manifest();
    $story = $manifest['stories'][$story_name] ?? null;

    if (!is_array($story) || empty($story['photos'])) {
        return;
    }

    $heading_id = 'cbn-photo-story-' . sanitize_html_class($story_name);
    ?>
    <section class="cbn-photo-story cbn-photo-story--<?php echo esc_attr(sanitize_html_class($story_name)); ?>" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
      <header class="cbn-photo-story__header" data-sol-reveal>
        <p><?php echo esc_html((string) ($story['eyebrow'] ?? 'CBN')); ?></p>
        <h2 id="<?php echo esc_attr($heading_id); ?>"><?php echo esc_html((string) ($story['title'] ?? 'Club Baloncesto Navalcarnero')); ?></h2>
      </header>
      <div class="cbn-photo-story__rail" tabindex="0" role="region" aria-labelledby="<?php echo esc_attr($heading_id); ?>">
        <?php foreach ($story['photos'] as $photo_id) : ?>
          <?php $photo = cbn_get_club_photo((string) $photo_id); ?>
          <?php if (!$photo) : continue; endif; ?>
          <figure class="cbn-photo-story__item" data-sol-reveal>
            <?php
            cbn_render_club_photo(
                (string) $photo_id,
                [
                    'sizes' => '(max-width: 760px) 82vw, (max-width: 1180px) 48vw, 42vw',
                    'loading' => 'lazy',
                ]
            );
            ?>
            <span class="cbn-photo-story__caption" aria-hidden="true">
              <span class="cbn-photo-story__credit">&copy; CBN</span>
            </span>
          </figure>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
}
