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
			// Sortierung ist WooCommerce-Standardmarkup (eigenes <form>, kein
			// Teil von [data-bw-filter-form]) — hier separat eingebunden, damit
			// ein Sortierwechsel ebenfalls per AJAX läuft statt per Reload (§13).
			ordering: document.querySelector( '.woocommerce-ordering select[name="orderby"]' ),
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

	function currentParams() {
		var els = getShopEls();
		var params = els.form ? serializeForm( els.form ) : new URLSearchParams();

		if ( els.ordering && els.ordering.value ) {
			params.set( 'orderby', els.ordering.value );
		}

		return params;
	}

	/**
	 * Bringt Filterformular + Sortierung mit einem per Browser-Back/Forward
	 * wiederhergestellten Query-State in Einklang (§13 "Back/Forward
	 * sinnvoll unterstützen") — sonst zeigt das Panel nach einem
	 * Verlaufswechsel veraltete Checkbox-/Preis-/Sortierwerte.
	 */
	function syncFormFromParams( params ) {
		var els = getShopEls();

		if ( els.form ) {
			Array.prototype.forEach.call( els.form.elements, function ( field ) {
				if ( ! field.name || 'button' === field.type || 'submit' === field.type ) {
					return;
				}

				if ( 'checkbox' === field.type ) {
					field.checked = params.getAll( field.name ).indexOf( field.value ) !== -1;
				} else {
					field.value = params.get( field.name ) || '';
				}
			} );
		}

		if ( els.ordering ) {
			els.ordering.value = params.get( 'orderby' ) || els.ordering.value;
		}
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
		els.grid.setAttribute( 'aria-busy', 'true' );

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
				els.grid.setAttribute( 'aria-busy', 'false' );
			} );
	}

	function submitFilter( pushState ) {
		var els = getShopEls();
		if ( ! els.form ) {
			return;
		}
		fetchProducts( currentParams(), 1, pushState !== false );
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

		if ( els.ordering ) {
			// WC rendert den Sortier-Select mit onchange="this.form.submit()"
			// (voller Reload) — entfernen und stattdessen an denselben
			// AJAX-Pfad wie die übrigen Filter anhängen (§13/§15).
			els.ordering.onchange = null;
			els.ordering.addEventListener( 'change', function () {
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
			fetchProducts( currentParams(), page, true );
			if ( els.content ) {
				els.content.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}
		} );

		window.addEventListener( 'popstate', function ( event ) {
			if ( event.state && event.state.bwFilter ) {
				var restoredParams = new URLSearchParams( event.state.params );
				syncFormFromParams( restoredParams );
				fetchProducts( restoredParams, event.state.page || 1, false );
			}
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'filter', initFilter );
	}
} )();
