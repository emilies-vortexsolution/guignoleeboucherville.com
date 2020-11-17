<?php
$sct_video_url       = get_field( 'header_video_url' );
$sct_video_thumbnail = ! empty( get_field( 'header_video_thumbnail' ) ) ? get_field( 'header_video_thumbnail' ) : get_default_img_src( 'video' );
?>
<section class="block-header-video alignfull">
  <div class="container">
    <div class="header-video__columns">
      <div class="header-video__col--title">
        <h1 class="sr-only"><?php echo esc_html( get_the_title() ); ?></h1>
        <img src="<?php echo esc_url( get_logo_url() ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="header-video__logo">
      </div>
      <?php if ( ! empty( $sct_video_url ) ) : ?>
        <div class="header-video__col--video">
          <a class="header-video__video" href="<?php echo esc_url( $sct_video_url ); ?>" data-fancybox aria-labelledby="<?php echo esc_html_x( 'Play the video in a lightbox window.', 'aria label video link', 'vtx' ); ?>">
            <div class="header-video__video-thumbnail" aria-hidden="true">
              <div class="header-video__video-thumbnail__image" role="presentation" style="background-image:url(<?php echo esc_url( $sct_video_thumbnail ); ?>);"></div>
              <div class="header-video__video-thumbnail__overlay" role="presentation"></div>
              <div class="header-video__video-thumbnail__inner">
                <?php
                echo get_file_contents_by_url( get_theme_asset_url( 'images/svg/play.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
                ?>
                <span><?php echo esc_html_x( 'Play', 'video thumbnail action', 'vtx' ); ?></span>
              </div>
            </div>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
