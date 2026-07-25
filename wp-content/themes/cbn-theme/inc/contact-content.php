<?php
/**
 * "Contacto" page content helpers and form handler.
 *
 * Follows the same placeholder-content pattern as inc/club-content.php:
 * ACF fields (when present) override defaults, otherwise the theme falls
 * back to verified public club details documented in docs/clubs/.
 */

if (!defined('ABSPATH')) {
    exit;
}

function cbn_get_contact_content(): array
{
    $defaults = cbn_get_contact_defaults();

    $content = [
        'intro' => [
            'label' => cbn_get_contact_acf_field('cbn_contact_intro_label', $defaults['intro']['label']),
            'title' => cbn_get_contact_acf_field('cbn_contact_intro_title', $defaults['intro']['title']),
            'lead' => cbn_get_contact_acf_field('cbn_contact_intro_lead', $defaults['intro']['lead']),
        ],
        'channels' => [
            'email' => cbn_get_contact_acf_field('cbn_contact_email', $defaults['channels']['email']),
            'phone' => cbn_get_contact_acf_field('cbn_contact_phone', $defaults['channels']['phone']),
            'address' => cbn_get_contact_acf_field('cbn_contact_address', $defaults['channels']['address']),
        ],
        'form' => $defaults['form'],
        'location' => $defaults['location'],
        'social' => [
            'heading' => $defaults['social']['heading'],
            'items' => [
                [
                    'label' => 'Instagram',
                    'url' => cbn_get_contact_acf_field('cbn_contact_social_instagram_url', $defaults['social']['items'][0]['url']),
                ],
                [
                    'label' => 'Facebook',
                    'url' => cbn_get_contact_acf_field('cbn_contact_social_facebook_url', $defaults['social']['items'][1]['url']),
                ],
                [
                    'label' => 'X',
                    'url' => cbn_get_contact_acf_field('cbn_contact_social_x_url', $defaults['social']['items'][2]['url']),
                ],
                [
                    'label' => 'YouTube',
                    'url' => cbn_get_contact_acf_field('cbn_contact_social_youtube_url', $defaults['social']['items'][3]['url']),
                ],
            ],
        ],
    ];

    return apply_filters('cbn_contact_content', $content);
}

function cbn_get_contact_defaults(): array
{
    return [
        'intro' => [
            'label' => 'Contacto',
            'title' => 'Hablemos',
            'lead' => 'Escríbenos para dudas sobre inscripciones, equipos, tienda o cualquier otra consulta. Te respondemos lo antes posible.',
        ],
        'channels' => [
            'email' => 'administracion@cbnavalcarnero.es',
            'phone' => '(+34) 696 849 235',
            'address' => 'C/ Río Ebro, s/n — Pabellón Municipal La Estación, Navalcarnero (Madrid)',
        ],
        'form' => [
            'heading' => 'Formulario de contacto',
            'text' => 'Rellena el formulario y nos pondremos en contacto contigo lo antes posible.',
            'subject_options' => [
                'inscripciones' => 'Inscripciones',
                'equipos' => 'Equipos',
                'tienda' => 'Tienda',
                'otro' => 'Otro',
            ],
            'consent_text' => 'Acepto que mis datos se utilicen para responder a esta consulta (texto legal definitivo pendiente de aprobación).',
            'messages' => [
                'ok' => 'Gracias por tu mensaje. Te responderemos lo antes posible.',
                'invalid' => 'Revisa los campos obligatorios y el consentimiento antes de enviar el formulario.',
                'rate_limited' => 'Has enviado varios mensajes seguidos. Espera un rato antes de volver a escribirnos, o llámanos por teléfono si es urgente.',
                'error' => 'No se ha podido enviar el mensaje. Inténtalo de nuevo más tarde.',
            ],
        ],
        'location' => [
            'heading' => 'Dónde estamos',
            'facilities' => [
                [
                    'name' => 'Pabellón Municipal La Estación',
                    'address' => 'C/ Río Ebro, s/n, 28600 Navalcarnero (Madrid)',
                    'note' => 'Sede principal de entrenamientos y partidos del club.',
                    'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Polideportivo+La+Estacion+Navalcarnero',
                ],
                [
                    'name' => 'Pabellones del Colegio María Martín',
                    'address' => 'C/ Víctimas del Terrorismo, s/n, Navalcarnero (Madrid)',
                    'note' => 'Instalaciones complementarias para escuela y categorías inferiores.',
                    'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Colegio+Maria+Martin+Navalcarnero',
                ],
            ],
        ],
        'social' => [
            'heading' => 'Síguenos',
            'items' => [
                ['label' => 'Instagram', 'url' => '#'],
                ['label' => 'Facebook', 'url' => 'https://www.facebook.com/NavalcarneroCB'],
                ['label' => 'X', 'url' => 'https://twitter.com/navalcarnerocb'],
                ['label' => 'YouTube', 'url' => 'https://www.youtube.com/embed/videoseries?list=PL_92L61ehE_rTO5oEm6eM-w0veWg70sco'],
            ],
        ],
    ];
}

function cbn_get_contact_acf_field(string $field_name, mixed $default): mixed
{
    static $contact_page_id = null;

    if (null === $contact_page_id) {
        $contact_page = get_page_by_path('contacto', OBJECT, 'page');
        $contact_page_id = $contact_page instanceof WP_Post ? $contact_page->ID : 0;
    }

    $source_id = $contact_page_id > 0 ? $contact_page_id : get_queried_object_id();
    $value = function_exists('get_field')
        ? get_field($field_name, $source_id ?: false)
        : ($source_id ? get_post_meta($source_id, $field_name, true) : null);

    if ($value === null || $value === false || $value === '') {
        return $default;
    }

    return $value;
}

