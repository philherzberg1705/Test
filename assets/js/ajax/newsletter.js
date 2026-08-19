/**
 * Newsletter-AJAX (§15/§19/§20): Absenden ohne Reload, Loading-State,
 * Erfolgs-/Fehlerstatus inline im Formular.
 */
( function () {
	'use strict';

	function setLoading( form, isLoading ) {
		var submit = form.querySelector( '[data-bw-newsletter-submit]' );
		form.classList.toggle( 'is-loading', isLoading );
		if ( submit ) {
			submit.disabled = isLoading;
		}
	}

	function setStatus( form, message, isError ) {
		var status = form.querySelector( '[data-bw-newsletter-status]' );
		if ( ! status ) {
			return;
		}
		status.textContent = message;
		status.classList.toggle( 'is-error', !! isError );
		status.classList.toggle( 'is-success', ! isError );
	}

	function handleSubmit( event ) {
		event.preventDefault();

		var form = event.currentTarget;
		var emailField = form.querySelector( 'input[name="email"]' );
		var consentField = form.querySelector( 'input[name="consent"]' );

		if ( ! emailField.checkValidity() ) {
			setStatus( form, form.dataset.bwErrorEmail || 'Bitte eine gültige E-Mail-Adresse angeben.', true );
			emailField.focus();
			return;
		}

		if ( ! consentField.checked ) {
			setStatus( form, form.dataset.bwErrorConsent || 'Bitte der Einwilligung zustimmen.', true );
			consentField.focus();
			return;
		}

		var data = window.bodywingsData;
		if ( ! data ) {
			return;
		}

		setLoading( form, true );
		setStatus( form, '', false );

		var body = new URLSearchParams();
		body.set( 'action', 'bodywings_newsletter_subscribe' );
		body.set( 'nonce', data.nonce );
		body.set( 'email', emailField.value );
		body.set( 'consent', consentField.checked ? '1' : '' );

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
					setStatus( form, json.data && json.data.message ? json.data.message : 'Danke für die Anmeldung!', false );
					form.reset();
				} else {
					setStatus( form, json && json.data && json.data.message ? json.data.message : 'Anmeldung fehlgeschlagen.', true );
				}
			} )
			.catch( function () {
				setStatus( form, 'Anmeldung fehlgeschlagen. Bitte später erneut versuchen.', true );
			} )
			.finally( function () {
				setLoading( form, false );
			} );
	}

	function initNewsletter() {
		document.querySelectorAll( '[data-bw-newsletter-form]' ).forEach( function ( form ) {
			form.addEventListener( 'submit', handleSubmit );
		} );
	}

	if ( window.bodywings && window.bodywings.register ) {
		window.bodywings.register( 'newsletter', initNewsletter );
	}
} )();
