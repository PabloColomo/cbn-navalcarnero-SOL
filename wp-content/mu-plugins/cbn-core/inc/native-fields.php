<?php
/**
 * Dependency-free CBN content fields.
 *
 * The project ships ACF JSON definitions, but the public repository does not
 * bundle ACF. These native meta boxes read the same definitions so an
 * administrator can manage every essential field without installing another
 * plugin. If ACF is installed later, these boxes disable themselves.
 */

if (!defined('ABSPATH')) {
    exit;
}

// accepted_args = 2: the callback needs ($post_type, $post) and WordPress
// only passes the second argument when asked. Without it every edit screen
// with a CBN field group died with an ArgumentCountError fatal.
add_action('add_meta_boxes', 'cbn_native_fields_register_meta_box', 10, 2);
add_action('save_post', 'cbn_native_fields_save', 10, 2);
add_action('admin_enqueue_scripts', 'cbn_native_fields_enqueue_assets');

function cbn_native_fields_enabled(): bool
{
    return !function_exists('get_field');
}

/**
 * Returns the matching ACF-compatible field group for a post.
 *
 * @return array<string, mixed>|null
 */
function cbn_native_fields_group_for_post(WP_Post $post): ?array
{
    $filename = '';

    if ('cbn_team' === $post->post_type) {
        $filename = 'group_cbn_team_details.json';
    } elseif ('cbn_match' === $post->post_type) {
        $filename = 'group_cbn_match_details.json';
    } elseif ('cbn_sponsor' === $post->post_type) {
        $filename = 'group_cbn_sponsor_details.json';
    } elseif ('page' === $post->post_type) {
        $front_page_id = (int) get_option('page_on_front');
        $slug = (string) $post->post_name;

        if ($front_page_id > 0 && $post->ID === $front_page_id) {
            $filename = 'group_cbn_home_content.json';
        } elseif ('contacto' === $slug || 'page-contacto.php' === get_page_template_slug($post)) {
            $filename = 'group_cbn_contact_content.json';
        } elseif ('inscripcion' === $slug || 'page-inscripcion.php' === get_page_template_slug($post)) {
            $filename = 'group_cbn_registration_content.json';
        } elseif ('el-club' === $slug || 'page-el-club.php' === get_page_template_slug($post)) {
            return cbn_native_fields_club_group();
        }
    }

    return '' !== $filename ? cbn_native_fields_load_group($filename) : null;
}

/**
 * @return array<string, mixed>|null
 */
