<?php
/**
 * Pista Viva storefront for the "Tienda" page (slug: tienda).
 *
 * This is deliberately an honest pre-catalogue state. Products, prices,
 * stock, fulfilment and payment methods must come from confirmed club data.
 */

$cbn_enqueue_shop_assets = static function (): void {
    $style_path = get_theme_file_path('assets/src/css/sol-shop.css');
    $style_version = file_exists($style_path)
        ? (string) filemtime($style_path)
        : (string) wp_get_theme()->get('Version');

    wp_enqueue_style(
        'cbn-sol-shop',
        get_theme_file_uri('assets/src/css/sol-shop.css'),
        ['cbn-sol-experience'],
        $style_version
    );
};

if (did_action('wp_enqueue_scripts')) {
    $cbn_enqueue_shop_assets();
} else {
    add_action('wp_enqueue_scripts', $cbn_enqueue_shop_assets, 20);
}

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-shop" data-cbn-sol>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <?php if (have_posts()) : ?>
    <?php
    while (have_posts()) :
        the_post();
        $cbn_shop_title = get_the_title() ?: __('Tienda CBN', 'cbn');
        $cbn_shop_content = trim((string) get_the_content());
        $cbn_shop_has_featured_image = has_post_thumbnail();
        $cbn_shop_photo_id = $cbn_shop_has_featured_image
            ? null
            : (cbn_get_club_photo_feature('shop_hero') ?: 'club-025');
        $cbn_shop_image_alt = $cbn_shop_has_featured_image ? '' : (cbn_get_club_photo($cbn_shop_photo_id)['alt'] ?? '');

        if ($cbn_shop_has_featured_image) {
            $cbn_shop_thumbnail_id = get_post_thumbnail_id();
            $cbn_shop_image_alt = (string) get_post_meta($cbn_shop_thumbnail_id, '_wp_attachment_image_alt', true);
        }
        ?>
      <article <?php post_class('cbn-sol-shop__page'); ?>>
        <section class="cbn-sol-shop-hero" aria-labelledby="cbn-sol-shop-title">
          <div class="cbn-sol-shop-hero__court" aria-hidden="true">
            <span></span><i></i><b></b>
          </div>

          <div class="cbn-sol-shop-hero__copy" data-sol-reveal>
            <p class="cbn-sol-section-index">Tienda oficial &middot; Club Baloncesto Navalcarnero</p>
            <h1 id="cbn-sol-shop-title"><?php echo esc_html($cbn_shop_title); ?></h1>
            <p class="cbn-sol-shop-status"><span aria-hidden="true"></span><?php esc_html_e('Catálogo en preparación', 'cbn'); ?></p>

            <?php if ($cbn_shop_content) : ?>
              <div class="cbn-sol-shop-hero__editorial">
                <?php echo apply_filters('the_content', $cbn_shop_content); ?>
              </div>
            <?php else : ?>
              <p class="cbn-sol-shop-hero__editorial"><?php esc_html_e('Estamos preparando el catalogo oficial del club.', 'cbn'); ?></p>
            <?php endif; ?>

            <p class="cbn-sol-shop-hero__honesty">
              <?php esc_html_e('Todavía no hay productos, precios, disponibilidad ni compra online publicados.', 'cbn'); ?>
            </p>

            <div class="cbn-sol-shop-hero__actions">
              <a class="cbn-sol-button cbn-sol-button--shot" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
                <span><?php esc_html_e('Consultar al club', 'cbn'); ?></span>
              </a>
              <a class="cbn-sol-text-link" href="<?php echo esc_url(home_url('/inscripcion/')); ?>" data-cbn-swish>
                <?php esc_html_e('Ir a inscripciones', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
              </a>
            </div>

            <button class="cbn-sol-audio-invite" type="button" data-cbn-sound-secondary aria-pressed="false">
              <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
              <span><strong>Escucha la pista</strong><small>Activa el ambiente de parquet y red</small></span>
            </button>
          </div>

          <figure class="cbn-sol-shop-hero__visual" data-sol-reveal data-cbn-parallax>
            <div class="cbn-sol-shop-hero__image">
              <?php if ($cbn_shop_photo_id) : ?>
                <?php cbn_render_club_photo($cbn_shop_photo_id, ['sizes' => '(max-width: 760px) 100vw, 46vw', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
              <?php else : ?>
                <?php
                echo wp_get_attachment_image(
                    $cbn_shop_thumbnail_id,
                    'full',
                    false,
                    [
                        'alt' => $cbn_shop_image_alt,
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                        'decoding' => 'async',
                        'sizes' => '(max-width: 760px) 100vw, 46vw',
                    ]
                );
                ?>
              <?php endif; ?>
            </div>
            <figcaption aria-hidden="true">
              <strong class="cbn-photo-credit">&copy; CBN</strong>
            </figcaption>
            <span class="cbn-sol-shop-hero__label" aria-hidden="true">CBN<br>STORE</span>
          </figure>
        </section>

        <section class="cbn-sol-shop-roadmap" aria-labelledby="cbn-sol-shop-roadmap-title">
          <header class="cbn-sol-shop-heading" data-sol-reveal>
            <div>
              <p class="cbn-sol-section-index">01 &middot; Estado real</p>
              <h2 id="cbn-sol-shop-roadmap-title"><?php esc_html_e('Antes de abrir la pista', 'cbn'); ?></h2>
            </div>
            <p><?php esc_html_e('Publicaremos la tienda cuando la informacion comercial y el proceso completo esten confirmados.', 'cbn'); ?></p>
          </header>

          <ol class="cbn-sol-shop-roadmap__grid">
            <li data-sol-reveal data-cbn-tilt>
              <span>01</span>
              <small><?php esc_html_e('Pendiente', 'cbn'); ?></small>
              <h3><?php esc_html_e('Catálogo real', 'cbn'); ?></h3>
              <p><?php esc_html_e('Productos, tallas, precios y disponibilidad deben ser facilitados y confirmados por el club.', 'cbn'); ?></p>
              <i aria-hidden="true"></i>
            </li>
            <li data-sol-reveal data-cbn-tilt>
              <span>02</span>
              <small><?php esc_html_e('Pendiente', 'cbn'); ?></small>
              <h3><?php esc_html_e('Entrega y cambios', 'cbn'); ?></h3>
              <p><?php esc_html_e('Las opciones de envio, recogida y gestion de cambios todavia no estan publicadas.', 'cbn'); ?></p>
              <i aria-hidden="true"></i>
            </li>
            <li data-sol-reveal data-cbn-tilt>
              <span>03</span>
              <small><?php esc_html_e('Pendiente', 'cbn'); ?></small>
              <h3><?php esc_html_e('Compra verificada', 'cbn'); ?></h3>
              <p><?php esc_html_e('La compra se activara solo despues de configurar y comprobar el proceso de pago en un entorno de pruebas.', 'cbn'); ?></p>
              <i aria-hidden="true"></i>
            </li>
          </ol>

          <aside class="cbn-sol-shop-roadmap__notice" data-sol-reveal aria-label="Estado de compra online">
            <strong><?php esc_html_e('Ahora mismo', 'cbn'); ?></strong>
            <p><?php esc_html_e('Esta pagina no tiene carrito y no solicita datos de pago.', 'cbn'); ?></p>
            <span aria-hidden="true">00</span>
          </aside>
        </section>

        <?php cbn_render_club_photo_story('shop'); ?>

        <section class="cbn-sol-shop-contact" aria-labelledby="cbn-sol-shop-contact-title">
          <div class="cbn-sol-shop-contact__copy" data-sol-reveal>
            <p class="cbn-sol-section-index">02 &middot; Mientras tanto</p>
            <h2 id="cbn-sol-shop-contact-title"><?php esc_html_e('Hablemos fuera de la cancha', 'cbn'); ?></h2>
            <p><?php esc_html_e('Si necesitas informacion del club o quieres hacernos una consulta sobre la futura tienda, escribenos.', 'cbn'); ?></p>
            <div>
              <a class="cbn-sol-button cbn-sol-button--light" href="<?php echo esc_url(home_url('/contacto/')); ?>" data-cbn-swish>
                <?php esc_html_e('Contactar', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
              </a>
              <a class="cbn-sol-shop-contact__link" href="<?php echo esc_url(home_url('/equipos/')); ?>" data-cbn-swish>
                <?php esc_html_e('Conocer los equipos', 'cbn'); ?> <span aria-hidden="true">&#8599;</span>
              </a>
            </div>
          </div>
        </section>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <section class="cbn-sol-shop-missing" aria-labelledby="cbn-sol-shop-missing-title">
      <p class="cbn-sol-section-index">Tienda CBN</p>
      <h1 id="cbn-sol-shop-missing-title"><?php esc_html_e('Catálogo en preparación', 'cbn'); ?></h1>
      <p><?php esc_html_e('Todavía no hay contenido de tienda publicado.', 'cbn'); ?></p>
      <a class="cbn-sol-button" href="<?php echo esc_url(home_url('/contacto/')); ?>"><?php esc_html_e('Contactar', 'cbn'); ?></a>
    </section>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
