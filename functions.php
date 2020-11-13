<?php
try {
  if ( false === is_file( __DIR__ . '/vendor/autoload.php' ) ) {
    throw new Exception( 'Composer error : autoload.php does not exist. <br> You must run `npm install` (or `Composer install` )' );
  } else {
    require __DIR__ . '/vendor/autoload.php';
  }
} catch ( Exception $exception ) {
  wp_die( wp_kses( $exception->getMessage(), array( 'br' => array() ) ), 'Composer error', 500 );
}



$required_files_and_folder = array(
  //Theme base
  'acf',
  'admin',
  'breadcrumbs',
  'common',
  'controller',
  'option-page',
  'plugins',
  'plugins-errors-prevention',
  'assets.php',
  'class-tgm-plugin-activation.php',
  'customizer.php',
  'extras.php',
  'option-page-control.php',
  'post-type-link-to-page.php',
  'required-plugins.php',
  'settings.php',
  'setup.php',
  'titles.php',
  'utilities.php',
  'wrapper.php',
  'search',
  //Project addition

);

foreach ( $required_files_and_folder as $required_folder ) {
  $file_or_dir = get_template_directory() . '/inc/' . $required_folder;

  if ( is_file( $file_or_dir ) ) {
    require $file_or_dir;
  } elseif ( is_dir( $file_or_dir ) ) {
    $files = glob( $file_or_dir . '/' . $required_folder . '.*.php' );
    foreach ( $files as $file ) {
      require $file;
    }
  }
}

/**
 * Only includes for admin.
 */
add_action( 'admin_init', 'admin_includes' );
function admin_includes() {
  require 'inc/admin/svgFontReader/svg-font-reader.class.php';
}
