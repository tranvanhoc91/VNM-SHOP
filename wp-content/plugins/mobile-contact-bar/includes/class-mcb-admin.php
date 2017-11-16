<?php

defined( 'ABSPATH' ) or exit;

/**
 * Administration related class
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 * 
 */
final class Mobile_Contact_Bar_Admin {

  public static $slug        = 'mobile-contact-bar';
  public static $plugin_data = null;

  public static $settings    = null;
  public static $contacts    = null;

  public static $option      = null;




  /**
   * Creates the default option and inserts it to the Options table during the plugin activation
   * 
   * @since 0.0.1
   * 
   * @param bool $network_wide Whether to enable the plugin for all sites in the network or just for the current site
   * 
   * @calledby https://codex.wordpress.org/Function_Reference/register_activation_hook
   * @in mobile-contact-bar.php
   * 
   * @global $wpdb
   * 
   * @uses https://codex.wordpress.org/Function_Reference/current_user_can()
   * @uses https://codex.wordpress.org/Function_Reference/add_blog_option()
   * @uses https://codex.wordpress.org/Function_Reference/add_option()
   * @uses https://codex.wordpress.org/Function_Reference/set_transient()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::get_default_option()
   * 
   */
  public static function on_activation( $network_wide = false ) {
    if( ! current_user_can( 'activate_plugins' )) {
      return;
    }

    $default_option = self::get_default_option();

    if( $network_wide ) {

      global $wpdb;

      $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

      foreach( $blog_ids as $blog_id ) {
        add_blog_option( $blog_id, 'mcb_option', $default_option );
      }

    } else {
      add_option( 'mcb_option', $default_option );
      set_transient( self::$slug, '1', 120 );
    }
  }




  /**
   * Hooks WP's and plugin's admin actions and filters
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded
   * @in mobile-contact-bar.php
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_enqueue_scripts_wp_pointer()
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_enqueue_scripts()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
   * @filter class-mcb-admin.php Mobile_Contact_Bar_Admin::plugin_action_links()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Filter_Reference/pre_update_option_(option_name)
   * @filter class-mcb-admin.php Mobile_Contact_Bar_Admin::pre_update_option()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/init
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::init()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wpmu_new_blog
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::wpmu_new_blog()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_menu()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::plugin_upgrade()
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_init()
   * 
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_footer
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_footer()
   * 
   * 
   * @uses https://codex.wordpress.org/Function_Reference/load_plugin_textdomain()
   * @uses https://codex.wordpress.org/Function_Reference/plugin_basename
   * @uses https://codex.wordpress.org/Function_Reference/get_transient
   * @uses https://codex.wordpress.org/Function_Reference/delete_transient
   * 
   */
  public static function plugins_loaded() {

    load_plugin_textdomain( self::$slug, false, dirname( plugin_basename( MOBILE_CONTACT_BAR_PLUGIN_PATH )) . '/languages' );

    // display wp-pointer after plugin activation
    if( get_transient( self::$slug )) {
      add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts_wp_pointer' ));
      delete_transient( self::$slug );
    }


    add_filter( 'plugin_action_links_' . plugin_basename( MOBILE_CONTACT_BAR_PLUGIN_PATH ), array( __CLASS__, 'plugin_action_links' ));
    add_filter( 'pre_update_option_mcb_option' , array( __CLASS__, 'pre_update_option' ), 10, 2 );


    add_action( 'init'                  , array( __CLASS__, 'init' ));
    add_action( 'wpmu_new_blog'         , array( __CLASS__, 'wpmu_new_blog' ));
    add_action( 'admin_menu'            , array( __CLASS__, 'admin_menu' ));
    add_action( 'admin_init'            , array( __CLASS__, 'plugin_upgrade' ));
    add_action( 'admin_init'            , array( __CLASS__, 'admin_init' ));
    add_action( 'admin_enqueue_scripts' , array( __CLASS__, 'admin_enqueue_scripts' ));
    add_action( 'admin_footer'          , array( __CLASS__, 'admin_footer' ));
  }




  /**
   * Includes plugin's classes
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/init
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://developer.wordpress.org/reference/functions/plugin_dir_path
   * @uses https://developer.wordpress.org/reference/functions/get_option
   * @uses class-mcb-settings.php Mobile_Contact_Bar_Settings::settings()
   * @uses class-mcb-contacts.php Mobile_Contact_Bar_Contacts::contacts()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_keys_recursive()
   * 
   */
  public static function init() {

    include_once( plugin_dir_path( MOBILE_CONTACT_BAR_PLUGIN_PATH ) . 'includes/class-mcb-settings.php' );
    include_once( plugin_dir_path( MOBILE_CONTACT_BAR_PLUGIN_PATH ) . 'includes/class-mcb-contacts.php' );

    
    self::$settings = self::sanitize_keys_recursive( Mobile_Contact_Bar_Settings::settings() );
    self::$contacts = self::sanitize_keys_recursive( Mobile_Contact_Bar_Contacts::contacts() );
    self::$option   = get_option( 'mcb_option' );
  }






