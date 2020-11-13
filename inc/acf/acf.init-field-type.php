<?php
$required_files_and_folder = array(
  'image-select-field-type',
  'post-type-select-field-type',
);


foreach ( $required_files_and_folder as $required_folder ) {
  $file_or_dir = __DIR__ . '/' . $required_folder;

  if ( is_file( $file_or_dir ) ) {
    require $file_or_dir;
  } elseif ( is_dir( $file_or_dir ) ) {
    $files = glob( $file_or_dir . '/' . $required_folder . '.*.php' );
    foreach ( $files as $file ) {
      require $file;
    }
  }
}
