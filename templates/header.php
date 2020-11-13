<header class="site-header" role="banner">
  
  <div class="navbar">
    <div class="container navbar__inner">
      
      <nav class="nav-primary-wrapper">
        <?php
        if ( has_nav_menu( 'primary_navigation' ) ) :
          wp_nav_menu(
            array(
              'theme_location'      => 'primary_navigation',
              'menu_class'          => 'nav nav-primary',
              'container'           => false,
              'depth'               => 6,
              'has_toggle_dropdown' => true,
              'make_childless_last' => false,
            )
          );
        endif;
        ?>
      </nav>

      <?php get_template_part( 'templates/mobile-menu-toggle' ); ?>
    </div>
  </div>

  <?php get_template_part( 'templates/mobile-menu' ); ?>
  
</header>
