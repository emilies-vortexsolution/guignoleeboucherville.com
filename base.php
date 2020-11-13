<?php
use Roots\Sage\Setup;
use Roots\Sage\Wrapper;
?><!doctype html>
<html class='no-js' <?php language_attributes(); ?>>
  <?php get_template_part( 'templates/head' ); ?>
  <body <?php body_class(); ?>>
    <!--[if IE]>
      <div class="alert alert-warning">
        <?php echo wp_kses_post( __( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'vtx' ) ); ?>
      </div>
    <![endif]-->

    <!-- Add a skip to content button when user is tabing -->
    <nav id="skip-nav">
      <ul>
        <li><a href="#main-content"><?php echo esc_html_x( 'Skip to main content', 'skip nav link', 'vtx' ); ?></a></li>
      </ul>
    </nav>

    <?php
      do_action( 'get_header' );
      get_template_part( 'templates/header' );
    ?>
    <div id="main-content" class="content-container" role="document">
      <div class="content row">
        <main class="main">
          <?php require Wrapper\template_path(); ?>
        </main><!-- /.main -->
      </div><!-- /.content -->
    </div><!-- /.content-container -->
    <?php
      do_action( 'get_footer' );
      get_template_part( 'templates/footer' );
      wp_footer();
    ?>
  </body>
</html>
