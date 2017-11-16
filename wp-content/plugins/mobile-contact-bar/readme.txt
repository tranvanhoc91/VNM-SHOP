=== Mobile Contact Bar ===
Contributors: anna.bansaghi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YXJAZ7Q5EJFUA
Tags: communication, social media, social link, contact, mobile, telephone, phone, cell phone, icon, action, mobile action, mobile device, profile, social media profile

Requires at least: 3.5.0
Tested up to: 4.8
Stable tag: 1.3.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Allow your visitors to contact you via phone, email, or Social Media


== Description ==

*Mobile Contact Bar is a compact and highly customizable plugin, which allows your visitors to contact you directly via phone, email, Social Media, or any custom URL.*

Display settings for bar, icons and contacts are editable under the *Settings &rarr; Mobile Contact Bar* menu in WordPress Admin.

The contact bar appears if at least one contact information is set, and on those screens where the width is below of the *Maximum Screen Width* setting.

= Features =

* Set your contact information and Social Media profiles
* Design bar and icons via plugin settings or directly via CSS
* Change the order of the icons as you wish
* Add subject, body, cc, bcc parameters to email, add predefined text messages
* Show or hide bar toggle - depending on your needs
* Add custom URLs using the `mcb_admin_update_contacts` filter and [Font Awesome Icons](http://fontawesome.io/icons/)

For more information see the [FAQ](https://wordpress.org/plugins/mobile-contact-bar/faq/).



= Tested with =

* Twenty Ten
* Twenty Eleven
* Twenty Twelve
* Twenty Thirteen
* Twenty Fourteen
* Twenty Fifteen :S
* Twenty Sixteen
* Twenty Seventeen



= Developers =

*Administration Hooks*

* `mcb_admin_update_contacts( array $contacts )` filter allows you to modify the *Contact List* section. Typically, you add new items to that list.

* `mcb_admin_update_settings( array $settings )` filter allows you to modify the *Bar Display Settings* section and the *Icon Display Settings* section. If you can not wait for the official enhancement, you can add settings to those sections by yourself.


*Front end Hooks*

* `mcb_front_escape_uri( string $uri, string $id, string $protocol, string $resource )` filter allows you to perform custom specification and validation of a contact’s reference. The plugin already supports the `http`, `https`, `mailto`, `skype`, `sms` and the `tel` protocols.

* `mcb_front_render_html( array $contacts, array $settings )` action gives you full control over the representation.




= Credits =

* [Font Awesome](http://fontawesome.io/) the iconic font and CSS toolkit by Dave Gandy
* [WP Color Picker](https://github.com/Codestar/codestar-wp-color-picker) color picker with alpha channel by Codestar




= Please Vote and Enjoy =

Your votes really make a difference! Thanks.





== Installation ==

1. Upload `mobile-contact-bar` to the `/wp-content/plugins/` directory
2. Activate the plugin through the *Plugins* menu in WordPress Admin
3. Click on the new submenu item *Mobile Contact Bar* under the *Settings* menu
4. Edit your contact bar settings
5. Fill in the information that you want displayed (email address, phone number, Social Media profiles)
6. Check your site and its contact bar appearance on a mobile device



== Frequently Asked Questions ==

= I want to display contacts which are not listed on the MCB settings page like SlideShare, Reddit or Steam. Can I do that? =

Yes, you can. The `mcb_admin_update_contacts` filter allows you to modify the contact list. You can add new items to that list, and they will appear at the bottom of the MCB settings page. For proper icon names check the [Font Awesome Icons](http://fontawesome.io/icons/).

E.g. insert the following code snippet to your active theme's `functions.php` file:

    function my_mcb_admin_update_contacts( $contacts ) {

        $contacts['slideshare'] = array(
            'icon'        => 'slideshare',   // Font Awesome Icon name
            'title'       => 'SlideShare',
            'protocol'    => 'http',
            'placeholder' => 'http://www.slideshare.net/username'
        );
        return $contacts;
    }

    add_filter( 'mcb_admin_update_contacts', 'my_mcb_admin_update_contacts' );


= How can I fine-tune the display of my contact bar using CSS styles? =

Enter custom CSS using the `#mcb-bar` and `#mcb-toggle` selectors, and that will override the styles defined by the plugin.

E.g. insert the following code snippet to your active theme's `style.css` file:

    #mcb-bar {
        background-image: linear-gradient(to bottom, #2a95c5, #21759b);
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
    }

    #mcb-bar .fa {
        color: #fff;
        text-decoration: none;
    }

    #mcb-bar ul li,
    #mcb-bar ul li:first-child {
        border-color: #fff;
        border-width: 2px;
    }

    #mcb-toggle:before {
        background-image: linear-gradient(to bottom, #2a95c5, #21759b);
    }


= Different styles on front-page and on the rest of the site =

WordPress adds `home` class to the `body` tag of the front-page. Narrow down the MCB related selection with the `.home` class selector:

    .home #mcb-bar .fa {
        color: yellow;
    }


= I need a copyright icon which links visitors to my copyright page =

Use the `mcb_admin_update_contacts` filter for adding the [copyright](http://fontawesome.io/icon/copyright/) icon to the contact list. Enter the URL of your existing page to the newly created contact item.

    function my_mcb_admin_update_contacts( $contacts ) {

        $contacts['copyright'] = array(
            'icon'        => 'copyright',   // Font Awesome Icon name
            'title'       => 'Copyright',
            'protocol'    => 'http',
            'placeholder' => 'http://mysite.com/mycopyrightpage'
        );
        return $contacts;
    }

    add_filter( 'mcb_admin_update_contacts', 'my_mcb_admin_update_contacts' );



== Screenshots ==

1. WordPress Admin &rarr; Settings &rarr; Mobile Contact Bar


== Changelog ==

= 1.3.0 =
* [Added] Texting option - https://wordpress.org/support/topic/text-with-pre-filled-option/

= 1.2.3 =
* [Fixed] Use &lt;tbody&gt; explicitely in contacts' table
* [Fixed] Fix issue with array_filter()

= 1.2.2 =
* [Fixed] Fix issue with array_filter()

= 1.2.1 =
* [Fixed] Fix issue with empty arrays

= 1.2.0 =
* [Added] Dragable / orderable contacts
* [Added] Add option for setting subject, body, cc, bcc of email - https://wordpress.org/support/topic/add-subject-and-body-to-email/
* [Updated] Refresh option page UI using meta boxes
* [Updated] Font Awesome 4.7.0
* [Fixed] Prepare plugin for localization
* [Fixed] Full form of plugin name in class and global names
* [Fixed] Sanitize phone number and add a plus sign (+) prefix
* [Fixed] Sanitize all keys of the settings and the contacts arrays

= 1.1.2 =
* [Updated] Font Awesome 4.6.3
* [Fixed] Fix the left aligned icons in the CSS - https://wordpress.org/support/topic/does-your-plugin-support-the-hemingway-theme/

= 1.1.1 =
* [Updated] Font Awesome 4.6.1
* [Fixed] Fix admin styles
* [Fixed] Fix bar styles

= 1.1.0 =
* [Added] Add new option for opening links in a new tab - https://wordpress.org/support/topic/no-instagram-icon/

= 1.0.1 =
* [Fixed] Improve setting and contact validation (sanitization)
* [Fixed] Set the default value of the fixed bar position to true
* [Fixed] Remove obsolate workarounds

= 1.0.0 =
* [Upgraded] Official release

= 0.0.2 =
* [Fixed] Fix default option creation issue during network activation

= 0.0.1 =
* [Started] Initial release


== Upgrade Notice ==


= 1.0.0 =
* Official release

= 0.0.1 =
* Initial release
