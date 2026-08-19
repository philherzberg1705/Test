/**
 * Side-Cart (§15/§16/§20): Add-to-Cart auf der Produktseite, Mengenän-
 * derung und Entfernen im Panel — alles AJAX, ein gemeinsames Response-
 * Format (siehe inc/ajax/cart.php), kein Reload.
 */
( function () {
	'use strict';

	function getContentEl() {
		return document.querySelector( '[data-bw-cart-content]' );
	}

	function updateBadge( count ) {
		var badge = document.querySelector( '[data-bw-cart-badge]' );
		if ( ! badge ) {
			return;
		}
		badge.textContent = String( count );
		if ( count > 0 ) {
			badge.removeAttribute( 'hidden' );
		} else {
			badge.setAttribute( 'hidden', '' );
		}
	}

	function request( action, extraParams ) {
		var data = window.bodywingsData;
		var content = getContentEl();

		if ( ! data || ! content ) {
			return Promise.resolve( null );
		}

		content.classList.add( 'is-loading' );

		var body = new URLSearchParams( extraParams || {} );
		body.set( 'action', action );
		body.set( 'nonce', data.nonce );

		return fetch( data.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( json && json.success ) {
					content.innerHTML = json.data.miniCartHtml;
					updateBadge( json.data.cartCount );
				}
				return json;
			} )
			.finally( function () {
				content.classList.remove( 'is-loading' );
			} );
	}

	function handleAddToCartSubmit( event ) {
		var form = event.target.closest( 'form.cart' );
		if ( ! form ) {
			return;
		}

		event.preventDefault();

		var submitBtn = form.querySelector( '.single_add_to_cart_button' );
		if ( submitBtn ) {
			submitBtn.classList.add( 'is-loading' );
			submitBtn.disabled = true;
		}

		var params = new URLSearchParams( new FormData( form ) );

		request( 'bodywings_add_to_cart', params ).then( function ( json ) {
			if ( submitBtn ) {
				submitBtn.classList.remove( 'is-loading' );
				submitBtn.disabled = false;
			}

			if ( json && json.success && window.bodywings && window.bodywings.openOffcanvas ) {
				window.bodywings.openOffcanvas( 'cart' );
			}
		} );
	}

	function handleContentChange( event ) {
		var input = event.target.closest( '[data-bw-cart-qty]' );
		if ( ! input ) {
			return;
		}

		var quantity = parseInt( input.value, 10 );
		if ( isNaN( quantity ) || quantity < 0 ) {
			return;
		}

		request( 'bodywings_update_cart_item', {
			cart_item_key: input.getAttribute( 'data-cart-item-key' ),
			quantity: quantity,
		} );
	}

	function handleContentClick( event ) {
		var removeBtn = event.target.closest( '[data-bw-cart-remove]' );
		if ( ! removeBtn ) {
			return;
		}

		event.preventDefault();

		request( 'bodywings_remove_cart_item', {
			cart_item_key: removeBtn.getAttribute( 'data-cart-item-key' ),
		} );
	}

	function initCart() {
		document.addEventListener( 'submit', handleAddToCartSubmit );

		var content = getContentEl();
		if ( content ) {
			content.addEventListener( 'change', handleContentChange );
			content.addEventListener( 'click', handleContentClick );
		}
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'cart', initCart );
	}
} )();