function cbn_native_fields_load_group(string $filename): ?array
{
    static $cache = [];

    if (array_key_exists($filename, $cache)) {
        return $cache[$filename];
    }

    $path = get_theme_file_path('acf-json/' . basename($filename));

    if (!is_readable($path)) {
        $cache[$filename] = null;
        return null;
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    $cache[$filename] = is_array($decoded) && isset($decoded['fields']) ? $decoded : null;

    return $cache[$filename];
}

/**
 * Structured fields for El Club, whose original helper predates its ACF JSON.
 *
 * @return array<string, mixed>
 */
function cbn_native_fields_club_group(): array
{
    $text = static fn (string $name, string $label, string $default = ''): array => [
        'name' => $name,
        'label' => $label,
        'type' => 'text',
        'default_value' => $default,
    ];
    $textarea = static fn (string $name, string $label, string $default = ''): array => [
        'name' => $name,
        'label' => $label,
        'type' => 'textarea',
        'default_value' => $default,
    ];
    $tab = static fn (string $label): array => ['name' => '', 'label' => $label, 'type' => 'tab'];

    return [
        'title' => 'CBN El Club',
        'fields' => [
            $tab('Presentación'),
            $text('cbn_club_intro_label', 'Etiqueta', 'El club'),
            $text('cbn_club_intro_title', 'Título', 'Baloncesto de Navalcarnero, para Navalcarnero'),
            $textarea('cbn_club_intro_lead', 'Entradilla', 'Promovemos y divulgamos el baloncesto en Navalcarnero, con el foco puesto en el deporte de base.'),
            $textarea('cbn_club_intro_text', 'Texto principal', 'Trabajamos cada temporada para que niños, niñas y jóvenes de la localidad tengan un lugar donde aprender, competir y crecer a través del baloncesto.'),
            $text('cbn_club_intro_primary_label', 'Botón principal', 'Inscribirse'),
            $text('cbn_club_intro_primary_url', 'URL del botón principal', '/inscripcion/'),
            $text('cbn_club_intro_secondary_label', 'Botón secundario', 'Contactar'),
            $text('cbn_club_intro_secondary_url', 'URL del botón secundario', '/contacto/'),
            $tab('Secciones'),
            $text('cbn_club_values_heading', 'Título de valores', 'Nuestros valores'),
            $text('cbn_club_facilities_heading', 'Título de instalaciones', 'Instalaciones'),
            $text('cbn_club_stats_heading', 'Título de datos', 'El club en datos'),
            $tab('Escuela y cantera'),
            $text('cbn_club_school_label', 'Etiqueta', 'Escuela y cantera'),
            $text('cbn_club_school_title', 'Título', 'La cantera, nuestra prioridad'),
            $textarea('cbn_club_school_text', 'Texto', 'La Escuela CBN es la puerta de entrada al club: desde los primeros botes hasta la competición federada, acompañamos a cada jugador y jugadora en su progreso.'),
            $text('cbn_club_school_cta_label', 'Botón', 'Ver equipos'),
            $text('cbn_club_school_cta_url', 'URL del botón', '/equipos/'),
            $tab('Llamada final'),
            $text('cbn_club_cta_title', 'Título', '¿Quieres formar parte del club?'),
            $textarea('cbn_club_cta_text', 'Texto', 'Escríbenos si tienes dudas o inscríbete directamente en la temporada actual.'),
            $text('cbn_club_cta_primary_label', 'Botón principal', 'Inscribirse'),
            $text('cbn_club_cta_primary_url', 'URL principal', '/inscripcion/'),
            $text('cbn_club_cta_secondary_label', 'Botón secundario', 'Contactar'),
            $text('cbn_club_cta_secondary_url', 'URL secundaria', '/contacto/'),
        ],
    ];
}

function cbn_native_fields_register_meta_box(string $post_type, WP_Post $post): void
{
    if (!cbn_native_fields_enabled() || !current_user_can('manage_options')) {
        return;
    }

    $group = cbn_native_fields_group_for_post($post);

    if (!$group || empty($group['fields'])) {
        return;
    }

    add_meta_box(
        'cbn-native-fields',
        isset($group['title']) ? (string) $group['title'] : 'Datos CBN',
        'cbn_native_fields_render_meta_box',
        $post_type,
        'normal',
        'high',
        ['group' => $group]
    );
}

/**
 * @param array<string, mixed> $box
 */
function cbn_native_fields_render_meta_box(WP_Post $post, array $box): void
{
    $group = $box['args']['group'] ?? [];
    $fields = is_array($group['fields'] ?? null) ? $group['fields'] : [];

    wp_nonce_field('cbn_native_fields_' . $post->ID, 'cbn_native_fields_nonce');
    ?>
    <div class="cbn-native-fields" data-cbn-native-fields>
      <p class="cbn-native-fields__intro">
        Estos datos alimentan directamente la web pública. Revisa la información antes de actualizar o publicar.
      </p>
      <?php if ('cbn_team' === $post->post_type || 'cbn_player' === $post->post_type) : ?>
        <div class="cbn-native-fields__privacy">
          <strong>Privacidad de cantera:</strong> no publiques nombres, imágenes o datos de menores sin la autorización correspondiente.
        </div>
      <?php endif; ?>
      <div class="cbn-native-fields__grid">
        <?php foreach ($fields as $field) : ?>
          <?php cbn_native_fields_render_field($post, is_array($field) ? $field : []); ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
}

/**
 * @param array<string, mixed> $field
 */
function cbn_native_fields_render_field(WP_Post $post, array $field): void
{
    $type = (string) ($field['type'] ?? 'text');
    $label = (string) ($field['label'] ?? 'Campo');

    if ('tab' === $type) {
        printf(
            '<h3 class="cbn-native-fields__section">%s</h3>',
            esc_html($label)
        );
        return;
    }

    $name = sanitize_key((string) ($field['name'] ?? ''));

    if ('' === $name) {
        return;
    }

    $has_value = metadata_exists('post', $post->ID, $name);
    $value = $has_value ? get_post_meta($post->ID, $name, true) : ($field['default_value'] ?? '');
    $input_id = 'cbn-native-' . $name;
    $instructions = trim((string) ($field['instructions'] ?? ''));
    $classes = 'cbn-native-field cbn-native-field--' . sanitize_html_class($type);

    if (in_array($type, ['textarea', 'relationship', 'image'], true)) {
        $classes .= ' cbn-native-field--wide';
    }
    ?>
    <div class="<?php echo esc_attr($classes); ?>">
      <?php if ('true_false' !== $type) : ?>
        <label for="<?php echo esc_attr($input_id); ?>"><strong><?php echo esc_html($label); ?></strong></label>
      <?php endif; ?>

      <?php if ('textarea' === $type) : ?>
        <textarea id="<?php echo esc_attr($input_id); ?>" name="cbn_native[<?php echo esc_attr($name); ?>]" rows="5"><?php echo esc_textarea((string) $value); ?></textarea>
      <?php elseif ('select' === $type) : ?>
        <select id="<?php echo esc_attr($input_id); ?>" name="cbn_native[<?php echo esc_attr($name); ?>]">
          <?php foreach ((array) ($field['choices'] ?? []) as $choice_value => $choice_label) : ?>
            <option value="<?php echo esc_attr((string) $choice_value); ?>" <?php selected((string) $value, (string) $choice_value); ?>><?php echo esc_html((string) $choice_label); ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ('true_false' === $type) : ?>
        <input type="hidden" name="cbn_native[<?php echo esc_attr($name); ?>]" value="0">
        <label class="cbn-native-field__toggle" for="<?php echo esc_attr($input_id); ?>">
          <input id="<?php echo esc_attr($input_id); ?>" type="checkbox" name="cbn_native[<?php echo esc_attr($name); ?>]" value="1" <?php checked((bool) $value); ?>>
          <span><?php echo esc_html($label); ?></span>
        </label>
      <?php elseif ('post_object' === $type) : ?>
        <?php cbn_native_fields_render_post_select($field, $name, $input_id, $value, false); ?>
      <?php elseif ('relationship' === $type) : ?>
        <?php cbn_native_fields_render_post_select($field, $name, $input_id, $value, true); ?>
      <?php elseif ('image' === $type) : ?>
        <?php cbn_native_fields_render_image_field($name, $input_id, $value); ?>
      <?php else : ?>
        <?php
        $html_type = 'text';
        if ('number' === $type) {
            $html_type = 'number';
        } elseif ('date_picker' === $type) {
            $html_type = 'date';
        } elseif ('time_picker' === $type) {
            $html_type = 'time';
        } elseif (str_ends_with($name, '_email')) {
            $html_type = 'email';
        }

        $display_value = (string) $value;
        if ('date' === $html_type && preg_match('/^\d{8}$/', $display_value)) {
            $display_value = substr($display_value, 0, 4) . '-' . substr($display_value, 4, 2) . '-' . substr($display_value, 6, 2);
        }
        if ('time' === $html_type && strlen($display_value) > 5) {
            $display_value = substr($display_value, 0, 5);
        }
        ?>
        <input
          id="<?php echo esc_attr($input_id); ?>"
          type="<?php echo esc_attr($html_type); ?>"
          name="cbn_native[<?php echo esc_attr($name); ?>]"
          value="<?php echo esc_attr($display_value); ?>"
          <?php echo 'number' === $html_type ? 'step="1"' : ''; ?>
        >
      <?php endif; ?>

      <?php if ('' !== $instructions) : ?>
        <p class="description"><?php echo esc_html($instructions); ?></p>
      <?php endif; ?>
    </div>
    <?php
}

/**
 * @param array<string, mixed> $field
 * @param mixed                $value
 */
function cbn_native_fields_render_post_select(array $field, string $name, string $input_id, $value, bool $multiple): void
{
    $post_types = array_values(array_filter(array_map('sanitize_key', (array) ($field['post_type'] ?? ['post']))));
    $selected_ids = array_map('absint', $multiple ? (array) $value : [$value]);
    $posts = get_posts([
        'post_type' => $post_types ?: ['post'],
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
    ]);
    ?>
    <select
      id="<?php echo esc_attr($input_id); ?>"
      name="cbn_native[<?php echo esc_attr($name); ?>]<?php echo $multiple ? '[]' : ''; ?>"
      <?php echo $multiple ? 'multiple size="8"' : ''; ?>
    >
      <?php if (!$multiple) : ?><option value="">Sin relación</option><?php endif; ?>
      <?php foreach ($posts as $related_post) : ?>
        <?php
        // get_post_type_object() returns null for an unregistered type (a
        // deactivated plugin, orphaned content); dereferencing it would be a
        // fatal error in the editor.
        $related_type = get_post_type_object($related_post->post_type);
        $related_label = $related_type instanceof WP_Post_Type
            ? $related_type->labels->singular_name
            : $related_post->post_type;
        ?>
        <option value="<?php echo esc_attr((string) $related_post->ID); ?>" <?php selected(in_array($related_post->ID, $selected_ids, true)); ?>>
          <?php echo esc_html(get_the_title($related_post) . ' · ' . $related_label); ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php
}

/**
 * @param mixed $value
 */
function cbn_native_fields_render_image_field(string $name, string $input_id, $value): void
{
    $attachment_id = absint($value);
    $preview_url = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
    ?>
    <div class="cbn-native-media" data-cbn-native-media>
      <input id="<?php echo esc_attr($input_id); ?>" type="hidden" name="cbn_native[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr((string) $attachment_id); ?>" data-cbn-native-media-input>
      <div class="cbn-native-media__preview" data-cbn-native-media-preview>
        <?php if ($preview_url) : ?><img src="<?php echo esc_url($preview_url); ?>" alt=""><?php endif; ?>
      </div>
      <div class="cbn-native-media__actions">
        <button type="button" class="button button-secondary" data-cbn-native-media-select>Elegir imagen</button>
        <button type="button" class="button-link-delete" data-cbn-native-media-remove <?php echo $attachment_id ? '' : 'hidden'; ?>>Quitar</button>
      </div>
    </div>
    <?php
}

function cbn_native_fields_enqueue_assets(string $hook_suffix): void
{
    if (!cbn_native_fields_enabled() || !in_array($hook_suffix, ['post.php', 'post-new.php'], true)) {
        return;
    }

    wp_enqueue_media();

    $script_path = dirname(__DIR__) . '/assets/admin.js';
    wp_enqueue_script(
        'cbn-core-admin',
        WPMU_PLUGIN_URL . '/cbn-core/assets/admin.js',
        [],
        file_exists($script_path) ? (string) filemtime($script_path) : CBN_ROLE_SCHEMA_VERSION,
        true
    );
}

function cbn_native_fields_save(int $post_id, WP_Post $post): void
{
    if (!cbn_native_fields_enabled()
        || wp_is_post_autosave($post_id)
        || wp_is_post_revision($post_id)
        || !current_user_can('manage_options')
        || !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    $nonce = isset($_POST['cbn_native_fields_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['cbn_native_fields_nonce']))
        : '';

    if (!wp_verify_nonce($nonce, 'cbn_native_fields_' . $post_id)) {
        return;
    }

    $group = cbn_native_fields_group_for_post($post);
    $raw_values = isset($_POST['cbn_native']) && is_array($_POST['cbn_native'])
        ? wp_unslash($_POST['cbn_native'])
        : [];

    if (!$group || !is_array($group['fields'] ?? null)) {
        return;
    }

    foreach ($group['fields'] as $field) {
        if (!is_array($field) || 'tab' === ($field['type'] ?? '') || empty($field['name'])) {
            continue;
        }

        $name = sanitize_key((string) $field['name']);
        if (!str_starts_with($name, 'cbn_')) {
            continue;
        }

        $raw_value = $raw_values[$name] ?? null;
        $value = cbn_native_fields_sanitize_value($raw_value, $field);

        if ('' === $value || [] === $value || null === $value) {
            delete_post_meta($post_id, $name);
        } else {
            update_post_meta($post_id, $name, $value);
        }
    }
}

/**
 * @param mixed                $raw_value
 * @param array<string, mixed> $field
 * @return mixed
 */
function cbn_native_fields_sanitize_value($raw_value, array $field)
{
    $type = (string) ($field['type'] ?? 'text');
    $name = (string) ($field['name'] ?? '');

    if ('true_false' === $type) {
        return '1' === (string) $raw_value ? '1' : '0';
    }

    if ('relationship' === $type) {
        return array_values(array_filter(array_map('absint', (array) $raw_value)));
    }

    if (in_array($type, ['post_object', 'image'], true)) {
        return absint($raw_value);
    }

    if (is_array($raw_value) || null === $raw_value) {
        return '';
    }

    $raw_value = (string) $raw_value;

    if ('textarea' === $type) {
        return sanitize_textarea_field($raw_value);
    }

    if ('select' === $type) {
        $choices = array_map('strval', array_keys((array) ($field['choices'] ?? [])));
        return in_array($raw_value, $choices, true) ? $raw_value : '';
    }

    if ('number' === $type) {
        return is_numeric($raw_value) ? (string) (0 + $raw_value) : '';
    }

    if ('date_picker' === $type) {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw_value) ? $raw_value : '';
    }

    if ('time_picker' === $type) {
        return preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $raw_value) ? $raw_value : '';
    }

    if (str_ends_with($name, '_email')) {
        return sanitize_email($raw_value);
    }

    if (str_ends_with($name, '_url')) {
        return esc_url_raw($raw_value);
    }

    return sanitize_text_field($raw_value);
}
