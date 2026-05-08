<?php
/**
 * Overview page controller.
 *
 * @package Tangnest_Bebras
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Overview {

	/**
	 * Render the Overview admin page.
	 *
	 * @since 2.8.0
	 */
	public function render(): void {
		global $wpdb;

		$courses = TNQ_Tutor_Helper::get_accessible_courses();

		// For each course, count distinct students with baseline and endline results.
		$course_data = [];
		foreach ( $courses as $course ) {
			$cid   = $course['course_id'];
			$total = TNQ_Tutor_Helper::get_enrolled_count( $cid );

			$baseline_count = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(DISTINCT student_id) FROM {$wpdb->prefix}tnq_results
				 WHERE tutor_course_id = %d AND assessment_type = 'baseline'",
				$cid
			) );

			$endline_count = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(DISTINCT student_id) FROM {$wpdb->prefix}tnq_results
				 WHERE tutor_course_id = %d AND assessment_type = 'endline'",
				$cid
			) );

			$course_data[] = [
				'course_id'      => $cid,
				'title'          => $course['title'],
				'total'          => $total,
				'baseline_count' => $baseline_count,
				'endline_count'  => $endline_count,
			];
		}

		include __DIR__ . '/views/overview.php';
	}
}
