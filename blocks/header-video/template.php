<?php
$sct_video = '';
?>
<section class="block-header-video alignfull">
  <div class="container">
    <div class="header-video__columns">
      <div class="header-video__col--title">
        <h1 class="sr-only"><?php echo esc_html( get_the_title() ); ?></h1>
        <img src="<?php echo esc_url( get_logo_url() ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="header-video__logo">
      </div>
    </div>
  </div>
</section>
