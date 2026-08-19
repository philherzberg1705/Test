/**
 * Live-Suche im Header-Offcanvas (§14): AJAX, debounced, animierte
 * Ergebnisliste. Enter/Submit führt weiter zur vollen Ergebnisseite
 * (normales Formular, kein JS nötig).
 */
( function () {
	'use strict';

	var DEBOUNCE_MS = 300;
	var MIN_CHARS = 2;

	/**
	 * DOM statt innerHTML-String-Konkatenation: Produktname/-preis kommen
	 * aus Nutzereingaben-abhängigen Daten (Produkttitel können HTML
	 * enthalten) — textContent verhindert XSS über die Live-Suche (§32).
	 */
	function renderResults( container, results, term, moreUrl ) {
		container.textContent = '';

		if ( ! results.length ) {
			if ( term.length >= MIN_CHARS ) {
				var empty = document.createElement( 'p' );
				empty.className = 'bw-search-results__empty';
				empty.textContent = container.dataset.bwEmptyText || 'Keine Ergebnisse gefunden.';
				container.appendChild( empty );
			}
			container.classList.remove( 'is-visible' );
			return;
		}

		var list = document.createElement( 'ul' );
		list.className = 'bw-search-results__list';

		results.forEach( function ( item ) {
			var li = document.createElement( 'li' );
			li.className = 'bw-search-results__item';

			var link = document.createElement( 'a' );
			link.href = item.permalink;

			var img = document.createElement( 'img' );
			img.src = item.image;
			img.alt = '';
			img.loading = 'lazy';

			var meta = document.createElement( 'span' );
			meta.className = 'bw-search-results__meta';

			var name = document.createElement( 'span' );
			name.className = 'bw-search-results__name';
			name.textContent = item.name;

			var price = document.createElement( 'span' );
			price.className = 'bw-search-results__price';
			price.textContent = item.priceHtml;

			meta.append( name, price );
			link.append( img, meta );
			li.appendChild( link );
			list.appendChild( li );
		} );

		container.appendChild( list );

		if ( moreUrl ) {
			var more = document.createElement( 'a' );
			more.className = 'bw-search-results__more';
			more.href = moreUrl;
			more.textContent = 'Alle Ergebnisse ansehen';
			container.appendChild( more );
		}

		requestAnimationFrame( function () {
			container.classList.add( 'is-visible' );
		} );
	}

	function initSearch() {
		var form = document.querySelector( '[data-bw-search-form]' );
		var input = form ? form.querySelector( 'input[name="s"]' ) : null;
		var results = document.querySelector( '[data-bw-search-results]' );

		if ( ! form || ! input || ! results ) {
			return;
		}

		var timer = null;

		input.addEventListener( 'input', function () {
			window.clearTimeout( timer );
			var term = input.value.trim();

			if ( term.length < MIN_CHARS ) {
				renderResults( results, [], term, '' );
				return;
			}

			timer = window.setTimeout( function () {
				var data = window.bodywingsData;
				if ( ! data ) {
					return;
				}

				var body = new URLSearchParams();
				body.set( 'action', 'bodywings_live_search' );
				body.set( 'nonce', data.nonce );
				body.set( 'term', term );

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
							renderResults( results, json.data.results, term, json.data.moreUrl );
						}
					} )
					.catch( function () {
						/* Netzwerkfehler: still bleiben, Nutzer kann Enter für volle Suche drücken. */
					} );
			}, DEBOUNCE_MS );
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'search', initSearch );
	}
} )();
