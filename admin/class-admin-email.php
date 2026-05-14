<?php
/**
 * AJAX handler — send branded HTML CT assessment report to parent email.
 *
 * @package Tangnest_Bebras
 * @since   2.9.9
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

		// ── Template variables ──────────────────────────────────────────────────
		$student_first_name = $user_data->first_name ?: $user_data->display_name;
		$parent_name        = $parent['parent_name'] ?: __( 'Parent', 'tangnest-bebras' );

		$logo_url        = get_option( 'tnq_school_logo_url',
			get_site_url() . '/wp-content/uploads/2025/05/tanngnest-stem-education-in-Rwanda-1-300x120-1.png'
		);
		$school_name     = get_option( 'tnq_school_name',     'Tangnest STEM Academy' );
		$school_location = get_option( 'tnq_school_location', 'Kigali, Rwanda' );

		// Score bar: ■ filled, □ empty.
		$make_bar = function( int $score, int $max = 3 ): string {
			$bar = '';
			for ( $i = 1; $i <= $max; $i++ ) {
				$bar .= $i <= $score ? '■' : '□';
			}
			return $bar;
		};
		$make_stars = function( int $score ): string {
			if ( $score >= 7 ) return '★★★';
			if ( $score >= 4 ) return '★★☆';
			return '★☆☆';
		};

		// Baseline values (always present when email is sent from student page).
		$baseline_total   = $baseline ? (int) $baseline->score_total        : 0;
		$baseline_algo    = $baseline ? (int) $baseline->score_algorithmic   : 0;
		$baseline_pattern = $baseline ? (int) $baseline->score_pattern       : 0;
		$baseline_logical = $baseline ? (int) $baseline->score_logical       : 0;
		$baseline_date    = $baseline ? date( 'd M Y', strtotime( $baseline->completed_at ) ) : '';
		$baseline_stars   = $make_stars( $baseline_total );
		$algo_bar         = $make_bar( $baseline_algo );
		$pattern_bar      = $make_bar( $baseline_pattern );
		$logical_bar      = $make_bar( $baseline_logical );

		if ( $baseline_total >= 8 ) {
			$motivational_message = "🏆 Outstanding! {$student_first_name} is a CT superstar!";
		} elseif ( $baseline_total >= 6 ) {
			$motivational_message = "🌟 Great work! {$student_first_name} is making excellent progress!";
		} elseif ( $baseline_total >= 4 ) {
			$motivational_message = "💪 Good effort! {$student_first_name} is on the right track!";
		} else {
			$motivational_message = "🌱 Keep going! {$student_first_name} is building strong thinking skills!";
		}

		// ── Build HTML body ─────────────────────────────────────────────────────
		$body = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#F8F9FF;font-family:Arial,sans-serif;">

  <!-- Header -->
  <div style="background:#0F1B3D;padding:24px;text-align:center;">
    <img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $school_name ) . '" height="60" style="max-width:200px;">
  </div>

  <!-- Hero -->
  <div style="padding:32px;text-align:center;">
    <h1 style="color:#0F1B3D;font-size:24px;">🌟 ' . esc_html( $student_first_name ) . '\'s CT Assessment Report</h1>
    <p style="color:#555;font-size:16px;">Dear <strong>' . esc_html( $parent_name ) . '</strong>, here are your child\'s latest results from ' . esc_html( $school_name ) . '.</p>
  </div>';

		if ( $baseline ) {
			$body .= '
  <!-- Baseline Score Card -->
  <div style="background:#ffffff;padding:24px;border-radius:12px;margin:0 16px 16px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
    <h2 style="color:#1A56A0;margin-top:0;">📋 Baseline Assessment &mdash; ' . esc_html( $baseline_date ) . '</h2>
    <table width="100%" cellpadding="8" style="border-collapse:collapse;">
      <tr style="background:#F0F4FF;">
        <td style="font-weight:bold;color:#0F1B3D;">Total Score</td>
        <td style="font-size:22px;font-weight:bold;color:#1A56A0;">' . esc_html( $baseline_total ) . '/9 &nbsp;' . esc_html( $baseline_stars ) . '</td>
      </tr>
      <tr>
        <td style="color:#1A56A0;">🔵 Algorithmic Thinking</td>
        <td>' . esc_html( $baseline_algo ) . '/3 &nbsp;' . esc_html( $algo_bar ) . '</td>
      </tr>
      <tr style="background:#FFFBF0;">
        <td style="color:#F39C12;">🟡 Patterns</td>
        <td>' . esc_html( $baseline_pattern ) . '/3 &nbsp;' . esc_html( $pattern_bar ) . '</td>
      </tr>
      <tr>
        <td style="color:#1E8449;">🟢 Logical Reasoning</td>
        <td>' . esc_html( $baseline_logical ) . '/3 &nbsp;' . esc_html( $logical_bar ) . '</td>
      </tr>
    </table>
  </div>';
		}

		if ( $endline ) {
			$endline_total   = (int) $endline->score_total;
			$endline_algo    = (int) $endline->score_algorithmic;
			$endline_pattern = (int) $endline->score_pattern;
			$endline_logical = (int) $endline->score_logical;
			$endline_date    = date( 'd M Y', strtotime( $endline->completed_at ) );
			$endline_stars   = $make_stars( $endline_total );

			$body .= '
  <!-- Endline Score Card -->
  <div style="background:#ffffff;padding:24px;border-radius:12px;margin:0 16px 16px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
    <h2 style="color:#1E8449;margin-top:0;">✅ Endline Assessment &mdash; ' . esc_html( $endline_date ) . '</h2>
    <table width="100%" cellpadding="8" style="border-collapse:collapse;">
      <tr style="background:#F0FFF4;">
        <td style="font-weight:bold;color:#0F1B3D;">Total Score</td>
        <td style="font-size:22px;font-weight:bold;color:#1E8449;">' . esc_html( $endline_total ) . '/9 &nbsp;' . esc_html( $endline_stars ) . '</td>
      </tr>
      <tr>
        <td style="color:#1A56A0;">🔵 Algorithmic Thinking</td>
        <td>' . esc_html( $endline_algo ) . '/3</td>
      </tr>
      <tr style="background:#FFFBF0;">
        <td style="color:#F39C12;">🟡 Patterns</td>
        <td>' . esc_html( $endline_pattern ) . '/3</td>
      </tr>
      <tr>
        <td style="color:#1E8449;">🟢 Logical Reasoning</td>
        <td>' . esc_html( $endline_logical ) . '/3</td>
      </tr>
    </table>
  </div>';

			if ( $baseline ) {
				$delta = $endline_total - $baseline_total;
				$body .= '
  <!-- Growth Panel -->
  <div style="background:#1E8449;color:white;padding:24px;border-radius:12px;margin:0 16px 16px;text-align:center;">
    <h2 style="margin-top:0;">📈 Growth Summary</h2>
    <p style="font-size:18px;">' . esc_html( $student_first_name ) . ' improved by <strong>' . esc_html( $delta ) . ' points</strong> from Baseline to Endline!</p>
  </div>';
			}
		}

		$body .= '
  <!-- Motivational Message -->
  <div style="background:#FFF8E7;padding:24px;border-radius:12px;margin:0 16px 16px;text-align:center;border-left:4px solid #F39C12;">
    <h3 style="color:#F39C12;margin-top:0;">' . esc_html( $motivational_message ) . '</h3>
    <p style="color:#555;">Keep encouraging <strong>' . esc_html( $student_first_name ) . '</strong> &mdash; every session builds stronger thinking skills.</p>
  </div>

  <!-- Footer -->
  <div style="background:#0F1B3D;color:#aaa;padding:24px;text-align:center;margin-top:16px;">
    <img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $school_name ) . '" height="40" style="margin-bottom:12px;opacity:0.8;"><br>
    <p style="margin:4px 0;">' . esc_html( $school_name ) . ' &bull; ' . esc_html( $school_location ) . '</p>
    <p style="margin:4px 0;font-size:12px;">For questions, contact your teacher or reply to this email.</p>
  </div>

</body>
</html>';

		// ── Send ────────────────────────────────────────────────────────────────
		$to      = $parent['parent_email'];
		$subject = "🌟 {$student_first_name}'s CT Assessment Results \u{2014} {$school_name}";
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $school_name . ' <noreply@tangnest.rw>',
			'Reply-To: ' . get_option( 'admin_email' ),
		];

		// Capture PHPMailer error detail for diagnostics.
		$mail_error = '';
		add_action( 'wp_mail_failed', function ( $error ) use ( &$mail_error ) {
			$mail_error = $error->get_error_message();
		} );

		// Force From address to match sending domain (avoids shared-host filtering).
		add_filter( 'wp_mail_from',      [ __CLASS__, 'mail_from' ] );
		add_filter( 'wp_mail_from_name', [ __CLASS__, 'mail_from_name' ] );

		$sent = wp_mail( $to, $subject, $body, $headers );

		remove_filter( 'wp_mail_from',      [ __CLASS__, 'mail_from' ] );
		remove_filter( 'wp_mail_from_name', [ __CLASS__, 'mail_from_name' ] );

		if ( $sent ) {
			wp_send_json_success( [ 'message' => 'Report sent to ' . $to ] );
		} else {
			wp_send_json_error( [
				'message' => 'Could not send email.',
				'debug'   => $mail_error ?: 'wp_mail() returned false — no PHPMailer error captured.',
				'to'      => $to,
				'from'    => 'noreply@tangnest.rw',
			] );
		}
	}

	public static function mail_from( string $email ): string {
		return 'noreply@tangnest.rw';
	}

	public static function mail_from_name( string $name ): string {
		return get_option( 'tnq_school_name', 'Tangnest STEM Academy' );
	}
}
