<?php
/**
 * Mandatory params:
 * @var string $title Won't show without a title.
 *
 * Possible params:
 * @var string url
 * @var int position Usefull for microdata. If 0, it won't be shown.
 *
 * Automatic params:
 * @var string title_wrapper_tag `a` or `span` depending on if there is a URL
 * @var string title_wrapper_link_attrs href, target, etc.
 * @var bool is_current If it's the actual current page.
 */
?>

<li 
  class="breadcrumbs__item"
  itemprop="itemListElement" itemscope
  itemtype="https://schema.org/ListItem"
  >
  
  <?php // This tag is compressed to make sure there is no unndessary white spaces inside the title. ?>
  <<?php echo esc_html( $title_wrapper_tag ); ?>
    <?php echo wp_kses_post( $title_wrapper_link_attrs ); ?>
    class="breadcrumbs__item__title-wrapper"
    itemprop="item"
    ><span class="breadcrumbs__item__title" itemprop="name"><?php echo esc_html( $title ); ?></span></<?php echo esc_html( $title_wrapper_tag ); ?>>
    
  <?php if ( $position ) { ?>
    <meta itemprop="position" content="<?php echo esc_attr( $position ); ?>">
  <?php } ?>
</li>
