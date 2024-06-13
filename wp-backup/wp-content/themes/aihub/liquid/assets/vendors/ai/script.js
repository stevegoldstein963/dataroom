jQuery( document ).ready( function ( $ ) {

	/* Request */
	$( 'form#liquid-ai-form' ).on( 'submit', function ( e ) {
		$( 'form#liquid-ai-form .button' ).toggleClass( 'disabled' );
		add_log( '[STARTED] - Create post, prompt: ' + $( 'form#liquid-ai-form #prompt' ).val() );

		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'liquid_ai_post_actions',
				prompt: $( 'form#liquid-ai-form #prompt' ).val(),
				model: $( 'form#liquid-ai-form #model' ).val(),
				temperature: $( 'form#liquid-ai-form #temperature' ).val(),
				operation: $( 'form#liquid-ai-form #operation' ).val(),
				image: $( "form#liquid-ai-form #image" ).is( ":checked" ) ? true : false,
				language: $( 'form#liquid-ai-form #language' ).val(),
				tone_of_voice: $( 'form#liquid-ai-form #tone-of-voice' ).val(),
				security: $( 'form#liquid-ai-form #security' ).val()
			},
			success: function ( data ) {

				$( 'form#liquid-ai-form .button' ).toggleClass( 'disabled' );

				if ( data.error === true ) {
					add_log( data.message );
					alert( data.message );
				} else {
					$( '.liquid-ai-template-content' ).toggleClass( 'result' );

					console.log( data );
					add_form_data( 'insert_data', data.post );

					add_log( data.message );
					if ( data.total_tokens ) {
						add_log( data.total_tokens );
					}
					add_log( '[DONE]' );
				}
			}
		} );
		e.preventDefault();
	} );

	/* Insert data to result form */
	function add_form_data( action, post ) {

		var title = $( 'form#liquid-ai-form-result #title' ),
			content = $( 'form#liquid-ai-form-result #content' ),
			tags = $( 'form#liquid-ai-form-result #tags' );

		switch ( action ) {
			case "insert_data":
				title.val( post.title );
				content.val( post.content );
				tags.val( post.tags );
				break;
		}

		if ( post.image == 'true' ) {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'liquid_ai_get_images',
					query: tags.val(),
				},
				success: function ( data ) {
					if ( data.error === true ) {
						add_log( data.message );
						alert( data.message );
					}
					add_log( 'Image Requested' );
					$( 'form#liquid-ai-form-result .generated-images' ).css( 'display', 'block' );
					$( 'form#liquid-ai-form-result .generated-images' ).append( data.message );
				}
			} );
		}

	}

	/* Result Form to Insert Post */
	$( 'form#liquid-ai-form-result' ).on( 'submit', function ( e ) {
		$( 'form#liquid-ai-form-result .button' ).toggleClass( 'disabled' );

		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'liquid_ai_update_post',
				posts: {
					post_id: $( 'form#liquid-ai-form-result #post_id' ).val(),
					title: $( 'form#liquid-ai-form-result #title' ).val(),
					content: $( 'form#liquid-ai-form-result #content' ).val(),
					image: $( 'form#liquid-ai-form-result input[name="generated-image"]:checked' ).val(),
					tags: $( 'form#liquid-ai-form-result #tags' ).val(),
				},
				security: $( 'form#liquid-ai-form #security' ).val()
			},
			success: function ( data ) {
				$( 'form#liquid-ai-form-result .button' ).toggleClass( 'disabled' );
				$( '.liquid-ai-template-content' ).toggleClass( 'result' );
				$( ".liquid-ai-template" ).css( 'display', 'none' );

				if ( data.error === true ) {
					add_log( data.message );
					alert( data.message );
				} else {
					add_log( data.message );
					console.log( data.message );
					console.log( data );
					window.location.href = data.redirect;
				}

			}
		} );

		e.preventDefault();
	} );

	// Logging actions
	function add_log( message ) {
		$.ajax( {
			url: ajaxurl,
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


	// Add button to block editor & classic editor
	let blockLoaded = false;
	let classicLoaded = false;
	let blockLoadedInterval = setInterval( function () {
		if ( document.querySelector( '#editor' ) ) {
			blockLoaded = true;
		}
		if ( document.querySelector( '.liquid-ai-action-classic' ) ) {
			classicLoaded = true;
		}
		if ( blockLoaded ) {
			console.log( 'LIQUID AI: Block editor loaded!' );
			$( '.edit-post-header__toolbar' ).before( `<div class="liquid-ai-action components-button edit-post-fullscreen-mode-close"><img class="liquid-ai-logo" alt="Liquid AI" src="${ liquid_ai.logoUrl }"> Liquid AI</div>` );

			$( ".liquid-ai-action" ).click( function () {
				$( ".liquid-ai-template" ).css( 'display', 'grid' );
			} );

			$( ".liquid-ai-template--close" ).click( function () {
				$( ".liquid-ai-template" ).css( 'display', 'none' );
			} );

			clearInterval( blockLoadedInterval );
		}
		if ( classicLoaded ) {
			console.log( 'Liquid AI: Classic editor loaded!' );

			$( ".liquid-ai-action" ).click( function () {
				$( ".liquid-ai-template" ).css( 'display', 'grid' );
			} );

			$( ".liquid-ai-template--close" ).click( function () {
				$( ".liquid-ai-template" ).css( 'display', 'none' );
			} );

			clearInterval( blockLoadedInterval );
		}
	}, 500 );

	$( ".liquid-ai-recreate" ).click( function () {
		add_log( "Clicked: re-create" );
		$( '.liquid-ai-template-content' ).toggleClass( 'result' );
		$( 'form#liquid-ai-form-result .generated-images .generated-images-wrapper' ).detach();
		$( 'form#liquid-ai-form-result .generated-images' ).css( 'display', 'none' );
	} );

	// block extended

	$( document ).on( 'click', '.liquid-ai-examples span', function ( e ) {
		$( '#liquid-ai-gutenberg-prompt' ).val( $( this ).text() );
	} );

	const addFilter = wp.hooks.addFilter;
	const BlockControls = wp.blockEditor.BlockControls;
	const ToolbarButton = wp.components.ToolbarButton;

	const Hub_Block_Extended = function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== 'core/paragraph' ) {
				return BlockEdit( props );
			}

			return [
				wp.element.createElement(
					BlockControls,
					null,
					wp.element.createElement(
						ToolbarButton,
						{
							icon: 'hub-logo',
							label: 'Liquid AI',
							onClick: function () {
								jQuery.confirm( {
									columnClass: 'lqd-updates',
									//type: 'dark',
									title: 'Liquid AI <div id="liquid-ai-modal-ripple" class="lds-ripple" style="position:relative;left:10px;top:-6px"><div></div><div></div></div>',
									content: `
											<p>Explain to Liquid AI what you want to do. Keep it short.</p>
											<input id="liquid-ai-gutenberg-prompt" style="width:99%;padding:1em;border-color:99%" placeholder="Enter prompt..." type="text" required />
											<p>Examples:</p>
											<div class="liquid-ai-examples">
											<span>summarize</span>
											<span>translate to Spanish</span>
											<span>fix the grammer</span>
											<span>make more creative</span>
											</div>`,
									closeIcon: true,
									closeIconClass: 'dashicons dashicons-no',
									buttons: {
										new: {
											btnClass: 'btn-blue',
											text: 'Confirm →',
											action: function () {
												var $modal = this;
												jQuery( '#liquid-ai-modal-ripple' ).css( 'display', 'inline' );
												jQuery.post( ajaxurl, { action: 'liquid_ai_gutenberg', data: { prompt: jQuery( '#liquid-ai-gutenberg-prompt' ).val(), content: props.attributes.content, clientId: props.clientId } }, function ( response ) {
													if ( response.error ) {
														alert( response.message );
													} else {
														wp.data.dispatch( 'core/block-editor' ).updateBlockAttributes( props.clientId, { content: response.output } );
														$modal.close();
														add_log( response.total_tokens );
													}
												} );
												// prevent the modal from closing
												return false;
											}
										},
									}
								} );
								//console.log(props);
							}
						}
					)
				),
				BlockEdit( props )
			];
		};

	}
	addFilter( 'editor.BlockEdit', 'your-namespace', Hub_Block_Extended );


} );