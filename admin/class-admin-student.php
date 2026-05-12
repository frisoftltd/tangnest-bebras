<?php
/**
 * Student Detail page controller.
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Student {

	public function render(): void {
		global $wpdb;

		$student_id = isset( $_GET['student_id'] ) ? (int) $_GET['student_id'] : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$course_id  = isset( $_GET['course_id'] )  ? (int) $_GET['course_id']  : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $student_id ) {
			echo '<div class="tnq-admin-wrap"><div class="tnq-main-content"><p>'
				. esc_html__( 'No student specified.', 'tangnest-bebras' )
				. '</p></div></div>';
			return;
		}

		$user_data = get_userdata( $student_id );
		if ( ! $user_data ) {
			echo '<div class="tnq-admin-wrap"><div class="tnq-main-content"><p>'
				. esc_html__( 'Student not found.', 'tangnest-bebras' )
				. '</p></div></div>';
			return;
		}

		// ── Identity ───────────────────────────────────────────────────────────
		$display_name = $user_data->display_name;
		$first_name   = $user_data->first_name ?: $user_data->display_name;
		$course_title = $course_id ? get_the_title( $course_id ) : '';

		// ── Avatar ─────────────────────────────────────────────────────────────
		$avatar_palette = [ '#1A56A0', '#F39C12', '#1E8449', '#8E44AD', '#E74C3C', '#16A085' ];
		$avatar_color   = $avatar_palette[ $student_id % 6 ];

		$name_parts = explode( ' ', trim( $display_name ) );
		$initials   = strtoupper( mb_substr( $name_parts[0], 0, 1 ) );
		if ( count( $name_parts ) > 1 ) {
			$initials .= strtoupper( mb_substr( end( $name_parts ), 0, 1 ) );
		}

		// ── Latest results ─────────────────────────────────────────────────────
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

		// ── Derived ────────────────────────────────────────────────────────────
		$age_band = $baseline ? $baseline->age_band : ( $endline ? $endline->age_band : '' );
		$back_url = add_query_arg(
			[ 'page' => 'tnq-results', 'course_id' => $course_id ],
			admin_url( 'admin.php' )
		);
		$parent = TNQ_Student_Meta::get( $student_id );

		include __DIR__ . '/views/student.php';
	}

	/**
	 * Star rating HTML (★ ☆ HTML entities) for a score out of 9.
	 */
	public static function stars( int $score ): string {
		if ( $score >= 7 ) {
			$filled = 3;
		} elseif ( $score >= 4 ) {
			$filled = 2;
		} else {
			$filled = 1;
		}

		$html = '<span class="tnq-stars">';
		for ( $i = 1; $i <= 3; $i++ ) {
			$class = $i <= $filled ? 'tnq-star tnq-star-filled' : 'tnq-star tnq-star-empty';
			$html .= '<span class="' . $class . '">&#9733;</span>';
		}
		$html .= '</span>';
		return $html;
	}

	/**
	 * Insight message for a score out of 9.
	 */
	public static function insight( string $first_name, int $score ): string {
		$name = esc_html( $first_name );
		if ( $score >= 7 ) {
			return "{$name} is doing brilliantly! Keep it up! 🌟";
		}
		if ( $score >= 4 ) {
			return "{$name} is making good progress. Keep practising! 💪";
		}
		return "{$name} is still learning. Extra practice will help! 📚";
	}

	/**
	 * Growth banner message for endline − baseline delta.
	 */
	public static function growth_message( string $first_name, int $delta ): string {
		$name = esc_html( $first_name );
		if ( $delta >= 3 ) {
			return "🎉 {$name} made excellent progress!";
		}
		if ( $delta >= 1 ) {
			return "🎉 Great improvement! {$name} improved by {$delta} points.";
		}
		if ( $delta === 0 ) {
			return "📚 {$name} maintained their score. Keep practising!";
		}
		return "💪 {$name} needs extra support. Review with the teacher.";
	}
}
