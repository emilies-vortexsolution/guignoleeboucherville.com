<header class="site-header" role="banner">
  
  <div class="navbar">
    <div class="container navbar__inner">
      <div class="navbar__home-link-wrapper">
        <a class="navbar__home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <img src="<?php echo esc_url( get_logo_url() ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="navbar__logo">
        </a>
      </div>
      
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
  
  <?php
  get_partial(
    'templates/breadcrumbs/breadcrumbs',
    array(
      'show_home' => true,
    )
  );
  ?>
</header>
