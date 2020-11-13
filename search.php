<?php get_template_part( 'templates/page', 'header' ); ?>

<?php if ( ! have_posts() ) : ?>
  <div class="alert alert-warning">
    <?php echo esc_html__( 'Sorry, no results were found.', 'vtx' ); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php
while ( have_posts() ) :
  the_post();
  ?>
  <?php get_template_part( 'templates/search/item' ); ?>
<?php endwhile; ?>

<?php the_posts_navigation(); ?>
