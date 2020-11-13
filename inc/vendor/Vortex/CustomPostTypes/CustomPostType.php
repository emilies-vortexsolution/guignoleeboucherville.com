<?php


namespace Vortex\CustomPostTypes;

use PostTypes\PostType as PostType;

class CustomPostType extends PostType {

  public $menu_target;
  public $menu_position;

  /**
   * Create the labels for the PostType
   * @return array
   */
  public function createLabels() {
     // default labels
    $labels = array(
      'name'               => $this->plural,
      'singular_name'      => $this->singular,
      'menu_name'          => $this->plural,
      'all_items'          => $this->plural,
      'add_new'            => _x( 'Add New', 'Custom Post Type', 'vtx-admin' ),
      /* translators: %s is replaced with the singulare Custom Post Type name */
      'add_new_item'       => sprintf( _x( 'Add New %s', 'Custom Post Type', 'vtx-admin' ), $this->singular ),
      /* translators: %s is replaced with the singulare Custom Post Type name */
      'edit_item'          => sprintf( _x( 'Edit %s', 'Custom Post Type', 'vtx-admin' ), $this->singular ),
      /* translators: %s is replaced with the singulare Custom Post Type name */
      'new_item'           => sprintf( _x( 'New %s', 'Custom Post Type', 'vtx-admin' ), $this->singular ),
      /* translators: %s is replaced with the singulare Custom Post Type name */
      'view_item'          => sprintf( _x( 'View %s', 'Custom Post Type', 'vtx-admin' ), $this->singular ),
      /* translators: %s is replaced with the plural Custom Post Type name */
      'search_items'       => sprintf( _x( 'Search %s', 'Custom Post Type', 'vtx-admin' ), $this->plural ),
      /* translators: %s is replaced with the plural Custom Post Type name */
      'not_found'          => sprintf( _x( 'No %s found', 'Custom Post Type', 'vtx-admin' ), $this->plural ),
      /* translators: %s is replaced with the singulare Custom Post Type name */
      'not_found_in_trash' => sprintf( _x( 'No %s found in Trash', 'Custom Post Type', 'vtx-admin' ), $this->plural ),
      /* translators: %s is replaced with the plural Custom Post Type name */
      'parent_item_colon'  => sprintf( _x( 'Parent %s:', 'Custom Post Type', 'vtx-admin' ), $this->singular ),
    );

    return array_replace_recursive( $labels, $this->labels );
  }

  /**
   * This method set the menu postion.
   * Thanks to MADL for the functions used inside.
   *
   * @param Int $priority - Highest priority get placed closest to the target. So first item should have highest.
   * @param String $target
   * @param Int $relative_position
   *
   * @author Sariha Chabert <sariha.c@vortexsolution.com>
   *
   */
  public function menu_position( $priority = 100, $target = 'edit.php?post_type=page', $relative_position = 1 ) {

    $this->menu_priority = $priority;
    $this->menu_target   = $target;
    $this->menu_position = $relative_position;

    $this->menu_slug = "post_type={$this->name}";
    if ( 'post' === $this->name ) {
      $this->menu_slug = 'edit.php';
    }

    /**
     * set menu postion in admin
     */
    if ( is_admin() ) {
      add_action(
        'menu_order',
        function ( $menu_items ) {
          return change_menu_item_position( $menu_items, $this->menu_slug, $this->menu_target, $this->menu_position );
        },
        $this->menu_priority
      );
    }
  }

  /**
   * This method set the menu color.
   *
   * @param String $target
   * @param Int $relative_position
   *
   * @author Marc-André De Launière <marc-andre.d@vortexsolution.com>
   *
   */
  public function menu_color( $color = PRIMARY_COLOR ) {

    $this->menu_color = $color;

    /**
     * set menu color in admin
     */
    if ( is_admin() ) {
      add_action(
        'admin_head',
        function() {
          apply_admin_button_style( $this->name, $this->menu_color );
        }
      );
    }
  }
}
