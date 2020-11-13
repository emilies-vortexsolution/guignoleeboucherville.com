<?php

/**
 * Generate the json required plugins files here : http://plugins.wp.vortexdev.com/
 * Add your json files in directory "required plugins"
 */

add_action( 'tgmpa_register', 'vtx_register_required_plugins' );

function vtx_register_required_plugins() {

  $plugins = get_required_plugins();

  $config = array(
    'id'           => 'vtx',
    'default_path' => '',
    'menu'         => 'tgmpa-install-plugins',
    'parent_slug'  => 'themes.php',
    'capability'   => 'edit_theme_options',
    'has_notices'  => true,
    'dismissable'  => false,
    'dismiss_msg'  => '',
    'is_automatic' => true,
    'message'      => '',
  );

  tgmpa( $plugins, $config );
}


function get_vtx_plugin_url( $plugin_file ) {
  return 'http://plugins.wp.vortexdev.com/?key=x1wiWSVY8xtetpdD3FszWn9TmUZJK6jw&plugin-name=' . $plugin_file;
}


function get_required_plugins() {

  //grab the json files
  $json_files = glob( get_stylesheet_directory() . '/settings/required-plugins/*.json' );

  if ( empty( $json_files ) ) {
      return array();
  }

    $required_plugins = array();

  //all plugins are merged here.
  //in case of plugin duplicate, the last loaded file (based on name) win.
  foreach ( $json_files as $file ) {
    $plugins          = json_decode( file_get_contents( $file, true ), true );
    $required_plugins = $plugins + $required_plugins;
  }

  //grab the source, and do some cleanup
  foreach ( $required_plugins as &$plugin_data ) {

    if ( ! empty( $plugin_data['plugin_name'] ) ) {
      $plugin_data['name'] = $plugin_data['plugin_name'];
      unset( $plugin_data['plugin_name'] );
    }

    if ( empty( $plugin_data['source'] ) && ! empty( $plugin_data['file_name'] ) ) {
      $plugin_data['source'] = get_vtx_plugin_url( $plugin_data['file_name'] );
    }
  }

  return $required_plugins;
}

add_filter( 'http_request_host_is_external', 'authorize_vtx_same_server', 10, 3 );
function authorize_vtx_same_server( $authorised, $host ) {

  if ( 'plugins.wp.vortexdev.com' === $host ) {
    return true;
  }

  return $authorised;
}
