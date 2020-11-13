<?php

/**
 * Records facets to filesystem.
 * Only facets will be recorded.
 *
 */

define( 'FACET_JSON_DIR', get_stylesheet_directory() . '/settings/facet-json/' );

add_action( 'option_facetwp_settings', 'vtx_facetwp_facets' );
function vtx_facetwp_facets( $facets ) {

  $files = glob( FACET_JSON_DIR . 'facet-*.json' );

  $facet_from_file = array();

  foreach ( $files as $file ) {
    $settings = file_get_contents( $file, true );
    if ( ! empty( $settings ) ) {
      $settings = json_decode( $settings, true );
      if ( ! empty( $settings ) ) {
        unset( $settings['_code'] );
        $facet_from_file[] = $settings;
      }
    }
  }

  if ( ! empty( $facet_from_file ) ) {
     $facets         = json_decode( $facets );
     $facets->facets = $facet_from_file;
     $facets         = wp_json_encode( $facets );
  }

  return $facets;
}

add_action( 'update_option_facetwp_settings', 'vtx_facetwp_update_options', 10, 3 );
function vtx_facetwp_update_options( $old_value, $value, $option ) { // phpcs:ignore

  $facets = json_decode( $value, true );

  //delete all facets
  $files = glob( FACET_JSON_DIR . 'facet-*.json' );

  foreach ( $files as $file ) {
    wp_delete_file( $file );
  }

  //rewrite saved facets
  foreach ( $facets['facets'] as $facet ) {
    unset( $facet['_code'] );
    write_facet_settings( $facet['name'], $facet );
  }
}

/**
 * Write settings to files
 *
 * @param $name
 * @param $settings
 */
function write_facet_settings( $name, $settings ) {

  $name = "facet-{$name}";
  $file = FACET_JSON_DIR . $name . '.json';

  $settings = wp_json_encode( $settings, JSON_PRETTY_PRINT );

  \Vortex\files\puts_content( $file, $settings );

}
