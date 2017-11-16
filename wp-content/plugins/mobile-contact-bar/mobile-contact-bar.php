<?php

/**
 * Plugin Name: Mobile Contact Bar
 * Plugin URI:  https://wordpress.org/plugins/mobile-contact-bar/
 * Description: Allow your visitors to contact you directly via phone, email, or Social Media
 * Version:     1.3.0
 * Author:      Anna Bansaghi
 * Author URI:  https://github.com/bansaghi/
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Copyright:   Anna Bansaghi
 * Text Domain: mobile-contact-bar
 * Domain Path: /languages
 */

/**
 * Main plugin file
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
 * @event https://codex.wordpress.org/Function_Reference/register_activation_hook
 * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::on_activation()
 *
 * @event https://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
 * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
 * @action class-mcb-front.php Mobile_Contact_Bar_Front::plugins_loaded()
 * 
 * @uses https://codex.wordpress.org/Function_Reference/is_admin
 * @uses https://developer.wordpress.org/reference/functions/plugin_dir_path
 * 
 */
defined( 'ABSPATH' ) or exit;

define( 'MOBILE_CONTACT_BAR_PLUGIN_PATH', __FILE__ );


/* -------------------------------------------------------------------------- */
/*                        Administration functionality                        */
/* -------------------------------------------------------------------------- */
if( is_admin() ) {

  include_once( plugin_dir_path( __FILE__ ) . 'includes/class-mcb-admin.php' );
  register_activation_hook( __FILE__, array( 'Mobile_Contact_Bar_Admin', 'on_activation' ));
  add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Admin', 'plugins_loaded' ));




/* -------------------------------------------------------------------------- */
/*                           Frontend functionality                           */
/* -------------------------------------------------------------------------- */
} else {

  include_once( plugin_dir_path( __FILE__ ) . 'includes/class-mcb-front.php' );
  add_action( 'plugins_loaded', array( 'Mobile_Contact_Bar_Front', 'plugins_loaded' ));
}



