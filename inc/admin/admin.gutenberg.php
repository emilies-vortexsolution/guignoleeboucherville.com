<?php

// Add support for full and wide align images.
add_theme_support( 'align-wide' );

// Remove Gutenberg custom font size for paragraph
add_theme_support( 'disable-custom-font-sizes' );

//limit to 3 option for paragraph font size
add_theme_support(
  'editor-font-sizes',
  array(
    array(
      'name'      => esc_html_x( 'Small', 'Block param name', 'vtx' ),
      'shortName' => esc_html_x( 'S', 'Block param shortname', 'vtx' ),
      'size'      => 14,
      'slug'      => 'small',
    ),
    array(
      'name'      => esc_html_x( 'Large', 'Block param name', 'vtx' ),
      'shortName' => esc_html_x( 'L', 'Block param shortname', 'vtx' ),
      'size'      => 18,
      'slug'      => 'large',
    ),
  )
);
