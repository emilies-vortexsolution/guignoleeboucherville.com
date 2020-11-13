<?php
//see: https://posttypes.jjgrainger.co.uk/post-types/create-a-post-type
use Vortex\CustomPostTypes\CustomPostType;

add_action(
  'after_setup_theme',
  function () {

    $names            = array(
      'name'     => 'partner',
      'singular' => _x( 'Partner', 'Post type singular name', 'vtx' ),
      'plural'   => _x( 'Partners', 'Post type plurar name', 'vtx' ),
    );
    $sample_post_type = new CustomPostType( $names );

    $sample_post_type->options(
      array(
        'supports'     => array( 'title', 'thumbnail' ),
        'show_in_rest' => true,
        'public'       => true,
        'has_archive'  => false,
        'rewrite'      => array(
          'slug' => false,
        ),
      )
    );
    $sample_post_type->icon( 'dashicons-businessman' );
    $sample_post_type->register();
  }
);
