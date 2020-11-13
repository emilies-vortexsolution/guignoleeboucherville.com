<?php

OPTIONS_SITE::init();

class OPTIONS_SITE {

  public static $name;

  public static function init() {
    self::$name = 'site';

    if ( function_exists( 'acf_add_options_page' ) ) {
      add_action( 'acf/init', array( __CLASS__, 'init_option_page' ) );
    }

    // Modifier la position du bouton menu dans l'admin
    add_action(
      'menu_order',
      function( $menu_items ) {
        return change_menu_item_position( $menu_items, self::$name . '_options', 'themes.php', 0 );
      },
      100
    );

    // Modifier le style du bouton
    add_action(
      'admin_head',
      function() {
        apply_admin_button_style( self::$name . '_options', PRIMARY_COLOR, 'toplevel' );
      }
    );
  }

  public static function init_option_page() {

    $prefix = self::$name . '_';

    $option_page = acf_add_options_page(
      array(
        'id'          => "{$prefix}options",
        'option_name' => "{$prefix}options",
        'menu_slug'   => "{$prefix}options",
        'menu_title'  => _x( 'Options', 'Admin menu title', 'vtx-admin' ),
        'page_title'  => _x( 'Options', 'Admin page title', 'vtx-admin' ),
        'icon_url'    => get_template_directory_uri() . '/assets/favicon/favicon-16x16.png',
        'position'    => 5,
        'redirect'    => false,
      )
    );

    acf_add_options_sub_page(
      array(
        'id'                       => "{$option_page['id']}_post_type_link_to_page",
        'option_name'              => 'post_type_link_to_page',
        'menu_slug'                => 'post_type_link_to_page',
        'menu_title'               => _x( 'Lien entre une page et un type de publication', 'Admin menu title', 'vtx-admin' ),
        'page_title'               => _x( 'Lien entre une page et un type de publication', 'Admin page title', 'vtx-admin' ),
        'parent_slug'              => $option_page['menu_slug'],
        'redirect_to_default_lang' => true,
        'do_flush_rewrite'         => true,
      )
    );

    acf_add_options_sub_page(
      array(
        'id'          => "{$option_page['id']}_default_image_by_post_type",
        'option_name' => 'default_image_by_post_type',
        'menu_slug'   => 'default_image_by_post_type',
        'menu_title'  => _x( 'Default image by post type', 'Admin menu title', 'vtx-admin' ),
        'page_title'  => _x( 'Default image by post type', 'Admin page title', 'vtx-admin' ),
        'parent_slug' => $option_page['menu_slug'],
      )
    );

    if ( search_can_be_inited() ) {
      acf_add_options_sub_page(
        array(
          'id'                       => "{$option_page['id']}_global_search",
          'option_name'              => 'global_search_options',
          'menu_slug'                => 'global_search_options',
          'menu_title'               => _x( 'Recherche globale', 'Admin menu title', 'vtx-admin' ),
          'page_title'               => _x( 'Recherche globale', 'Admin page title', 'vtx-admin' ),
          'parent_slug'              => $option_page['menu_slug'],
          'redirect_to_default_lang' => false,
          'do_flush_rewrite'         => true,
        )
      );
    }

  }
}
