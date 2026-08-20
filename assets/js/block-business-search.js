/**
 * Geschäftssuche: Leaflet-Karte + Liste + clientseitige Suche (§13/§14-
 * Analogie: kein Server-Roundtrip nötig, siehe render.php-Kommentar).
 * Die Liste ist bereits serverseitig vollständig vorhanden (§25) — dieses
 * Skript fügt nur die Karte hinzu und filtert Liste+Marker parallel.
 */
( function () {
	'use strict';

	function initBusinessSearch( root ) {
		if ( ! window.L ) {
			return;
		}

		var mapEl = root.querySelector( '[data-bw-business-search-map]' );
		var listEl = root.querySelector( '[data-bw-business-search-list]' );
		var input = root.querySelector( '[data-bw-business-search-input]' );

		if ( ! mapEl || ! listEl ) {
			return;
		}

		var profiles;
		try {
			profiles = JSON.parse( root.getAttribute( 'data-bw-business-search-profiles' ) || '[]' );
		} catch ( e ) {
			profiles = [];
		}

		if ( ! profiles.length ) {
			return;
		}

		var imagesUrl = root.getAttribute( 'data-bw-leaflet-images' ) || '';
		window.L.Icon.Default.mergeOptions( {
			iconUrl: imagesUrl + 'marker-icon.png',
			iconRetinaUrl: imagesUrl + 'marker-icon-2x.png',
			shadowUrl: imagesUrl + 'marker-shadow.png',
		} );

		var map = window.L.map( mapEl, { scrollWheelZoom: false } );
		window.L.tileLayer( 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
			maxZoom: 19,
		} ).addTo( map );

		var markersById = {};
		var latLngs = [];

		profiles.forEach( function ( profile ) {
			var marker = window.L.marker( [ profile.lat, profile.lng ] ).addTo( map );
			var popupParts = [ '<strong>' + escapeHtml( profile.name ) + '</strong>' ];
			if ( profile.address ) {
				popupParts.push( escapeHtml( profile.address ) );
			}
			if ( profile.website ) {
				popupParts.push( '<a href="' + escapeAttr( profile.website ) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml( profile.website ) + '</a>' );
			}
			marker.bindPopup( popupParts.join( '<br>' ) );

			markersById[ profile.id ] = marker;
			latLngs.push( [ profile.lat, profile.lng ] );
		} );

		if ( latLngs.length > 1 ) {
			map.fitBounds( latLngs, { padding: [ 32, 32 ] } );
		} else {
			map.setView( latLngs[ 0 ], 14 );
		}

		var items = Array.prototype.slice.call( listEl.querySelectorAll( '[data-bw-business-search-item]' ) );

		items.forEach( function ( item ) {
			var trigger = item.querySelector( '[data-bw-business-search-item-trigger]' );
			if ( ! trigger ) {
				return;
			}
			trigger.addEventListener( 'click', function () {
				var id = item.getAttribute( 'data-id' );
				var marker = markersById[ id ];
				if ( marker ) {
					map.setView( marker.getLatLng(), Math.max( map.getZoom(), 14 ) );
					marker.openPopup();
				}
			} );
		} );

		if ( input ) {
			input.addEventListener( 'input', function () {
				var query = input.value.trim().toLowerCase();

				items.forEach( function ( item ) {
					var id = item.getAttribute( 'data-id' );
					var matches = ! query || item.textContent.toLowerCase().indexOf( query ) !== -1;
					item.hidden = ! matches;

					var marker = markersById[ id ];
					if ( ! marker ) {
						return;
					}
					if ( matches && ! map.hasLayer( marker ) ) {
						marker.addTo( map );
					} else if ( ! matches && map.hasLayer( marker ) ) {
						map.removeLayer( marker );
					}
				} );
			} );
		}
	}

	function escapeHtml( value ) {
		var div = document.createElement( 'div' );
		div.textContent = value || '';
		return div.innerHTML;
	}

	function escapeAttr( value ) {
		return ( value || '' ).replace( /"/g, '&quot;' );
	}

	function initAll() {
		document.querySelectorAll( '[data-bw-business-search]' ).forEach( initBusinessSearch );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
} )();
