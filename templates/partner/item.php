<?php
$args = wp_parse_args(
  $args,
  array(
    'partner' => get_post(),
  )
);

$partner   = $args['partner'];
$p_website = get_field( 'partner_website', $partner->ID );
$p_logo    = get_the_post_thumbnail_url( $partner->ID );
/* translators: %s is partner's name */
$url_label = sprintf( _x( 'Visit %s\' website, opens in a new tab', 'link to external website', 'vtx' ), $partner->post_title );
?>

<div class="listing--partners__item">

  <?php if ( $p_website ) { ?>
    <a  class="listing--partners__item__inner" href="<?php echo esc_url( $p_website ); ?>" target="_blank" rel="noopener noreferrer" aria-labelledby="<?php echo esc_attr( $url_label ); ?>">
  <?php } else { ?>
    <div class="listing--partners__item__inner">
  <?php } ?>

  <div class="listing--partners__item__content">
    <?php if ( $p_logo ) { ?>
      <figure>
        <img src="<?php echo esc_url( $p_logo ); ?>" alt="<?php echo esc_attr( $partner->post_title ); ?>">
      </figure>
    <?php } else { ?>
      <span class="listing--partners__item__title"><?php echo esc_html( $partner->post_title ); ?></span>
    <?php } ?>
  </div>

  <?php if ( $p_website ) { ?>
    </a>
  <?php } else { ?>
    </div>
  <?php } ?>

</div>
