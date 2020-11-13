<?php while ( have_posts() ) :
  the_post(); ?>
  <article <?php post_class(); ?>>
    <?php get_template_part( 'templates/post/single/header' ); ?>
    <?php get_template_part( 'templates/content' ); ?>
    <?php get_template_part( 'templates/post/single/footer' ); ?>
    <?php comments_template( '/templates/comments.php' ); ?>
  </article>
<?php endwhile; ?>