  /**
   * Creates default option and inserts it into the Options table on blog creation
   * 
   * @param int $blog_id Blog ID of the created blog
   * 
   * @since 1.0.0
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/wpmu_new_blog
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/add_blog_option
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::get_default_option()
   * 
   */
  public static function wpmu_new_blog( $blog_id ) {

    add_blog_option( $blog_id, 'mcb_option', self::get_default_option() );
  }




  /**
   * Loads CSS styles and JavaScript scripts for showing wp-pointer after plugin activation
   * 
   * @since 0.0.1
   * 
   * @param string $hook The specific admin page
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @event https://developer.wordpress.org/reference/hooks/admin_print_footer_scripts
   * @action class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_print_footer_scripts()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/is_plugin_active_for_network
   * @uses https://codex.wordpress.org/Function_Reference/plugin_basename
   * @uses https://developer.wordpress.org/reference/functions/wp_enqueue_style
   * @uses https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_script
   * 
   */
  public static function admin_enqueue_scripts_wp_pointer( $hook ) {

    if( 'plugins.php' == $hook && ! is_plugin_active_for_network( plugin_basename( MOBILE_CONTACT_BAR_PLUGIN_PATH ))) {
      wp_enqueue_style(  'wp-pointer' );
      wp_enqueue_script( 'wp-pointer' );
      add_action( 'admin_print_footer_scripts', array( __CLASS__, 'admin_print_footer_scripts' ));
    }
  }




  /**
   * Adds 'Settings' link to the plugins overview page
   * 
   * @since 0.0.1
   * 
   * @param  array $links Associative array of links
   * @return array Updated links
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/admin_url
   * @uses https://codex.wordpress.org/Function_Reference/esc_html_2
   * 
   */
  public static function plugin_action_links( $links ) {

    return array_merge(
        $links,
        array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=' . self::$slug ) . '">' . esc_html__( 'Settings' ) . '</a>' )
    );
  }




  /**
   * Outputs wp-pointer after plugin activation
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://developer.wordpress.org/reference/hooks/admin_print_footer_scripts
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_enqueue_scripts_wp_pointer()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/esc_html_2
   * 
   */
  public static function admin_print_footer_scripts() {

    $content  = sprintf( '<h3>%s &rarr; %s</h3>', esc_html__( 'Settings' ), esc_html__( 'Mobile Contact Bar', 'mobile-contact-bar' ));
    $content .= sprintf( '<p>%s</p>', esc_html__( 'Start editing the settings of your contact bar.', 'mobile-contact-bar' ));

    ?><script type="text/javascript">
      !(function($) {

        $(document).ready(function() {
          $('#menu-settings').pointer({
            content: '<?php echo $content; ?>',
            position: {
              edge  : 'left',
              align : 'center'
            }
          }).pointer('open');
        });

      })(jQuery);
    </script><?php
  }




  /**
   * Updates plugin version
   * 
   * @since 1.0.0
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @global $wpdb
   * 
   * @uses https://codex.wordpress.org/Function_Reference/is_plugin_active_for_network
   * @uses https://codex.wordpress.org/Function_Reference/plugin_basename
   * @uses https://developer.wordpress.org/reference/functions/get_blog_option
   * @uses https://codex.wordpress.org/Function_Reference/update_blog_option
   * @uses https://codex.wordpress.org/Function_Reference/update_option
   * 
   */
  public static function plugin_upgrade() {

    if( is_plugin_active_for_network( plugin_basename( MOBILE_CONTACT_BAR_PLUGIN_PATH ))) {

      global $wpdb;

      $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

      foreach( $blog_ids as $blog_id ) {
        $option = get_blog_option( $blog_id, 'mcb_option' );

        if( $option['version'] < self::$plugin_data['Version'] ) {
          $option['version'] = self::$plugin_data['Version'];
        }
        
        $option['settings'] = array_replace(
          array_map( function( $field ) { return $field['default']; }, self::$settings ),
          $option['settings']
        );

        update_blog_option( $blog_id, 'mcb_option', $option );
      }

    } else {

      if( self::$option['version'] < self::$plugin_data['Version'] ) {
        self::$option['version'] = self::$plugin_data['Version'];
        
        self::$option['settings'] = array_replace(
          array_map( function( $field ) { return $field['default']; }, self::$settings ),
          self::$option['settings']
        );

        update_option( 'mcb_option', self::$option );
      }
    }
  }




  /**
   * Adds option page to the admin menu
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/get_plugin_data
   * @uses https://codex.wordpress.org/Function_Reference/add_options_page
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::option_page()
   *    
   */
  public static function admin_menu() {

    // Workaround: should be in the init method
    self::$plugin_data = get_plugin_data( MOBILE_CONTACT_BAR_PLUGIN_PATH );

    add_options_page(
        __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
        __( 'Mobile Contact Bar', 'mobile-contact-bar' ),
        'manage_options',
        self::$slug,
        array( __CLASS__, 'option_page' ));
  }




