### Créer un post type

À placer dans /inc/[fonctionnalité] dans un fichier [fonctionnalité].post-type.php

```
//see: https://posttypes.jjgrainger.co.uk/post-types/create-a-post-type
use Vortex\CustomPostTypes\CustomPostType;

add_action(
  'after_setup_theme',
  function () {

    $names = array(
      'name'     => 'sample-post-type',
      'singular' => _x( 'SamplePostType', 'Post type singular name', 'vtx' ),
      'plural'   => _x( 'SamplePostTypes', 'Post type plurar name', 'vtx' ),
      'slug'     => 'sample-taxonomy',
    );
    $sample_post_type = new CustomPostType( $names );

    $sample_post_type->options(
      array(
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'show_in_rest' => true,
        'public'       => true,
        'has_archive'  => false,
      )
    );
    $sample_post_type->icon( 'dashicons-businessman' );
    $sample_post_type->taxonomy( 'sector' );
    $sample_post_type->menu_position();
    $sample_post_type->menu_color();
    $sample_post_type->register();
  }
);
