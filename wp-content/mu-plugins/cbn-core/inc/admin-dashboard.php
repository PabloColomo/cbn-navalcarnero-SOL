<?php
/**
 * Branded, task-focused administration experience for CBN.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'cbn_admin_register_control_panel');
add_action('wp_dashboard_setup', 'cbn_admin_register_dashboard_widget');
add_action('admin_enqueue_scripts', 'cbn_admin_enqueue_styles');
add_action('login_enqueue_scripts', 'cbn_admin_enqueue_login_styles');
add_filter('login_headerurl', 'cbn_admin_login_header_url');
add_filter('login_headertext', 'cbn_admin_login_header_text');
add_filter('login_message', 'cbn_admin_login_message');
add_filter('admin_body_class', 'cbn_admin_body_class');
add_filter('admin_footer_text', 'cbn_admin_footer_text');
add_filter('gettext', 'cbn_admin_translate_essential_core_labels', 20, 3);
add_filter('gettext_with_context', 'cbn_admin_translate_essential_core_label_with_context', 20, 4);

/**
 * @return array<string, array{label:string, singular:string, list_url:string, new_url:string, private?:bool}>
 */
function cbn_admin_content_types(): array
{
    return [
        'post' => [
            'label' => 'Noticias',
            'singular' => 'Nueva noticia',
            'list_url' => admin_url('edit.php'),
            'new_url' => admin_url('post-new.php'),
        ],
        'page' => [
            'label' => 'Páginas',
            'singular' => 'Nueva página',
            'list_url' => admin_url('edit.php?post_type=page'),
            'new_url' => admin_url('post-new.php?post_type=page'),
        ],
        'cbn_team' => [
            'label' => 'Equipos',
            'singular' => 'Nuevo equipo',
            'list_url' => admin_url('edit.php?post_type=cbn_team'),
            'new_url' => admin_url('post-new.php?post_type=cbn_team'),
        ],
        'cbn_match' => [
            'label' => 'Partidos',
            'singular' => 'Nuevo partido',
            'list_url' => admin_url('edit.php?post_type=cbn_match'),
            'new_url' => admin_url('post-new.php?post_type=cbn_match'),
        ],
        'cbn_sponsor' => [
            'label' => 'Patrocinadores',
            'singular' => 'Nuevo patrocinador',
            'list_url' => admin_url('edit.php?post_type=cbn_sponsor'),
            'new_url' => admin_url('post-new.php?post_type=cbn_sponsor'),
        ],
        'cbn_document' => [
            'label' => 'Documentos',
            'singular' => 'Nuevo documento',
            'list_url' => admin_url('edit.php?post_type=cbn_document'),
            'new_url' => admin_url('post-new.php?post_type=cbn_document'),
        ],
        'cbn_player' => [
            'label' => 'Jugadores · privado',
            'singular' => 'Nuevo jugador',
            'list_url' => admin_url('edit.php?post_type=cbn_player'),
            'new_url' => admin_url('post-new.php?post_type=cbn_player'),
            'private' => true,
        ],
    ];
}

function cbn_admin_register_control_panel(): void
{
    add_menu_page(
        'Panel CBN',
        'Panel CBN',
        'manage_options',
        'cbn-panel',
        'cbn_admin_render_control_panel',
        'dashicons-awards',
        2
    );
}

function cbn_admin_register_dashboard_widget(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    wp_add_dashboard_widget(
        'cbn_dashboard_widget',
        'CBN · Acciones rápidas',
        'cbn_admin_render_dashboard_widget'
    );
}

/**
 * @return array{published:int, working:int}
 */
function cbn_admin_count_content(string $post_type): array
{
    $counts = wp_count_posts($post_type);

    return [
        'published' => (int) ($counts->publish ?? 0),
        'working' => (int) ($counts->draft ?? 0)
            + (int) ($counts->pending ?? 0)
            + (int) ($counts->future ?? 0),
    ];
}