  /**
   * Outputs the content of the option page
   * 
   * @since 0.0.1
   * 
   * @usedby https://codex.wordpress.org/Function_Reference/add_options_page
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_menu()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/esc_html_e
   * @uses https://codex.wordpress.org/Function_Reference/settings_fields
   * @uses https://codex.wordpress.org/Function_Reference/submit_button
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::do_settings_sections()
   * 
   */
  public static function option_page() {

    ?><div id="mcb-option-page" class="wrap">
      <h2><?php esc_html_e( 'Mobile Contact Bar', 'mobile-contact-bar' ); ?></h2>

      <div class="metabox-holder" id="poststuff">
        <div class="meta-box-sortables">
          <form action="options.php" method="post">
            <?php

            settings_fields( 'mcb_option_group' );

            self::do_settings_sections();

            submit_button();
            
            ?>
          </form>
        </div>
      </div>
    </div><?php
  }




  /**
   * Adds meta boxes to the option page
   * 
   * @since 1.2.0
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::option_page()
   * 
   * @global $wp_settings_sections
   * 
   * @uses https://developer.wordpress.org/reference/functions/add_meta_box 
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::section_callback()
   * @uses https://developer.wordpress.org/reference/functions/do_meta_boxes 
   * 
   */
  private static function do_settings_sections() {
    global $wp_settings_sections;

    if( ! isset( $wp_settings_sections[self::$slug] )) {
      return;
    }

    foreach( (array) $wp_settings_sections[self::$slug] as $section ) {
      add_meta_box(
          $section['id'],
          $section['title'],
          array( __CLASS__, 'section_callback' ),
          self::$slug,
          'advanced',
          'default',
          $section
      );
    }

    do_meta_boxes( self::$slug, 'advanced', $wp_settings_sections[self::$slug] );
  }




  /**
   * Outputs a meta box
   * 
   * @since 1.2.0
   * 
   * @param $object is the $wp_settings_sections[self::$slug]
   * @param $section Passed from add_meta_box as sixth parameter
   * 
   * @usedby https://developer.wordpress.org/reference/functions/add_meta_box 
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::do_settings_sections()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/sanitize_key
   * @uses https://codex.wordpress.org/Function_Reference/esc_html_e
   * @uses https://developer.wordpress.org/reference/functions/do_settings_fields
   * 
   */
  public static function section_callback( $object, $section ) {

    $id = 'mcb-table-' . sanitize_key( str_replace( 'section_', '', $section['id'] ));

    ?><table id="<?php echo $id; ?>" class="form-table">
    <?php if( 'mcb-table-contacts' == $id ) { ?>
      <tfoot class="wp-ui-text-highlight">
        <tr>
          <td colspan="2">
            <i class="fa fa-share fa-lg fa-rotate-270" aria-hidden="true"></i><?php esc_html_e( 'Drag and drop to reorder', 'mobile-contact-bar' ); ?>
          </td>
        </tr>
      </tfoot>
    <?php } ?>
      <tbody><?php
        do_settings_fields( self::$slug, $section['id'] ); ?>
      </tbody>
    </table><?php
  }




  /**
   * Adds sections and fields to the option page
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/register_setting
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * @uses https://codex.wordpress.org/Function_Reference/add_settings_section
   * @uses https://codex.wordpress.org/Function_Reference/add_settings_field
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::setting_callback()
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::contact_callback()
   * @callable class-mcb-admin.php Mobile_Contact_Bar_Admin::contact_parameter_callback()
   * @uses https://codex.wordpress.org/Function_Reference/sanitize_html_class
   * @uses https://codex.wordpress.org/Function_Reference/esc_html
   *
   */
  public static function admin_init() {

    register_setting( 'mcb_option_group', 'mcb_option', array( __CLASS__, 'sanitize_input' ));

    $section_bar  = array_filter( self::$settings, function( $field ) { return 'bar'  == $field['section']; });
    $section_icon = array_filter( self::$settings, function( $field ) { return 'icon' == $field['section']; });



    /* -------------------------------------------------------------------------- */
    /*                                Section Bar                                 */
    /* -------------------------------------------------------------------------- */
    add_settings_section(
        'section_bar',
        __( 'Bar Display Settings', 'mobile-contact-bar' ),
        false,
        self::$slug
    );

    foreach( $section_bar as $id => $field ) {
      add_settings_field(
          $id,
          $field['title'],
          array( __CLASS__, 'setting_callback' ),
          self::$slug,
          'section_bar',
          array( 'id' => $id, 'field' => $field )
      ); 
    }



    /* -------------------------------------------------------------------------- */
    /*                                Section Icon                                */
    /* -------------------------------------------------------------------------- */
    add_settings_section(
        'section_icon',
        __( 'Icon Display Settings', 'mobile-contact-bar' ),
        false,
        self::$slug
    );

    foreach( $section_icon as $id => $field ) {
      add_settings_field(
          $id,
          $field['title'],
          array( __CLASS__, 'setting_callback' ),
          self::$slug,
          'section_icon',
          array( 'id' => $id, 'field' => $field )
      ); 
    }



    /* -------------------------------------------------------------------------- */
    /*                               Section Contacts                             */
    /* -------------------------------------------------------------------------- */
    add_settings_section(
        'section_contacts',
        __( 'Contact List', 'mobile-contact-bar' ),
        false,
        self::$slug
    );


    // reorder contact list
    if( isset( self::$option['contacts'] )) {

      $order = array_merge( array_keys( self::$option['contacts'] ), array_keys( array_diff_key( self::$contacts, self::$option['contacts'] )));

      $tmp = array();
      foreach( $order as $key ) {
        $tmp[$key] = self::$contacts[$key];
      }

      self::$contacts = $tmp;
    }


    $index = 0;
    foreach( self::$contacts as $id => $contact ) {

      add_settings_field(
          $id,
          sprintf('<span class="circle">%d</span><i class="fa fa-lg fa-%s mcb-contact-icon" aria-hidden="true"></i>%s',
            +($index + 1),
            sanitize_html_class( $contact['icon'] ),
            esc_html( $contact['title'] )
          ),
          array( __CLASS__, 'contact_callback' ),
          self::$slug,
          'section_contacts',
          array( 'id' => $id, 'contact' => $contact )
      );

      if( isset( $contact['parameters'] )) {

        foreach( $contact['parameters'] as $attribute => $field ) {

          add_settings_field(
              $id . '-' . $attribute,
              $attribute,
              array( __CLASS__, 'contact_parameter_callback' ),
              self::$slug,
              'section_contacts',
              array( 'id' => $id, 'attribute' => $attribute, 'field' => $field )
          );
        }
      }
      ++$index;
    }
  }




