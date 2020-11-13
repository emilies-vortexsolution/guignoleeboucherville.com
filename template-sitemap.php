<?php
/**
 * Template Name: Sitemap
 *
 * @todo Add more control over more object type like taxonomies, terms, media, etc.
 * @todo Add possibilities to make list hierarchical.
 */

while ( have_posts() ) :
  the_post();
  ?>

  <?php get_template_part( 'templates/page', 'header' ); ?>
  <?php get_template_part( 'templates/content' ); ?>
  <?php
  get_partial(
    'templates/sitemap/sitemap',
    array(
      'post_types' => array(
        'page',
        'post',
      ),
    )
  );
  ?>
    
  <?php
endwhile;
