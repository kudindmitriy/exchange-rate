(function( $ ) {
	'use strict';

	$(document).ready(function(){

		function update_exchange_rate () {
			var dataJSON = {
				action: 'shortcode_update_exchange_rate',
				nonce_code : localize.nonce,
				currency_pair: $( '#currency_pair' ).val()
			};

			$.ajax({
				cache: false,
				type: "POST",
				url: localize.ajaxurl,
				data: dataJSON,
				success: function( response ){
					var data = JSON.parse(response);

					$('#exchange_last_update').text(data.stt_last_update);
					$('#exchange_sell_price').text(data.sell_price);
					$('#exchange_buy_price').text(data.buy_price);
				},
				error: function( xhr, status, error ) {
					console.log( 'Status: ' + xhr.status );
					console.log( 'Error: ' + xhr.responseText );
				}
			});
		}
		setInterval(update_exchange_rate, 15000);
	});


})( jQuery );
