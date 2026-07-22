<?php
/**
 * Pista Viva 404 page.
 */

$cbn_system_style_path = get_theme_file_path('assets/src/css/sol-system.css');

add_action(
    'wp_enqueue_scripts',
    static function () use ($cbn_system_style_path): void {
        wp_enqueue_style(
            'cbn-sol-system',
            get_theme_file_uri('assets/src/css/sol-system.css'),
            ['cbn-sol-experience'],
            file_exists($cbn_system_style_path)
                ? (string) filemtime($cbn_system_style_path)
                : CBN_THEME_VERSION
        );
    },
    20
);

$cbn_system_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
$cbn_system_teams_url = get_post_type_archive_link('cbn_team') ?: home_url('/equipos/');

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-system cbn-sol-system--404" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <section class="cbn-sol-system-404" aria-labelledby="cbn-system-404-title">
    <div class="cbn-sol-system-404__court" aria-hidden="true">
      <span></span><i></i><b></b>
    </div>

    <div class="cbn-sol-system-404__copy" data-sol-reveal>
      <p class="cbn-sol-section-index">Error 404 · Balón fuera</p>
      <h1 id="cbn-system-404-title">
        <span>Fuera de</span>
        <em>pista.</em>
      </h1>
      <p>La dirección que has abierto no existe o ya no está disponible.</p>

      <nav class="cbn-sol-system-404__actions" aria-label="Volver a una sección disponible">
        <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url(home_url('/')); ?>" data-cbn-swish>
          <span>Volver al inicio</span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_system_teams_url); ?>" data-cbn-swish>
          Ver equipos <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
          Contacto <span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
        </a>
      </nav>
    </div>

    <aside class="cbn-sol-system-scoreboard" aria-label="Página no encontrada" data-sol-reveal data-cbn-parallax>
      <header>
        <span><i aria-hidden="true"></i> Página no encontrada</span>
        <span>CBN · Navalcarnero</span>
      </header>
      <div class="cbn-sol-system-scoreboard__number" aria-hidden="true">
        <span>4</span>
        <img src="<?php echo esc_url($cbn_system_logo_url); ?>" alt="" width="400" height="400">
        <span>4</span>
      </div>
      <div class="cbn-sol-system-scoreboard__message">
        <small>Siguiente posesión</small>
        <strong>Elige una ruta<br>y vuelve al juego.</strong>
      </div>
      <form class="cbn-sol-system-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <label for="cbn-404-search">Buscar en la web</label>
        <div>
          <input id="cbn-404-search" type="search" name="s" placeholder="Noticias, equipos, partidos…">
          <button type="submit" data-cbn-swish>
            <span class="cbn-visually-hidden">Buscar</span><span class="cbn-sol-arrow-up-right" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </aside>
  </section>
</main>

<?php get_footer(); ?>
