    <?php
    $cbn_footer_contact = function_exists('cbn_get_contact_content')
        ? cbn_get_contact_content()
        : cbn_get_contact_defaults();
    $cbn_footer_email = (string) $cbn_footer_contact['channels']['email'];
    $cbn_footer_phone = (string) $cbn_footer_contact['channels']['phone'];
    $cbn_footer_phone_url = 'tel:' . preg_replace('/[^\d+]/', '', $cbn_footer_phone);
    $cbn_footer_admin_url = current_user_can('manage_options')
        ? admin_url('admin.php?page=cbn-panel')
        : wp_login_url(admin_url('admin.php?page=cbn-panel'));
    ?>
    <footer class="cbn-site-footer cbn-sol-footer">
      <div class="cbn-sol-footer__statement">
        <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="Club Baloncesto Navalcarnero, inicio">
          <img
            src="<?php echo esc_url(get_theme_file_uri('assets/src/images/cbn-logo.png')); ?>"
            alt=""
            width="400"
            height="400"
          >
        </a>
        <p>Baloncesto de base · Cantera · Comunidad</p>
        <strong>Navalcarnero<br><em>juega aquí.</em></strong>
      </div>

      <div class="cbn-sol-footer__grid">
        <div class="cbn-sol-footer__contact">
          <small>Contacto</small>
          <a href="<?php echo esc_url('mailto:' . $cbn_footer_email); ?>"><?php echo esc_html($cbn_footer_email); ?></a>
          <a href="<?php echo esc_url($cbn_footer_phone_url); ?>"><?php echo esc_html($cbn_footer_phone); ?></a>
          <p><?php echo esc_html((string) $cbn_footer_contact['channels']['address']); ?></p>
        </div>

        <nav class="cbn-sol-footer__nav" aria-label="Enlaces del pie">
          <div>
            <small>Club</small>
            <a href="<?php echo esc_url(home_url('/el-club/')); ?>">El club</a>
            <a href="<?php echo esc_url(home_url('/equipos/')); ?>">Equipos</a>
            <a href="<?php echo esc_url(home_url('/partidos/')); ?>">Partidos</a>
            <a href="<?php echo esc_url(home_url('/noticias/')); ?>">Noticias</a>
          </div>
          <div>
            <small>Participa</small>
            <a href="<?php echo esc_url(home_url('/inscripcion/')); ?>">Inscripciones</a>
            <a href="<?php echo esc_url(home_url('/tienda/')); ?>">Tienda</a>
            <a href="<?php echo esc_url(home_url('/sponsors/')); ?>">Patrocinadores</a>
            <a href="<?php echo esc_url(home_url('/contacto/')); ?>">Contacto</a>
          </div>
          <div>
            <small>Información</small>
            <a href="<?php echo esc_url(home_url('/documentacion/')); ?>">Documentación</a>
            <a href="<?php echo esc_url(home_url('/privacidad/')); ?>">Privacidad</a>
            <a href="<?php echo esc_url(home_url('/aviso-legal/')); ?>">Aviso legal</a>
            <a href="<?php echo esc_url($cbn_footer_admin_url); ?>" rel="nofollow">
              <?php echo current_user_can('manage_options') ? 'Panel CBN' : 'Acceso administrador'; ?>
            </a>
          </div>
        </nav>

        <div class="cbn-sol-footer__social">
          <small>En redes</small>
          <?php foreach ($cbn_footer_contact['social']['items'] as $cbn_footer_social) : ?>
            <?php if (cbn_contact_social_is_placeholder((string) $cbn_footer_social['url'])) : ?>
              <?php continue; ?>
            <?php endif; ?>
            <a href="<?php echo esc_url((string) $cbn_footer_social['url']); ?>" target="_blank" rel="noopener noreferrer">
              <?php echo esc_html((string) $cbn_footer_social['label']); ?> ↗
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="cbn-sol-footer__bottom">
        <p>&copy; <?php echo esc_html(date('Y')); ?> Club Baloncesto Navalcarnero · CIF G-80115298</p>
        <p>Hecho para vivir la pista.</p>
      </div>
    </footer>

    <?php wp_footer(); ?>
  </body>
</html>