function cbn_admin_render_control_panel(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('No tienes permisos para acceder a este panel.', 'cbn'));
    }

    $front_page_id = (int) get_option('page_on_front');
    $front_page_edit_url = $front_page_id > 0 ? get_edit_post_link($front_page_id, 'raw') : '';
    $admin_email = sanitize_email((string) get_option('admin_email'));
    ?>
    <div class="wrap cbn-admin-wrap">
      <section class="cbn-admin-hero">
        <div>
          <p class="cbn-admin-eyebrow">Club Baloncesto Navalcarnero</p>
          <h1>Panel de juego</h1>
          <p>Publica, actualiza y revisa la web desde un único vestuario digital.</p>
        </div>
        <div class="cbn-admin-hero__actions">
          <a class="button button-primary button-hero" href="<?php echo esc_url(admin_url('post-new.php')); ?>">Publicar noticia</a>
          <a class="button button-hero" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener">Ver la web ↗</a>
        </div>
      </section>

      <section class="cbn-admin-section" aria-labelledby="cbn-content-heading">
        <div class="cbn-admin-section__heading">
          <div>
            <p>Marcador editorial</p>
            <h2 id="cbn-content-heading">Contenido del club</h2>
          </div>
          <?php if ($front_page_edit_url) : ?>
            <a class="button" href="<?php echo esc_url($front_page_edit_url); ?>">Editar portada</a>
          <?php endif; ?>
        </div>

        <div class="cbn-admin-content-grid">
          <?php foreach (cbn_admin_content_types() as $post_type => $config) : ?>
            <?php $count = cbn_admin_count_content($post_type); ?>
            <article class="cbn-admin-content-card<?php echo !empty($config['private']) ? ' is-private' : ''; ?>">
              <header>
                <span><?php echo esc_html($config['label']); ?></span>
                <?php if (!empty($config['private'])) : ?><small>NO PÚBLICO</small><?php endif; ?>
              </header>
              <div class="cbn-admin-content-card__score">
                <strong><?php echo esc_html((string) $count['published']); ?></strong>
                <span>publicados</span>
              </div>
              <p><?php echo esc_html((string) $count['working']); ?> en borrador, revisión o programación.</p>
              <footer>
                <a href="<?php echo esc_url($config['list_url']); ?>">Gestionar</a>
                <a href="<?php echo esc_url($config['new_url']); ?>"><?php echo esc_html($config['singular']); ?> →</a>
              </footer>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <div class="cbn-admin-lower-grid">
        <section class="cbn-admin-panel" aria-labelledby="cbn-tools-heading">
          <p class="cbn-admin-eyebrow">Caja de herramientas</p>
          <h2 id="cbn-tools-heading">Gestión diaria</h2>
          <div class="cbn-admin-link-grid">
            <a href="<?php echo esc_url(admin_url('media-new.php')); ?>"><strong>Subir imágenes</strong><span>Biblioteca multimedia →</span></a>
            <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><strong>Editar navegación</strong><span>Menús del sitio →</span></a>
            <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=cbn_season&post_type=cbn_team')); ?>"><strong>Temporadas</strong><span>Organización deportiva →</span></a>
            <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=cbn_sponsor_tier&post_type=cbn_sponsor')); ?>"><strong>Niveles de sponsor</strong><span>Agrupar colaboradores →</span></a>
          </div>
        </section>

        <aside class="cbn-admin-panel cbn-admin-panel--dark" aria-labelledby="cbn-rules-heading">
          <p class="cbn-admin-eyebrow">Reglas del vestuario</p>
          <h2 id="cbn-rules-heading">Antes de publicar</h2>
          <ul>
            <li>Comprueba fechas, rivales, marcadores y enlaces.</li>
            <li>No publiques datos de menores sin autorización.</li>
            <li>Usa imágenes propias o con permiso y completa su texto alternativo.</li>
            <li>Guarda como borrador si la información todavía no está confirmada.</li>
          </ul>
        </aside>
      </div>

      <section class="cbn-admin-notice" aria-labelledby="cbn-forms-heading">
        <div>
          <p class="cbn-admin-eyebrow">Contacto e inscripciones</p>
          <h2 id="cbn-forms-heading">Las solicitudes no se almacenan en WordPress</h2>
        </div>
        <p>Los formularios se envían por correo a <strong><?php echo esc_html($admin_email ?: 'el email de administración'); ?></strong>. Revisa esa bandeja y su carpeta de spam. Este diseño evita guardar datos personales de cantera en la base de datos pública.</p>
      </section>
    </div>
    <?php
}