  /**
   * Sanitizes the option's value (settings and contacts)
   * 
   * @since 0.0.1
   * 
   * @param  array $input Multidimensional array for the option
   * @return array The sanitized option
   * 
   * @usedby https://codex.wordpress.org/Function_Reference/register_setting
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_init()
   * 
   * @uses https://developer.wordpress.org/reference/functions/sanitize_text_field
   * @uses https://codex.wordpress.org/Function_Reference/absint
   * @uses https://codex.wordpress.org/Function_Reference/sanitize_email
   * @uses https://codex.wordpress.org/Function_Reference/is_email
   * @uses https://codex.wordpress.org/Function_Reference/esc_url_raw
   * @uses https://codex.wordpress.org/Function_Reference/sanitize_key
   * @uses https://developer.wordpress.org/reference/functions/rawurlencode_deep
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_hex_color()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_rgba_color()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_float()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_phone_number()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_keys_recursive()
   * 
   */
  public static function sanitize_input( $input ) {

    $in_settings  = $input['settings'];
    $in_contacts  = $input['contacts'];
    $out_settings = array();
    $out_contacts = array();



    /* -------------------------------------------------------------------------- */
    /*                                  Settings                                  */
    /* -------------------------------------------------------------------------- */
    // workaround empty checkboxes
    $in_settings = array_replace(
        array_map( function( $field ) { if( 'checkbox' == $field['type'] ) { return 0; }}, self::$settings ),
        $in_settings
    );

    // all settings will be saved, at least with their default values
    $in_settings = array_replace(
        array_map( function( $field ) { return $field['default']; }, self::$settings ),
        $in_settings
    );

    foreach( $in_settings as $id => $value ) {

      switch( self::$settings[$id]['type'] ) {

        case 'select':
          if( in_array( $value, array_keys( self::$settings[$id]['options'] ))) {
            $out_settings[$id] = sanitize_text_field( $value );
          } else {
            $out_settings[$id] = sanitize_text_field( self::$settings[$id]['default'] );
          }
          break;


        case 'color-picker':
          $color = self::sanitize_hex_color( $value );

          if( ! $color ) {
            $color = self::sanitize_rgba_color( $value );
          }
          if( ! $color ) {
            $color = self::sanitize_hex_color( self::$settings[$id]['default'] );
          }

          $out_settings[$id] = $color;
          break;


        case 'checkbox':
        case 'number':
          $out_settings[$id] = absint( $value );
          break;


        case 'slider':
          $float = self::sanitize_float( $value );
          $out_settings[$id] = $float ? $float : self::sanitize_float( self::$settings[$id]['default'] );
          break;
      }
    }



    /* -------------------------------------------------------------------------- */
    /*                                 Contacts                                   */
    /* -------------------------------------------------------------------------- */
    foreach( $in_contacts as $id => $contact ) {

      $resource = '';
      switch( self::$contacts[$id]['protocol'] ) {

        case 'tel':
        case 'sms':
          $resource = self::sanitize_phone_number( $contact['url'] );
          break;

        case 'skype':
          $resource = sanitize_text_field( $contact['url'] );
          break;

        case 'mailto':
          $resource = sanitize_email( $contact['url'] );
          $resource = is_email( $resource ) ? $resource : '';
          break;

        case 'http':
        case 'https':
          $resource = esc_url_raw( $contact['url'] );
          break;

        default:
          $resource = sanitize_text_field( $contact['url'] );
          break;
      }

      if( ! empty( $resource )) {
        $out_contacts[$id] = array(
            'icon'     => sanitize_key( self::$contacts[$id]['icon'] ),
            'protocol' => sanitize_key( self::$contacts[$id]['protocol'] ),
            'resource' => $resource,
        );
      }


      if( isset( self::$contacts[$id]['parameters'] )) {

        $in_parameters  = array_filter( array_intersect_key( $contact, self::$contacts[$id]['parameters'] ));
        $out_parameters = rawurlencode_deep( $in_parameters );

        if( array_filter( $out_parameters )) {
          $out_contacts[$id]['parameters'] = array_filter( $out_parameters );
        }
      }
    }

    // two sublists: first one is for contacts with icon, second one is for contacts without icons but with parameters
    $displayable  = array_filter( $out_contacts, function( $contact ) { return isset( $contact['icon'] ); });
    $storable     = array_diff_key( $out_contacts, $displayable ); // contacts with parameters only
    $out_contacts = array_merge( $displayable, $storable );


    return array_filter( array_replace(
        self::$option,
        array(
            'settings' => self::sanitize_keys_recursive( $out_settings ),
            'contacts' => array_filter( $out_contacts ) ? self::sanitize_keys_recursive( array_filter( $out_contacts )) : null,
        )
    ));
  }




