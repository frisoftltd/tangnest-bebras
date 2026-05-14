<?php
/**
 * Student Detail / Rankings page controller.
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Student {

	public function render(): void {
		$student_id = isset( $_GET['student_id'] ) ? (int) $_GET['student_id'] : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $student_id > 0 ) {
			$this->render_single_student( $student_id );
		} else {
			$this->render_rankings_list();
		}
	}

	// ── Single student report ────────────────────────────────────────────────

	private function render_single_student( int $student_id ): void {
		global $wpdb;

		$course_id = isset( $_GET['course_id'] ) ? (int) $_GET['course_id'] : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$user_data = get_userdata( $student_id );
		if ( ! $user_data ) {
			echo '<div class="tnq-admin-wrap"><div class="tnq-main-content"><p>'
				. esc_html__( 'Student not found.', 'tangnest-bebras' )
				. '</p></div></div>';
			return;
		}

		// ── Identity ─────────────────────────────────────────────────────────
		$display_name = $user_data->display_name;
		$first_name   = $user_data->first_name ?: $user_data->display_name;
		$course_title = $course_id ? get_the_title( $course_id ) : '';

		// ── Avatar ───────────────────────────────────────────────────────────
		$avatar_palette = [ '#1A56A0', '#F39C12', '#1E8449', '#8E44AD', '#E74C3C', '#16A085' ];
		$avatar_color   = $avatar_palette[ $student_id % 6 ];

		$name_parts = explode( ' ', trim( $display_name ) );
		$initials   = strtoupper( mb_substr( $name_parts[0], 0, 1 ) );
		if ( count( $name_parts ) > 1 ) {
			$initials .= strtoupper( mb_substr( end( $name_parts ), 0, 1 ) );
		}

		// ── Latest results ───────────────────────────────────────────────────
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

		// ── Derived ──────────────────────────────────────────────────────────
		$age_band = $baseline ? $baseline->age_band : ( $endline ? $endline->age_band : '' );
		$back_url = add_query_arg(
			[ 'page' => 'tnq-student-detail' ],
			admin_url( 'admin.php' )
		);
		$parent = TNQ_Student_Meta::get( $student_id );

		include __DIR__ . '/views/student.php';
	}

	// ── Full rankings list ───────────────────────────────────────────────────

	private function render_rankings_list(): void {
		global $wpdb;

		$per_page = isset( $_GET['per_page'] ) ? (int) $_GET['per_page'] : 25;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! in_array( $per_page, [ 10, 25, 50, 100 ], true ) ) {
			$per_page = 25;
		}
		$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$raw_students = $wpdb->get_results(
			"SELECT student_id,
				MAX(CASE WHEN assessment_type = 'endline'  THEN score_total ELSE NULL END) AS endline_score,
				MAX(CASE WHEN assessment_type = 'baseline' THEN score_total ELSE NULL END) AS baseline_score,
				MAX(tutor_course_id) AS course_id
			FROM {$wpdb->prefix}tnq_results
			GROUP BY student_id"
		);

		$student_xp = [];
		foreach ( $raw_students as $row ) {
			$endline_score  = $row->endline_score  !== null ? (int) $row->endline_score  : null;
			$baseline_score = $row->baseline_score !== null ? (int) $row->baseline_score : null;
			$best_score     = $endline_score !== null ? $endline_score : (int) $baseline_score;
			$student_xp[]   = [
				'student_id'     => (int) $row->student_id,
				'xp'             => $best_score * 100,
				'endline_score'  => $endline_score,
				'baseline_score' => $baseline_score,
				'course_id'      => (int) $row->course_id,
			];
		}
		usort( $student_xp, fn( $a, $b ) => $b['xp'] - $a['xp'] );

		$total_students = count( $student_xp );
		$total_pages    = max( 1, (int) ceil( $total_students / $per_page ) );
		$paged          = min( $paged, $total_pages );
		$offset         = ( $paged - 1 ) * $per_page;
		$page_students  = array_slice( $student_xp, $offset, $per_page );

		$avatar_pool = [
			'objects/orange.svg',
			'objects/corn.svg',
			'objects/plant-growing.svg',
			'patterns/star.svg',
			'objects/bulb-lit.svg',
		];

		$rankings = [];
		foreach ( $page_students as $rank_index => $s ) {
			$user = get_userdata( $s['student_id'] );
			if ( ! $user ) {
				continue;
			}
			$absolute_rank = $offset + $rank_index + 1;

			if ( $s['endline_score'] !== null && $s['baseline_score'] !== null ) {
				$status = 'Both';
			} elseif ( $s['endline_score'] !== null ) {
				$status = 'Endline only';
			} else {
				$status = 'Baseline only';
			}

			if ( $absolute_rank === 1 ) {
				$rank_class = 'gold';
			} elseif ( $absolute_rank === 2 ) {
				$rank_class = 'silver';
			} elseif ( $absolute_rank === 3 ) {
				$rank_class = 'bronze';
			} else {
				$rank_class = 'normal';
			}

			$rankings[] = [
				'rank'       => $absolute_rank,
				'rank_class' => $rank_class,
				'student_id' => $s['student_id'],
				'name'       => $user->display_name,
				'xp'         => $s['xp'],
				'status'     => $status,
				'avatar'     => $avatar_pool[ $s['student_id'] % count( $avatar_pool ) ],
				'course_id'  => $s['course_id'],
			];
		}

		include __DIR__ . '/views/student-rankings.php';
	}

	// ── Static helpers ───────────────────────────────────────────────────────

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
	 * Normalise a phone number to international format for WhatsApp.
	 * Strips non-digits and prepends Rwanda country code (250) if number
	 * starts with a leading 0.
	 */
	public static function normalise_phone( string $raw ): string {
		$digits = preg_replace( '/\D/', '', $raw );

		// Leading 0 → strip it and prepend Rwanda country code 250.
		if ( strlen( $digits ) > 0 && $digits[0] === '0' ) {
			$digits = '250' . substr( $digits, 1 );
		}

		return $digits;
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