/**
 * Whether a social link is a real URL rather than the unset "#" placeholder.
 */
function cbn_contact_social_is_placeholder(string $url): bool
{
    return '' === trim($url) || '#' === trim($url);
}

add_action('admin_post_cbn_contact_submit', 'cbn_handle_contact_submit');
add_action('admin_post_nopriv_cbn_contact_submit', 'cbn_handle_contact_submit');

/**
 * Handles the native contact form POST. Verifies nonce and honeypot,
 * sanitizes all input, requires RGPD consent and a valid email, sends a
 * plaintext notification to the site admin email, and redirects back to
 * the contacto page with a status query var. Never stores submissions.
 */
function cbn_handle_contact_submit(): void
{
    $fallback_redirect = home_url('/contacto/');
    $redirect_raw = isset($_POST['cbn_contact_redirect'])
        ? esc_url_raw(wp_unslash($_POST['cbn_contact_redirect']))
        : '';
    $redirect_base = wp_validate_redirect($redirect_raw, $fallback_redirect);

    // Rate limit before anything else, and count every attempt including the
    // ones that fail below: a bot probing with a stale nonce must burn its
    // allowance too, otherwise the counter is trivial to sidestep.
    $client_ip = cbn_form_client_ip();

    if (cbn_form_rate_limit_exceeded('cbn_contact', 'ip', $client_ip)) {
        cbn_form_log_rejection('cbn_contact', 'rate limit por IP');
        wp_safe_redirect(add_query_arg('cbn_contact', 'rate_limited', $redirect_base));
        exit;
    }

    cbn_form_register_attempt('cbn_contact', 'ip', $client_ip);

    $nonce = isset($_POST['cbn_contact_nonce']) ? sanitize_text_field(wp_unslash($_POST['cbn_contact_nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'cbn_contact_submit')) {
        cbn_form_log_rejection('cbn_contact', 'nonce inválido');
        wp_safe_redirect(add_query_arg('cbn_contact', 'error', $redirect_base));
        exit;
    }

    // Honeypot: real visitors never fill this hidden field. Bots that do
    // are silently told the form "worked" so they do not keep probing.
    $honeypot = isset($_POST['cbn_contact_website'])
        ? sanitize_text_field(wp_unslash($_POST['cbn_contact_website']))
        : '';

    if ('' !== $honeypot) {
        wp_safe_redirect(add_query_arg('cbn_contact', 'ok', $redirect_base));
        exit;
    }

    // Time trap: a human cannot read and complete this form in under three
    // seconds. Unlike the honeypot this redirects to 'invalid' rather than a
    // silent "ok": a hidden field can only ever be filled by a bot, but a real
    // visitor with browser autofill could conceivably submit very fast, and
    // silently dropping their message would be worse than the spam we avoid.
    // Resubmitting from the reloaded page passes.
    $time_token = isset($_POST['cbn_form_ts']) ? sanitize_text_field(wp_unslash($_POST['cbn_form_ts'])) : '';

    if (!cbn_form_time_trap_passed('cbn_contact', $time_token)) {
        cbn_form_log_rejection('cbn_contact', 'trampa temporal');
        wp_safe_redirect(add_query_arg('cbn_contact', 'invalid', $redirect_base));
        exit;
    }

    $lengths = cbn_form_max_lengths();

    $name = isset($_POST['cbn_contact_name'])
        ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_contact_name'])), $lengths['name'])
        : '';
    $email = isset($_POST['cbn_contact_email'])
        ? cbn_form_limit_length(sanitize_email(wp_unslash($_POST['cbn_contact_email'])), $lengths['email'])
        : '';
    $subject_key = isset($_POST['cbn_contact_subject'])
        ? cbn_form_limit_length(sanitize_text_field(wp_unslash($_POST['cbn_contact_subject'])), $lengths['short'])
        : '';
    $message = isset($_POST['cbn_contact_message'])
        ? cbn_form_limit_length(sanitize_textarea_field(wp_unslash($_POST['cbn_contact_message'])), $lengths['message'])
        : '';
    $consent = isset($_POST['cbn_contact_consent']) ? sanitize_text_field(wp_unslash($_POST['cbn_contact_consent'])) : '';

    $subject_options = cbn_get_contact_defaults()['form']['subject_options'];
    $subject_label = $subject_options[$subject_key] ?? '';

    $is_valid = '' !== $name
        && '' !== $message
        && '' !== $subject_label
        && '1' === $consent
        && is_email($email);

    if (!$is_valid) {
        wp_safe_redirect(add_query_arg('cbn_contact', 'invalid', $redirect_base));
        exit;
    }

    // Second bucket, keyed on the address: stops a single sender rotating
    // through IPs, and stops one address being used to bomb the club inbox.
    if (cbn_form_rate_limit_exceeded('cbn_contact', 'email', $email)) {
        cbn_form_log_rejection('cbn_contact', 'rate limit por email');
        wp_safe_redirect(add_query_arg('cbn_contact', 'rate_limited', $redirect_base));
        exit;
    }

    cbn_form_register_attempt('cbn_contact', 'email', $email);

    $admin_email = get_option('admin_email');
    $mail_subject = sprintf('[Contacto CBN] %s', $subject_label);
    $mail_body = implode(
        "\n",
        [
            sprintf('Nombre: %s', $name),
            sprintf('Email: %s', $email),
            sprintf('Asunto: %s', $subject_label),
            '',
            'Mensaje:',
            $message,
        ]
    );

    $sent = wp_mail($admin_email, $mail_subject, $mail_body, cbn_form_mail_headers($email, $name));

    wp_safe_redirect(add_query_arg('cbn_contact', $sent ? 'ok' : 'error', $redirect_base));
    exit;
}
