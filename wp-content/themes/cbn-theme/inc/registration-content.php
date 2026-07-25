<?php
/**
 * "Inscripcion" page content helpers and form handler.
 *
 * Follows the same placeholder-content and admin-post pattern as
 * inc/contact-content.php: ACF fields (when present) override defaults,
 * the form posts to admin-post.php with a nonce and a honeypot, every
 * value is sanitized server-side, and the club is notified by email.
 * Deliberately NO database storage: this form collects a minor's
 * personal data (player birth date, guardian identity) which must not
 * sit in wp_postmeta/wp_options without a retention and access policy.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_registration_content(): array
{
    $defaults = cbn_get_registration_defaults();

    $content = [
        'intro' => [
            'label' => cbn_get_registration_acf_field('cbn_registration_intro_label', $defaults['intro']['label']),
            'title' => cbn_get_registration_acf_field('cbn_registration_intro_title', $defaults['intro']['title']),
            'lead' => cbn_get_registration_acf_field('cbn_registration_intro_lead', $defaults['intro']['lead']),
            'season' => cbn_get_registration_acf_field('cbn_registration_season', $defaults['intro']['season']),
        ],
        'steps' => $defaults['steps'],
        'form' => $defaults['form'],
    ];

    return apply_filters('cbn_registration_content', $content);
}

function cbn_get_registration_defaults(): array
{
    return [
        'intro' => [
            'label' => 'Inscripciones',
            'title' => 'Únete al club',
            'lead' => 'Envía la solicitud de inscripción y el club se pondrá en contacto contigo para confirmar plaza, categoría y siguientes pasos.',
            'season' => 'Temporada 2026/2027',
        ],
        'steps' => [
            [
                'title' => 'Envía tu solicitud',
                'text' => 'Rellena el formulario con los datos del jugador o jugadora y los datos de contacto.',
            ],
            [
                'title' => 'El club confirma plaza y categoría',
                'text' => 'Revisamos la solicitud y te confirmamos por email la disponibilidad y la categoría.',
            ],
            [
                'title' => 'Recibes instrucciones de pago y documentación',
                'text' => 'Te enviamos por email los siguientes pasos y la documentación necesaria.',
            ],
            [
                'title' => 'Inscripción completada',
                'text' => 'Con el pago y la documentación recibidos, la inscripción queda formalizada.',
            ],
        ],
        'form' => [
            'heading' => 'Formulario de inscripción',
            'text' => 'Completa los datos del jugador o jugadora. Si es menor de edad, también necesitamos los datos de un padre, madre o tutor legal.',
            'sex_options' => [
                'femenino' => 'Femenino',
                'masculino' => 'Masculino',
            ],
            'relationship_options' => [
                'padre' => 'Padre',
                'madre' => 'Madre',
                'tutor' => 'Tutor legal',
            ],
            'consent_rgpd_text' => 'Acepto que estos datos se utilicen para tramitar la inscripción (texto legal definitivo pendiente de aprobación del club).',
            'consent_guardian_text' => 'Declaro que soy el padre, madre o tutor legal y autorizo esta solicitud.',
            'note' => 'Estos datos viajan por email al club y no se almacenan en la web. Las instrucciones de pago llegarán por email; los pagos se activarán en una fase posterior.',
            'messages' => [
                'ok' => 'Gracias por tu solicitud. El club revisará los datos y te contactará para confirmar plaza y categoría.',
                'invalid' => 'Revisa los campos obligatorios, la fecha de nacimiento y el consentimiento antes de enviar la solicitud.',
                'minor_guardian' => 'Para jugadores o jugadoras menores de edad son obligatorios los datos y el consentimiento del padre, madre o tutor legal.',
                'rate_limited' => 'Ya hemos recibido varias solicitudes desde aquí. Espera un rato antes de enviar otra, o escríbenos a la dirección de contacto del club.',
                'error' => 'No se ha podido enviar la solicitud. Inténtalo de nuevo más tarde.',
            ],
        ],
    ];
}

function cbn_get_registration_acf_field(string $field_name, mixed $default): mixed
{
    $source_id = get_queried_object_id();
    $value = function_exists('get_field')
        ? get_field($field_name, $source_id ?: false)
        : ($source_id ? get_post_meta($source_id, $field_name, true) : null);

    if ($value === null || $value === false || $value === '') {
        return $default;
    }

    return $value;
}

add_action('admin_post_cbn_registration_submit', 'cbn_handle_registration_submit');
add_action('admin_post_nopriv_cbn_registration_submit', 'cbn_handle_registration_submit');

/**
 * Handles the native registration form POST. Verifies nonce and
 * honeypot, sanitizes all input, validates required fields plus a
 * plausible birth date, requires guardian identity and consent when the
 * player is a minor, sends a plaintext notification to the site admin
 * email, and redirects back to the inscripcion page with a status query
 * var. Never stores submissions (see file docblock).
 */
