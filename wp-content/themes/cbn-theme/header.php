<!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <script>document.documentElement.classList.add('cbn-js');</script>

    <a class="cbn-skip-link" href="#primary">Saltar al contenido</a>

    <header class="cbn-site-header" data-cbn-header>
      <a class="cbn-brand" href="<?php echo esc_url(home_url('/')); ?>">
        <img
          class="cbn-brand-mark"
          src="<?php echo esc_url(get_theme_file_uri('assets/src/images/cbn-logo.png')); ?>"
          alt=""
          width="400"
          height="400"
        >
        <span class="cbn-brand-text">
          <span>Club Baloncesto</span>
          <span>Navalcarnero</span>
        </span>
      </a>

      <button
        class="cbn-nav-toggle"
        type="button"
        aria-label="Abrir o cerrar el menú principal"
        aria-expanded="false"
        aria-controls="cbn-primary-nav"
        data-cbn-nav-toggle
      >
        <span class="cbn-nav-toggle__bars" aria-hidden="true">
          <span></span>
          <span></span>
          <span></span>
        </span>
        <span class="cbn-nav-toggle__label">Menú</span>
      </button>

      <?php if (has_nav_menu('primary')) : ?>
        <?php
        wp_nav_menu(
            [
                'theme_location' => 'primary',
                'container' => 'nav',
                'container_class' => 'cbn-primary-nav',
                'container_id' => 'cbn-primary-nav',
                'container_aria_label' => 'Menú principal',
                'menu_class' => 'cbn-primary-nav__list',
                'depth' => 1,
                'fallback_cb' => false,
            ]
        );
        ?>
      <?php else : ?>
        <nav id="cbn-primary-nav" class="cbn-primary-nav" aria-label="Menú principal">
          <ul class="cbn-primary-nav__list">
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a></li>
            <li><a href="<?php echo esc_url(home_url('/el-club/')); ?>">El club</a></li>
            <li><a href="<?php echo esc_url(home_url('/equipos/')); ?>">Equipos</a></li>
            <li><a href="<?php echo esc_url(home_url('/partidos/')); ?>">Partidos</a></li>
            <li><a href="<?php echo esc_url(home_url('/noticias/')); ?>">Noticias</a></li>
            <li><a href="<?php echo esc_url(home_url('/tienda/')); ?>">Tienda</a></li>
            <li><a href="<?php echo esc_url(home_url('/sponsors/')); ?>">Sponsors</a></li>
            <li><a href="<?php echo esc_url(home_url('/contacto/')); ?>">Contacto</a></li>
          </ul>
        </nav>
      <?php endif; ?>

      <div class="cbn-header-actions">
        <button
          class="cbn-sound-toggle"
          type="button"
          aria-pressed="false"
          aria-label="Activar o desactivar sonidos de la pista"
          data-cbn-sound-toggle
        >
          <span class="cbn-sound-toggle__icon" aria-hidden="true"><i></i><i></i><i></i></span>
          <span class="cbn-sound-toggle__label">Sonido pista</span>
          <span class="cbn-sound-toggle__state" data-cbn-sound-state>OFF</span>
        </button>
        <?php if (current_user_can('manage_options')) : ?>
          <a class="cbn-header-cta cbn-header-cta--admin" href="<?php echo esc_url(admin_url('admin.php?page=cbn-panel')); ?>">
            Panel CBN <span aria-hidden="true">&rarr;</span>
          </a>
        <?php else : ?>
          <a class="cbn-header-cta" href="<?php echo esc_url(home_url('/inscripcion/')); ?>">
            Inscribirse <span aria-hidden="true">&rarr;</span>
          </a>
        <?php endif; ?>
      </div>
    </header>
