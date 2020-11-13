<?php
/**
 * @param $field : Besoin du champ au complet pour pouvoir bien redistribuer les valeurs
 */
function get_images_by_field( $field = null ) {

  if ( empty( $field ) ) {
    return array();
  }

  switch ( $field['get_image_method'] ) {
    case 'images_folder_path':
          return get_images_by_theme_folder( $field['images_folder_path'] );

    case 'images_list':
          return get_images_by_strings_list( $field['images_list'] );

    case 'font':
          return get_images_by_font_path( $field['font_path'] );
  }

  return array();
}


function get_images_by_theme_folder( $path ) {

  $path                 = trim( $path, '/' );
  $images_directory_uri = get_stylesheet_directory() . "/$path";
  $images               = glob( $images_directory_uri . '/*.{jpg,jpeg,png,svg,gif,bmp}', GLOB_BRACE );

  // RETURN si pas d'images
  if ( empty( $images ) ) {
    return array();
  }

  foreach ( $images as &$image_path ) {
    $image_path = str_replace( $images_directory_uri, $path, $image_path );
  }

  return $images;
}


/**
 * Simplement explode la liste par les nouvelles ligne et renvoyer le tableau.
 */
function get_images_by_strings_list( $strings_list ) {
  // New line exploded array
  $exploded_strings_list = explode( PHP_EOL, $strings_list );
  foreach ( $exploded_strings_list as &$string ) {
    $string = trim( $string );
  }

  return $exploded_strings_list;
}


/**
 * Convertit une font en array d'images.
 */
function get_images_by_font_path( $font_path ) {
  $font_path = str_replace( '.svg', '', $font_path );
  $font_path = get_template_directory_uri() . "/$font_path.svg";

  $svg_font_reader = new SVG_Font_Reader(); // initiate the class
  $glyphs_all      = $svg_font_reader->get_glyphs( $font_path ); // get glyphs as array

  return $glyphs_all;
}


function get_html_image_processed_by_definition( $processed_image_definition ) {

  $html = '';

  if ( $processed_image_definition['is_icon'] ) {
    $html = "<span class='{$processed_image_definition['class']}' title='{$processed_image_definition['value']}'>{$processed_image_definition['glyph']}</span>";

  } elseif ( ! empty( $processed_image_definition['url'] ) ) {
    $html = "<img src='{$processed_image_definition['url']}' alt='' title='{$processed_image_definition['url']}'>";

  } elseif ( ! empty( $processed_image_definition['class'] ) ) {
    $html = "<span class='{$processed_image_definition['class']}' title='{$processed_image_definition['class']}'></span>";

  }

  return $html;
}


function process_image_definition( $image_definition, $key, $field ) {

  $processed_image_definition = array(
    'original'  => $image_definition,
    'value'     => $image_definition,
    'url'       => '',
    'class'     => '',
    'is_icon'   => false,
    'glyph'     => '',
    'font_name' => '',
  );

  if ( '?' === utf8_decode( $image_definition ) ) {
    $processed_image_definition['is_icon'] = true;
    $processed_image_definition['value']   = $key;
    $processed_image_definition['glyph']   = $image_definition;

    $exploded                                = explode( '\\', $field['font_path'] );
    $processed_image_definition['font_name'] = end( $exploded );
    if ( $processed_image_definition['font_name'] === $field['font_path'] ) {
      $exploded                                = explode( '/', $field['font_path'] );
      $processed_image_definition['font_name'] = end( $exploded );
    }

    $processed_image_definition['class'] = $processed_image_definition['font_name'];
  } elseif ( preg_match( '/\.[a-zA-Z]{1,4}$/i', $image_definition ) ) {
    // Vérifier si la définition de l'image porte vers un URL ...

    // ... et vérifier s'il est relatif ou complet
    if ( false === strpos( $image_definition, 'http' ) ) {
      // ... comme il n'est pas complet, on le rend complet.
      // $image_url = get_stylesheet_directory_uri() . "/$image_definition";
      $processed_image_definition['url'] = convert_relative_path_to_url( $image_definition );
    } else {
      $processed_image_definition['url'] = $image_definition;
    }
  } else {
    // ... sinon, on assume que ce sont des classes.
    $processed_image_definition['class'] = $image_definition;
  }

  return $processed_image_definition;
}

function convert_relative_path_to_url( $image_path ) {
  return get_stylesheet_directory_uri() . "/$image_path";
}

function convert_relative_path_to_full_directory_path( $image_path ) {
  return get_stylesheet_directory() . "/$image_path";
}

function convert_path_to_file_name( $file_path ) {
  return preg_replace( '/.*\/([^\.\/]+)\.[a-zA-Z]{1,4}$/i', '$1', $file_path );
}
