/**
 * Bewerbungsformular (§15/§20): AJAX-Submit inkl. Lebenslauf-Upload via
 * FormData (multipart) — fetch() setzt den Content-Type/Boundary-Header
 * dafür automatisch, wenn er nicht manuell gesetzt wird.
 */
( function () {
	'use strict';

	function setStatus( statusEl, message, isError ) {
		statusEl.textContent = message;
		statusEl.classList.toggle( 'is-error', !! isError );
		statusEl.classList.toggle( 'is-success', ! isError );
	}

	function handleSubmit( event ) {
		var form = event.target.closest( '[data-bw-job-application-form]' );
		if ( ! form ) {
			return;
		}

		event.preventDefault();

		var data = window.bodywingsData;
		var statusEl = form.querySelector( '[data-bw-job-application-status]' );
		var submitBtn = form.querySelector( '[data-bw-job-application-submit]' );

		if ( ! data || ! statusEl ) {
			return;
		}

		if ( ! form.reportValidity() ) {
			return;
		}

		var body = new FormData( form );
		body.set( 'action', 'bodywings_submit_job_application' );
		body.set( 'nonce', data.nonce );

		if ( submitBtn ) {
			submitBtn.classList.add( 'is-loading' );
			submitBtn.disabled = true;
		}
		setStatus( statusEl, '', false );

		fetch( data.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( json && json.success ) {
					setStatus( statusEl, json.data.message, false );
					form.reset();
					// Nur die Formularfelder ausblenden, nicht das <form>
					// selbst — die Status-Meldung (aria-live) liegt als
					// Kind desselben <form> und muss sichtbar bleiben.
					Array.prototype.forEach.call( form.children, function ( child ) {
						if ( child !== statusEl ) {
							child.hidden = true;
						}
					} );
				} else {
					setStatus( statusEl, ( json && json.data && json.data.message ) || 'Es ist ein Fehler aufgetreten.', true );
				}
			} )
			.catch( function () {
				setStatus( statusEl, 'Netzwerkfehler. Bitte erneut versuchen.', true );
			} )
			.finally( function () {
				if ( submitBtn ) {
					submitBtn.classList.remove( 'is-loading' );
					submitBtn.disabled = false;
				}
			} );
	}

	function initJobApplication() {
		if ( ! document.querySelector( '[data-bw-job-application-form]' ) ) {
			return;
		}
		document.addEventListener( 'submit', handleSubmit );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'job-application', initJobApplication );
	}
} )();
