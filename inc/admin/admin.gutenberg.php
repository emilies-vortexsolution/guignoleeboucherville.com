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

// Wrap file block in a div

function vtx_heading_decorated_style( $block_content, $block ) {
  if ( 'core/heading' === $block['blockName'] && 'is-style-decorated' === $block['attrs']['className'] ) {
    $content  = '<div class="block-heading decorated-heading">';
    $content .= get_file_contents_by_url( get_theme_asset_url( 'images/svg/guy-gauche.svg' ) );
    $content .= $block_content;
    $content .= get_file_contents_by_url( get_theme_asset_url( 'images/svg/guy-droite.svg' ) );
    $content .= '</div>';
    return $content;
  }
  return $block_content;
}

add_filter( 'render_block', 'vtx_heading_decorated_style', 10, 2 );