function cbn_admin_render_dashboard_widget(): void
{
    $news = cbn_admin_count_content('post');
    $matches = cbn_admin_count_content('cbn_match');
    ?>
    <div class="cbn-dashboard-widget">
      <div><strong><?php echo esc_html((string) $news['published']); ?></strong><span>noticias</span></div>
      <div><strong><?php echo esc_html((string) $matches['published']); ?></strong><span>partidos</span></div>
    </div>
    <p class="cbn-dashboard-widget__actions">
      <a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php')); ?>">Nueva noticia</a>
      <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=cbn-panel')); ?>">Abrir Panel CBN</a>
    </p>
    <?php
}

function cbn_admin_enqueue_styles(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $style_path = dirname(__DIR__) . '/assets/admin.css';
    wp_enqueue_style(
        'cbn-core-admin',
        WPMU_PLUGIN_URL . '/cbn-core/assets/admin.css',
        [],
        file_exists($style_path) ? (string) filemtime($style_path) : CBN_ROLE_SCHEMA_VERSION
    );
}

function cbn_admin_enqueue_login_styles(): void
{
    $style_path = dirname(__DIR__) . '/assets/login.css';
    wp_enqueue_style(
        'cbn-core-login',
        WPMU_PLUGIN_URL . '/cbn-core/assets/login.css',
        [],
        file_exists($style_path) ? (string) filemtime($style_path) : CBN_ROLE_SCHEMA_VERSION
    );

    $logo_url = get_theme_file_uri('assets/src/images/cbn-logo.png');
    wp_add_inline_style(
        'cbn-core-login',
        ':root{--cbn-login-logo:url("' . esc_url_raw($logo_url) . '");}'
    );
}

function cbn_admin_login_header_url(): string
{
    return home_url('/');
}

function cbn_admin_login_header_text(): string
{
    return 'Club Baloncesto Navalcarnero';
}

function cbn_admin_login_message(string $message): string
{
    return $message . '<p class="cbn-login-intro">Acceso reservado al equipo de administración del CBN.</p>';
}

function cbn_admin_body_class(string $classes): string
{
    return $classes . ' cbn-admin-ui';
}

function cbn_admin_footer_text(string $text): string
{
    return current_user_can('manage_options')
        ? 'Panel CBN · Contenido, cantera y comunidad'
        : $text;
}

/**
 * Provides a compact Spanish fallback for the essential login/editor labels
 * when the host has not installed the official WordPress language pack.
 */
function cbn_admin_translate_essential_core_labels(string $translation, string $text, string $domain): string
{
    global $pagenow;

    if ('default' !== $domain || (!is_admin() && 'wp-login.php' !== $pagenow)) {
        return $translation;
    }

    // Static: this filter fires on every single translation call, thousands of
    // times per admin page load. Rebuilding the array each time is waste.
    static $labels = null;

    if (null !== $labels) {
        return $labels[$text] ?? $translation;
    }

    $labels = [
        'Username or Email Address' => 'Usuario o correo electrónico',
        'Password' => 'Contraseña',
        'Remember Me' => 'Recuérdame',
        'Log In' => 'Acceder',
        'Lost your password?' => '¿Has olvidado tu contraseña?',
        'Go to %s' => 'Volver a %s',
        '&larr; Go to %s' => '&larr; Volver a %s',
        '← Go to %s' => '← Volver a %s',
        'Show password' => 'Mostrar contraseña',
        'Hide password' => 'Ocultar contraseña',
        'Dashboard' => 'Escritorio',
        'Posts' => 'Noticias',
        'All Posts' => 'Todas las noticias',
        'Add New Post' => 'Añadir noticia',
        'Media' => 'Medios',
        'Library' => 'Biblioteca',
        'Pages' => 'Páginas',
        'All Pages' => 'Todas las páginas',
        'Comments' => 'Comentarios',
        'Appearance' => 'Apariencia',
        'Menus' => 'Menús',
        'Users' => 'Usuarios',
        'Tools' => 'Herramientas',
        'Settings' => 'Ajustes',
        'Publish' => 'Publicar',
        'Update' => 'Actualizar',
        'Save draft' => 'Guardar borrador',
        'Preview' => 'Previsualizar',
        'Move to Trash' => 'Mover a la papelera',
        'Featured image' => 'Imagen destacada',
        'Set featured image' => 'Asignar imagen destacada',
        'Log Out' => 'Cerrar sesión',
    ];

    return $labels[$text] ?? $translation;
}

function cbn_admin_translate_essential_core_label_with_context(string $translation, string $text, string $context, string $domain): string
{
    return cbn_admin_translate_essential_core_labels($translation, $text, $domain);
}
