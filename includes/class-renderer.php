<?php
/**
 * Renders question HTML for the quiz engine.
 *
 * Generates the full .tnq-quiz container with all questions
 * and the navigation chrome. PHP renders everything; JS shows
 * one question at a time and handles interaction.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Renderer {

	/**
	 * Render a complete quiz container.
	 *
	 * @param array  $questions       Question definitions.
	 * @param string $mode            'practice' | 'baseline' | 'endline'
	 * @param string $age_band        '7-8' | '9-10' | '11-12'
	 * @param array  $options {
	 *   bool $preview  If true, adds admin answer-reveal panel.
	 * }
	 * @return string  HTML.
	 */
	public static function render_quiz( array $questions, string $mode, string $age_band, array $options = [] ): string {
		if ( empty( $questions ) ) {
			return '<div class="tnq-message">No questions found for this assessment.</div>';
		}

		$preview      = ! empty( $options['preview'] );
		$assess_type  = in_array( $mode, [ 'baseline', 'endline' ], true ) ? $mode : '';
		$total        = count( $questions );
		$is_practice  = 'practice' === $mode;

		ob_start();
		?>
		<div class="tnq-quiz"
			data-mode="<?php echo esc_attr( $mode ); ?>"
			data-age="<?php echo esc_attr( $age_band ); ?>"
			data-assess-type="<?php echo esc_attr( $assess_type ); ?>">

			<!-- Progress bar -->
			<div class="tnq-progress">
				<div class="tnq-progress-bar">
					<div class="tnq-progress-fill" style="width:0%"></div>
				</div>
				<span class="tnq-progress-label">Question 1 of <?php echo esc_html( $total ); ?></span>
			</div>

			<!-- Timer (hidden in practice mode, shown by JS for assessments) -->
			<?php if ( ! $is_practice ) : ?>
			<div class="tnq-timer" style="display:none">
				<?php echo TNQ_Icons::icon( 'timer-clock', [ 'class' => 'tnq-timer-icon' ] ); ?>
				<span class="tnq-timer-display">1:30</span>
				<span class="tnq-timer-expired-msg"></span>
			</div>
			<?php endif; ?>

			<!-- Question cards (all rendered, JS shows one at a time) -->
			<div class="tnq-questions">
				<?php foreach ( $questions as $i => $q ) : ?>
					<?php echo self::render_question( $q, $mode, $i, $preview ); ?>
				<?php endforeach; ?>
			</div>

			<!-- Hint box (practice only) -->
			<?php if ( $is_practice ) : ?>
			<div class="tnq-hint-box">
				<strong>Hint:</strong>
				<span class="tnq-hint-text"></span>
			</div>
			<?php endif; ?>

			<!-- Practice feedback area -->
			<?php if ( $is_practice ) : ?>
			<div class="tnq-feedback" role="alert">
				<span class="tnq-feedback-icon"></span>
				<span class="tnq-feedback-msg"></span>
			</div>
			<div class="tnq-explanation"></div>
			<?php endif; ?>

			<!-- Navigation -->
			<div class="tnq-nav">
				<?php if ( $is_practice ) : ?>
				<button class="tnq-btn tnq-btn-ghost tnq-btn-hint" type="button">
					<?php echo TNQ_Icons::icon( 'hint-bulb' ); ?>
					Hint
				</button>
				<?php endif; ?>

				<button class="tnq-btn tnq-btn-primary tnq-btn-check" type="button">
					<?php echo $is_practice ? 'Check my answer' : 'Next question &rarr;'; ?>
				</button>

				<?php if ( $is_practice ) : ?>
				<button class="tnq-btn tnq-btn-secondary tnq-btn-next" type="button" style="display:none">
					Next question &rarr;
				</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a single question card.
	 */
	public static function render_question( array $q, string $mode, int $index = 0, bool $preview = false ): string {
		$type     = $q['type']       ?? 'unknown';
		$id       = $q['id']         ?? "q-$index";
		$skill    = $q['skill']      ?? '';
		$diff     = $q['difficulty'] ?? '';
		$title    = $q['title']      ?? '';
		$instr    = $q['instruction'] ?? '';
		$hint     = $q['hint']       ?? '';
		$expl     = $q['practice_explanation'] ?? '';

		// Compute correct answer for data-answer attribute (JSON)
		$answer_for_js = self::get_answer_for_js( $q );

		ob_start();
		?>
		<div class="tnq-question"
			data-id="<?php echo esc_attr( $id ); ?>"
			data-type="<?php echo esc_attr( $type ); ?>"
			data-index="<?php echo esc_attr( $index ); ?>"
			data-answer="<?php echo esc_attr( wp_json_encode( $answer_for_js ) ); ?>"
			<?php if ( $hint ) : ?>data-hint="<?php echo esc_attr( $hint ); ?>"<?php endif; ?>
			style="<?php echo $index > 0 ? 'display:none' : ''; ?>">

			<!-- Question header -->
			<div class="tnq-question-header">
				<?php if ( ! empty( $q['title_icon'] ) ) : ?>
				<div class="tnq-question-title-icon">
					<?php echo TNQ_Icons::icon( $q['title_icon'] ); ?>
				</div>
				<?php endif; ?>
				<h2 class="tnq-question-title"><?php echo esc_html( $title ); ?></h2>
			</div>

			<!-- Instruction -->
			<p class="tnq-instruction"><?php echo esc_html( $instr ); ?></p>

			<!-- Interaction widget -->
			<div class="tnq-interaction tnq-<?php echo esc_attr( str_replace( '_', '-', $type ) ); ?>">
				<?php echo self::render_interaction( $q, $mode ); ?>
			</div>

			<!-- Drag fallback hint (shown by JS after 5s) -->
			<?php if ( in_array( $type, [ 'drag-sequence', 'drag-sort' ], true ) ) : ?>
			<div class="tnq-drag-hint">
				Tap a card to pick it up, then tap where you want to place it.
			</div>
			<?php endif; ?>

			<!-- Practice explanation (hidden until Check clicked) -->
			<?php if ( 'practice' === $mode && $expl ) : ?>
			<div class="tnq-explanation"><?php echo esc_html( $expl ); ?></div>
			<?php endif; ?>

			<!-- Admin preview reveal -->
			<?php if ( $preview ) : ?>
			<div class="tnq-preview-meta">
				<dl>
					<dt>ID</dt>        <dd><?php echo esc_html( $id ); ?></dd>
					<dt>Skill</dt>     <dd><?php echo esc_html( $skill ); ?></dd>
					<dt>Type</dt>      <dd><?php echo esc_html( $type ); ?></dd>
					<dt>Difficulty</dt><dd><?php echo esc_html( $diff ); ?></dd>
				</dl>
				<div class="tnq-answer-reveal">
					<strong>Correct answer:</strong>
					<code><?php echo esc_html( wp_json_encode( $answer_for_js ) ); ?></code>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Route to the appropriate interaction renderer.
	 */
	private static function render_interaction( array $q, string $mode ): string {
		$type = $q['type'] ?? '';
		switch ( $type ) {
			case 'drag-sequence': return self::render_drag_sequence( $q );
			case 'loop-count':    return self::render_loop_count( $q );
			case 'click-color':   return self::render_click_color( $q );
			case 'pattern-next':  return self::render_pattern_next( $q );
			case 'match-pairs':   return self::render_match_pairs( $q, $mode );
			case 'drag-sort':     return self::render_drag_sort( $q );
			default:
				return '<p style="color:red">Unknown interaction type: ' . esc_html( $type ) . '</p>';
		}
	}

	// ── drag-sequence ────────────────────────────────────────────
	private static function render_drag_sequence( array $q ): string {
		$items  = $q['items'] ?? [];
		$answer = $q['answer'] ?? [];

		// Shuffle items (reshuffle if shuffled order matches answer)
		$shuffled = $items;
		$tries    = 0;
		do {
			shuffle( $shuffled );
			$shuffled_ids = array_column( $shuffled, 'id' );
			$tries++;
		} while ( $shuffled_ids === $answer && $tries < 10 );

		ob_start();
		?>
		<div class="tnq-drag-sequence">
			<p style="font-size:13px;color:#666;margin-bottom:8px;">Drag the cards into the right order:</p>
			<div class="tnq-sequence-area">
				<?php foreach ( $answer as $pos => $id ) : ?>
				<div class="tnq-sequence-slot">
					<span class="tnq-sequence-number"><?php echo esc_html( $pos + 1 ); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="tnq-source-area">
				<?php foreach ( $shuffled as $item ) : ?>
				<div class="tnq-card" data-item-id="<?php echo esc_attr( $item['id'] ); ?>" draggable="true" tabindex="0">
					<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
					<span class="tnq-card-label"><?php echo esc_html( $item['label'] ); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── loop-count ───────────────────────────────────────────────
	private static function render_loop_count( array $q ): string {
		$min     = (int) ( $q['min']    ?? 1 );
		$max     = (int) ( $q['max']    ?? 30 );
		$initial = (int) ( $q['initial'] ?? $min );
		$tiles   = (int) ( $q['tiles']  ?? 0 );

		ob_start();
		?>
		<div class="tnq-loop-count" data-min="<?php echo esc_attr( $min ); ?>" data-max="<?php echo esc_attr( $max ); ?>" data-initial="<?php echo esc_attr( $initial ); ?>" tabindex="0">
			<div class="tnq-loop-display">
				<?php if ( $tiles > 0 ) : ?>
				<div class="tnq-tiles-grid">
					<?php for ( $i = 0; $i < $tiles; $i++ ) : ?>
					<div class="tnq-tile" style="background:var(--tnq-primary)"></div>
					<?php endfor; ?>
				</div>
				<?php endif; ?>
				<div class="tnq-counter-row">
					<button class="tnq-counter-btn" data-dir="-" type="button" aria-label="Decrease" <?php echo $initial <= $min ? 'disabled' : ''; ?>>&#8722;</button>
					<div class="tnq-counter-value" aria-live="polite"><?php echo esc_html( $initial ); ?></div>
					<button class="tnq-counter-btn" data-dir="+" type="button" aria-label="Increase" <?php echo $initial >= $max ? 'disabled' : ''; ?>>&#43;</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── click-color ──────────────────────────────────────────────
	private static function render_click_color( array $q ): string {
		$svg_key      = $q['svg']         ?? 'leaf';
		$colors       = $q['colors']       ?? [ '#27ae60', '#f1c40f', '#795548' ];
		$color_labels = $q['color_labels'] ?? [ 'Green', 'Yellow', 'Brown' ];
		$adjacency    = $q['adjacency']    ?? [];

		ob_start();
		?>
		<div class="tnq-click-color" data-adjacency="<?php echo esc_attr( wp_json_encode( $adjacency ) ); ?>">
			<div class="tnq-color-workspace">
				<div class="tnq-svg-canvas">
					<?php echo TNQ_Icons::icon( $svg_key, [ 'class' => 'tnq-colorable-svg', 'style' => 'width:180px;height:180px' ] ); ?>
				</div>
				<div class="tnq-color-palette">
					<?php foreach ( $colors as $idx => $hex ) : ?>
					<div class="tnq-color-item">
						<div class="tnq-color-swatch" data-color="<?php echo esc_attr( $hex ); ?>" style="background:<?php echo esc_attr( $hex ); ?>" title="<?php echo esc_attr( $color_labels[ $idx ] ?? '' ); ?>" role="button" tabindex="0" aria-label="Select color: <?php echo esc_attr( $color_labels[ $idx ] ?? $hex ); ?>"></div>
						<div class="tnq-color-swatch-label"><?php echo esc_html( $color_labels[ $idx ] ?? '' ); ?></div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── pattern-next ─────────────────────────────────────────────
	private static function render_pattern_next( array $q ): string {
		$pattern = $q['pattern'] ?? [];
		$choices = $q['choices'] ?? [];

		ob_start();
		?>
		<div class="tnq-pattern-next">
			<div class="tnq-pattern-row">
				<?php foreach ( $pattern as $item ) : ?>
				<div class="tnq-pattern-slot">
					<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
				</div>
				<?php endforeach; ?>
				<div class="tnq-pattern-blank" aria-label="What comes next?">?</div>
			</div>
			<p style="font-size:14px;color:#666;margin:8px 0;">What comes next?</p>
			<div class="tnq-choices">
				<?php foreach ( $choices as $choice ) : ?>
				<div class="tnq-card" data-choice-id="<?php echo esc_attr( $choice['id'] ); ?>" tabindex="0" role="button">
					<?php echo TNQ_Icons::icon( $choice['icon'] ?? '' ); ?>
					<?php if ( ! empty( $choice['label'] ) ) : ?>
					<span class="tnq-card-label"><?php echo esc_html( $choice['label'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── match-pairs ──────────────────────────────────────────────
	private static function render_match_pairs( array $q, string $mode ): string {
		$left  = $q['left']  ?? [];
		$right = $q['right'] ?? [];
		$pairs = $q['pairs'] ?? [];

		// Shuffle right side so it doesn't align with left
		$right_shuffled = $right;
		shuffle( $right_shuffled );

		ob_start();
		?>
		<div class="tnq-match-pairs" data-pairs="<?php echo esc_attr( wp_json_encode( $pairs ) ); ?>" data-mode="<?php echo esc_attr( $mode ); ?>">
			<div class="tnq-pairs-workspace">
				<div class="tnq-pairs-col tnq-pairs-left">
					<?php foreach ( $left as $item ) : ?>
					<div class="tnq-card tnq-pair-item" data-pair-id="<?php echo esc_attr( $item['id'] ); ?>" tabindex="0" role="button">
						<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
						<span class="tnq-card-label"><?php echo esc_html( $item['label'] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
				<div class="tnq-pairs-connectors"></div>
				<div class="tnq-pairs-col tnq-pairs-right">
					<?php foreach ( $right_shuffled as $item ) : ?>
					<div class="tnq-card tnq-pair-item" data-pair-id="<?php echo esc_attr( $item['id'] ); ?>" tabindex="0" role="button">
						<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
						<span class="tnq-card-label"><?php echo esc_html( $item['label'] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── drag-sort ────────────────────────────────────────────────
	private static function render_drag_sort( array $q ): string {
		$items = $q['items'] ?? [];
		$bins  = $q['bins']  ?? [];

		// All items start in source
		$shuffled = $items;
		shuffle( $shuffled );

		ob_start();
		?>
		<div class="tnq-drag-sort">
			<div class="tnq-sort-layout">
				<div class="tnq-sort-source">
					<?php foreach ( $shuffled as $item ) : ?>
					<div class="tnq-card" data-item-id="<?php echo esc_attr( $item['id'] ); ?>" tabindex="0" role="button">
						<?php if ( ! empty( $item['icon'] ) ) : ?>
						<?php echo TNQ_Icons::icon( $item['icon'] ); ?>
						<?php endif; ?>
						<span class="tnq-card-label"><?php echo esc_html( $item['label'] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
				<div class="tnq-sort-bins">
					<?php foreach ( $bins as $binIdx => $binLabel ) : ?>
					<div class="tnq-bin" data-bin-index="<?php echo esc_attr( $binIdx ); ?>">
						<div class="tnq-bin-label"><?php echo esc_html( $binLabel ); ?></div>
						<div class="tnq-bin-items"></div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── Helpers ──────────────────────────────────────────────────

	/**
	 * Return the correct answer in a JS-friendly form.
	 * For click-color: return adjacency array (validation, not specific colors).
	 * For match-pairs: return pairs array.
	 * For drag-sort: return {id: bin_index} map.
	 * Others: return question['answer'] as-is.
	 */
	private static function get_answer_for_js( array $q ) {
		$type = $q['type'] ?? '';

		switch ( $type ) {
			case 'click-color':
				// JS validates by adjacency rule
				return $q['adjacency'] ?? [];

			case 'drag-sort':
				// Build {itemId: binIndex} map
				$map = [];
				foreach ( $q['items'] ?? [] as $item ) {
					$map[ $item['id'] ] = $item['bin'];
				}
				return $map;

			case 'match-pairs':
				return $q['pairs'] ?? [];

			default:
				return $q['answer'] ?? null;
		}
	}

	/**
	 * Render the [tnq_results] shortcode output.
	 */
	public static function render_results( int $student_id ): string {
		$rows = TNQ_Storage::get_all_results( $student_id );

		// Filter to baseline and endline only
		$baseline = null;
		$endline  = null;
		foreach ( $rows as $row ) {
			if ( 'baseline' === $row->assessment_type ) $baseline = $row;
			if ( 'endline'  === $row->assessment_type ) $endline  = $row;
		}

		if ( ! $baseline && ! $endline ) {
			return '<div class="tnq-quiz"><div class="tnq-message">You haven\'t taken any assessments yet. Check with your teacher.</div></div>';
		}

		ob_start();
		?>
		<div class="tnq-quiz">
			<div class="tnq-results-screen" style="text-align:left">
				<h2 style="color:var(--tnq-primary);margin-bottom:20px">Your Assessment Results</h2>

				<?php if ( $baseline ) : ?>
				<h3>Baseline</h3>
				<?php echo self::render_result_row( $baseline ); ?>
				<?php endif; ?>

				<?php if ( $endline ) : ?>
				<h3 style="margin-top:24px">Endline</h3>
				<?php echo self::render_result_row( $endline ); ?>
				<?php endif; ?>

				<?php if ( $baseline && $endline ) :
					$growth = (int) $endline->score_total - (int) $baseline->score_total;
					if ( $growth > 0 ) : ?>
				<div class="tnq-growth-line tnq-growth-positive">You improved by +<?php echo esc_html( $growth ); ?> point<?php echo abs( $growth ) !== 1 ? 's' : ''; ?>!</div>
				<?php elseif ( 0 === $growth ) : ?>
				<div class="tnq-growth-line tnq-growth-zero">You held steady.</div>
				<?php else : ?>
				<div class="tnq-growth-line tnq-growth-negative">This was a tricky one &#8212; don&#8217;t worry, you&#8217;re learning!</div>
				<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function render_result_row( object $row ): string {
		ob_start();
		?>
		<div class="tnq-score-display" style="font-size:36px"><?php echo esc_html( $row->score_total ); ?> / 9</div>
		<div class="tnq-skill-bars">
			<div class="tnq-skill-row">
				<span class="tnq-skill-name">Algorithmic</span>
				<div class="tnq-skill-bar-track">
					<div class="tnq-skill-bar-fill" style="width:<?php echo round( ( $row->score_algorithmic / 3 ) * 100 ); ?>%"></div>
				</div>
				<span class="tnq-skill-fraction"><?php echo esc_html( $row->score_algorithmic ); ?> / 3</span>
			</div>
			<div class="tnq-skill-row">
				<span class="tnq-skill-name">Pattern</span>
				<div class="tnq-skill-bar-track">
					<div class="tnq-skill-bar-fill" style="width:<?php echo round( ( $row->score_pattern / 3 ) * 100 ); ?>%"></div>
				</div>
				<span class="tnq-skill-fraction"><?php echo esc_html( $row->score_pattern ); ?> / 3</span>
			</div>
			<div class="tnq-skill-row">
				<span class="tnq-skill-name">Logical</span>
				<div class="tnq-skill-bar-track">
					<div class="tnq-skill-bar-fill" style="width:<?php echo round( ( $row->score_logical / 3 ) * 100 ); ?>%"></div>
				</div>
				<span class="tnq-skill-fraction"><?php echo esc_html( $row->score_logical ); ?> / 3</span>
			</div>
		</div>
		<div class="tnq-interpretation" style="margin-top:12px">
			<?php echo esc_html( TNQ_Scorer::overall_interpretation( (int) $row->score_total ) ); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
