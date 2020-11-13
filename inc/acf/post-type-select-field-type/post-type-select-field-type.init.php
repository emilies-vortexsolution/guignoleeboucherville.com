<?php

add_action( 'acf/include_field_types', 'include_field_type_post_type_select' );
function include_field_type_post_type_select() {
  include_once 'post-type-select-helpers.php';
  include_once 'post-type-select-v5.php';
}


add_action( 'acf/input/admin_enqueue_scripts', 'enqueue_field_type_post_type_select_scripts' );
function enqueue_field_type_post_type_select_scripts() {
  wp_enqueue_style( 'post-type-select-css', get_template_directory_uri() . '/inc/acf/post-type-select-field-type/css/styles.css', array(), '1.0.0' );
}
