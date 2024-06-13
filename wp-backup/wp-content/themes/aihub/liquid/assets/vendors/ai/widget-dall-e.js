jQuery( document ).ready( function ( $ ) {

	// Request
	$( '.lqd-dall-e--form' ).on( 'submit', function ( e ) {

		let wrapper = $( this ).parent(); // select .lqd-dall-e
		let images = wrapper.find('.lqd-dall-e--results-images');
		let options = JSON.parse($( this ).attr( 'data-options' ));

		wrapper.addClass('loading');
		wrapper.removeClass( 'error-login' );
		wrapper.removeClass( 'error-limit' );
		wrapper.removeClass( 'success' );
		images.empty();

		$.ajax( {
			url: liquidTheme.uris.ajax,
			type: 'POST',
			data: {
				action: 'liquid_ai_dall_e',
				prompt: $( '.lqd-dall-e #prompt' ).val(),
				n: options.n,
				l: options.l,
				size: options.size,
				security: $( '.lqd-dall-e #security' ).val()
			},
			success: function ( data ) {
				wrapper.removeClass( 'loading' );
				if ( data.error === true ) {
					add_log( data.message );
					if ( data.reason ) {
						if ( data.reason === 'login' ){
							wrapper.addClass( 'error-login' );
						} else if (data.reason === 'limit') {
							wrapper.addClass( 'error-limit' );
						}
					} else {
						alert( data.message );
					}
				} else {
					wrapper.addClass( 'success' );
					images.append(data.output);
				}
			}
		} );
		e.preventDefault();
	} );

	// Tags
	$( document ).on( 'click', '.lqd-dall-e--tags .lqd-dall-e--tag', function ( e ) {
		$( '.lqd-dall-e--form #prompt' ).val( $( this ).attr( 'data-prompt' ) );
	} );

	// Logging actions
	function add_log( message ) {
		$.ajax( {
			url: liquidTheme.uris.ajax,
			type: 'POST',
			data: {
				action: 'liquid_ai_add_log',
				log: get_log_time() + message,
			},
			success: function ( data ) {
				//console.log(data.message);
			}
		} );
	}

	function get_log_time() {
		let date = new Date().toLocaleString();
		return '[' + date + '] - ';
	}

} );