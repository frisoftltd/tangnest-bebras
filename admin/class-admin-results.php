<?php
/**
 * All Results page controller.
 *
 * @package Tangnest_Bebras
 * @since   2.9.2
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Results {

	private const PER_PAGE = 25;

	public function render(): void {
		global $wpdb;

		// ── Filters ────────────────────────────────────────────────────────────
		$selected_course_id = isset( $_GET['course_id'] ) ? (int) $_GET['course_id'] : 0;  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$selected_age_band  = isset( $_GET['age_band'] )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? sanitize_text_field( wp_unslash( $_GET['age_band'] ) )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: '';

		$current_page = max( 1, (int) ( $_GET['paged'] ?? 1 ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! in_array( $selected_age_band, [ '7-8', '9-10', '11-12' ], true ) ) {
			$selected_age_band = '';
		}

		// ── Courses for dropdown ───────────────────────────────────────────────
		$courses = TNQ_Tutor_Helper::get_accessible_courses();

		if ( ! $selected_course_id && ! empty( $courses ) ) {
			$selected_course_id = (int) $courses[0]['course_id'];
		}

		// ── Student list ───────────────────────────────────────────────────────
		$all_students       = [];
		$students_page      = [];
		$results_by_student = [];
		$total_students     = 0;
		$total_pages        = 1;

		if ( $selected_course_id ) {
			$all_students   = TNQ_Tutor_Helper::get_enrolled_students( $selected_course_id );
			$total_students = count( $all_students );
			$total_pages    = max( 1, (int) ceil( $total_students / self::PER_PAGE ) );
			$current_page   = min( $current_page, $total_pages );
			$offset         = ( $current_page - 1 ) * self::PER_PAGE;
			$students_page  = array_slice( $all_students, $offset, self::PER_PAGE );
		}

		// ── Results for visible students ───────────────────────────────────────
		if ( ! empty( $students_page ) ) {
			$student_ids = array_map( 'intval', array_column( $students_page, 'user_id' ) );
			$ids_in      = implode( ',', $student_ids );

			$age_filter_sql = '';
			if ( $selected_age_band !== '' ) {
				$age_filter_sql = $wpdb->prepare( ' AND age_band = %s', $selected_age_band );
			}

			$sub_sql = "SELECT student_id, assessment_type, MAX(completed_at) AS latest
			            FROM {$wpdb->prefix}tnq_results
			            WHERE student_id IN ({$ids_in})
			              AND assessment_type IN ('baseline','endline')
			              {$age_filter_sql}
			            GROUP BY student_id, assessment_type";

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$rows = $wpdb->get_results(
				"SELECT r.student_id, r.assessment_type, r.age_band,
				        r.score_total, r.score_algorithmic, r.score_pattern, r.score_logical,
				        r.completed_at
				 FROM {$wpdb->prefix}tnq_results r
				 INNER JOIN ( {$sub_sql} ) sub
				         ON r.student_id      = sub.student_id
				        AND r.assessment_type = sub.assessment_type
				        AND r.completed_at    = sub.latest
				 WHERE r.student_id IN ({$ids_in})
				   AND r.assessment_type IN ('baseline','endline')
				   {$age_filter_sql}"
			);

			foreach ( $rows as $row ) {
				$results_by_student[ (int) $row->student_id ][ $row->assessment_type ] = $row;
			}
		}

		include __DIR__ . '/views/results.php';
	}
}
