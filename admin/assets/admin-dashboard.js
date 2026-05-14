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

	// WhatsApp button — builds URL in JS so encodeURIComponent preserves %0A line breaks.
	function bindWhatsAppButton() {
		var btn = document.getElementById( 'tnq-whatsapp-btn' );
		if ( ! btn ) { return; }

		btn.addEventListener( 'click', function () {
			var d = btn.dataset;
			var stars = function ( score ) {
				return score >= 7 ? '★★★' : score >= 4 ? '★★☆' : '★☆☆';
			};

			var lines = [];
			lines.push( 'Dear ' + d.parent + ',' );
			lines.push( '' );
			lines.push( '*' + d.name + "'s CT Assessment Results*" );
			lines.push( d.school + ', ' + d.location );
			lines.push( '' );
			lines.push( '*Baseline Assessment (' + d.baselineDate + '):*' );
			lines.push( '- Total: ' + d.baselineTotal + '/9 ' + stars( parseInt( d.baselineTotal, 10 ) ) );
			lines.push( '- [A] Algorithmic: ' + d.baselineAlgo + '/3' );
			lines.push( '- [P] Pattern: ' + d.baselinePattern + '/3' );
			lines.push( '- [L] Logical: ' + d.baselineLogical + '/3' );

			if ( d.endlineTotal ) {
				var delta    = parseInt( d.endlineTotal, 10 ) - parseInt( d.baselineTotal, 10 );
				var deltaStr = delta >= 0 ? '+' + delta : '' + delta;
				lines.push( '' );
				lines.push( '*Endline Assessment (' + d.endlineDate + '):*' );
				lines.push( '- Total: ' + d.endlineTotal + '/9 ' + stars( parseInt( d.endlineTotal, 10 ) ) );
				lines.push( '- [A] Algorithmic: ' + d.endlineAlgo + '/3' );
				lines.push( '- [P] Pattern: ' + d.endlinePattern + '/3' );
				lines.push( '- [L] Logical: ' + d.endlineLogical + '/3' );
				lines.push( '' );
				lines.push( '*Growth: ' + deltaStr + ' points*' );
			}

			lines.push( '' );
			lines.push( d.motivation );
			lines.push( '' );
			lines.push( 'For more details, contact your teacher at ' + d.school + '.' );

			var message = lines.join( '\n' );
			var url     = 'https://wa.me/' + d.phone + '?text=' + encodeURIComponent( message );
			window.open( url, '_blank' );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		animateBars();
		bindEmailButton();
		bindWhatsAppButton();
	} );
}());