  /**
   * Callback function which outputs a setting field
   * 
   * @since 0.0.1
   * 
   * @param array $args Associative array for field's arguments
   * 
   * @usedby https://codex.wordpress.org/Function_Reference/add_settings_field
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_init()
   * 
   * @uses https://developer.wordpress.org/reference/functions/esc_attr
   * @uses https://codex.wordpress.org/Function_Reference/esc_html
   * @uses https://codex.wordpress.org/Function_Reference/selected
   * @uses https://codex.wordpress.org/Function_Reference/checked
   * 
   */
  public static function setting_callback( $args ) {

    extract( $args );

    switch( $field['type'] ) {

      case 'color-picker':
        printf(
            '<input type="text" id="mcb-%1$s" name="mcb_option[settings][%1$s]" class="cs-wp-color-picker" value="%2$s">',
            esc_attr( $id ),
            esc_attr( self::$option['settings'][$id] )
        );
        break;

      case 'select':
        printf(
            '<select id="mcb-%1$s" name="mcb_option[settings][%1$s]">',
            esc_attr( $id )
        );
        foreach( $field['options'] as $value => $text ) {
          printf(
              '<option value="%s" %s>%s</option>',
              esc_attr( $value ),
              selected( $value, self::$option['settings'][$id], false ),
              esc_html( $text )
          );
        }
        echo '</select>';
        break;

      case 'checkbox':
        printf(
            '<label for="mcb-%1$s"><input type="checkbox" id="mcb-%1$s" name="mcb_option[settings][%1$s]" %2$s value="1">%3$s</label>',
            esc_attr( $id ),
            checked( self::$option['settings'][$id], 1, false ),
            esc_html( $field['label'] )
        );
        break;

      case 'number':
        printf(
            '<input type="number" id="mcb-%1$s" name="mcb_option[settings][%1$s]" class="small-text" min="%2$d" value="%3$d">
            <span>%4$s</span>',
            esc_attr( $id ),
            esc_attr( $field['min'] ),
            esc_attr( self::$option['settings'][$id] ),
            esc_html( $field['postfix'] )
        );
        break;

      case 'slider':
        printf(
            '<div class="mcb-slider" value=""></div>
            <input type="text" id="mcb-%1$s" name="mcb_option[settings][%1$s]" class="small-text mcb-slider-input" value="%2$s" readonly="readonly">',
            esc_attr( $id ),
            esc_attr( self::$option['settings'][$id] )
        );
        break;
    }

    if( isset( $field['desc'] )) {
      printf(
          '<p class="description">%s</p>',
          esc_html( $field['desc'] )
      );
    }
  }




  /**
   * Callback function which outputs a contact field
   * 
   * @since 0.0.1
   * 
   * @param array $args Associative array for contact's arguments
   * 
   * @usedby https://codex.wordpress.org/Function_Reference/add_settings_field
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_init()
   * 
   * @uses https://developer.wordpress.org/reference/functions/esc_attr
   * @uses https://codex.wordpress.org/Function_Reference/esc_attr_2
   * 
   */
  public static function contact_callback( $args ) {

    extract( $args );

    $_button = '';

    if( isset( $contact['parameters'] )) {
      $_button = sprintf( '<button type="button" class="button button-default" aria-label="Parameters" aria-expanded="false" title="%s"><i class="fa fa-plus" aria-hidden="true"></i></button>',
      esc_attr__( 'Query string parameters', 'mobile-contact-bar' )
      );
    }

    printf(
        '<div class="mcb-contact-wrapper" data-key="%1$s"><input type="text" id="mcb-%1$s" name="mcb_option[contacts][%1$s][url]" class="mcb-contact-url" value="%2$s" placeholder="%3$s">%4$s</div>',
        esc_attr( $id ),
        isset( self::$option['contacts'][$id]['resource'] ) ? esc_attr( self::$option['contacts'][$id]['resource'] ) : '',
        esc_attr( $contact['placeholder'] ),
        $_button
    );
  }




