<?php
/**
 * To catch all private and public methods and properties call, extends any placeholder class with this.
 * The only exceptions are static properties wich need to be added manually and won't trigger a notice.
 */
// @codingStandardsIgnoreStart
class VTX_Class_Placeholder {

  /**
   * For more precision of how it's used, copy the constructor into the class that extends this.
   */
  function __construct( ...$args ) {
    vtx_notice_use_of_inactive_plugin( 'Not specified' );
  }

  function __call( $name, $args ) {
    vtx_notice_use_of_inactive_plugin( $name );
  }

  function __get( $name ) {
    vtx_notice_use_of_inactive_plugin( $name );
  }

  public static function __callStatic( $name, $args ) {
    vtx_notice_use_of_inactive_plugin( $name );
  }
}
// @codingStandardsIgnoreEnd
