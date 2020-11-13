<?php
$favicon_path = get_template_directory_uri() . '/assets/favicon';

$favicon_ico_filename = 'favicon.ico';
if ( is_admin() ) {
  $favicon_ico_filename = 'favicon-admin.png';
}
?>

<link rel="manifest" href="<?php echo esc_url( $favicon_path . '/manifest.json' ); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $favicon_path . '/apple-touch-icon.png' ); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( $favicon_path . '/favicon-32x32.png' ); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $favicon_path . '/favicon-16x16.png' ); ?>">
<link rel="shortcut icon" href="<?php echo esc_url( $favicon_path . '/' . $favicon_ico_filename ); ?>">
<meta name="msapplication-config" content="<?php echo esc_url( $favicon_path . '/browserconfig.xml' ); ?>">

<meta name="apple-mobile-web-app-status-bar-style" content="<?php echo esc_html( PRIMARY_COLOR ); ?>">
<link rel="mask-icon" href="<?php echo esc_url( $favicon_path . '/safari-pinned-tab.svg' ); ?>" color="<?php echo esc_html( PRIMARY_COLOR ); ?>">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
