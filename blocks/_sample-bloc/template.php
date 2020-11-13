<?php
/**
 * Default block's data are inside the variable `$block`.
 * To get any other ACF data, use `get_field()`.
 */
$block_classes  = 'sample-block'; // The block name class.
$block_classes .= ' theme-block';
$block_classes .= ! empty( $block['className'] ) ? " {$block['className']}" : '';
$block_classes .= ' ' . get_block_align_class( $block );
?>
<section class="<?php echo esc_attr( $block_classes ); ?>">
  This will be visible on front-end.
</section>
