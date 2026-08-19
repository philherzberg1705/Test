/**
 * Produktfilter (§13/§15/§20): AJAX-Refresh ohne Reload, Loading-State,
 * URL-State über history.pushState, Back/Forward über popstate.
 */
( function () {
	'use strict';

	function getShopEls() {
		return {
			form: document.querySelector( '[data-bw-filter-form]' ),
			grid: document.querySelector( '[data-bw-shop-grid]' ),
			pagination: document.querySelector( '[data-bw-shop-pagination]' ),
			resultCount: document.querySelector( '[data-bw-shop-result-count]' ),
			content: document.querySelector( '.bw-shop__content' ),
		};
	}

	function serializeForm( form ) {
		var params = new URLSearchParams( new FormData( form ) );
		// Leere Werte nicht in die URL schreiben (sauberer Query-String).
		Array.from( params.keys() ).forEach( function ( key ) {
			if ( '' === params.get( key ) ) {
				params.delete( key );
			}
		} );
		return params;
	}

	function fetchProducts( params, page, pushState ) {
		var els = getShopEls();
		if ( ! els.form || ! els.grid ) {
			return;
		}

		var data = window.bodywingsData;
		if ( ! data ) {
			return;
		}

		els.content.classList.add( 'is-loading' );

		var body = new URLSearchParams( params.toString() );
		body.set( 'action', 'bodywings_filter_products' );
		body.set( 'nonce', data.nonce );
		body.set( 'paged', String( page || 1 ) );

		var context = els.form.dataset.bwFilterContext ? JSON.parse( els.form.dataset.bwFilterContext ) : null;
		if ( context && context.taxonomy && context.term_id ) {
			body.set( 'taxonomy', context.taxonomy );
			body.set( 'term_id', context.term_id );
		}

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

				els.grid.innerHTML = json.data.html;
				els.pagination.innerHTML = json.data.pagination;
				if ( els.resultCount ) {
					els.resultCount.textContent = json.data.countText;
				}

				if ( pushState ) {
					var url = new URL( window.location.href );
					url.search = params.toString();
					if ( page > 1 ) {
						url.searchParams.set( 'paged', String( page ) );
					}
					window.history.pushState( { bwFilter: true, params: params.toString(), page: page }, '', url.toString() );
				}

				// Klick-Handler für Karten/Wishlist laufen per Event-Delegation
				// (document), neue Karten funktionieren dadurch ohne Reinit.
				document.dispatchEvent( new CustomEvent( 'bodywings:grid-updated' ) );
			} )
			.catch( function () {
				// Netzwerkfehler: Nutzer behält die zuletzt sichtbaren Ergebnisse.
			} )
			.finally( function () {
				els.content.classList.remove( 'is-loading' );
			} );
	}

	function submitFilter( pushState ) {
		var els = getShopEls();
		if ( ! els.form ) {
			return;
		}
		fetchProducts( serializeForm( els.form ), 1, pushState !== false );
	}

	function initFilter() {
		var els = getShopEls();
		if ( ! els.form ) {
			return;
		}

		els.form.addEventListener( 'change', function () {
			submitFilter( true );
		} );

		var resetBtn = els.form.querySelector( '[data-bw-filter-reset]' );
		if ( resetBtn ) {
			resetBtn.addEventListener( 'click', function () {
				els.form.reset();
				submitFilter( true );
			} );
		}

		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( '[data-bw-filter-page]' );
			if ( ! link ) {
				return;
			}
			event.preventDefault();
			var url = new URL( link.href );
			var page = parseInt( url.searchParams.get( 'paged' ) || '1', 10 );
			fetchProducts( serializeForm( els.form ), page, true );
			if ( els.content ) {
				els.content.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		} );

		window.addEventListener( 'popstate', function ( event ) {
			if ( event.state && event.state.bwFilter ) {
				fetchProducts( new URLSearchParams( event.state.params ), event.state.page || 1, false );
			}
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'filter', initFilter );
	}
} )();
