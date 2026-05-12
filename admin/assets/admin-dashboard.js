/**
 * CT Assessments admin dashboard JS.
 * Handles: skill bar animations, email AJAX on Student Detail page.
 */

/* global ajaxurl */

(function () {
	'use strict';

	// Animate .tnq-bar-fill elements from 0 to their data-width value.
	function animateBars() {
		var bars = document.querySelectorAll('.tnq-bar-fill[data-width]');
		if ( ! bars.length ) { return; }
		// Double RAF ensures the initial width:0 is painted before transition fires.
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				bars.forEach( function ( bar ) {
					bar.style.width = bar.getAttribute( 'data-width' );
				} );
			} );
		} );
	}

	// Email AJAX on Student Detail page.
	function bindEmailButton() {
		var btn = document.getElementById( 'tnq-email-btn' );
		if ( ! btn ) { return; }

		var statusEl  = document.getElementById( 'tnq-email-status' );
		var origLabel = btn.textContent;

		btn.addEventListener( 'click', function () {
			btn.disabled    = true;
			btn.textContent = 'Sending…';

			var body = new FormData();
			body.append( 'action',     'tnq_email_report' );
			body.append( 'nonce',      btn.getAttribute( 'data-nonce' ) );
			body.append( 'student_id', btn.getAttribute( 'data-student-id' ) );
			body.append( 'course_id',  btn.getAttribute( 'data-course-id' ) );

			fetch( ajaxurl, {
				method:      'POST',
				body:        body,
				credentials: 'same-origin',
			} )
			.then( function ( r ) { return r.json(); } )
			.then( function ( resp ) {
				statusEl.style.display = 'block';
				if ( resp.success ) {
					statusEl.className   = 'tnq-email-status tnq-email-status-success';
					statusEl.textContent = resp.data.message;
					btn.parentNode.removeChild( btn );
				} else {
					statusEl.className   = 'tnq-email-status tnq-email-status-error';
					statusEl.textContent = ( resp.data && resp.data.debug )
						? 'Error: ' + resp.data.debug
						: ( resp.data && resp.data.message ) || 'Could not send email.';
					btn.disabled         = false;
					btn.textContent      = origLabel;
				}
			} )
			.catch( function () {
				statusEl.style.display = 'block';
				statusEl.className     = 'tnq-email-status tnq-email-status-error';
				statusEl.textContent   = 'Network error. Please try again.';
				btn.disabled           = false;
				btn.textContent        = origLabel;
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		animateBars();
		bindEmailButton();
	} );
}());
