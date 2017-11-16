<?php

/**
 * Options table cleaning process
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 */

/*
 * @global $wpdb
 * 
 * @uses https://codex.wordpress.org/Function_Reference/current_user_can
 * @uses https://codex.wordpress.org/Function_Reference/is_multisite
 * @uses https://codex.wordpress.org/Function_Reference/delete_blog_option
 * @uses https://codex.wordpress.org/Function_Reference/delete_option
 * 
 */
defined( 'ABSPATH' ) and defined( 'WP_UNINSTALL_PLUGIN' ) or exit;
current_user_can( 'activate_plugins' ) or exit;


if( function_exists( 'is_multisite' ) && is_multisite() ) {

  global $wpdb;

  $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

  foreach( $blog_ids as $blog_id ) {
    delete_blog_option( $blog_id, 'mcb_option' );
  }

} else {
  delete_option( 'mcb_option' );
}

?>
