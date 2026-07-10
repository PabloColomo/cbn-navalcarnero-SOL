<article <?php post_class('cbn-card'); ?> data-cbn-reveal>
  <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
  <?php if (has_post_thumbnail()) : ?>
    <a href="<?php the_permalink(); ?>">
      <?php the_post_thumbnail('large'); ?>
    </a>
  <?php endif; ?>
  <div>
    <?php the_excerpt(); ?>
  </div>
</article>
