<?php

defined( 'ABSPATH' ) or exit;

/**
 * Frontend related class
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 */
final class Mobile_Contact_Bar_Front {

  public static $option = null;


  /**
   * Hooks WP frontend actions
   * 
   * @since 0.0.1
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_head 
   * @action class-mcb-front.php Mobile_Contact_Bar_Front::wp_head()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
   * @action class-mcb-front.php Mobile_Contact_Bar_Front::wp_enqueue_scripts()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_footer
   * @action class-mcb-front.php Mobile_Contact_Bar_Front::wp_footer()
   * 
   * @uses https://developer.wordpress.org/reference/functions/get_option
   * 
   */
  public static function plugins_loaded() {

    self::$option = get_option( 'mcb_option' );

    if( ! empty( self::$option ) && isset( self::$option['contacts'] ) && isset( self::$option['styles'] ) && self::$option['settings']['bar_is_active'] ) {
      self::$option['contacts'] = array_filter( self::$option['contacts'], function( $contact ) { return isset( $contact['icon'] ); });

      if( ! empty( self::$option['contacts'] )) {
        add_action( 'wp_head'            , array( __CLASS__, 'wp_head' ), 7 );
        add_action( 'wp_enqueue_scripts' , array( __CLASS__, 'wp_enqueue_scripts' ));
        add_action( 'wp_footer'          , array( __CLASS__, 'wp_footer' ));
      }
    }
  }




  /**
   * Links Font Awesome icons related CSS styles
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
   * @in class-mcb-front.php Mobile_Contact_Bar_Front::plugins_loaded()
   * 
   * @uses https://developer.wordpress.org/reference/functions/wp_enqueue_style
   * @uses https://codex.wordpress.org/Function_Reference/plugins_url
   * 
   * @link http://fontawesome.io
   * 
   */
  public static function wp_enqueue_scripts() {
    wp_enqueue_style( 'fa',
        plugins_url( 'fonts/font-awesome/css/font-awesome.min.css', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
        false,
        '4.7.0',
        'all'
    );
  }




  /**
   * Adds plugin related CSS styles within the head section
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_head 
   * @in class-mcb-front.php Mobile_Contact_Bar_Front::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/esc_html_2
   * 
   */
  public static function wp_head() {
    ?>
    <style id="mobile-contact-bar-css" type="text/css" media="screen"><?php echo str_replace( '&quot;', '"', esc_html__( self::$option['styles'] )); ?></style>
    <?php
  }




  /**
   * Invokes mcb_front_render_html action only once
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wp_footer
   * @in class-mcb-front.php Mobile_Contact_Bar_Front::plugins_loaded()
   * 
   * @event mcb_front_render_html
   * 
   * @event mcb_front_render_html
   * @action class-mcb-front.php Mobile_Contact_Bar_Front::render_html()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/has_action
   * 
   */
  public static function wp_footer() {

    if( ! has_action( 'mcb_front_render_html' )) {
      add_action( 'mcb_front_render_html', array( __CLASS__, 'render_html' ), 10, 2 );
    }

    do_action( 'mcb_front_render_html', self::$option['contacts'], self::$option['settings'] );
  }




  /**
   * Outputs contact bar to the browser
   * 
   * @since 0.0.1
   * 
   * @param array $contacts Associative array of displayable contacts
   * @param array $settings Associative array of bar and icon settings
   * 
   * @action
   * @event mcb_front_render_html
   * @in class-mcb-front.php Mobile_Contact_Bar_Front::wp_footer()
   * 
   * @event mcb_front_escape_uri
   * 
   * @uses https://codex.wordpress.org/Function_Reference/did_action
   * @uses https://developer.wordpress.org/reference/functions/esc_attr
   * @uses https://codex.wordpress.org/Function_Reference/esc_url
   * @uses https://developer.wordpress.org/reference/functions/add_query_arg
   * 
   */
  public static function render_html( $contacts, $settings ) {
    if( 1 === did_action( 'mcb_front_render_html' )) {

      $html = '';

      $html .= '<div id="mcb-wrap">';

        if( $settings['bar_is_toggle'] ) {
          $html .= '<input id="mcb-toggle-checkbox" name="mcb-toggle-checkbox" type="checkbox">';
        }

        if( $settings['bar_is_toggle'] && 'bottom' == $settings['bar_position'] ) {        
          $html .= '<label for="mcb-toggle-checkbox" id="mcb-toggle"></label>';
        }

        $html .= '<div id="mcb-bar">';

          $html .= '<ul>';

            foreach( $contacts as $id => $contact ) {

              $url = '';
              $new_tab = '';
              switch( $contact['protocol'] ) {

                case 'tel':
                case 'sms':
                case 'mailto':
                case 'skype':
                  $url = $contact['protocol'] . ':' . esc_attr( $contact['resource'] );
                  break;

                case 'http':
                case 'https':
                  $url = esc_url( $contact['resource'] );
                  $new_tab = $settings['bar_is_new_tab'] ? ' target="_blank"' : '';
                  break;

                default:
                  $url = apply_filters( 'mcb_front_escape_uri', '', $id, $contact['protocol'], $contact['resource'] );
              }

              if( isset( $contact['parameters'] )) {
                $url = add_query_arg( $contact['parameters'], $url );
              }

              $html .= sprintf( '<li><a data-rel="external" href="%s"%s><i class="fa %s fa-%s"></i></a></li>',
                  $url,
                  $new_tab,
                  '1x' == $settings['icon_size'] ? '' : 'fa-' . esc_attr( $settings['icon_size'] ),
                  esc_attr( $contact['icon'] )
              );
            }

          $html .= '</ul>';
        $html .= '</div>';

        if( $settings['bar_is_toggle'] && 'top' == $settings['bar_position'] ) {
          $html .= '<label for="mcb-toggle-checkbox" id="mcb-toggle"></label>';
        }


      $html .= '</div>';


      echo $html;
    }
  }

}

