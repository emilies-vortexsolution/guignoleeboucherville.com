### Créer une taxonomy

À placer dans /inc/[fonctionnalité] dans un fichier [fonctionnalité].taxonomy.php

```
//see: https://posttypes.jjgrainger.co.uk/taxonomies/create-a-taxonomy
use Vortex\CustomPostTypes\CustomTaxonomy;

add_action(
  'after_setup_theme',
  function () {

    /**
     * technology
     */
    $sample_taxonomy = new CustomTaxonomy(
      array(
        'name'     => 'sample-taxonomy',
        'singular' => _x( 'SampleTaxonomy', 'Taxonomy singular name', 'vtx' ),
        'plural'   => _x( 'SampleTaxonomies', 'Taxonomy plurar name', 'vtx' ),
        'slug'     => 'sample-taxonomy',
      )
    );

    $sample_taxonomy->options(
      array(
        'hierarchical' => true,
      )
    );

    $sample_taxonomy->register();

  }
);