  /**
   * Callback function which outputs the query string parameters of a contact
   * 
   * @since 1.2.0
   * 
   * @param array $args Associative array for contact parameter's arguments
   * 
   * @usedby https://codex.wordpress.org/Function_Reference/add_settings_field
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::admin_init()
   * 
   * @uses https://developer.wordpress.org/reference/functions/esc_attr
   * @uses https://codex.wordpress.org/Function_Reference/esc_textarea
   * 
   */
  public static function contact_parameter_callback( $args ) {

    extract( $args );
    
    switch( $field ) {

      case 'text':
        printf( '<div class="mcb-contact-parameter-wrapper" data-key="%1$s"><input type="text" id="mcb-%1$s-%2$s" name="mcb_option[contacts][%1$s][%2$s]" value="%3$s"></div>',
          esc_attr( $id ),
          esc_attr( $attribute ),
          isset( self::$option['contacts'][$id]['parameters'][$attribute] ) ? esc_attr( rawurldecode( self::$option['contacts'][$id]['parameters'][$attribute] )) : ''
        );
        break;

      case 'textarea':
        printf( '<div class="mcb-contact-parameter-wrapper" data-key="%1$s"><textarea id="mcb-%s-%s" name="mcb_option[contacts][%1$s][%2$s]">%3$s</textarea></div>',
          esc_attr( $id ),
          esc_attr( $attribute ),
          isset( self::$option['contacts'][$id]['parameters'][$attribute] ) ? esc_textarea( rawurldecode( self::$option['contacts'][$id]['parameters'][$attribute] )) : ''
        );
        break;

      case 'email':
        printf( '<div class="mcb-contact-parameter-wrapper" data-key="%1$s"><input type="email" id="mcb-%1$s-%2$s" name="mcb_option[contacts][%1$s][%2$s]" value="%3$s"></div>',
          esc_attr( $id ),
          esc_attr( $attribute ),
          isset( self::$option['contacts'][$id]['parameters'][$attribute] ) ? esc_attr( rawurldecode( self::$option['contacts'][$id]['parameters'][$attribute] )) : ''
        );
        break;
    }
  }




