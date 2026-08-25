/**
 * Easy Accordion Block — admin dashboard behaviour.
 *
 * Handles the FAQ / preview accordions, the onboarding "Set up" buttons and
 * the copy-system-report button.
 */
( function () {
	'use strict';

	var settings = window.esabAdmin || {};

	/**
	 * Accordions. Any [data-esab-accordion] container whose children each hold
	 * a button[aria-expanded] toggles a single open item at a time.
	 */
	function initAccordions() {
		var groups = document.querySelectorAll( '[data-esab-accordion]' );

		Array.prototype.forEach.call( groups, function ( group ) {
			var buttons = group.querySelectorAll( 'button[aria-expanded]' );

			Array.prototype.forEach.call( buttons, function ( button ) {
				button.addEventListener( 'click', function () {
					var item = button.parentElement;
					var isOpen = 'true' === button.getAttribute( 'aria-expanded' );

					Array.prototype.forEach.call( buttons, function ( other ) {
						other.setAttribute( 'aria-expanded', 'false' );
						other.parentElement.classList.remove( 'is-open' );
					} );

					if ( ! isOpen ) {
						button.setAttribute( 'aria-expanded', 'true' );
						item.classList.add( 'is-open' );
					}
				} );
			} );
		} );
	}

	/**
	 * Mark an onboarding step complete when its "Set up" button is used.
	 * Fired as a beacon so navigation is never delayed.
	 */
	function initSetupSteps() {
		if ( ! settings.ajaxUrl || ! settings.nonce ) {
			return;
		}

		var actions = document.querySelectorAll( '[data-esab-step]' );

		Array.prototype.forEach.call( actions, function ( action ) {
			action.addEventListener( 'click', function () {
				var body = new URLSearchParams();
				body.append( 'action', 'esab_complete_step' );
				body.append( 'nonce', settings.nonce );
				body.append( 'step', action.getAttribute( 'data-esab-step' ) );

				// PHP only populates $_POST for form-encoded bodies, so the
				// beacon has to carry that content type explicitly.
				if ( navigator.sendBeacon ) {
					navigator.sendBeacon(
						settings.ajaxUrl,
						new Blob( [ body.toString() ], {
							type: 'application/x-www-form-urlencoded',
						} )
					);
					return;
				}

				fetch( settings.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: body.toString(),
				} ).catch( function () {} );
			} );
		} );
	}

	/**
	 * Copy-to-clipboard buttons.
	 */
	function initCopyButtons() {
		var buttons = document.querySelectorAll( '[data-esab-copy]' );

		Array.prototype.forEach.call( buttons, function ( button ) {
			var label = button.querySelector( 'span' );

			button.addEventListener( 'click', function () {
				var text = button.getAttribute( 'data-esab-copy' );
				var original = label ? label.textContent : '';

				var done = function ( message ) {
					if ( ! label ) {
						return;
					}
					label.textContent = message;
					window.setTimeout( function () {
						label.textContent = original;
					}, 2000 );
				};

				// Fallback for non-secure contexts (plain http admin) and for
				// clipboard writes the browser refuses, e.g. an unfocused document.
				var legacyCopy = function () {
					var field = document.createElement( 'textarea' );
					field.value = text;
					field.setAttribute( 'readonly', 'readonly' );
					field.style.position = 'fixed';
					field.style.opacity = '0';
					document.body.appendChild( field );
					field.select();

					var ok = false;
					try {
						ok = document.execCommand( 'copy' );
					} catch ( error ) {
						ok = false;
					}

					document.body.removeChild( field );

					done(
						ok
							? ( settings.i18n && settings.i18n.copied ) || 'Copied'
							: ( settings.i18n && settings.i18n.copyFailed ) || 'Copy failed'
					);
				};

				if ( navigator.clipboard && window.isSecureContext ) {
					navigator.clipboard.writeText( text ).then( function () {
						done( ( settings.i18n && settings.i18n.copied ) || 'Copied' );
					}, legacyCopy );
					return;
				}

				legacyCopy();
			} );
		} );
	}

	function init() {
		initAccordions();
		initSetupSteps();
		initCopyButtons();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
