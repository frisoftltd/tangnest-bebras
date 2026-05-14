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

		// Current user for the welcome message.
		$current_user = wp_get_current_user();

		// ── Skill stats ────────────────────────────────────────────────────────
		// Each row has 3 questions per skill.  Mastery = correct / (attempts × 3).
		$skill_row = $wpdb->get_row(
			"SELECT
				COUNT(*) as attempts,
				COALESCE(SUM(score_algorithmic), 0) as algo_correct,
				COALESCE(SUM(score_pattern), 0)     as pattern_correct,
				COALESCE(SUM(score_logical), 0)     as logical_correct
			FROM {$wpdb->prefix}tnq_results"
		);

		$attempts        = (int) $skill_row->attempts;
		$total_questions = $attempts > 0 ? $attempts * 3 : 0;

		$algo_correct    = (int) $skill_row->algo_correct;
		$pattern_correct = (int) $skill_row->pattern_correct;
		$logical_correct = (int) $skill_row->logical_correct;

		$algo_pct    = $total_questions > 0 ? (int) round( $algo_correct / $total_questions * 100 ) : 0;
		$pattern_pct = $total_questions > 0 ? (int) round( $pattern_correct / $total_questions * 100 ) : 0;
		$logical_pct = $total_questions > 0 ? (int) round( $logical_correct / $total_questions * 100 ) : 0;

		$skills = [
			[
				'name'    => 'Algorithmic Thinking',
				'color'   => '#1A56A0',
				'icon'    => 'objects/bulb-lit.svg',
				'total'   => $total_questions,
				'correct' => $algo_correct,
				'pct'     => $algo_pct,
				'label'   => $algo_pct >= 80 ? 'Excellent' : ( $algo_pct >= 60 ? 'Good' : 'Fair' ),
			],
			[
				'name'    => 'Patterns',
				'color'   => '#F39C12',
				'icon'    => 'patterns/star.svg',
				'total'   => $total_questions,
				'correct' => $pattern_correct,
				'pct'     => $pattern_pct,
				'label'   => $pattern_pct >= 80 ? 'Excellent' : ( $pattern_pct >= 60 ? 'Good' : 'Fair' ),
			],
			[
				'name'    => 'Logical Reasoning',
				'color'   => '#1E8449',
				'icon'    => 'objects/sun.svg',
				'total'   => $total_questions,
				'correct' => $logical_correct,
				'pct'     => $logical_pct,
				'label'   => $logical_pct >= 80 ? 'Excellent' : ( $logical_pct >= 60 ? 'Good' : 'Fair' ),
			],
		];

		// ── Course data ────────────────────────────────────────────────────────
		$courses     = TNQ_Tutor_Helper::get_accessible_courses();
		$course_data = [];
		foreach ( $courses as $course ) {
			$cid     = $course['course_id'];
			$total   = TNQ_Tutor_Helper::get_enrolled_count( $cid );
			$excerpt = wp_trim_words( get_the_excerpt( $cid ), 12, '...' );

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
				'excerpt'        => $excerpt,
				'total'          => $total,
				'baseline_count' => $baseline_count,
				'endline_count'  => $endline_count,
				'baseline_pct'   => $total > 0 ? (int) round( $baseline_count / $total * 100 ) : 0,
				'endline_pct'    => $total > 0 ? (int) round( $endline_count / $total * 100 ) : 0,
			];
		}

		// ── Overall completion ─────────────────────────────────────────────────
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$both_count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM (
				SELECT student_id
				FROM {$wpdb->prefix}tnq_results
				WHERE assessment_type IN ('baseline','endline')
				GROUP BY student_id
				HAVING COUNT(DISTINCT assessment_type) = 2
			) AS fully_done"
		);

		$total_enrolled = array_sum( array_column( $course_data, 'total' ) );
		$overall_pct    = $total_enrolled > 0 ? (int) round( $both_count / $total_enrolled * 100 ) : 0;

		if ( $overall_pct >= 80 ) {
			$motivation = 'Excellent progress! Your class is thriving!';
		} elseif ( $overall_pct >= 50 ) {
			$motivation = 'Great job! Keep encouraging your students.';
		} else {
			$motivation = "You're just getting started! Complete more activities.";
		}

		// ── Full rankings with pagination ──────────────────────────────────────
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$per_page = isset( $_GET['per_page'] ) ? max( 1, min( 100, (int) $_GET['per_page'] ) ) : 25;
		$page     = isset( $_GET['paged'] )    ? max( 1, (int) $_GET['paged'] )                : 1;
		// phpcs:enable
		$offset = ( $page - 1 ) * $per_page;

		$avatar_pool = [
			'objects/orange.svg',
			'objects/corn.svg',
			'objects/plant-growing.svg',
			'patterns/star.svg',
			'objects/bulb-lit.svg',
		];

		// Collect all enrolled student IDs across accessible courses.
		$all_student_ids = [];
		foreach ( $courses as $course ) {
			$enrolled_posts = get_posts( [
				'post_type'      => 'tutor_enrolled',
				'post_parent'    => (int) $course['course_id'],
				'post_status'    => 'completed',
				'posts_per_page' => -1,
			] );
			foreach ( $enrolled_posts as $ep ) {
				$all_student_ids[ (int) $ep->post_author ] = true;
			}
		}

		$students = [];
		foreach ( array_keys( $all_student_ids ) as $uid ) {
			$user = get_userdata( $uid );
			if ( ! $user ) continue;

			$endline = $wpdb->get_var( $wpdb->prepare(
				"SELECT score_total FROM {$wpdb->prefix}tnq_results
				 WHERE student_id = %d AND assessment_type = 'endline'
				 ORDER BY completed_at DESC LIMIT 1",
				$uid
			) );
			$baseline = $wpdb->get_var( $wpdb->prepare(
				"SELECT score_total FROM {$wpdb->prefix}tnq_results
				 WHERE student_id = %d AND assessment_type = 'baseline'
				 ORDER BY completed_at DESC LIMIT 1",
				$uid
			) );

			$score     = ! is_null( $endline ) ? (int) $endline : ( ! is_null( $baseline ) ? (int) $baseline : 0 );
			$has_taken = ! is_null( $endline ) || ! is_null( $baseline );

			$students[] = [
				'user_id'   => $uid,
				'name'      => $user->first_name ?: $user->display_name,
				'xp'        => $score * 100,
				'has_taken' => $has_taken,
				'avatar'    => $avatar_pool[ $uid % count( $avatar_pool ) ],
			];
		}

		usort( $students, fn( $a, $b ) => $b['xp'] - $a['xp'] );

		$total       = count( $students );
		$paginated   = array_slice( $students, $offset, $per_page );
		$total_pages = $total > 0 ? (int) ceil( $total / $per_page ) : 1;

		include __DIR__ . '/views/overview.php';
	}
}