function cbn_handle_registration_submit(): void
{
    $fallback_redirect = home_url('/inscripcion/');
    $redirect_raw = isset($_POST['cbn_registration_redirect'])
        ? esc_url_raw(wp_unslash($_POST['cbn_registration_redirect']))
        : '';
    $redirect_base = wp_validate_redirect($redirect_raw, $fallback_redirect);

    // Stricter than the contact form on purpose: every accepted submission
    // emails a minor's personal data, so a flood is a privacy incident and
    // not merely an inbox nuisance.
    $client_ip = cbn_form_client_ip();

    if (cbn_form_rate_limit_exceeded('cbn_registration', 'ip', $client_ip)) {
        cbn_form_log_rejection('cbn_registration', 'rate limit por IP');
        wp_safe_redirect(add_query_arg('cbn_registration', 'rate_limited', $redirect_base));
        exit;
    }

    cbn_form_register_attempt('cbn_registration', 'ip', $client_ip);

    $nonce = isset($_POST['cbn_registration_nonce']) ? sanitize_text_field(wp_unslash($_POST['cbn_registration_nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'cbn_registration_submit')) {
        cbn_form_log_rejection('cbn_registration', 'nonce inválido');
        wp_safe_redirect(add_query_arg('cbn_registration', 'error', $redirect_base));
        exit;
    }

    // Honeypot: real visitors never fill this hidden field. Bots that do
    // are silently told the form "worked" so they do not keep probing.
    $honeypot = isset($_POST['cbn_registration_website'])
        ? sanitize_text_field(wp_unslash($_POST['cbn_registration_website']))
        : '';

    if ('' !== $honeypot) {
        wp_safe_redirect(add_query_arg('cbn_registration', 'ok', $redirect_base));
        exit;
    }

    // This form is longer than the contact one, so the floor is higher: five
    // seconds is still far below what a human needs to fill three sections.
    // Redirects to 'invalid' rather than a silent "ok" so a legitimate but
    // very fast applicant is told to resubmit instead of losing the request.
    $time_token = isset($_POST['cbn_form_ts']) ? sanitize_text_field(wp_unslash($_POST['cbn_form_ts'])) : '';

    if (!cbn_form_time_trap_passed('cbn_registration', $time_token, 5)) {
        cbn_form_log_rejection('cbn_registration', 'trampa temporal');
        wp_safe_redirect(add_query_arg('cbn_registration', 'invalid', $redirect_base));
        exit;
    }

    $lengths = cbn_form_max_lengths();

    // Datos del jugador/a.
    $player_name = isset($_POST['cbn_reg_player_name']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_name'])), $lengths['name']) : '';
    $player_birthdate_raw = isset($_POST['cbn_reg_player_birthdate']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_birthdate'])), $lengths['short']) : '';
    $player_sex_key = isset($_POST['cbn_reg_player_sex']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_sex'])), $lengths['short']) : '';
    $player_dni = isset($_POST['cbn_reg_player_dni']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_dni'])), $lengths['dni']) : '';
    $player_height = isset($_POST['cbn_reg_player_height']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_height'])), $lengths['short']) : '';
    $player_weight = isset($_POST['cbn_reg_player_weight']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_player_weight'])), $lengths['short']) : '';

    // Datos del padre/madre/tutor.
    $guardian_name = isset($_POST['cbn_reg_guardian_name']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_guardian_name'])), $lengths['name']) : '';
    $guardian_phone = isset($_POST['cbn_reg_guardian_phone']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_guardian_phone'])), $lengths['phone']) : '';
    $guardian_email = isset($_POST['cbn_reg_guardian_email']) ? cbn_form_limit_length(sanitize_email(wp_unslash($_POST['cbn_reg_guardian_email'])), $lengths['email']) : '';
    $guardian_relationship_key = isset($_POST['cbn_reg_guardian_relationship']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_guardian_relationship'])), $lengths['short']) : '';

    // Contacto del solicitante.
    $contact_phone = isset($_POST['cbn_reg_contact_phone']) ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_reg_contact_phone'])), $lengths['phone']) : '';
    $contact_email = isset($_POST['cbn_reg_contact_email']) ? cbn_form_limit_length(sanitize_email(wp_unslash($_POST['cbn_reg_contact_email'])), $lengths['email']) : '';

    // Consentimientos.
    $consent_rgpd = isset($_POST['cbn_reg_consent_rgpd']) ? sanitize_text_field(wp_unslash($_POST['cbn_reg_consent_rgpd'])) : '';
    $consent_guardian = isset($_POST['cbn_reg_consent_guardian']) ? sanitize_text_field(wp_unslash($_POST['cbn_reg_consent_guardian'])) : '';

    $form_defaults = cbn_get_registration_defaults()['form'];
    $sex_label = $form_defaults['sex_options'][$player_sex_key] ?? '';
    $relationship_label = $form_defaults['relationship_options'][$guardian_relationship_key] ?? '';

    $player_birthdate = cbn_registration_parse_birthdate($player_birthdate_raw);
    $age = cbn_registration_calculate_age($player_birthdate_raw);

    $is_valid = '' !== $player_name
        && null !== $age
        && '' !== $sex_label
        && '' !== $contact_phone
        && is_email($contact_email)
        && '1' === $consent_rgpd;

    if (!$is_valid) {
        wp_safe_redirect(add_query_arg('cbn_registration', 'invalid', $redirect_base));
        exit;
    }

    $is_minor = $age < 18;

    if ($is_minor) {
        $guardian_valid = '' !== $guardian_name
            && '' !== $guardian_phone
            && is_email($guardian_email)
            && '1' === $consent_guardian;

        if (!$guardian_valid) {
            wp_safe_redirect(add_query_arg('cbn_registration', 'minor_guardian', $redirect_base));
            exit;
        }
    }

    // Second bucket, keyed on the applicant's address.
    if (cbn_form_rate_limit_exceeded('cbn_registration', 'email', $contact_email)) {
        cbn_form_log_rejection('cbn_registration', 'rate limit por email');
        wp_safe_redirect(add_query_arg('cbn_registration', 'rate_limited', $redirect_base));
        exit;
    }

    cbn_form_register_attempt('cbn_registration', 'email', $contact_email);

    $admin_email = get_option('admin_email');
    $mail_subject = 'SOLICITUD DE INSCRIPCION (datos personales - tratar segun RGPD)';

    $mail_body = implode(
        "\n",
        [
            'SOLICITUD DE INSCRIPCION (datos personales - tratar segun RGPD)',
            '',
            'Datos del jugador/a:',
            sprintf('Nombre y apellidos: %s', $player_name),
            sprintf(
                'Fecha de nacimiento: %s (edad: %d años)',
                $player_birthdate instanceof DateTimeImmutable
                    ? $player_birthdate->format('d/m/Y')
                    : 'No indicada',
                $age
            ),
            sprintf('Sexo: %s', $sex_label),
            sprintf('DNI: %s', '' !== $player_dni ? $player_dni : 'No indicado'),
            sprintf('Estatura: %s', '' !== $player_height ? $player_height : 'No indicada'),
            sprintf('Peso: %s', '' !== $player_weight ? $player_weight : 'No indicado'),
            '',
            'Datos del padre/madre/tutor:',
            sprintf('Nombre y apellidos: %s', '' !== $guardian_name ? $guardian_name : 'No indicado'),
            sprintf('Teléfono: %s', '' !== $guardian_phone ? $guardian_phone : 'No indicado'),
            sprintf('Email: %s', '' !== $guardian_email ? $guardian_email : 'No indicado'),
            sprintf('Relación: %s', '' !== $relationship_label ? $relationship_label : 'No indicada'),
            '',
            'Contacto del solicitante:',
            sprintf('Teléfono: %s', $contact_phone),
            sprintf('Email: %s', $contact_email),
            '',
            sprintf('Menor de edad: %s', $is_minor ? 'Sí' : 'No'),
            sprintf('Consentimiento RGPD: %s', '1' === $consent_rgpd ? 'Sí' : 'No'),
            sprintf('Consentimiento padre/madre/tutor: %s', '1' === $consent_guardian ? 'Sí' : 'No'),
        ]
    );

    // Reply-To carries the applicant's contact address. The display name is
    // intentionally NOT the player's name: for a minor that would put
    // personal data into a header that gets copied around on every reply.
    $sent = wp_mail($admin_email, $mail_subject, $mail_body, cbn_form_mail_headers($contact_email));

    wp_safe_redirect(add_query_arg('cbn_registration', $sent ? 'ok' : 'error', $redirect_base));
    exit;
}

/**
 * Parses the birth date posted by the <input type="date"> field.
 *
 * Deliberately strict: only a literal Y-m-d calendar date is accepted.
 * `new DateTimeImmutable($string)` would also accept PHP relative formats
 * ("-20 years", "yesterday"), which a crafted POST could use to fake a
 * plausible age and skip the guardian branch below. The leading "!" in the
 * format resets all unparsed fields to the Unix epoch so the comparison is
 * date-only, and the site timezone is used on both sides so the 18-year
 * boundary cannot shift by a day.
 */
function cbn_registration_parse_birthdate(string $raw_date): ?DateTimeImmutable
{
    if ('' === $raw_date) {
        return null;
    }

    $birth_date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw_date, wp_timezone());
    $parse_errors = DateTimeImmutable::getLastErrors();

    if (!$birth_date instanceof DateTimeImmutable) {
        return null;
    }

    // getLastErrors() returns false (PHP >= 8.2) or an array of counters.
    if (is_array($parse_errors)
        && (!empty($parse_errors['warning_count']) || !empty($parse_errors['error_count']))
    ) {
        return null;
    }

    // Rejects overflow dates such as 2020-02-31, which createFromFormat
    // would silently roll over to 2020-03-02.
    if ($birth_date->format('Y-m-d') !== $raw_date) {
        return null;
    }

    return $birth_date;
}

/**
 * Returns the age in completed years for a posted birth date, or null when
 * the date cannot be parsed or falls outside a sane range: no future dates,
 * and an age between 3 and 99 years.
 */
function cbn_registration_calculate_age(string $raw_date): ?int
{
    $birth_date = cbn_registration_parse_birthdate($raw_date);

    if (!$birth_date instanceof DateTimeImmutable) {
        return null;
    }

    $today = new DateTimeImmutable('today', wp_timezone());

    if ($birth_date > $today) {
        return null;
    }

    $age = $today->diff($birth_date)->y;

    if ($age < 3 || $age > 99) {
        return null;
    }

    return $age;
}
