<?php
add_action( 'acf/include_field_types', 'include_field_type_image_select' );
function include_field_type_image_select() {
  include_once 'image-select-helpers.php';
  include_once 'image-select-v5.php';
}

add_action( 'acf/input/admin_enqueue_scripts', 'enqueue_field_type_image_select_scripts' );
function enqueue_field_type_image_select_scripts() {
  wp_enqueue_script( 'image-select-js', get_template_directory_uri() . '/inc/acf/image-select-field-type/js/fields-logic.js', array( 'jquery' ), '1.0.0', true );
  wp_enqueue_style( 'image-select-css', get_template_directory_uri() . '/inc/acf/image-select-field-type/css/styles.css', array(), '1.0.0' );
}
