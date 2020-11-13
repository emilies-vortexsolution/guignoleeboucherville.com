<div id="mobile-menu-wrapper">

  <div id="mobile-menu">
    
    <nav class="nav-mobile-wrapper">
      <?php
      if ( has_nav_menu( 'mobile_navigation' ) ) :
        wp_nav_menu(
          array(
            'theme_location'      => 'mobile_navigation',
            'menu_class'          => 'nav nav-mobile',
            'depth'               => 6,
            'has_toggle_dropdown' => true,
            'make_childless_last' => false,
          )
        );
      endif;
      ?>
    </nav>
  </div>
</div>
