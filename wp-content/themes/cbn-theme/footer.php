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
          <a href="mailto:administracion@cbnavalcarnero.es">administracion@cbnavalcarnero.es</a>
          <a href="tel:+34696849235">(+34) 696 849 235</a>
          <p>C/ Río Ebro, s/n<br>Pabellón Municipal La Estación<br>28600 Navalcarnero, Madrid</p>
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
          </div>
        </nav>

        <div class="cbn-sol-footer__social">
          <small>En redes</small>
          <a href="https://www.facebook.com/NavalcarneroCB" target="_blank" rel="noreferrer">Facebook ↗</a>
          <a href="https://twitter.com/navalcarnerocb" target="_blank" rel="noreferrer">X / Twitter ↗</a>
          <a href="https://www.youtube.com/embed/videoseries?list=PL_92L61ehE_rTO5oEm6eM-w0veWg70sco" target="_blank" rel="noreferrer">YouTube ↗</a>
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