  /**
   * Loads CSS styles and JavaScript scripts for plugin option page
   * 
   * @since 0.0.1
   * 
   * @param string $hook The specific admin page
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/plugins_url
   * @uses https://developer.wordpress.org/reference/functions/wp_enqueue_style
   * @uses https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_script
   * 
   * @link https://github.com/Codestar/codestar-wp-color-picker
   * @link http://fontawesome.io/
   */
  public static function admin_enqueue_scripts( $hook ) {

    if( 'settings_page_' . self::$slug == $hook ) {

      // default color picker styles and scripts
      wp_enqueue_style(  'wp-color-picker' );
      wp_enqueue_script( 'wp-color-picker' );


      // Codestar's color picker styles and scripts
      wp_enqueue_style( 'cs-wp-color-picker',
          plugins_url( 'css/cs-wp-color-picker.min.css', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          array( 'wp-color-picker' ),
          '1.1.0',
          'all'
      );
      wp_enqueue_script( 'cs-wp-color-picker-js',
          plugins_url( 'js/cs-wp-color-picker.min.js', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          array( 'wp-color-picker' ),
          '1.1.0',
          false
      );


      // Font Awesome styles
      wp_enqueue_style( 'fa',
          plugins_url( 'fonts/font-awesome/css/font-awesome.min.css', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          false,
          '4.7.0',
          'all'
      );


      // custom styles and scripts
      wp_enqueue_style( 'mcb-admin',
          plugins_url( 'css/mcb-admin.css', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          array( 'cs-wp-color-picker', 'fa' ),
          '1.1.0',
          'all'
      );
      wp_enqueue_script( 'mcb-admin-js',
          plugins_url( 'js/mcb-admin.js', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          array( 'jquery', 'jquery-ui-slider', 'jquery-ui-sortable', 'cs-wp-color-picker-js' ),
          '1.1.0',
          false
      );
    }
  }




  /**
   * Generates frontend CSS styles, and stores them in the option
   * 
   * @since 0.0.1
   * 
   * @param  array $new_value The new value
   * @param  array $old_value The old value
   * @return array The updated option
   * 
   * @filter
   * @event https://codex.wordpress.org/Plugin_API/Filter_Reference/pre_update_option_(option_name)
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   */
  public static function pre_update_option( $new_value, $old_value ) {

    $settings = $new_value['settings'];

    $styles = '';

    $position = 'fixed';
    if( ! $settings['bar_is_fixed'] ) {
      if( 'top' == $settings['bar_position'] ) {
        $position = 'absolute';
      } else {
        $position = 'relative';
      }
    }


    // #mcb-wrap
    $styles .= '#mcb-wrap{';
      $styles .= 'display:block;';
      $styles .= 'fixed' == $position ? '' : 'float:left;';
      $styles .= 'opacity:' . $settings['bar_opacity'] . ';';
      $styles .= 'position:' . $position . ';';
      $styles .= 'left:0;';
      $styles .=  'top' == $settings['bar_position'] ? 'top:0;' : 'bottom:0;';
      $styles .= 'text-align:center;';
      $styles .= 'relative' == $position && 'bottom' == $settings['bar_position'] ? 'vertical-align:bottom;' : '';
      $styles .= 'width:100%;';
      $styles .= 'z-index:99999;';
    $styles .= '}';


    // #mcb-bar
    $styles .= '#mcb-bar{';
      $styles .= 'background-color:' . $settings['bar_color'] . ';';
      $styles .= 'height:' . $settings['bar_height'] . 'px;';
      $styles .= 'width:inherit;';
    $styles .= '}';


    // #mcb-bar ul
    $styles .= '#mcb-bar ul{';
      $styles .= 'display:table;';
      $styles .= 'height:inherit;';
      $styles .= 'list-style-type:none;';
      $styles .= 'margin:0 auto;';
      $styles .= 'max-width:' . $settings['icon_max_panel_width'] . '%;';
      $styles .= 'padding:0;';
      $styles .= 'table-layout:fixed;';
      $styles .= 'width:inherit;';
    $styles .= '}';


    // #mcb-bar ul li
    $styles .= '#mcb-bar ul li{';
      $styles .= 'display:table-cell;';
      $styles .= 'text-align:center;';
      $styles .= 'vertical-align:middle;';
      $styles .= $settings['icon_is_border'] ? 'border-width:' . $settings['icon_border_width'] . 'px;' .
                'border-style:solid solid solid none;' .
                'border-color:' . $settings['icon_border_color'] . ';' : '';
    $styles .= '}';


    // #mcb-bar ul li:first-child
    $styles .= $settings['icon_is_border']
        ? '#mcb-bar ul li:first-child{ border-left:' . $settings['icon_border_width'] . 'px solid ' . $settings['icon_border_color'] . ';}'
        : '';


    // #mcb-bar a
    $styles .= '#mcb-bar a{';
      //$styles .= 'display:block;';
      $styles .= 'margin:0 auto;';
      $styles .= 'width:inherit;';
    $styles .= '}';


    // #mcb-bar .fa
    $styles .= '#mcb-bar .fa{';
      $styles .= 'color:'. $settings['icon_color'] . ';';
    $styles .= '}';


    // @media query
    $styles .= '@media screen and (min-width:' . $settings['bar_max_screen_width'] . 'px){#mcb-wrap{display:none;}}';


    if( $settings['bar_is_toggle'] ) {

      // #mcb-bar
      $styles .= '#mcb-bar{';
        $styles .= 'overflow:hidden;';
        $styles .= '-webkit-transition:height 1s ease;';
        $styles .= '-moz-transition:height 1s ease;';
        $styles .= '-o-transition:height 1s ease;';
        $styles .= 'transition:height 1s ease;';
      $styles .= '}';


      // #mcb-toggle-checkbox
      $styles .= '#mcb-toggle-checkbox{';
        $styles .= 'display:none;';
      $styles .= '}';


      // #mcb-toggle-checkbox:checked ~ #mcb-bar
      $styles .= '#mcb-toggle-checkbox:checked ~ #mcb-bar{';
        $styles .= 'height:0;';
      $styles .= '}';


      // .mcb-toggle
      $styles .= '#mcb-toggle{';
        $styles .= 'cursor:pointer;';
        $styles .= 'display:inline-block;';
        $styles .= 'margin:0 auto;';
        $styles .= 'padding:1.2em 2.8em;';
        $styles .= 'position:relative;';
        $styles .= 'vertical-align:bottom;';
      $styles .= '}';


      // .mcb-toggle:before
      $styles .= '#mcb-toggle:before{';
        $styles .= 'background-color:' . $settings['bar_toggle_color'] . ';';
        $styles .= 'top' == $settings['bar_position'] ? 'border-radius:0 0 10px 10px;' : 'border-radius:10px 10px 0 0;';
        $styles .= 'content:"";';
        $styles .= 'position:absolute;';
        $styles .= 'top' == $settings['bar_position'] ? 'transform: perspective(5px) rotateX(-2deg);' : 'transform:perspective(5px) rotateX(2deg);';
        $styles .= 'top:0;right:0;bottom:0;left:0;';
        $styles .= 'z-index:-1;';
      $styles .= '}';

    }

    return array_replace(
        $new_value,
        array( 'styles' => $styles )
    );
  }




  /**
   * Outputs plugin's colophon
   * 
   * @since 0.0.1
   * 
   * @action
   * @event https://codex.wordpress.org/Plugin_API/Action_Reference/admin_footer
   * @in class-mcb-admin.php Mobile_Contact_Bar_Admin::plugins_loaded()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/get_current_screen
   * @uses https://codex.wordpress.org/Function_Reference/esc_url_raw
   * @uses https://codex.wordpress.org/Function_Reference/esc_attr_2
   * @uses https://codex.wordpress.org/Function_Reference/plugins_url
   * 
   */
  public static function admin_footer() {

    if( 'settings_page_' . self::$slug == get_current_screen()->base ) {

      $github = sprintf(
          '<a href="%s" target="_blank"><i class="fa fa-lg fa-github" title="%s"></i></a>',
          esc_url_raw( 'https://github.com/bansaghi/mobile-contact-bar', array( 'https' )),
          esc_attr__( 'Fork me on GitHub', 'mobile-contact-bar' )
      );

      $wordpress = sprintf(
          '<a href="%s" target="_blank"><i class="fa fa-lg fa-wordpress" title="%s"></i></a>',
          esc_url_raw( 'https://wordpress.org/support/view/plugin-reviews/mobile-contact-bar', array( 'https' )),
          esc_attr__( 'Rate this plugin', 'mobile-contact-bar' )
      );

      $paypal = sprintf(
          '<a href="%s" target="_blank"><img src="%s" title="%s" alt="%s"/></a>',
          esc_url_raw( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YXJAZ7Q5EJFUA', array( 'https' )),
          plugins_url( 'css/images/btn_donate_SM.gif', MOBILE_CONTACT_BAR_PLUGIN_PATH ),
          esc_attr__( 'Donate via PayPal', 'mobile-contact-bar' ),
          esc_attr__( 'Donation Button', 'mobile-contact-bar' )
      );

      ?><div id="mcb-footer" role="contentinfo">
        <span class="mcb-footer-author"><?php echo date('Y'); ?> &copy; Anna Bansaghi</span>
        <span class="mcb-footer-links"><?php echo $github, $wordpress, $paypal; ?></span>
      </div><?php
    }
  }




  /**
   * Gets default option
   * 
   * @since 1.0.0
   * 
   * @return array Option initialized with version number and default settings
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::on_activation()
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::wpmu_new_blog()
   * 
   * @uses https://codex.wordpress.org/Function_Reference/get_plugin_data
   * @uses https://developer.wordpress.org/reference/functions/plugin_dir_path
   * @uses class-mcb-settings.php Mobile_Contact_Bar_Settings::settings()
   * @uses class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_keys_recursive()
   * 
   */
  private static function get_default_option() {

    $plugin_data = get_plugin_data( MOBILE_CONTACT_BAR_PLUGIN_PATH );

    include_once( plugin_dir_path( MOBILE_CONTACT_BAR_PLUGIN_PATH ) . 'includes/class-mcb-settings.php' );

    return array(
        'version'  => $plugin_data['Version'],
        'settings' => array_map( function( $field ) { return $field['default']; }, self::sanitize_keys_recursive( Mobile_Contact_Bar_Settings::settings() )),
    );
  }




  /**
   * Sanitizes hexadecimal color value
   * 
   * @since 0.0.1
   * 
   * @param  string $color Color value
   * @return string Sanitized hexadecmial color
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * 
   */
  private static function sanitize_hex_color( $color ) {
    if( '' === $color ) {
      return null;
    }

    if( preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color )) {
      return $color;
    }

    return null;
  }




  /**
   * Sanitizes RGBA color value
   * 
   * @since 0.0.1
   * 
   * @param  string $color Color value
   * @return string Sanitized RGBA color
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * 
   */
  private static function sanitize_rgba_color( $color ) {
    if( '' === $color ) {
      return null;
    }

    if( preg_match('/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)$/', $color )) {
      return $color;
    }
 
    return null;
  }




  /**
   * Sanitizes float value
   * 
   * @since 0.0.1
   * 
   * @param  string $opacity Opacity value
   * @return string Sanitized opacity
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * 
   */
  private static function sanitize_float( $opacity ) {
    if( '' === $opacity ) {
      return null;
    }

    if( preg_match('/^0|1|0\.\d{1,2}$/', $opacity )) {
      return $opacity;
    }

    return null;
  }




  /**
   * Sanitizes phone number
   * 
   * @since 1.2.0
   * 
   * @param  string $phone Phone number
   * @return string Sanitized phone number with a plus sign (+) prefix
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * 
   */
  private static function sanitize_phone_number( $phone ) {
    if( '' === $phone ) {
      return null;
    }

    $phone = str_replace( array( ' ', '-', '+', '(', ')' ), '', $phone );

    if( preg_match('/^(\d{1,})$/', $phone )) {
      return '+' . $phone;
    }

    return null;
  }




  /**
   * Sanitizes all keys of a multidinemsional array
   * 
   * @since 1.2.0
   * 
   * @param  array $array Associative array
   * @return array Array with sannitized keys
   * 
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::init()
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_input()
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::get_default_option()
   * @usedby class-mcb-admin.php Mobile_Contact_Bar_Admin::sanitize_keys_recursive()
   * 
   */
  private static function sanitize_keys_recursive( $array ) { 

    $new_array = array();

    foreach( $array as $key => $value ) {
      if( is_array( $value )) {
        $new_array[sanitize_key( $key )] = self::sanitize_keys_recursive( $value );
      }
      else {
        $new_array[sanitize_key( $key )] = $value;
      }
    }
    return $new_array;
  }

}

