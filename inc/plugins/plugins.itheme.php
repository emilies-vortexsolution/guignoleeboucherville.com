<?php

define( 'ITHEME_PLUGIN_FILE', ABSPATH . 'wp-content/plugins/ithemes-security-pro/ithemes-security-pro.php' );
define( 'ITHEME_PLUGIN_FILE_IMPORTER', ABSPATH . 'wp-content/plugins/ithemes-security-pro/pro/import-export/importer.php' );
define( 'ITHEME_JSON_DIR', get_stylesheet_directory() . '/settings/itheme-json/' );

/**
 * On plugin activation proceed to import default configuration from itsec_options.json
 */
register_activation_hook( ITHEME_PLUGIN_FILE, 'import_itheme_default_settings' );
function import_itheme_default_settings() {
  require_once ITHEME_PLUGIN_FILE_IMPORTER;

  $path = ITHEME_JSON_DIR . 'itsec_options.json';
  $type = 'file';
  ITSEC_Import_Export_Importer::import_from_file_path( $path, $type );
}
