<?php
/**
 * Mandatory params:
 * @var array $post_types List of post types and/or arrays.
 *                        If the element is an array, it can have these arguments:
 *                         - post_type (mandatory)
 *                         - title
 *                         - orderby
 *                         - order
 *                         - apply_filters // For this to do anything, a filter needs to be in place: https://developer.wordpress.org/reference/functions/add_filter/
 *
 * Automatic params:
 * @var array $sitemap_section Contain all the necessaries to populate the sitemap.
 */

// Cancel rendering the partial if there is no data.
if ( empty( $post_types ) ) {
  return;
}
?>

<section class="sitemap">
  <div class="sitemap-inner container">
      
    <?php foreach ( $sitemap_section as $section ) { ?>
      <?php
      get_template_part_with_data(
        'templates/sitemap/sitemap-section',
        $section['post_type'],
        $section
      );
      ?>
    <?php } ?>
  </div>
</section>
