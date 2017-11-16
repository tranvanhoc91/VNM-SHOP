<?php

defined( 'ABSPATH' ) or exit;

/**
 * Mobile Contacts
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 */
final class Mobile_Contact_Bar_Contacts {


  /**
   * Defines the list of mobile contacts
   * 
   * @since 0.0.1
   * 
   * @return array Associative array of contacts
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::init()
   * 
   */
  public static function contacts() {

    return apply_filters( 'mcb_admin_update_contacts', array(

        'phone' => array(
            'icon'        => 'phone',
            'title'       => __( 'Phone Number', 'mobile-contact-bar' ),
            'protocol'    => 'tel',
            'placeholder' => '+15417543010',
        ),
        'text' => array(
            'icon'        => 'comment-o',
            'title'       => __( 'Phone Number', 'mobile-contact-bar' ),
            'protocol'    => 'sms',
            'placeholder' => '+15417543010',
            'parameters'  => array( 'body' => 'text' ),
        ),
        'email' => array(
            'icon'        => 'envelope-o',
            'title'       => __( 'Email Address', 'mobile-contact-bar' ),
            'protocol'    => 'mailto',
            'placeholder' => 'username@example.com',
            'parameters'  => array( 'subject' => 'text', 'body' => 'textarea', 'cc' => 'email', 'bcc' => 'email' ),
        ),
        'skype' => array(
            'icon'        => 'skype',
            'title'       => 'Skype',
            'protocol'    => 'skype',
            'placeholder' => 'skypename',
        ),
        'address' => array(
            'icon'        => 'map-marker',
            'title'       => 'Google Maps',
            'protocol'    => 'https',
            'placeholder' => 'https://google.com/maps/place/Dacre+St,+London+UK/',
        ),
        'facebook' => array(
            'icon'        => 'facebook-official',
            'title'       => 'Facebook',
            'protocol'    => 'https',
            'placeholder' => 'https://www.facebook.com/username',
        ),
        'twitter' => array(
            'icon'        => 'twitter',
            'title'       => 'Twitter',
            'protocol'    => 'https',
            'placeholder' => 'https://twitter.com/username',
        ),
        'googleplus' => array(
            'icon'        => 'google-plus',
            'title'       => 'Google+',
            'protocol'    => 'https',
            'placeholder' => 'https://plus.google.com/username',
        ),
        'youtube' => array(
            'icon'        => 'youtube-play',
            'title'       => 'YouTube',
            'protocol'    => 'https',
            'placeholder' => 'https://www.youtube.com/user/username',
        ),
        'pinterest' => array(
            'icon'        => 'pinterest-p',
            'title'       => 'Pinterest',
            'protocol'    => 'http',
            'placeholder' => 'http://pinterest.com/username',
        ),
        'tumblr' => array(
            'icon'        => 'tumblr',
            'title'       => 'Tumblr',
            'protocol'    => 'http',
            'placeholder' => 'http://username.tumblr.com',
        ),
        'linkedin' => array(
            'icon'        => 'linkedin',
            'title'       => 'LinkedIn',
            'protocol'    => 'http',
            'placeholder' => 'http://www.linkedin.com/in/username',
        ),
        'vimeo' => array(
            'icon'        => 'vimeo-square',
            'title'       => 'Vimeo',
            'protocol'    => 'https',
            'placeholder' => 'https://vimeo.com/username',
        ),
        'flickr' => array(
            'icon'        => 'flickr',
            'title'       => 'Flickr',
            'protocol'    => 'http',
            'placeholder' => 'http://www.flickr.com/people/username',
        ),
    ));
  }

}
