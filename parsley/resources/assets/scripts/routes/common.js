export default {
  init() {
    /* slick */
    $('.slick-carousel').slick();

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
