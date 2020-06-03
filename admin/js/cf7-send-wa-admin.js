(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$( document ).ready( function(){
		function ThirdParty() {
			var self = this;
			self.provider = ko.observable(cf7sendwa.provider);
		}
		var thirdparty = new ThirdParty();
		var element = $( '.sp-admin-page' )[0];
		ko.applyBindings( thirdparty, element );
	} )

	$( '.cf7-checkout-form' ).select2({
		placeholder: "Select Contact Form", 
		allowClear: true, 
		ajax: {
			method: 'post',
			url: cf7sendwa.ajaxurl,
			dataType: 'json',
			data: function( params ){
				return {
					search: params.term,
					action: 'select_contact_form',
					security: cf7sendwa.security
				};
			}
		}
	});

})( jQuery );
