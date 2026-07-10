<?php
/**
 * Template Name: Inscripcion
 *
 * Pista Viva registration flow: process, player and guardian details,
 * applicant contact and consent.
 */

$cbn_registration_style_path = get_theme_file_path('assets/src/css/sol-registration.css');

// Run after the shared theme loader so this page-scoped layer stays last.
add_action(
    'wp_enqueue_scripts',
    static function () use ($cbn_registration_style_path): void {
        wp_enqueue_style(
            'cbn-sol-registration',
            get_theme_file_uri('assets/src/css/sol-registration.css'),
            ['cbn-sol-experience'],
            file_exists($cbn_registration_style_path)
                ? (string) filemtime($cbn_registration_style_path)
                : CBN_THEME_VERSION
        );
    },
    20
);

$cbn_registration = cbn_get_registration_content();
$cbn_registration_status = isset($_GET['cbn_registration'])
    ? sanitize_key(wp_unslash($_GET['cbn_registration']))
    : '';
$cbn_registration_message = $cbn_registration['form']['messages'][$cbn_registration_status] ?? '';
$cbn_registration_logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');

get_header();
?>

<main id="primary" class="cbn-sol cbn-sol-registration-page" data-cbn-sol data-cbn-registration-page>
  <div class="cbn-sol__grain" aria-hidden="true"></div>

  <div class="cbn-sol-route cbn-sol-registration-page__route" aria-hidden="true" data-cbn-route-wrap>
    <svg viewBox="0 0 100 1200" preserveAspectRatio="none" focusable="false">
      <path
        class="cbn-sol-route__ghost"
        d="M14 0 C14 88 86 104 86 194 S20 294 20 386 S82 490 82 582 S18 688 18 780 S82 886 82 980 S52 1104 52 1200"
      ></path>
      <path
        class="cbn-sol-route__active"
        data-cbn-route
        d="M14 0 C14 88 86 104 86 194 S20 294 20 386 S82 490 82 582 S18 688 18 780 S82 886 82 980 S52 1104 52 1200"
      ></path>
    </svg>
    <span class="cbn-sol-route__ball" data-cbn-route-ball></span>
  </div>

  <section class="cbn-sol-registration-hero" aria-labelledby="cbn-registration-title">
    <div class="cbn-sol-registration-hero__court" aria-hidden="true">
      <svg viewBox="0 0 900 780" preserveAspectRatio="xMidYMid slice" focusable="false">
        <path d="M450 0v780M0 390h900"></path>
        <circle cx="450" cy="390" r="112"></circle>
        <path d="M0 166h180v448H0M900 166H720v448h180"></path>
        <path d="M180 275a115 115 0 0 1 0 230M720 275a115 115 0 0 0 0 230"></path>
        <path d="M0 72a314 314 0 0 1 0 636M900 72a314 314 0 0 0 0 636"></path>
      </svg>
    </div>

    <div class="cbn-sol-registration-hero__copy" data-sol-reveal>
      <p class="cbn-sol-kicker">
        <span><?php echo esc_html($cbn_registration['intro']['label']); ?></span>
        <span><?php echo esc_html($cbn_registration['intro']['season']); ?></span>
      </p>
      <h1 id="cbn-registration-title">
        <span><?php echo esc_html($cbn_registration['intro']['title']); ?></span>
        <em>Salta a pista.</em>
      </h1>
      <p class="cbn-sol-registration-hero__lead"><?php echo esc_html($cbn_registration['intro']['lead']); ?></p>

      <div class="cbn-sol-registration-hero__actions">
        <a class="cbn-sol-button cbn-sol-button--shot" href="#solicitud-inscripcion" data-cbn-swish>
          <span>Enviar solicitud</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </a>
        <a class="cbn-sol-text-link" href="#como-funciona" data-cbn-swish>
          Ver el proceso <span aria-hidden="true">↓</span>
        </a>
      </div>

      <button
        class="cbn-sol-audio-invite cbn-sol-registration-hero__audio"
        type="button"
        data-cbn-sound-secondary
        aria-pressed="false"
      >
        <span class="cbn-sol-audio-invite__wave" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
        <span><strong>Escucha la pista</strong><small>Sonido opcional, apagado por defecto</small></span>
      </button>
    </div>

    <aside class="cbn-sol-registration-board" aria-labelledby="cbn-registration-board-title" data-sol-reveal data-cbn-parallax>
      <header class="cbn-sol-registration-board__header">
        <span><i aria-hidden="true"></i> Solicitud CBN</span>
        <span><?php echo esc_html($cbn_registration['intro']['season']); ?></span>
      </header>

      <div class="cbn-sol-registration-board__identity">
        <img src="<?php echo esc_url($cbn_registration_logo_url); ?>" alt="" width="400" height="400">
        <div>
          <small>Tu próxima jugada</small>
          <h2 id="cbn-registration-board-title">Del formulario<br>al equipo.</h2>
        </div>
      </div>

      <ol class="cbn-sol-registration-board__steps" role="list">
        <?php foreach ($cbn_registration['steps'] as $cbn_step_index => $cbn_step) : ?>
          <li>
            <span aria-hidden="true"><?php echo esc_html(str_pad((string) ($cbn_step_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <strong><?php echo esc_html($cbn_step['title']); ?></strong>
            <i aria-hidden="true"><?php echo esc_html($cbn_step_index + 1 === count($cbn_registration['steps']) ? '✓' : '→'); ?></i>
          </li>
        <?php endforeach; ?>
      </ol>

      <p class="cbn-sol-registration-board__privacy">
        <span aria-hidden="true">✓</span>
        <?php echo esc_html($cbn_registration['form']['note']); ?>
      </p>
    </aside>

    <div class="cbn-sol-registration-hero__shot-clock" aria-hidden="true">
      <small>Dorsal</small><strong data-cbn-shot-clock>24</strong>
    </div>
  </section>

  <section class="cbn-sol-registration-ticker" aria-label="Proceso de inscripción">
    <p class="cbn-visually-hidden">Solicitud, confirmación, instrucciones e inscripción.</p>
    <div aria-hidden="true">
      <?php foreach ($cbn_registration['steps'] as $cbn_step) : ?>
        <span><?php echo esc_html($cbn_step['title']); ?></span><i>●</i>
      <?php endforeach; ?>
      <?php foreach ($cbn_registration['steps'] as $cbn_step) : ?>
        <span><?php echo esc_html($cbn_step['title']); ?></span><i>●</i>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="como-funciona" class="cbn-sol-registration-process" aria-labelledby="cbn-registration-steps-title">
    <header class="cbn-sol-registration-process__header" data-sol-reveal>
      <div>
        <p class="cbn-sol-section-index">01 · La jugada</p>
        <h2 id="cbn-registration-steps-title">Cómo<br><em>funciona.</em></h2>
      </div>
      <p>Cuatro pasos publicados por el club para completar el recorrido de la solicitud.</p>
    </header>

    <ol class="cbn-sol-registration-process__list" role="list">
      <?php foreach ($cbn_registration['steps'] as $cbn_step_index => $cbn_step) : ?>
        <li data-sol-reveal data-cbn-tilt>
          <header>
            <span><?php echo esc_html(str_pad((string) ($cbn_step_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <small><?php echo esc_html($cbn_step_index + 1 === count($cbn_registration['steps']) ? 'Fin de la jugada' : 'Siguiente pase'); ?></small>
          </header>
          <div class="cbn-sol-registration-process__diagram" aria-hidden="true">
            <span></span><i></i>
          </div>
          <h3><?php echo esc_html($cbn_step['title']); ?></h3>
          <p><?php echo esc_html($cbn_step['text']); ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </section>

  <section id="solicitud-inscripcion" class="cbn-sol-registration-form-zone" aria-labelledby="cbn-registration-form-title">
    <div class="cbn-sol-registration-form-zone__intro" data-sol-reveal>
      <p class="cbn-sol-section-index">02 · Tu ficha</p>
      <h2 id="cbn-registration-form-title"><?php echo esc_html($cbn_registration['form']['heading']); ?></h2>
      <p><?php echo esc_html($cbn_registration['form']['text']); ?></p>

      <div class="cbn-sol-registration-form-zone__privacy">
        <span aria-hidden="true">✓</span>
        <div>
          <strong>Privacidad de la solicitud</strong>
          <p><?php echo esc_html($cbn_registration['form']['note']); ?></p>
        </div>
      </div>

      <nav class="cbn-sol-registration-form-zone__nav" aria-label="Secciones del formulario">
        <a href="#datos-jugador">01 <span>Jugador/a</span></a>
        <a href="#datos-tutor">02 <span>Padre, madre o tutor</span></a>
        <a href="#datos-contacto">03 <span>Contacto</span></a>
        <a href="#consentimientos">04 <span>Consentimientos</span></a>
      </nav>
    </div>

    <div class="cbn-sol-registration-form-panel" data-sol-reveal>
      <header class="cbn-sol-registration-form-panel__header">
        <span><i aria-hidden="true"></i> Solicitud segura</span>
        <span>Los campos con * son obligatorios</span>
      </header>

      <?php if ('' !== $cbn_registration_message) : ?>
        <p
          class="cbn-contact-notice cbn-contact-notice--<?php echo esc_attr($cbn_registration_status); ?>"
          role="status"
          aria-live="polite"
        >
          <?php echo esc_html($cbn_registration_message); ?>
        </p>
      <?php endif; ?>

      <form class="cbn-contact-form cbn-sol-registration-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
        <input type="hidden" name="action" value="cbn_registration_submit">
        <input type="hidden" name="cbn_registration_redirect" value="<?php echo esc_url(get_permalink()); ?>">
        <?php wp_nonce_field('cbn_registration_submit', 'cbn_registration_nonce'); ?>

        <div class="cbn-contact-form__hp" aria-hidden="true">
          <label for="cbn-registration-website">Sitio web (dejar en blanco)</label>
          <input type="text" id="cbn-registration-website" name="cbn_registration_website" tabindex="-1" autocomplete="off">
        </div>

        <fieldset id="datos-jugador" class="cbn-registration-fieldset" data-form-section="01">
          <legend><span aria-hidden="true">01</span> Datos del jugador/a</legend>

          <div class="cbn-sol-registration-field-grid">
            <div class="cbn-form-row cbn-form-row--wide" data-field-index="01">
              <label for="cbn-reg-player-name">Nombre y apellidos <span aria-hidden="true">*</span></label>
              <input type="text" id="cbn-reg-player-name" name="cbn_reg_player_name" required aria-required="true" autocomplete="name">
            </div>

            <div class="cbn-form-row" data-field-index="02">
              <label for="cbn-reg-player-birthdate">Fecha de nacimiento <span aria-hidden="true">*</span></label>
              <input type="date" id="cbn-reg-player-birthdate" name="cbn_reg_player_birthdate" required aria-required="true">
            </div>

            <div class="cbn-form-row" data-field-index="03">
              <label for="cbn-reg-player-sex">Sexo <span aria-hidden="true">*</span></label>
              <select id="cbn-reg-player-sex" name="cbn_reg_player_sex" required aria-required="true">
                <option value="">Selecciona una opción</option>
                <?php foreach ($cbn_registration['form']['sex_options'] as $cbn_sex_key => $cbn_sex_label) : ?>
                  <option value="<?php echo esc_attr($cbn_sex_key); ?>"><?php echo esc_html($cbn_sex_label); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="cbn-form-row cbn-form-row--wide" data-field-index="04">
              <label for="cbn-reg-player-dni">DNI (si aplica)</label>
              <input type="text" id="cbn-reg-player-dni" name="cbn_reg_player_dni" autocomplete="off">
            </div>

            <div class="cbn-form-row" data-field-index="05">
              <label for="cbn-reg-player-height">Estatura en cm (solo si el club los solicita)</label>
              <input type="number" id="cbn-reg-player-height" name="cbn_reg_player_height" min="0" step="1" inputmode="numeric">
            </div>

            <div class="cbn-form-row" data-field-index="06">
              <label for="cbn-reg-player-weight">Peso en kg (solo si el club los solicita)</label>
              <input type="number" id="cbn-reg-player-weight" name="cbn_reg_player_weight" min="0" step="1" inputmode="numeric">
            </div>
          </div>
        </fieldset>

        <fieldset id="datos-tutor" class="cbn-registration-fieldset" data-form-section="02">
          <legend><span aria-hidden="true">02</span> Datos del padre/madre/tutor (obligatorio para menores de edad)</legend>

          <div class="cbn-sol-registration-field-grid">
            <div class="cbn-form-row cbn-form-row--wide" data-field-index="01">
              <label for="cbn-reg-guardian-name">Nombre y apellidos</label>
              <input type="text" id="cbn-reg-guardian-name" name="cbn_reg_guardian_name" autocomplete="name">
            </div>

            <div class="cbn-form-row" data-field-index="02">
              <label for="cbn-reg-guardian-phone">Teléfono</label>
              <input type="tel" id="cbn-reg-guardian-phone" name="cbn_reg_guardian_phone" autocomplete="tel">
            </div>

            <div class="cbn-form-row" data-field-index="03">
              <label for="cbn-reg-guardian-email">Email</label>
              <input type="email" id="cbn-reg-guardian-email" name="cbn_reg_guardian_email" autocomplete="email">
            </div>

            <div class="cbn-form-row cbn-form-row--wide" data-field-index="04">
              <label for="cbn-reg-guardian-relationship">Relación</label>
              <select id="cbn-reg-guardian-relationship" name="cbn_reg_guardian_relationship">
                <option value="">Selecciona una opción</option>
                <?php foreach ($cbn_registration['form']['relationship_options'] as $cbn_rel_key => $cbn_rel_label) : ?>
                  <option value="<?php echo esc_attr($cbn_rel_key); ?>"><?php echo esc_html($cbn_rel_label); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </fieldset>

        <fieldset id="datos-contacto" class="cbn-registration-fieldset" data-form-section="03">
          <legend><span aria-hidden="true">03</span> Contacto del solicitante</legend>
          <p class="cbn-registration-fieldset__hint">Si el jugador o jugadora es mayor de edad, indica sus propios datos de contacto.</p>

          <div class="cbn-sol-registration-field-grid">
            <div class="cbn-form-row" data-field-index="01">
              <label for="cbn-reg-contact-phone">Teléfono <span aria-hidden="true">*</span></label>
              <input type="tel" id="cbn-reg-contact-phone" name="cbn_reg_contact_phone" required aria-required="true" autocomplete="tel">
            </div>

            <div class="cbn-form-row" data-field-index="02">
              <label for="cbn-reg-contact-email">Email <span aria-hidden="true">*</span></label>
              <input type="email" id="cbn-reg-contact-email" name="cbn_reg_contact_email" required aria-required="true" autocomplete="email">
            </div>
          </div>
        </fieldset>

        <fieldset id="consentimientos" class="cbn-registration-fieldset cbn-registration-fieldset--consent" data-form-section="04">
          <legend><span aria-hidden="true">04</span> Consentimientos</legend>

          <div class="cbn-form-row cbn-form-row--checkbox">
            <input type="checkbox" id="cbn-reg-consent-rgpd" name="cbn_reg_consent_rgpd" value="1" required aria-required="true">
            <label for="cbn-reg-consent-rgpd"><?php echo esc_html($cbn_registration['form']['consent_rgpd_text']); ?></label>
          </div>

          <div class="cbn-form-row cbn-form-row--checkbox">
            <input type="checkbox" id="cbn-reg-consent-guardian" name="cbn_reg_consent_guardian" value="1">
            <label for="cbn-reg-consent-guardian"><?php echo esc_html($cbn_registration['form']['consent_guardian_text']); ?></label>
          </div>
        </fieldset>

        <p class="cbn-registration-note">
          <span aria-hidden="true">✓</span>
          <?php echo esc_html($cbn_registration['form']['note']); ?>
        </p>

        <button class="cbn-sol-button cbn-sol-button--shot cbn-sol-registration-form__submit" type="submit" data-cbn-swish>
          <span>Enviar solicitud</span>
          <span class="cbn-sol-button__arc" aria-hidden="true"><span></span></span>
        </button>
      </form>
    </div>
  </section>
</main>

<?php get_footer(); ?>
