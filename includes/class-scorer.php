<?php
/**
 * Answer validation and scoring.
 *
 * Implements the exact validation rules from design doc §8.3.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Scorer {

	/**
	 * Validate a single submitted answer against the question definition.
	 *
	 * @param array $question   Full question array from the question bank.
	 * @param mixed $submitted  The student's submitted value for this question.
	 * @return bool  true = correct, false = incorrect.
	 */
	public static function validate( array $question, $submitted ): bool {
		$type = $question['type'] ?? '';

		switch ( $type ) {
			case 'drag-sequence':
				return self::validate_drag_sequence( $question, $submitted );

			case 'loop-count':
				return self::validate_loop_count( $question, $submitted );

			case 'click-color':
				return self::validate_click_color( $question, $submitted );

			case 'pattern-next':
				return self::validate_pattern_next( $question, $submitted );

			case 'match-pairs':
				return self::validate_match_pairs( $question, $submitted );

			case 'drag-sort':
				return self::validate_drag_sort( $question, $submitted );

			default:
				return false;
		}
	}

	// ── drag-sequence ────────────────────────────────────────────
	private static function validate_drag_sequence( array $q, $submitted ): bool {
		$correct = $q['answer'] ?? [];
		if ( ! is_array( $submitted ) || count( $submitted ) !== count( $correct ) ) {
			return false;
		}
		return array_values( $submitted ) === array_values( $correct );
	}

	// ── loop-count ───────────────────────────────────────────────
	private static function validate_loop_count( array $q, $submitted ): bool {
		$correct = (int) ( $q['answer'] ?? 0 );
		return (int) $submitted === $correct;
	}

	// ── click-color ──────────────────────────────────────────────
	// Validity = every region painted AND no adjacent pair shares a color.
	private static function validate_click_color( array $q, $submitted ): bool {
		if ( ! is_array( $submitted ) ) {
			return false;
		}
		$regions   = $q['regions']   ?? [];
		$adjacency = $q['adjacency'] ?? [];

		// All regions must be painted (non-empty, not 'transparent', not 'none')
		foreach ( $regions as $region ) {
			$val = $submitted[ $region ] ?? '';
			if ( '' === $val || 'transparent' === $val || 'none' === $val ) {
				return false;
			}
		}

		// No adjacent pair may share a color
		foreach ( $adjacency as $pair ) {
			$a = $pair[0] ?? '';
			$b = $pair[1] ?? '';
			if ( ( $submitted[ $a ] ?? '' ) === ( $submitted[ $b ] ?? '' ) ) {
				return false;
			}
		}

		return true;
	}

	// ── pattern-next ─────────────────────────────────────────────
	private static function validate_pattern_next( array $q, $submitted ): bool {
		$correct = $q['answer'] ?? '';
		return (string) $submitted === (string) $correct;
	}

	// ── match-pairs ──────────────────────────────────────────────
	// submitted = array of [left_id, right_id] pairs.
	private static function validate_match_pairs( array $q, $submitted ): bool {
		$correct_pairs = $q['pairs'] ?? [];
		if ( ! is_array( $submitted ) || count( $submitted ) !== count( $correct_pairs ) ) {
			return false;
		}
		foreach ( $correct_pairs as $pair ) {
			$found = false;
			foreach ( $submitted as $sub ) {
				if ( is_array( $sub ) && isset( $sub[0], $sub[1] ) &&
					$sub[0] === $pair[0] && $sub[1] === $pair[1] ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				return false;
			}
		}
		return true;
	}

	// ── drag-sort ────────────────────────────────────────────────
	// submitted = {item_id: bin_index, ...}
	private static function validate_drag_sort( array $q, $submitted ): bool {
		$items = $q['items'] ?? [];
		if ( ! is_array( $submitted ) ) {
			return false;
		}
		foreach ( $items as $item ) {
			$id  = $item['id']  ?? '';
			$bin = $item['bin'] ?? null;
			if ( null === $bin ) {
				continue;
			}
			if ( ! array_key_exists( $id, $submitted ) ) {
				return false;
			}
			if ( (int) $submitted[ $id ] !== (int) $bin ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Score an entire submission: returns array with total + per-skill scores.
	 *
	 * @param array $questions  Full question definitions (in order).
	 * @param array $answers    Map of question_id => submitted_value.
	 * @return array {
	 *   score_total: int,
	 *   score_algorithmic: int,
	 *   score_pattern: int,
	 *   score_logical: int,
	 *   per_question: array<id, bool>
	 * }
	 */
	public static function score_all( array $questions, array $answers ): array {
		$total      = 0;
		$skill_map  = [ 'algorithmic' => 0, 'pattern' => 0, 'logical' => 0 ];
		$per_question = [];

		foreach ( $questions as $q ) {
			$id        = $q['id'] ?? '';
			$skill     = $q['skill'] ?? 'algorithmic';
			$submitted = $answers[ $id ] ?? null;

			if ( null === $submitted ) {
				$per_question[ $id ] = false;
				continue;
			}

			$correct = self::validate( $q, $submitted );
			$per_question[ $id ] = $correct;

			if ( $correct ) {
				$total++;
				if ( isset( $skill_map[ $skill ] ) ) {
					$skill_map[ $skill ]++;
				}
			}
		}

		return [
			'score_total'         => $total,
			'score_algorithmic'   => $skill_map['algorithmic'],
			'score_pattern'       => $skill_map['pattern'],
			'score_logical'       => $skill_map['logical'],
			'per_question'        => $per_question,
		];
	}

	// ── Interpretation strings ───────────────────────────────────

	/**
	 * Overall interpretation based on percentage band (doc §9.2).
	 */
	public static function overall_interpretation( int $total, int $max = 9 ): string {
		$pct = $max > 0 ? ( $total / $max ) : 0;

		if ( $pct >= 0.78 ) {
			return 'Your child is doing very well. They understand most ideas clearly. A little more practice will make them excellent.';
		} elseif ( $pct >= 0.56 ) {
			return 'Your child is doing well. They understand many ideas and are building strong thinking skills.';
		} elseif ( $pct >= 0.34 ) {
			return 'Your child is making progress. With more practice, their thinking skills will continue to grow.';
		} else {
			return 'Your child is still learning. Regular practice and encouragement will help them improve steadily.';
		}
	}

	/**
	 * Per-skill interpretation (doc §9.3).
	 */
	public static function skill_interpretation( string $skill, int $score ): string {
		$labels = [
			'algorithmic' => 'Algorithmic thinking',
			'pattern'     => 'Pattern recognition',
			'logical'     => 'Logical reasoning',
		];
		$label = $labels[ $skill ] ?? ucfirst( $skill );

		switch ( $score ) {
			case 3: return "$label: Excellent — 3 out of 3 correct.";
			case 2: return "$label: Good — 2 out of 3 correct.";
			case 1: return "$label: Keep practising — 1 out of 3 correct.";
			default: return "$label: Needs more practice — 0 out of 3 correct.";
		}
	}
}
