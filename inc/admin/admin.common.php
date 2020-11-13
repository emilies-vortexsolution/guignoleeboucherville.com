<?php

//Logo for wp-login.php
add_action( 'login_head', 'add_custom_login_logo' );
function add_custom_login_logo() {
  ?>
  <style type="text/css">
    .login h1 a {
      width: 320px;
      height: 86px;
      background: no-repeat url('<?php echo esc_url( get_logo_url() ); ?>') center center !important;
      background-size: contain !important;
    }
  </style>
  <?php
}
