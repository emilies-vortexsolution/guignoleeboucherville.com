<?php


namespace Vortex\CustomPostTypes;

use PostTypes\Taxonomy as Taxonomy;

class CustomTaxonomy extends Taxonomy {

  /**
   * Create labels for the Taxonomy
   * @return array
   */
  public function createLabels() {
     // default labels
    $labels = array(
      'name'                       => $this->plural,
      'singular_name'              => $this->singular,
      'menu_name'                  => $this->plural,
      /* translators: %s is replaced with the plural Taxonomy name */
      'all_items'                  => sprintf( _x( 'All %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the singular Taxonomy name */
      'edit_item'                  => sprintf( _x( 'Edit %s', 'taxonomy', 'vtx' ), $this->singular ),
      /* translators: %s is replaced with the singular Taxonomy name */
      'view_item'                  => sprintf( _x( 'View %s', 'taxonomy', 'vtx' ), $this->singular ),
      /* translators: %s is replaced with the singular Taxonomy name */
      'update_item'                => sprintf( _x( 'Update %s', 'taxonomy', 'vtx' ), $this->singular ),
      /* translators: %s is replaced with the singular Taxonomy name */
      'add_new_item'               => sprintf( _x( 'Add New %s', 'taxonomy', 'vtx' ), $this->singular ),
      /* translators: %s is replaced with the singular Taxonomy name */
      'new_item_name'              => sprintf( _x( 'New %s Name', 'taxonomy', 'vtx' ), $this->singular ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'parent_item'                => sprintf( _x( 'Parent %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'parent_item_colon'          => sprintf( _x( 'Parent %s:', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'search_items'               => sprintf( _x( 'Search %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'popular_items'              => sprintf( _x( 'Popular %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'separate_items_with_commas' => sprintf( _x( 'Seperate %s with commas', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'add_or_remove_items'        => sprintf( _x( 'Add or remove %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'choose_from_most_used'      => sprintf( _x( 'Choose from most used %s', 'taxonomy', 'vtx' ), $this->plural ),
      /* translators: %s is replaced with the plural Taxonomy name */
      'not_found'                  => sprintf( _x( 'No %s found', 'taxonomy', 'vtx' ), $this->plural ),
    );

    return array_replace( $labels, $this->labels );
  }


  /**
   * Create options for Taxonomy
   * @return array Options to pass to register_taxonomy
   */
  public function createOptions() {
     // default options
    $options = array(
      'hierarchical'      => true,
      'show_admin_column' => true,
      'show_in_rest'      => true,
      'rewrite'           => array(
        'slug' => $this->slug,
      ),
    );

    // replace defaults with the options passed
    $options = array_replace_recursive( $options, $this->options );

    // create and set labels
    if ( ! isset( $options['labels'] ) ) {
      $options['labels'] = $this->createLabels();
    }

    return $options;
  }

}
