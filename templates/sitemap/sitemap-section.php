<?php
/**
 * Mandatory params:
 * @var string $ post_type Post type of this section.
 *
 * Possible params:
 * @var string $title
 * @var string $orderby May be "title", "menu_order" or "date". Default: title
 * @var string $order May be "ASC" or "DESC". Default: ASC.
 * @var string $apply_filters For this to do anything, a filter needs to be in place: https://developer.wordpress.org/reference/functions/add_filter/
 *
 * Automatic params:
 * @var array $associated_posts All posts from the given post type
 */

// Cancel rendering the partial if there is no data.
if ( empty( $associated_posts ) ) {
  return;
}
?>

<div class="sitemap__section">
  <h2 class="sitemap__section__title"><?php echo esc_html( $title ); ?></h2>
  <?php
  get_template_part_with_data(
    'templates/sitemap/sitemap-list',
    $post_type,
    array(
      'post_type'        => $post_type,
      'orderby'          => $orderby,
      'order'            => $order,
      'apply_filters'    => $apply_filters,
      'associated_posts' => $associated_posts,
    )
  );
  ?>
</div>
