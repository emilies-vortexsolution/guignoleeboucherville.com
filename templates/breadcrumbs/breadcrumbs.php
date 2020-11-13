<?php
/**
 * Mandatory params: NONE
 *
 * Possible params:
 * @var object $object The page to base the breadcrumb on. Default: get_queried_object()
 * @var bool $show_home Make sure home is the first breadcrumb. Default: TRUE
 * @var bool $current_has_link Remove the url from the current page breadcrumb if false. Default: FALSE
 *
 * Automatic params:
 * @var array $items Is populated by `Vortex\Breadcrumbs\get_items( object )`.
 *                   It is automatic, but will take the argument `object` if given.
 *                   It's necessary to have at least one item for the breadcrumbs to display.
 *
 * @link https://www.w3.org/TR/wai-aria-practices/examples/breadcrumb/index.html Accessibility reference
 * @link https://developers.google.com/search/docs/data-types/breadcrumb?hl=fr#microdata_1 Google breadcrumbs avec Microdonnées
 */


// Bail if there is no crumbs.
if ( empty( $items ) ) {
  return;
}
?>

<nav class="breadcrumbs">
  <ol class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php
    $i = 0;
    foreach ( $items as $item ) {
      $item['position'] = $i++;
      get_partial( 'templates/breadcrumbs/breadcrumbs-item', $item );
    }
    ?>
  </ol>
</nav>
