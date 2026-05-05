<?php
/**
 * Tutor LMS integration helper.
 *
 * @package Tangnest_Bebras
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Tutor_Helper {

	/**
	 * Get courses accessible to the current WP user.
	 * Admin → all published courses.
	 * Teacher (tutor_instructor role) → only their own courses.
	 * Returns [] if Tutor LMS is not active.
	 *
	 * @since  2.8.0
	 * @return array [ ['course_id' => int, 'title' => string], ... ]
	 */
	public static function get_accessible_courses(): array {
		if ( ! function_exists( 'tutor' ) ) {
			return [];
		}

		$args = [
			'post_type'      => 'courses',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		];

		if ( ! current_user_can( 'manage_options' ) ) {
			$args['author'] = get_current_user_id();
		}

		$ids    = get_posts( $args );
		$result = [];
		foreach ( $ids as $id ) {
			$result[] = [
				'course_id' => (int) $id,
				'title'     => get_the_title( $id ),
			];
		}
		return $result;
	}

	/**
	 * Get enrolled students for a course.
	 * Returns [] if Tutor LMS is not active or course has no students.
	 *
	 * @since  2.8.0
	 * @param  int $course_id
	 * @return array [ ['user_id' => int, 'display_name' => string], ... ]
	 */
	public static function get_enrolled_students( int $course_id ): array {
		if ( ! function_exists( 'tutor' ) ) {
			return [];
		}

		global $wpdb;

		// Use Tutor LMS function if available.
		if ( function_exists( 'tutor_get_students_by_course_id' ) ) {
			$students = tutor_get_students_by_course_id( $course_id );
			if ( empty( $students ) ) {
				return [];
			}
			return array_map( function ( $s ) {
				return [
					'user_id'      => (int) $s->ID,
					'display_name' => $s->display_name,
				];
			}, $students );
		}

		// Fallback: query wp_tutor_enrolled directly.
		$table = $wpdb->prefix . 'tutor_enrolled';
		$rows  = $wpdb->get_results( $wpdb->prepare(
			"SELECT e.user_id, u.display_name
			 FROM {$table} e
			 JOIN {$wpdb->users} u ON u.ID = e.user_id
			 WHERE e.course_id = %d AND e.status = 'approved'",
			$course_id
		) );

		if ( empty( $rows ) ) {
			return [];
		}
		return array_map( function ( $r ) {
			return [
				'user_id'      => (int) $r->user_id,
				'display_name' => $r->display_name,
			];
		}, $rows );
	}
}
