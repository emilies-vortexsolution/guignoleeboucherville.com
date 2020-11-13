<?php

namespace Roots\Sage\Assets;

/**
 * Get paths for assets
 */
class JsonManifest {
  private $manifest;

  public function __construct( string $manifest_path ) {
    if ( file_exists( $manifest_path ) ) {
      require_once ABSPATH . '/wp-admin/includes/file.php';
      WP_Filesystem();
      global  $wp_filesystem;
      $this->manifest = json_decode( $wp_filesystem->get_contents( $manifest_path ), true );
    } else {
      $this->manifest = array();
    }
  }

  public function get() {
    return $this->manifest;
  }

  /**
   * @param string $key
   * @param string $default
   *
   * @return array|mixed|null
   */
  public function get_path( string $key = '', string $default = null ): array {
    $collection = $this->manifest;
    if ( is_null( $key ) ) {
      return $collection;
    }
    if ( isset( $collection[ $key ] ) ) {
      return $collection[ $key ];
    }
    foreach ( explode( '.', $key ) as $segment ) {
      if ( ! isset( $collection[ $segment ] ) ) {
        return $default;
      } else {
        $collection = $collection[ $segment ];
      }
    }
    return $collection;
  }
}

function asset_path( $filename ) {
  $dist_path = get_template_directory_uri() . '/dist/';
  $directory = dirname( $filename ) . '/';
  $file      = basename( $filename );
  static $manifest;

  if ( empty( $manifest ) ) {
    $manifest_path = get_template_directory() . '/dist/assets.json';
    $manifest      = new JsonManifest( $manifest_path );
  }

  if ( array_key_exists( $file, $manifest->get() ) ) {
    return $dist_path . $directory . $manifest->get()[ $file ];
  } else {
    return $dist_path . $directory . $file;
  }
}
