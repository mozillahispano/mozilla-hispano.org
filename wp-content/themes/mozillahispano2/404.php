<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

  <div id="contenido">
    <div class="portada-individual">
      <div class="texto-portada-individual">
        <h2 class="post-title">Página no encontrada</h2>
        <div id="beastainer">
          <img id="beast404le" src="/wp-content/themes/mozillahispano2/img/beast-404_LE.png" alt="" />
          <img id="beast404re" src="/wp-content/themes/mozillahispano2/img/beast-404_RE.png" alt="" />
          <img class="beast 404" src="/wp-content/themes/mozillahispano2/img/beast-404.png" alt="Página no encontrada" />
        </div>
        <div id="error404-info">
          <p>&Eacute;sta p&aacute;gina no fue encontrada, pero aqu&iacute; tienes varias opciones:</p>
          <ul>
            <li>Si necesitas ayuda, te podemos ayudar en el <a href="/foro/">foro de asistencia</a>.</li>
            <li>Si quieres colaborar con nosotros, visita la <a href="/documentacion/Colabora">p&aacute;gina de colaboraci&oacute;n</a>.</li>
            <li>Para iniciar de nuevo, ve a nuestra <a href="/">p&aacute;gina de inicio</a>.</li>
          </ul>
        </div>
      </div>
    </div>

    <?php get_sidebar(); ?>
  </div>

<?php get_footer(); ?>
