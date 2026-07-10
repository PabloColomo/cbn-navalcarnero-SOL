<?php get_header(); ?>

<main id="primary" class="cbn-section">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('template-parts/content', get_post_type()); ?>
    <?php endwhile; ?>
  <?php else : ?>
    <article class="cbn-card">
      <h1><?php esc_html_e('Contenido no encontrado', 'cbn'); ?></h1>
      <p><?php esc_html_e('Todavía no hay contenido publicado para esta sección.', 'cbn'); ?></p>
    </article>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
