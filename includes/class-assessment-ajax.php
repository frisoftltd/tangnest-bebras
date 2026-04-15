<?php
/**
 * AJAX handler for assessment submission.
 *
 * Endpoint: wp_ajax_tnq_submit_assessment
 * Flow per design doc §8:
 *   1. Verify nonce
 *   2. Verify logged-in user
 *   3. Verify valid assessment_type (baseline | endline)
 *   4. Check for duplicate completion — reject if already done
 *   5. Score answers
 *   6. Insert result row
 *   7. Return JSON with scores + interpretations
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Assessment_Ajax {

	public function init(): void {
		add_action( 'wp_ajax_tnq_submit_assessment', [ $this, 'handle_submission' ] );
		// Practice never submits; baseline/endline require login.
		// No nopriv handler — unauthenticated users are rejected.
	}

	public function handle_submission(): void {
		// 1. Nonce check
		if ( ! check_ajax_referer( 'tnq_assessment_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => 'Security check failed. Please refresh the page and try again.' ], 403 );
		}

		// 2. Login check
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'You must be logged in to submit an assessment.' ], 401 );
		}

		// 3. Validate assessment_type
		$assessment_type = sanitize_text_field( $_POST['assessment_type'] ?? '' );
		if ( ! in_array( $assessment_type, [ 'baseline', 'endline' ], true ) ) {
			wp_send_json_error( [ 'message' => 'Invalid assessment type.' ], 400 );
		}

		// Validate age_band
		$age_band = sanitize_text_field( $_POST['age_band'] ?? '' );
		if ( ! in_array( $age_band, [ '7-8', '9-10', '11-12' ], true ) ) {
			wp_send_json_error( [ 'message' => 'Invalid age band.' ], 400 );
		}

		$student_id = get_current_user_id();

		// 4. Duplicate check
		$existing = TNQ_Storage::get_result( $student_id, $assessment_type, $age_band );
		if ( $existing ) {
			wp_send_json_error( [ 'message' => 'You have already completed this assessment.' ], 409 );
		}

		// 5. Decode answers
		$answers_raw = stripslashes( $_POST['answers'] ?? '{}' );
		$answers     = json_decode( $answers_raw, true );
		if ( ! is_array( $answers ) ) {
			wp_send_json_error( [ 'message' => 'Invalid answers format.' ], 400 );
		}

		// Sanitize each answer value recursively
		$answers = $this->sanitize_answers( $answers );

		// 6. Load questions and score
		$questions = TNQ_Question_Bank::get_questions( $assessment_type, $age_band );
		if ( empty( $questions ) ) {
			wp_send_json_error( [ 'message' => 'Questions not found for this assessment.' ], 500 );
		}

		$scores = TNQ_Scorer::score_all( $questions, $answers );

		// Decode array answers before storage (they were JSON-encoded by JS)
		foreach ( $answers as $id => $val ) {
			if ( is_string( $val ) ) {
				$decoded = json_decode( $val, true );
				if ( null !== $decoded ) {
					$answers[ $id ] = $decoded;
				}
			}
		}

		// 7. Insert result
		$duration         = (int) ( $_POST['duration_seconds']  ?? 0 );
		$tutor_course_id  = (int) ( $_POST['tutor_course_id']   ?? 0 );
		$tutor_lesson_id  = (int) ( $_POST['tutor_lesson_id']   ?? 0 );

		$insert_data = [
			'student_id'        => $student_id,
			'assessment_type'   => $assessment_type,
			'age_band'          => $age_band,
			'score_total'       => $scores['score_total'],
			'score_algorithmic' => $scores['score_algorithmic'],
			'score_pattern'     => $scores['score_pattern'],
			'score_logical'     => $scores['score_logical'],
			'answers_json'      => wp_json_encode( $answers ),
			'duration_seconds'  => $duration,
			'tutor_course_id'   => $tutor_course_id ?: null,
			'tutor_lesson_id'   => $tutor_lesson_id ?: null,
		];

		$row_id = TNQ_Storage::insert_result( $insert_data );
		if ( false === $row_id ) {
			wp_send_json_error( [ 'message' => 'Failed to save your results. Please try again.' ], 500 );
		}

		// Growth calculation for endline
		$growth = null;
		if ( 'endline' === $assessment_type ) {
			$baseline = TNQ_Storage::get_result( $student_id, 'baseline', $age_band );
			if ( $baseline ) {
				$growth = $scores['score_total'] - (int) $baseline->score_total;
			}
		}

		// Build response
		$response = [
			'score_total'         => $scores['score_total'],
			'score_algorithmic'   => $scores['score_algorithmic'],
			'score_pattern'       => $scores['score_pattern'],
			'score_logical'       => $scores['score_logical'],
			'interpretation'      => TNQ_Scorer::overall_interpretation( $scores['score_total'] ),
			'skill_interpretations' => [
				'algorithmic' => TNQ_Scorer::skill_interpretation( 'algorithmic', $scores['score_algorithmic'] ),
				'pattern'     => TNQ_Scorer::skill_interpretation( 'pattern',     $scores['score_pattern'] ),
				'logical'     => TNQ_Scorer::skill_interpretation( 'logical',     $scores['score_logical'] ),
			],
			'growth'              => $growth,
		];

		wp_send_json_success( $response );
	}

	/**
	 * Sanitize answer values recursively.
	 * Strings → sanitize_text_field, arrays → recurse, numbers → cast.
	 */
	private function sanitize_answers( $value ) {
		if ( is_array( $value ) ) {
			return array_map( [ $this, 'sanitize_answers' ], $value );
		}
		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}
		if ( is_numeric( $value ) ) {
			return is_float( $value + 0 ) ? (float) $value : (int) $value;
		}
		return $value;
	}
}
