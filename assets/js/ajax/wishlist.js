/**
 * Globale Wishlist (§7): eingeloggt → AJAX/User-Meta, Gast → localStorage.
 * Ein Klick-Handler für beide Fälle, Icons auf Karten/Produktseite/Header
 * bleiben synchron, kleine Pop-Animation als Micro-Interaction.
 */
( function () {
	'use strict';

	var STORAGE_KEY = 'bodywingsWishlist';

	function isLoggedIn() {
		return !! ( window.bodywingsData && window.bodywingsData.isUserLoggedIn );
	}

	function getLocalIds() {
		try {
			var raw = window.localStorage.getItem( STORAGE_KEY );
			return raw ? JSON.parse( raw ) : [];
		} catch ( error ) {
			return [];
		}
	}

	function setLocalIds( ids ) {
		try {
			window.localStorage.setItem( STORAGE_KEY, JSON.stringify( ids ) );
		} catch ( error ) {
			// LocalStorage nicht verfügbar (z.B. privater Modus) — Funktion
			// bleibt clientseitig ohne Persistenz nutzbar.
		}
	}

	function setButtonState( button, active ) {
		button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
		button.classList.toggle( 'is-active', active );
		button.classList.add( 'is-pop' );
		button.addEventListener( 'animationend', function onEnd() {
			button.classList.remove( 'is-pop' );
			button.removeEventListener( 'animationend', onEnd );
		} );
	}

	function updateHeaderBadge( count ) {
		var badge = document.querySelector( '[data-bw-wishlist-badge]' );
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

	function toggleGuest( button, productId ) {
		var ids = getLocalIds();
		var index = ids.indexOf( productId );
		var active;

		if ( -1 === index ) {
			ids.push( productId );
			active = true;
		} else {
			ids.splice( index, 1 );
			active = false;
		}

		setLocalIds( ids );
		setButtonState( button, active );
		updateHeaderBadge( ids.length );
	}

	function toggleServer( button, productId ) {
		var data = window.bodywingsData;
		if ( ! data ) {
			return;
		}

		var body = new URLSearchParams();
		body.set( 'action', 'bodywings_wishlist_toggle' );
		body.set( 'nonce', data.nonce );
		body.set( 'product_id', productId );

		fetch( data.ajaxUrl, {
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
					setButtonState( button, json.data.inWishlist );
					updateHeaderBadge( json.data.count );
				}
			} );
	}

	/**
	 * Gäste-Zustand nach dem Laden korrigieren — der Server kennt
	 * localStorage nicht und rendert Buttons immer als "nicht aktiv".
	 */
	function syncButtonsFromLocalStorage() {
		if ( isLoggedIn() ) {
			return;
		}

		var ids = getLocalIds().map( String );
		document.querySelectorAll( '[data-bw-wishlist-toggle]' ).forEach( function ( button ) {
			var active = -1 !== ids.indexOf( button.getAttribute( 'data-product-id' ) );
			button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
			button.classList.toggle( 'is-active', active );
		} );
		updateHeaderBadge( ids.length );
	}

	/**
	 * Gäste-Wishlist beim Login einmalig serverseitig übernehmen.
	 */
	function mergeLocalIntoServer() {
		if ( ! isLoggedIn() ) {
			return;
		}

		var ids = getLocalIds();
		if ( ! ids.length ) {
			return;
		}

		var data = window.bodywingsData;
		if ( ! data ) {
			return;
		}

		var body = new URLSearchParams();
		body.set( 'action', 'bodywings_wishlist_merge' );
		body.set( 'nonce', data.nonce );
		ids.forEach( function ( id ) {
			body.append( 'ids[]', id );
		} );

		fetch( data.ajaxUrl, {
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
					window.localStorage.removeItem( STORAGE_KEY );
					updateHeaderBadge( json.data.count );
				}
			} );
	}

	function loadWishlistPage() {
		var grid = document.querySelector( '[data-bw-wishlist-grid]' );
		if ( ! grid ) {
			return;
		}

		var emptyMessage = document.querySelector( '[data-bw-wishlist-empty]' );
		var initialIds = [];

		try {
			initialIds = JSON.parse( grid.getAttribute( 'data-bw-wishlist-initial-ids' ) || '[]' );
		} catch ( error ) {
			initialIds = [];
		}

		var ids = isLoggedIn() ? initialIds : getLocalIds();

		if ( ! ids.length ) {
			grid.innerHTML = '';
			if ( emptyMessage ) {
				emptyMessage.removeAttribute( 'hidden' );
			}
			return;
		}

		var data = window.bodywingsData;
		if ( ! data ) {
			return;
		}

		var body = new URLSearchParams();
		body.set( 'action', 'bodywings_wishlist_get_products' );
		body.set( 'nonce', data.nonce );
		ids.forEach( function ( id ) {
			body.append( 'ids[]', id );
		} );

		fetch( data.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( ! json || ! json.success ) {
					return;
				}
				grid.innerHTML = json.data.html;
				if ( ! json.data.html && emptyMessage ) {
					emptyMessage.removeAttribute( 'hidden' );
				}
			} );
	}

	function onDocumentClick( event ) {
		var button = event.target.closest( '[data-bw-wishlist-toggle]' );
		if ( ! button ) {
			return;
		}

		event.preventDefault();
		var productId = button.getAttribute( 'data-product-id' );

		if ( isLoggedIn() ) {
			toggleServer( button, productId );
		} else {
			toggleGuest( button, productId );
		}
	}

	function initWishlist() {
		syncButtonsFromLocalStorage();
		mergeLocalIntoServer();
		loadWishlistPage();
		document.addEventListener( 'click', onDocumentClick );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'wishlist', initWishlist );
	}
} )();
