<?php use Roots\Sage\Titles; ?>

<div class="page-header">
  <div class="container">
    <h1><?php echo wp_kses_post( Titles\title() ); ?></h1>
  </div>
</div>

<?php
get_partial(
  'templates/breadcrumbs/breadcrumbs',
  array(
    'show_home' => true,
  )
);
?>
