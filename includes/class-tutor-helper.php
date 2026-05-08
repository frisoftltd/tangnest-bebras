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
	 *
	 * Scans all published posts/lessons for any CT Assessment shortcode and
	 * resolves their parent course via the _tutor_course_id_for_lesson post meta.
	 * This approach works even when the Tutor LMS API returns no results.
	 *
	 * @since  2.8.7
	 * @return array [ ['course_id' => int, 'title' => string], ... ]
	 */
	public static function get_accessible_courses(): array {
		global $wpdb;

		/*
		 * PREVIOUS IMPLEMENTATION (kept for reference):
		 * Relied on function_exists( 'tutor' ) and queried the 'courses' post
		 * type directly, then filtered by shortcode presence in course content.
		 * This returned [] whenever the Tutor LMS API was unavailable.
		 *
		 * if ( ! function_exists( 'tutor' ) ) { return []; }
		 * $args = [ 'post_type' => 'courses', 'post_status' => 'publish',
		 *           'posts_per_page' => -1, 'fields' => 'ids' ];
		 * if ( ! current_user_can( 'manage_options' ) ) {
		 *     $args['author'] = get_current_user_id();
		 * }
		 * $ids = get_posts( $args );
		 * $ids = array_filter( $ids, function ( $id ) {
		 *     $content = get_post_field( 'post_content', $id );
		 *     return strpos( $content, '[tnq_assess' ) !== false
		 *         || strpos( $content, '[tnq_practice' ) !== false;
		 * } );
		 */

		// All known CT Assessment shortcodes.
		$shortcodes = [
			'[tnq_assess',
			'[tnq_practice',
			'[tnq_results',
			'[tangnest_quiz',
		];

		// Build LIKE conditions for each shortcode.
		$conditions = implode( ' OR ', array_map(
			fn( $sc ) => $wpdb->prepare(
				'post_content LIKE %s',
				'%' . $wpdb->esc_like( $sc ) . '%'
			),
			$shortcodes
		) );

		// Find all published posts/lessons containing any CT shortcode.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$lesson_ids = $wpdb->get_col(
			"SELECT ID FROM {$wpdb->posts}
			 WHERE post_status = 'publish'
			 AND ( {$conditions} )"
		);

		if ( empty( $lesson_ids ) ) {
			return [];
		}

		// Resolve parent course for each lesson via _tutor_course_id_for_lesson meta.
		$course_ids = [];
		foreach ( $lesson_ids as $lesson_id ) {
			$course_id = (int) get_post_meta( $lesson_id, '_tutor_course_id_for_lesson', true );
			if ( $course_id && ! in_array( $course_id, $course_ids, true ) ) {
				$course_ids[] = $course_id;
			}
		}

		if ( empty( $course_ids ) ) {
			return [];
		}

		// Fetch the course post objects and return in expected format.
		$posts = get_posts( [
			'post_type'      => 'courses',
			'post__in'       => $course_ids,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		$result = [];
		foreach ( $posts as $post ) {
			$result[] = [
				'course_id' => (int) $post->ID,
				'title'     => $post->post_title,
			];
		}
		return $result;
	}

	/**
	 * Count enrolled (completed) students for a course.
	 *
	 * Queries wp_posts for the tutor_enrolled CPT introduced in Tutor LMS 4.0.
	 * The legacy wp_tutor_enrolled table does not exist in current installations.
	 *
	 * @since  2.8.8
	 * @param  int $course_id
	 * @return int
	 */
	public static function get_enrolled_count( int $course_id ): int {
		global $wpdb;
		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->posts}
			 WHERE post_type = 'tutor_enrolled'
			 AND post_parent = %d
			 AND post_status = 'completed'",
			$course_id
		) );
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

		// Fallback: count distinct students from tnq_results for this course.
		// Used when wp_tutor_enrolled does not exist on this installation.
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT DISTINCT r.student_id, u.display_name
			 FROM {$wpdb->prefix}tnq_results r
			 JOIN {$wpdb->users} u ON u.ID = r.student_id
			 WHERE r.tutor_course_id = %d",
			$course_id
		) );

		if ( empty( $rows ) ) {
			return [];
		}
		return array_map( function ( $r ) {
			return [
				'user_id'      => (int) $r->student_id,
				'display_name' => $r->display_name,
			];
		}, $rows );
	}
}
