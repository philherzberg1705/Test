/**
 * Live-Suche im Header-Offcanvas (§14): AJAX, debounced, animierte
 * Ergebnisliste. Enter/Submit führt weiter zur vollen Ergebnisseite
 * (normales Formular, kein JS nötig).
 */
( function () {
	'use strict';

	var DEBOUNCE_MS = 300;
	var MIN_CHARS = 2;

	function renderResults( container, results, term, moreUrl ) {
		if ( ! results.length ) {
			container.innerHTML = term.length >= MIN_CHARS
				? '<p class="bw-search-results__empty">' + ( container.dataset.bwEmptyText || 'Keine Ergebnisse gefunden.' ) + '</p>'
				: '';
			container.classList.remove( 'is-visible' );
			return;
		}

		var html = '<ul class="bw-search-results__list">';

		results.forEach( function ( item ) {
			html += '' +
				'<li class="bw-search-results__item">' +
					'<a href="' + item.permalink + '">' +
						'<img src="' + item.image + '" alt="" loading="lazy" />' +
						'<span class="bw-search-results__meta">' +
							'<span class="bw-search-results__name">' + item.name + '</span>' +
							'<span class="bw-search-results__price">' + item.priceHtml + '</span>' +
						'</span>' +
					'</a>' +
				'</li>';
		} );

		html += '</ul>';

		if ( moreUrl ) {
			html += '<a class="bw-search-results__more" href="' + moreUrl + '">Alle Ergebnisse ansehen</a>';
		}

		container.innerHTML = html;
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
