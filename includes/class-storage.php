<?php
/**
 * Database read/write for assessment results.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Storage {

	/**
	 * Fetch a student's result row for a given assessment + age band.
	 *
	 * @param int    $student_id
	 * @param string $assessment_type 'baseline' | 'endline'
	 * @param string $age_band        '7-8' | '9-10' | '11-12'
	 * @return object|null  Database row or null if not found.
	 */
	public static function get_result( int $student_id, string $assessment_type, string $age_band ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'tnq_results';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM `{$table}` WHERE student_id = %d AND assessment_type = %s AND age_band = %s LIMIT 1",
				$student_id,
				$assessment_type,
				$age_band
			)
		);
	}

	/**
	 * Fetch all result rows for a student.
	 *
	 * @param int $student_id
	 * @return object[]
	 */
	public static function get_all_results( int $student_id ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'tnq_results';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM `{$table}` WHERE student_id = %d ORDER BY completed_at ASC",
				$student_id
			)
		);
	}

	/**
	 * Insert a new result row.
	 *
	 * @param array $data  Must include: student_id, assessment_type, age_band,
	 *                     score_total, score_algorithmic, score_pattern,
	 *                     score_logical, answers_json, duration_seconds,
	 *                     tutor_course_id, tutor_lesson_id.
	 * @return int|false  Inserted row ID or false on failure.
	 */
	public static function insert_result( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'tnq_results';

		$row = [
			'student_id'        => (int) ( $data['student_id']        ?? 0 ),
			'assessment_type'   => $data['assessment_type']            ?? '',
			'age_band'          => $data['age_band']                   ?? '',
			'score_total'       => (int) ( $data['score_total']        ?? 0 ),
			'score_algorithmic' => (int) ( $data['score_algorithmic']  ?? 0 ),
			'score_pattern'     => (int) ( $data['score_pattern']      ?? 0 ),
			'score_logical'     => (int) ( $data['score_logical']      ?? 0 ),
			'answers_json'      => $data['answers_json']               ?? '',
			'duration_seconds'  => (int) ( $data['duration_seconds']   ?? 0 ),
			'completed_at'      => current_time( 'mysql', true ),
			'tutor_course_id'   => $data['tutor_course_id']            ? (int) $data['tutor_course_id'] : null,
			'tutor_lesson_id'   => $data['tutor_lesson_id']            ? (int) $data['tutor_lesson_id'] : null,
		];

		$formats = [ '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%d', '%d' ];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$inserted = $wpdb->insert( $table, $row, $formats );

		return $inserted ? $wpdb->insert_id : false;
	}
}
