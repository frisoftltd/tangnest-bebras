<?php
/**
 * Question bank registry.
 *
 * Loads question arrays from includes/questions/ and provides
 * filtered access by age band, assessment type, and skill.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Question_Bank {

	/** Loaded question arrays keyed by set name. */
	private static $sets = [];

	/** Load all question sets (idempotent). */
	private static function ensure_loaded(): void {
		if ( ! empty( self::$sets ) ) {
			return;
		}
		$dir = TNQ_PLUGIN_DIR . 'includes/questions/';
		foreach ( [ 'practice', 'baseline-7-8', 'endline-7-8' ] as $name ) {
			$file = $dir . $name . '.php';
			if ( file_exists( $file ) ) {
				self::$sets[ $name ] = require $file;
			} else {
				self::$sets[ $name ] = [];
			}
		}
	}

	/**
	 * Return all questions for a given shortcode combination.
	 *
	 * @param string $assessment_type  'practice' | 'baseline' | 'endline'
	 * @param string $age_band         '7-8' | '9-10' | '11-12'
	 * @return array<int,array>
	 */
	public static function get_questions( string $assessment_type, string $age_band ): array {
		self::ensure_loaded();

		if ( 'practice' === $assessment_type ) {
			return self::$sets['practice'] ?? [];
		}

		$key = $assessment_type . '-' . $age_band;
		return self::$sets[ $key ] ?? [];
	}

	/**
	 * Return a single question by ID (searches all sets).
	 */
	public static function get_by_id( string $id ): ?array {
		self::ensure_loaded();
		foreach ( self::$sets as $questions ) {
			foreach ( $questions as $q ) {
				if ( isset( $q['id'] ) && $q['id'] === $id ) {
					return $q;
				}
			}
		}
		return null;
	}

	/**
	 * Return all questions from all sets (for preview).
	 */
	public static function all(): array {
		self::ensure_loaded();
		return self::$sets;
	}
}
