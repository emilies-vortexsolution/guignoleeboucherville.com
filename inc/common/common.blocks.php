<?php

//blocks custom loading
if ( is_dir( get_stylesheet_directory() . '/blocks/' ) ) {
  foreach ( glob( get_stylesheet_directory() . '/blocks/*/init.php' ) as $file ) {
    if ( strpos( $file, '_sample-bloc' ) === false ) {
        require_once $file;
    }
  }
}

/**
 *
 *
 * @param $block
 * @param string $content
 * @param bool $is_preview
 * @param int $post_id
 *
 * @author Sariha Chabert <sariha.c@vortexsolution.com>
 *
 */
function render_custom_blocks_callback( array $block, string $content = '', bool $is_preview = false, int $post_id = 0 ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed

  $template      = str_replace( 'acf/', '', $block['name'] );
  $block_dir     = get_stylesheet_directory() . '/blocks/' . $template . '/';
  $template_file = $block_dir . 'template.php';

  if ( $is_preview || ( defined( 'REST_REQUEST' ) && true === REST_REQUEST ) ) {
      $preview_file = $block_dir . 'preview.php';

    if ( is_file( $preview_file ) ) {
        include $preview_file;
    } else {
        include $template_file;
    }
  } else {

    if ( is_file( $template_file ) ) {
        include $template_file;
    } else {
        echo '<!-- Template was not found : ' . esc_html( $template_file ) . ' ( post_id :' . esc_html( $post_id ) . ' ) -->';
    }
  }
}

function include_block( $block_name = '', $data = array() ) {
    $block = array(
      'blockName'    => $block_name,
      'attrs'        => array(
        'id'   => microtime(), //id have to be unique
        'name' => $block_name,
        'data' => $data,
      ),
      'innerContent' => array(),
    );

    echo esc_html( render_block( $block ) );
}

add_filter( 'acf/fields/wysiwyg/toolbars', 'add_custom_wysiwyg_toolbars' );
function add_custom_wysiwyg_toolbars( $toolbars ) {
    // this toolbar has only 1 row of buttons
    $toolbars['Minimum']    = array();
    $toolbars['Minimum'][1] = array( 'bold', 'italic', 'underline', 'link' );

    return $toolbars;
}

/**
 * Inside a block template. Use to get the full align class of that block.
 */
function get_block_align_class( $block ) {
  if ( empty( $block['align'] ) ) {
    return 'aligndefault';
  }
  return "align{$block['align']}";
}
