<?php

namespace Vortex\files;

function puts_content( $filename = '', $content = '' ) {

  global $wp_filesystem;

  if ( empty( $filename ) ) {
    return new \WP_Error( 'Vortex files error', __( 'Filename was not defined.', 'vtx' ) );
  }

  $url = wp_nonce_url( basename( $_SERVER['REQUEST_URI'] ), 'vtx_admin_url_nonce' );

  // Get filesystem credentials needed for WP_Filesystem.
  $creds = \request_filesystem_credentials( $url, '', false, false, null );
  if ( false === $creds ) {
    return new \WP_Error( 'Vortex files error', __( 'Credentials are required to save the file.', 'vtx' ) );
  }

  // Get the upload directory.
  $wp_upload_dir = wp_upload_dir();

  // When credentials are obtained, check to make sure they work.
  if ( ! WP_Filesystem( $creds, $wp_upload_dir['basedir'] ) ) {
    // Request_filesystem_credentials a second time, but this time with the $error flag set.
    \request_filesystem_credentials( $url, '', true, false, null );
    return new \WP_Error( 'Vortex files error', __( 'Credentials are required to save the file.', 'vtx' ) );
  }

  return $wp_filesystem->put_contents( $filename, $content );

}
