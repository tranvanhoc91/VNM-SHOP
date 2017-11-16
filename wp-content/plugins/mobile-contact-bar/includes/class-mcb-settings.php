<?php

defined( 'ABSPATH' ) or exit;

/**
 * Bar and Icon settings
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 */
final class Mobile_Contact_Bar_Settings {


  /**
   * Defines the array of Setting Fields
   * 
   * @since 0.0.1
   * 
   * @return array Associative array of settings, divided into two sections (Bar and Icon)
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::init()
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::get_default_option()
   * 
   */
  public static function settings() {

    return apply_filters( 'mcb_admin_update_settings', array(

        // Section Bar
        'bar_is_active' => array(
            'section' => 'bar',
            'type'    => 'checkbox',
            'default' => 1,
            'title'   => __( 'Show / Hide Bar', 'mobile-contact-bar' ),
            'label'   => __( 'Show contact bar', 'mobile-contact-bar' ),
            'desc'    => __( 'The bar will be shown if at least one contact information is set.', 'mobile-contact-bar' ),
        ),
        'bar_is_new_tab' => array(
            'section' => 'bar',
            'type'    => 'checkbox',
            'default' => 0,
            'title'   => __( 'Open in New Tab', 'mobile-contact-bar' ),
            'label'   => __( 'Open links in a new tab', 'mobile-contact-bar' ),
            'desc'    => __( 'The links will be opened in a new browser tab.', 'mobile-contact-bar' ),
        ),
        'bar_max_screen_width' => array(
            'section' => 'bar',
            'type'    => 'number',
            'default' => 960,
            'title'   => __( 'Max Screen Width', 'mobile-contact-bar' ),
            'postfix' => 'px',
            'min'     => 200,
            'desc'    => __( 'The bar will be shown on those screens where the width is below of this value.', 'mobile-contact-bar' ),
        ),
        'bar_color' => array(
            'section' => 'bar',
            'type'    => 'color-picker',
            'default' => '#666666',
            'title'   => __( 'Bar Color', 'mobile-contact-bar' ),
        ),
        'bar_opacity' => array(
            'section' => 'bar',
            'type'    => 'slider',
            'default' => 1,
            'title'   => __( 'Bar Opacity', 'mobile-contact-bar' ),
        ),
        'bar_position' => array(
            'section' => 'bar',
            'type'    => 'select',
            'default' => 'bottom',
            'title'   => __( 'Bar Position', 'mobile-contact-bar' ),
            'options' => array( 'bottom' => __( 'at the bottom', 'mobile-contact-bar' ), 'top' => __( 'at the top', 'mobile-contact-bar' )),
        ),
        'bar_is_fixed' => array(
            'section' => 'bar',
            'type'    => 'checkbox',
            'default' => 1,
            'title'   => __( 'Fixed Position', 'mobile-contact-bar' ),
            'label'   => __( 'Fix bar at its position', 'mobile-contact-bar' ),
        ),
        'bar_height' => array(
            'section' => 'bar',
            'type'    => 'number',
            'default' => 60,
            'title'   => __( 'Bar Height', 'mobile-contact-bar' ),
            'postfix' => 'px',
            'min'     => 0,
        ),
        'bar_is_toggle' => array(
            'section' => 'bar',
            'type'    => 'checkbox',
            'default' => 0,
            'title'   => __( 'Bar Toggle', 'mobile-contact-bar' ),
            'label'   => __( 'Activate toggle', 'mobile-contact-bar' ),
        ),
        'bar_toggle_color' => array(
            'section' => 'bar',
            'type'    => 'color-picker',
            'default' => '#333333',
            'title'   => '<em>' . __( 'Toggle Color', 'mobile-contact-bar' ) . '</em>',
        ),

        // Section Icon
        'icon_size' => array(
            'section' => 'icon',
            'type'    => 'select',
            'default' => '2x',
            'title'   => __( 'Icon Size', 'mobile-contact-bar' ),
            'options' => array( '1x' => '1x', 'lg' => '1.33x', '2x' => '2x', '3x' => '3x', '4x' => '4x', '5x' => '5x' ),
        ),
        'icon_color' => array(
            'section' => 'icon',
            'type'    => 'color-picker',
            'default' => '#ffffff',
            'title'   => __( 'Icon Color', 'mobile-contact-bar' ),
        ),
        'icon_is_border' => array(
            'section' => 'icon',
            'type'    => 'checkbox',
            'default' => 0,
            'title'   => __( 'Icon Border', 'mobile-contact-bar' ),
            'label'   => __( 'Draw border around icons', 'mobile-contact-bar' ),
        ),
        'icon_border_color' => array(
            'section' => 'icon',
            'type'    => 'color-picker',
            'default' => '#eeeeee',
            'title'   => '<em>' . __( 'Border Color', 'mobile-contact-bar' ) . '</em>',
        ),
        'icon_border_width' => array(
            'section' => 'icon',
            'type'    => 'number',
            'default' => 1,
            'title'   => '<em>' . __( 'Border Width', 'mobile-contact-bar' ) . '</em>',
            'postfix' => 'px',
            'min'     => 1,
        ),
        'icon_max_panel_width' => array(
            'section' => 'icon',
            'type'    => 'number',
            'default' => 100,
            'title'   => __( 'Max Icon Panel Width', 'mobile-contact-bar' ),
            'postfix' => '%',
            'min'     => 10,
            'max'     => 100,
            'desc'    => __( 'The icon panel will be squeezed at this percent.', 'mobile-contact-bar' ),
        ),
    ));
  }

}
