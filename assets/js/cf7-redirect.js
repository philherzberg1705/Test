/**
 * Weiterleitung zur Danke-Seite nach erfolgreichem CF7-Absenden. CF7 löst
 * "wpcf7mailsent" bereits nur bei tatsächlichem Mail-Erfolg aus (nicht bei
 * Validierungsfehlern/Spam) — hier keine eigene Erfolgsprüfung nötig (§31).
 *
 * Ohne konfigurierte Danke-Seite (bodywingsCf7.thankYouUrl === '') bleibt
 * CF7s eigene Erfolgsmeldung im Formular stehen (§25/§37).
 */
( function () {
	'use strict';

	document.addEventListener( 'wpcf7mailsent', function ( event ) {
		var url = window.bodywingsCf7 && window.bodywingsCf7.thankYouUrl;

		if ( url ) {
			window.location.assign( url );
		}
	}, false );
} )();
