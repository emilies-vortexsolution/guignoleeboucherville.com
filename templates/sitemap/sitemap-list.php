<?php
/**
 * Mandatory params:
 * @var string $post_type Post type of this list.
 *
 * Possible params:
 * @var string $orderby May be "title", "menu_order" or "date". Default: title
 * @var string $order May be "ASC" or "DESC". Default: ASC
 * @var string $apply_filters If not empty, the resulting associated posts will be passed through WordPress filters associated with taht name.
 *
 * Automatic params:
 * @var array $associated_posts All posts from the given post type.
 */

// Cancel rendering the partial if there is no data.
if ( empty( $associated_posts ) ) {
  return;
}
?>

<div class="sitemap-list-wrapper">
  <ul class="sitemap-list">

    <?php foreach ( $associated_posts as $that_post ) { ?>
      <li class="sitemap-list__item">
        <a class="sitemap-list__item__title" href="<?php echo esc_url( get_permalink( $that_post ) ); ?>"><?php echo wp_kses_post( $that_post->post_title ); ?></a>
      </li>
    <?php } ?>
  </ul>
</div>
