<?php
/**
 * Template Name: Landing
 */

while ( have_posts() ) :
  the_post();
  ?>

  <?php get_template_part( 'templates/content' ); ?>
    
  <?php
endwhile;
