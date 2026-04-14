<?php
/**
 * Database schema management — creates and migrates plugin tables.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Database {

	const SCHEMA_VERSION = '1.0';

	/**
	 * Create tables only when the stored schema version differs.
	 * Safe to call on every activation.
	 */
	public static function maybe_create_tables(): void {
		if ( get_option( 'tnq_db_version' ) === self::SCHEMA_VERSION ) {
			return;
		}
		self::create_results_table();
		update_option( 'tnq_db_version', self::SCHEMA_VERSION );
	}

	private static function create_results_table(): void {
		global $wpdb;

		$table   = $wpdb->prefix . 'tnq_results';
		$charset = $wpdb->get_charset_collate();

		// Two spaces between type and constraints required by dbDelta.
		$sql = "CREATE TABLE {$table} (
  id BIGINT NOT NULL AUTO_INCREMENT,
  student_id BIGINT NOT NULL,
  assessment_type ENUM('practice','baseline','endline') NOT NULL,
  age_band ENUM('7-8','9-10','11-12') NOT NULL,
  score_total TINYINT NOT NULL,
  score_algorithmic TINYINT NOT NULL,
  score_pattern TINYINT NOT NULL,
  score_logical TINYINT NOT NULL,
  answers_json LONGTEXT,
  duration_seconds SMALLINT,
  completed_at DATETIME NOT NULL,
  tutor_course_id BIGINT,
  tutor_lesson_id BIGINT,
  PRIMARY KEY  (id),
  KEY student_id (student_id),
  KEY completed_at (completed_at)
) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Drop all plugin tables. Called from uninstall.php only.
	 */
	public static function drop_tables(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}tnq_results" );
	}
}
