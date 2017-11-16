/**
 * Admin related JavaScript functions
 * 
 * @since 0.0.1
 * 
 * @package Mobile_Contact_Bar
 * @author Anna Bansaghi <anna.bansaghi@mamikon.net>
 * @license GPL-3.0
 * @link https://wordpress.org/plugins/mobile-contact-bar/
 * @copyright Anna Bansaghi
 */


!(function( $, window, document, undefined ) {
  'use strict';

  $(document).ready(function() {

    $('#mcb-option-page .postbox').each( function() {
      $(this).addClass("closed").find('.inside').addClass('hide');
      $(this).find("button.handlediv").attr("aria-expanded", false);
    });

    $('#mcb-option-page .postbox').on( 'click', 'h2, h3, .handlediv', function(){
      var $parent = $(this).closest(".postbox");

      $parent.toggleClass("closed").find('.inside').toggleClass('hide');

      $parent.find("button.handlediv").attr('aria-expanded', function( idx, attr ) {
        return 'true' == attr ? 'false' : 'true';
      });      
    });


    $( ".mcb-slider" ).slider({
      min  : 0,
      max  : 1,
      step : 0.01,
      orientation: "horizontal",
      
      create: function( event, ui ) {
        $(this).slider('value', $(event.target).next('input').val());
      },

      slide: function( event, ui ) {
        $(this).next('input').val( ui.value );
      }
    });


    $("#mcb-bar_is_toggle").click(function() {
      $("#mcb-bar_toggle_color").closest('tr').toggle(this.checked);
    }).triggerHandler('click');

    $("#mcb-bar_toggle_color")
      .closest('td').css('padding-top', 5 ).css('padding-left', 15 )
      .prev('th').css('padding-top', 5 )
      .children('em').css('padding-left', 15 );


    $("#mcb-icon_is_border").click(function() {
      $("#mcb-icon_border_color").closest('tr').toggle(this.checked);
      $("#mcb-icon_border_width").closest('tr').toggle(this.checked);
    }).triggerHandler('click');

    $("#mcb-icon_border_color")
      .closest('td').css('padding-top', 5 ).css('padding-bottom', 0 ).css('padding-left', 15 )
      .prev('th').css('padding-top', 5 ).css('padding-bottom', 0 )
      .children('em').css('padding-left', 15 );

    $("#mcb-icon_border_width")
      .closest('td').css('padding-top', 5 ).css('padding-left', 15 )
      .prev('th').css('padding-top', 5 )
      .children('em').css('padding-left', 15 );



    var $allParameters = null;

    $("#mcb-table-contacts tbody").sortable({
      items: 'tr:has(th:has(i))',

      // create event is a workaround for jquery-ui-sortable < 1.11 in WP between 3.5 and 4.0
      create: function( event, ui ) {
        $(event.target).children('tr:has(th:has(i))').addClass('ui-sortable-handle');
      },

      start: function( event, ui ) {
        ui.placeholder.height( ui.item.height() );
        $allParameters = $("#mcb-table-contacts tbody").children('tr:not(.ui-sortable-handle)');
        $allParameters.detach();
      },

      stop: function( event, ui ) {
        $("#mcb-table-contacts tbody").children('tr:has(th:has(i))').each(function( idx ) {
          var key = $(this).find('.mcb-contact-wrapper').data('key'),
              $parameters = $allParameters.filter(function( i, param ) { return key == $(param).find('.mcb-contact-parameter-wrapper').data('key'); });

          $(this).find('.circle').html( idx + 1 );
          $parameters.insertAfter($(this));          
        });
        $allParameters = null;
      }
    });




    $("#mcb-table-contacts .mcb-contact-url").each(function() {
      var $circle = $(this).closest('tr').find('.circle');
      this.value ? $circle.addClass('wp-ui-highlight') : $circle.removeClass('wp-ui-highlight');
    });

    $("#mcb-table-contacts .mcb-contact-url").on('input', function() {
      var $circle = $(this).closest('tr').find('.circle');
      this.value ? $circle.addClass('wp-ui-highlight') : $circle.removeClass('wp-ui-highlight');
    });



    $("#mcb-table-contacts tbody").find('tr:not([class])').addClass('hidden');

    $("#mcb-table-contacts tbody").on( 'click', 'button', function() {

      var $tr = $(this).closest('tr'),
          key = $tr.find('.mcb-contact-wrapper').data('key'),
          $parameters = $("#mcb-table-contacts tbody").children('tr:not(.ui-sortable-handle)').filter(function( idx, param ) { return key == $(param).find('.mcb-contact-parameter-wrapper').data('key'); });

      $parameters.toggleClass('hidden');
      $(this).find('i').toggleClass('fa-plus fa-minus');

      $(this).attr('aria-expanded', function( idx, attr ) {
        return 'true' == attr ? 'false' : 'true';
      });


      if( 'true' == $(this).attr("aria-expanded")) {
        $parameters.first().find('td input').focus();
        $tr.css('border-bottom-width', '0');
        $parameters.not( ":last" ).css('border-bottom-width', '0');
        
      } else {
        $tr.find('.mcb-contact-url').focus();
        $tr.css('border-bottom-width', '1px');
        $parameters.not( ":last" ).css('border-bottom-width', '1px');
      }
    });


  });
})( jQuery, window, document );
