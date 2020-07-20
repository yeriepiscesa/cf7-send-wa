(function( $ ) {
	'use strict';
	
	function SendWA_ViewModel() {
		var self = this;
		self.provider = ko.observable(cf7sendwa.provider);

		self.channelItems = ko.observableArray();
		self.addChannel = function(){
	        var data = {
	            title: '',
	            number: '',
	            active: true,
	            order: 0
	        };
	        self.channelItems.push( new ChannelItem( data ) );            
		}
	    self.removeChannel = function(){
	        self.channelItems.remove( this );
	    };                
	}
	
	function ChannelItem( data ) {
		this.title = data.title;
		this.number = data.number;
		this.active = data.active;
		this.order = data.order;
	}
	
	var sendwa = new SendWA_ViewModel();
	$( document ).ready( function(){
		var element = $( '.sp-admin-page' )[0];
		ko.applyBindings( sendwa, element );
		var channel = $( '#cf7sendwa_channel' ).val();
		if( channel != '' ) {
			var channelItems = $.parseJSON( decodeURIComponent( channel ) );
			_.each( channelItems, function( item, index, list ){
				sendwa.channelItems.push( new ChannelItem( item ) );
			} );
		}
	} );
	
	$( 'body' ).on( 'submit', '.sp-cf7sendwa-form', function( evt ){
		$( '#cf7sendwa_channel' ).val( encodeURIComponent( ko.toJSON( sendwa.channelItems ) ) );
	} );

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
