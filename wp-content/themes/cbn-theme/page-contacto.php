<?php
/**
 * Template Name: Contacto
 *
 * Pista Viva contact page: direct channels, native form, facilities and social.
 */

$cbn_contact_style_path = get_theme_file_path('assets/src/css/sol-contact.css');

// Run after the shared theme loader so this page-scoped layer stays last.
add_action(
    'wp_enqueue_scripts',
    static function () use ($cbn_contact_style_path): void {
        wp_enqueue_style(
            'cbn-sol-contact',
            get_theme_file_uri('assets/src/css/sol-contact.css'),
            ['cbn-sol-experience'],
            file_exists($cbn_contact_style_path)
                ? (string) filemtime($cbn_contact_style_path)
                : CBN_THEME_VERSION
        );
    },
    20
);

$cbn_contact = cbn_get_contact_content();
$cbn_contact_status = isset($_GET['cbn_contact'])
    ? sanitize_key(wp_unslash($_GET['cbn_contact']))
    : '';
$cbn_contact_message = $cbn_contact['form']['messages'][$cbn_contact_status] ?? '';
$cbn_contact_email_url = 'mailto:' . $cbn_contact['channels']['email'];
$cbn_contact_phone_url = 'tel:' . preg_replace('/[^\d+]/', '', $cbn_contact['channels']['phone']);
$cbn_contact_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-contact-page" data-cbn-sol data-cbn-contact-page>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-contact-page__route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 1000" preserveAspectRatio="none" focusable="false">
      <path
        class="cbn-sol-route__ghost"
        d="M14 0 C14 92 86 108 86 205 S20 318 20 415 S82 525 82 620 S18 728 18 815 S74 925 52 1000"
      ></path>
      <path
        class="cbn-sol-route__active"
        data-cbn-route
        d="M14 0 C14 92 86 108 86 205 S20 318 20 415 S82 525 82 620 S18 728 18 815 S74 925 52 1000"
      ></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-contact-hero" aria-labelledby="cbn-contact-title">
    <div class="cbn-sol-contact-hero__court" aria-hidden="true">
      <svg viewBox="0 0 900 780" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M450 0v780M0 390h900"></path>
        <circle cx="450" cy="390" r="112"></circle>
        <path d="M0 166h180v448H0M900 166H720v448h180"></path>
        <path d="M180 275a115 115 0 0 1 0 230M720 275a115 115 0 0 0 0 230"></path>
        <path d="M0 72a314 314 0 0 1 0 636M900 72a314 314 0 0 0 0 636"></path>
      </svg>
    </div>

    <div class="cbn-sol-contact-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker">
        <span><?php echo esc_html($cbn_contact['intro']['label']); ?></span>
        <span>Club Baloncesto Navalcarnero</span>
      </p>
      <h1 id="cbn-contact-title">
        <span><?php echo esc_html($cbn_contact['intro']['title']); ?></span>
        <em>En la pista.</em>
      </h1>
      <p class="cbn-sol-contact-hero__lead"><?php echo esc_html($cbn_contact['intro']['lead']); ?></p>

      <div class="cbn-sol-contact-hero__actions">
        <a class="cbn-sol-button cbn-sol-button--shot" href="#formulario-contacto" data-cbn-swish>
          <span>Escribir al club</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
        <a class="cbn-sol-text-link" href="<?php echo esc_url($cbn_contact_email_url); ?>" data-cbn-swish>
          Enviar email <span aria-hidden="true">↗</span>
        </a>
      </div>

      <button
        class="cbn-sol-audio-invite cbn-sol-contact-hero__audio"
        type="button"
        data-cbn-sound-secondary
        aria-pressed="false"
      >
        <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span><strong>Escucha la pista</strong><small>Sonido opcional, apagado por defecto</small></span>
      </button>
    </div>

    <aside class="cbn-sol-contact-board" aria-labelledby="cbn-contact-channels-title" data-sol-reveal data-cbn-parallax>
      <header class="cbn-sol-contact-board__header">
        <span><i aria-hidden="true"></i> Canales del club</span>
        <span>CBN · Navalcarnero</span>
      </header>

      <div class="cbn-sol-contact-board__brand">
        <img src="<?php echo esc_url($cbn_contact_logo_url); ?>" alt="" width="400" height="400">
        <div>
          <small>Línea directa</small>
          <h2 id="cbn-contact-channels-title">Elige tu<br>mejor pase.</h2>
        </div>
      </div>

      <ol class="cbn-sol-contact-board__channels">
        <li>
          <span aria-hidden="true">01</span>
          <div><small>Email</small><a href="<?php echo esc_url($cbn_contact_email_url); ?>" data-cbn-swish><?php echo esc_html($cbn_contact['channels']['email']); ?></a></div>
          <i aria-hidden="true">↗</i>
        </li>
        <li>
          <span aria-hidden="true">02</span>
          <div><small>Teléfono</small><a href="<?php echo esc_url($cbn_contact_phone_url); ?>" data-cbn-swish><?php echo esc_html($cbn_contact['channels']['phone']); ?></a></div>
          <i aria-hidden="true">↗</i>
        </li>
        <li>
          <span aria-hidden="true">03</span>
          <div><small>Dirección</small><p><?php echo esc_html($cbn_contact['channels']['address']); ?></p></div>
          <i aria-hidden="true">●</i>
        </li>
      </ol>

      <p class="cbn-sol-contact-board__note">
        <span aria-hidden="true">✓</span> Información de contacto publicada por el club.
      </p>
    </aside>

    <div class="cbn-sol-contact-hero__shot-clock" aria-hidden="true">
      <small>Pase</small><strong data-cbn-shot-clock>24</strong>
    </div>
  </section>

  <section class="cbn-sol-contact-ticker" aria-label="Opciones de contacto">
    <p class="cbn-visually-hidden">Equipos, inscripciones, tienda y otras consultas.</p>
    <div aria-hidden="true">
      <?php foreach ($cbn_contact['form']['subject_options'] as $cbn_subject_label) : ?>
        <span><?php echo esc_html($cbn_subject_label); ?></span><i>●</i>
      <?php endforeach; ?>
      <?php foreach ($cbn_contact['form']['subject_options'] as $cbn_subject_label) : ?>
        <span><?php echo esc_html($cbn_subject_label); ?></span><i>●</i>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="formulario-contacto" class="cbn-sol-contact-form-zone" aria-labelledby="cbn-contact-form-title">
    <div class="cbn-sol-contact-form-zone__intro" data-sol-reveal>
      <p class="cbn-sol-section-index">01 · Pase directo</p>
      <h2 id="cbn-contact-form-title"><?php echo esc_html($cbn_contact['form']['heading']); ?></h2>
      <p><?php echo esc_html($cbn_contact['form']['text']); ?></p>

      <div class="cbn-sol-contact-form-zone__subjects">
        <small>Temas disponibles</small>
        <ul role="list">
          <?php foreach ($cbn_contact['form']['subject_options'] as $cbn_subject_label) : ?>
            <li><?php echo esc_html($cbn_subject_label); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="cbn-sol-contact-form-panel" data-sol-reveal>
      <header class="cbn-sol-contact-form-panel__header">
        <span><i aria-hidden="true"></i> Formulario seguro</span>
        <span>Los campos con * son obligatorios</span>
      </header>

      <?php if ('' !== $cbn_contact_message) : ?>
        <p
          class="cbn-contact-notice cbn-contact-notice--<?php echo esc_attr($cbn_contact_status); ?>"
          role="status"
          aria-live="polite"
        >
          <?php echo esc_html($cbn_contact_message); ?>
        </p>
      <?php endif; ?>

      <form class="cbn-contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
        <input type="hidden" name="action" value="cbn_contact_submit">
        <input type="hidden" name="cbn_contact_redirect" value="<?php echo esc_url(get_permalink()); ?>">
        <?php wp_nonce_field('cbn_contact_submit', 'cbn_contact_nonce'); ?>

        <div class="cbn-contact-form__hp" aria-hidden="true">
          <label for="cbn-contact-website">Sitio web (dejar en blanco)</label>
          <input type="text" id="cbn-contact-website" name="cbn_contact_website" tabindex="-1" autocomplete="off">
        </div>

        <div class="cbn-form-row" data-field-index="01">
          <label for="cbn-contact-name">Nombre <span aria-hidden="true">*</span></label>
          <input type="text" id="cbn-contact-name" name="cbn_contact_name" required aria-required="true" autocomplete="name">
        </div>

        <div class="cbn-form-row" data-field-index="02">
          <label for="cbn-contact-email">Email <span aria-hidden="true">*</span></label>
          <input type="email" id="cbn-contact-email" name="cbn_contact_email" required aria-required="true" autocomplete="email">
        </div>

        <div class="cbn-form-row" data-field-index="03">
          <label for="cbn-contact-subject">Asunto <span aria-hidden="true">*</span></label>
          <select id="cbn-contact-subject" name="cbn_contact_subject" required aria-required="true">
            <?php foreach ($cbn_contact['form']['subject_options'] as $cbn_subject_key => $cbn_subject_label) : ?>
              <option value="<?php echo esc_attr($cbn_subject_key); ?>"><?php echo esc_html($cbn_subject_label); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="cbn-form-row" data-field-index="04">
          <label for="cbn-contact-message">Mensaje <span aria-hidden="true">*</span></label>
          <textarea id="cbn-contact-message" name="cbn_contact_message" rows="6" required aria-required="true"></textarea>
        </div>

        <div class="cbn-form-row cbn-form-row--checkbox" data-field-index="05">
          <input type="checkbox" id="cbn-contact-consent" name="cbn_contact_consent" value="1" required aria-required="true">
          <label for="cbn-contact-consent"><?php echo esc_html($cbn_contact['form']['consent_text']); ?></label>
        </div>

        <button class="cbn-sol-button cbn-sol-button--shot cbn-sol-contact-form__submit" type="submit" data-cbn-swish>
          <span>Enviar mensaje</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </button>
      </form>
    </div>
  </section>

  <?php cbn_render_club_photo_story('contact'); ?>

  <section class="cbn-sol-contact-locations" aria-labelledby="cbn-contact-location-title">
    <header class="cbn-sol-contact-locations__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">02 · Punto de encuentro</p>
        <h2 id="cbn-contact-location-title"><?php echo esc_html($cbn_contact['location']['heading']); ?></h2>
      </div>
      <p>Instalaciones publicadas para encontrar al club dentro y fuera de la jornada.</p>
    </header>

    <div class="cbn-sol-contact-locations__grid">
      <?php foreach ($cbn_contact['location']['facilities'] as $cbn_facility_index => $cbn_facility) : ?>
        <article class="cbn-sol-contact-location-card" data-sol-reveal data-cbn-tilt>
          <div class="cbn-sol-contact-location-card__court" aria-hidden="true">
            <span></span><i></i>
          </div>
          <header>
            <span><?php echo esc_html(str_pad((string) ($cbn_facility_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <small>Instalación CBN</small>
          </header>
          <h3><?php echo esc_html($cbn_facility['name']); ?></h3>
          <address><?php echo esc_html($cbn_facility['address']); ?></address>
          <p><?php echo esc_html($cbn_facility['note']); ?></p>
          <?php if (!empty($cbn_facility['maps_url'])) : ?>
            <a href="<?php echo esc_url($cbn_facility['maps_url']); ?>" target="_blank" rel="noopener noreferrer" data-cbn-swish>
              Ver en Google Maps <span aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cbn-sol-contact-social" aria-labelledby="cbn-contact-social-title">
    <div class="cbn-sol-contact-social__court" aria-hidden="true">
      <span></span><span></span><span></span>
    </div>
    <header data-sol-reveal>
      <p class="cbn-sol-section-index">03 · Sigue la jugada</p>
      <h2 id="cbn-contact-social-title"><?php echo esc_html($cbn_contact['social']['heading']); ?></h2>
    </header>

    <ul class="cbn-sol-contact-social__list" role="list">
      <?php foreach ($cbn_contact['social']['items'] as $cbn_social_index => $cbn_social_item) : ?>
        <?php if (cbn_contact_social_is_placeholder((string) $cbn_social_item['url'])) : ?>
          <?php continue; ?>
        <?php endif; ?>
        <li data-sol-reveal>
          <a class="cbn-sol-contact-social__link" href="<?php echo esc_url($cbn_social_item['url']); ?>" target="_blank" rel="noopener noreferrer" data-cbn-swish>
            <small><?php echo esc_html(str_pad((string) ($cbn_social_index + 1), 2, '0', STR_PAD_LEFT)); ?></small>
            <strong><?php echo esc_html($cbn_social_item['label']); ?></strong>
            <em aria-hidden="true">↗</em>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <footer class="cbn-sol-contact-social__footer" data-sol-reveal>
      <span>¿Prefieres un pase directo?</span>
      <a href="<?php echo esc_url($cbn_contact_email_url); ?>" data-cbn-swish><?php echo esc_html($cbn_contact['channels']['email']); ?> <i aria-hidden="true">↗</i></a>
    </footer>
  </section>
</main>

<?php get_footer(); ?>
