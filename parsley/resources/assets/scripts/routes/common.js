export default {
  init() {
    /* slick */
    $('.slick-carousel').slick();

    /* ACF */
    if(typeof window.acf !== 'undefined') {
      // Date picker & Google Maps compatibility
      $('.acf-google-map input.search, .acf-date-picker input.input').addClass('form-control');
      // Clean errors on submission
      window.acf.addAction('validation_begin', function($form){
        $form.find('.acf-error-message').remove();
      });
      // Add alert alert-danger & move below field
     window.acf.addAction('invalid_field', function(field){
        field.$el.find('.acf-notice.-error').addClass('alert alert-danger').insertAfter(field.$el.find('.acf-input'));
      });
    }

    /* return-to-top */
    jQuery(function () {
      jQuery( window ).scroll( function() {
        if (jQuery( this ).scrollTop() >= 50) {
          jQuery( '#return-to-top' ).fadeIn( 300 );
        }
        else {
          jQuery( '#return-to-top' ).fadeOut( 300 );
        }
      } );
      jQuery( '#return-to-top' ).click( function( event ) {
        event.preventDefault();
        jQuery( 'body,html' ).animate( { scrollTop : 0 }, 500 );
      } );
    } );
  },
  finalize() {
  },
};
