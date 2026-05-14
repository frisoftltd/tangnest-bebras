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

		// ── Top students ───────────────────────────────────────────────────────
		// XP = best score × 100 (endline preferred over baseline).
		$raw_students = $wpdb->get_results(
			"SELECT student_id,
				MAX(CASE WHEN assessment_type = 'endline'  THEN score_total ELSE NULL END) AS endline_score,
				MAX(CASE WHEN assessment_type = 'baseline' THEN score_total ELSE NULL END) AS baseline_score
			FROM {$wpdb->prefix}tnq_results
			GROUP BY student_id"
		);

		$student_xp = [];
		foreach ( $raw_students as $row ) {
			$score        = $row->endline_score !== null
				? (int) $row->endline_score
				: (int) $row->baseline_score;
			$student_xp[] = [
				'student_id' => (int) $row->student_id,
				'xp'         => $score * 100,
			];
		}
		usort( $student_xp, fn( $a, $b ) => $b['xp'] - $a['xp'] );

		$avatar_pool = [
			'objects/orange.svg',
			'objects/corn.svg',
			'objects/plant-growing.svg',
			'patterns/star.svg',
			'objects/bulb-lit.svg',
		];
		$rank_labels = [ '1st', '2nd', '3rd' ];
		$rank_colors = [ '#F39C12', '#9E9E9E', '#CD7F32' ];

		$top_students = [];
		foreach ( array_slice( $student_xp, 0, 3 ) as $i => $s ) {
			$user = get_userdata( $s['student_id'] );
			if ( ! $user ) {
				continue;
			}
			$first_name = $user->first_name ?: $user->display_name;
			$xp         = $s['xp'];

			if ( $xp >= 800 ) {
				$perf = 'Excellent!';
			} elseif ( $xp >= 600 ) {
				$perf = 'Amazing work!';
			} else {
				$perf = 'Keep it up!';
			}

			$top_students[] = [
				'name'       => $first_name,
				'xp'         => $xp,
				'perf'       => $perf,
				'rank'       => $rank_labels[ $i ],
				'rank_color' => $rank_colors[ $i ],
				'avatar'     => $avatar_pool[ $s['student_id'] % count( $avatar_pool ) ],
			];
		}

		include __DIR__ . '/views/overview.php';
	}
}
