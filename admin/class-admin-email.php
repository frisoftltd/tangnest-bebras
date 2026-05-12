<?php
/**
 * AJAX handler — send CT assessment report to parent email.
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Email {

	public function init(): void {
		add_action( 'wp_ajax_tnq_email_report', [ $this, 'handle' ] );
	}

	public function handle(): void {
		global $wpdb;

		if ( ! check_ajax_referer( 'tnq_email_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'tangnest-bebras' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'tangnest-bebras' ) ] );
		}

		$student_id = isset( $_POST['student_id'] ) ? (int) $_POST['student_id'] : 0;

		if ( ! $student_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid student ID.', 'tangnest-bebras' ) ] );
		}

		$user_data = get_userdata( $student_id );
		if ( ! $user_data ) {
			wp_send_json_error( [ 'message' => __( 'Student not found.', 'tangnest-bebras' ) ] );
		}

		$parent = TNQ_Student_Meta::get( $student_id );
		if ( empty( $parent['parent_email'] ) ) {
			wp_send_json_error( [ 'message' => __( 'No parent email address on record for this student.', 'tangnest-bebras' ) ] );
		}

		// ── Fetch latest results ────────────────────────────────────────────────
		$baseline = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}tnq_results
			 WHERE student_id = %d AND assessment_type = 'baseline'
			 ORDER BY completed_at DESC LIMIT 1",
			$student_id
		) );

		$endline = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}tnq_results
			 WHERE student_id = %d AND assessment_type = 'endline'
			 ORDER BY completed_at DESC LIMIT 1",
			$student_id
		) );

		// ── Build plain-text email body ─────────────────────────────────────────
		$display_name = $user_data->display_name;
		$age_band     = $baseline
			? $baseline->age_band
			: ( $endline ? $endline->age_band : 'N/A' );

		$divider = str_repeat( '-', 44 ) . "\n";

		$body  = "CT Assessment Report — Tangnest STEM Academy\n";
		$body .= $divider . "\n";
		$body .= "Student:  {$display_name}\n";
		$body .= "Age Band: {$age_band}\n\n";

		if ( $baseline ) {
			$body .= "BASELINE ASSESSMENT\n";
			$body .= "  Total:         {$baseline->score_total} / 9\n";
			$body .= "  Algorithmic:   {$baseline->score_algorithmic} / 3\n";
			$body .= "  Pattern:       {$baseline->score_pattern} / 3\n";
			$body .= "  Logical:       {$baseline->score_logical} / 3\n\n";
		}

		if ( $endline ) {
			$body .= "ENDLINE ASSESSMENT\n";
			$body .= "  Total:         {$endline->score_total} / 9\n";
			$body .= "  Algorithmic:   {$endline->score_algorithmic} / 3\n";
			$body .= "  Pattern:       {$endline->score_pattern} / 3\n";
			$body .= "  Logical:       {$endline->score_logical} / 3\n\n";
		}

		if ( $baseline && $endline ) {
			$delta = (int) $endline->score_total - (int) $baseline->score_total;
			$sign  = $delta >= 0 ? '+' : '';
			$body .= "GROWTH: {$sign}{$delta} points\n\n";
		}

		$body .= $divider;
		$body .= "For questions, contact Tangnest STEM Academy.\n";

		// ── Send ────────────────────────────────────────────────────────────────
		$to      = $parent['parent_email'];
		$subject = "{$display_name}'s CT Assessment Report \u{2014} Tangnest STEM Academy";
		$headers = [ 'Content-Type: text/plain; charset=UTF-8', 'From: ' . get_option( 'admin_email' ) ];

		$sent = wp_mail( $to, $subject, $body, $headers );

		if ( $sent ) {
			wp_send_json_success( [
				'message' => sprintf(
					/* translators: %s: parent email address */
					__( 'Report sent to %s', 'tangnest-bebras' ),
					$to
				),
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Could not send email. Please try again.', 'tangnest-bebras' ) ] );
		}
	}
}
