### Créer un template custom

À placer à la racine du thème, dans un fichier nommmé template-[nom_template].php

```
/**
 * Template Name: Custom Template
 */

while ( have_posts() ) :
  the_post();
  ?>

  <?php get_template_part( 'templates/page', 'header' ); ?>
  <?php get_template_part( 'templates/content' ); ?>
  
  <?php
endwhile;