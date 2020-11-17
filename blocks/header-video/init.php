<?php

// Check if function exists and hook into setup.
if ( function_exists( 'acf_register_block_type' ) ) {

  add_action(
    'acf/init',
    function() {

      $block_slug = basename( dirname( __FILE__ ) );

      // See : https://www.advancedcustomfields.com/resources/acf_register_block_type/ for documentation
      acf_register_block_type(
        array(
          'name'            => $block_slug,
          'title'           => _x( 'Header Video', 'Block name', 'vtx' ),
          'description'     => _x( 'A header section with video.', 'Block description', 'vtx' ),
          'render_callback' => 'render_custom_blocks_callback', //you should keep this that way.
          'align'           => '', // Set the default alignment for when the block is newly added in editor: “left”, “center”, “right”, “wide” or “full”
          'enqueue_assets'  => function() {
            wp_enqueue_script( 'fancybox-js' );
            wp_enqueue_style( 'fancybox-css' );
          },
          'mode'            => 'edit',
          'category'        => 'layout', // Core: common | formatting | layout | widgets | embed
          'icon'            => 'format-video', // WordPress’ Dashicons or Custom (see documentation)
          'supports'        => array(
            'align' => false, // Switch to true if you want all alignment options or go specific if you only want some. Eg.: `array( 'full', 'wide' )`
          ),
          'keywords'        => array( 'video' ), // An array of search terms to help user discover the block while searching. (Optionnal)
        )
      );

    }
  );
}
