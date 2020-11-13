<?php
/**
 * Save the plugin as inactive.
 * Because we create fake copy of real functions,
 * the only real way to check if the plugin is really inactive is by asking with `vtx_is_plugin_inactive()`.
 *
 * @param string $plugin_name
 */
function vtx_add_inactive_plugin( $plugin_name ) {
  global $vtx_theme_inactive_plugins;

  if ( ! isset( $vtx_theme_inactive_plugins ) ) {
    $vtx_theme_inactive_plugins = array();
  }

  $vtx_theme_inactive_plugins[ $plugin_name ] = $plugin_name;
}

/**
 * Will check the list of inactive plugins added with `vtx_add_inactive_plugin()`.
 * Beware, for this function to work, be sure to call it after the hook `admin_enqueue_scripts` in admin or `init` on the frontend.
 *
 * @param string $plugin_name
 *
 * @return bool
 */
function vtx_is_plugin_inactive( $plugin_name ) {
  global $vtx_theme_inactive_plugins;

  return isset( $vtx_theme_inactive_plugins[ $plugin_name ] );
}

/**
 * Save a notice inside $vtx_inactive_plugins_notices for later display in footer.
 *
 * @param string $plugin_name Name of the plugin of wich the action/variable belong.
 * @param string $action_or_var_name Name of the function, class, variable or constant that was used without being declared first.
 *
 * @return void;
 */
function vtx_notice_use_of_inactive_plugin( $action_or_var_name ) {
  if ( ! WP_DEBUG ) {
    return;
  }

  global $vtx_inactive_plugins_notices;

  if ( ! isset( $vtx_inactive_plugins_notices ) ) {
    $vtx_inactive_plugins_notices = array();
  }

  if ( empty( $vtx_inactive_plugins_notices[ $action_or_var_name ] ) ) {
    $vtx_inactive_plugins_notices[ $action_or_var_name ] = 1;
  } else {
    $vtx_inactive_plugins_notices[ $action_or_var_name ] += 1;
  }
}
