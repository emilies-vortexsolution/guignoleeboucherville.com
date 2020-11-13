<?php

// @codingStandardsIgnoreLine
if ( empty( $_REQUEST['plugin'] ) ) {
  add_action( 'init', 'vtx_test_plugins_and_create_placeholders', 9999 );
}
/**
 * Add the placeholders at init only if there is no request to activate plugins.
 */
function vtx_test_plugins_and_create_placeholders() {
  include 'prevention-instances.php';
}


if ( WP_DEBUG ) {
  if ( is_admin() ) {
    add_action( 'admin_footer', 'vtx_display_notices_of_inactive_plugins', 9999 );
  } else {
    add_action( 'wp_footer', 'vtx_display_notices_of_inactive_plugins', 9999 );
  }
}
/**
 * Display all the saved notices.
 * This function is linked to the `wp_footer` action only if `WP_DEBUG` is TRUE.
 */
function vtx_display_notices_of_inactive_plugins() {
  global $vtx_inactive_plugins_notices;

  if ( ! empty( $vtx_inactive_plugins_notices ) ) {
    ?>
      <ul 
        id="vtx-plugins-notices"
        style="display: inline-block; position: relative; z-index: 9999; margin: 10px; padding: 6px; border: 1px solid orange; font-size: 14px; background: white;"
        onclick="this.style.display = 'none';">
        <?php foreach ( $vtx_inactive_plugins_notices as $name => $count ) { ?>
            <li>
              <strong><?php echo esc_html( $name ); ?> <em>(<?php echo esc_html( $count ); ?>)</em></strong> was used while it's plugin is inactive.
            </li>
        <?php } ?>
      </ul>
    <?php
  }
}
