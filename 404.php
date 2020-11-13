<?php
$blocks = array(
  0 => array(
    'blockName'    => 'core/paragraph',
    'attrs'        => array(
      'id'   => microtime(),
      'name' => 'core/paragraph',
    ),
    'innerBlocks'  => array(),
    'innerContent' => array(
      '<p>',
      esc_html_x( 'We are sorry, but the page or file you are looking for can\'t be found.', '404 page content', 'vtx' ),
      '</p>',
    ),
  ),
  1 => array(
    'blockName'    => 'core/paragraph',
    'attrs'        => array(
      'id'   => microtime(),
      'name' => 'core/paragraph',
    ),
    'innerBlocks'  => array(),
    'innerContent' => array(
      '<p>',
      wp_kses_post(
        sprintf(
          /* translators: 404 page content, home page url */
          _x( 'We invite you to navigate from the <a href="%s">home page</a>.', '404 page content, home page url', 'vtx' ),
          get_home_url()
        )
      ),
      '</p>',
    ),
  ),
  2 => array(
    'blockName'    => 'core/paragraph',
    'attrs'        => array(
      'id'   => microtime(),
      'name' => 'core/paragraph',
    ),
    'innerBlocks'  => array(),
    'innerContent' => array(
      '<p>',
      esc_html_x( 'Thank you!', '404 page content', 'vtx' ),
      '</p>',
    ),
  ),
);
?>

<?php get_template_part( 'templates/page', 'header' ); ?>

<div class="entry-content">
  <?php
  foreach ( $blocks as $block ) :
    echo wp_kses_post( render_block( $block ) );
  endforeach;
  ?>
</div>
