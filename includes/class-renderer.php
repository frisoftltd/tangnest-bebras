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
				<button class="tnq-btn tnq-btn-back" type="button" style="display:none;background:white;color:#1A56A0;border:2px solid #1A56A0;border-radius:10px;padding:12px 24px;font-size:16px;font-weight:bold;cursor:pointer;min-height:48px;">
					&larr; Back
				</button>
				<?php if ( $is_practice ) : ?>
				<button class="tnq-btn tnq-btn-ghost tnq-btn-hint" type="button">
					<?php echo TNQ_Icons::icon( 'hint-bulb' ); ?>
					Hint
				</button>
				<?php endif; ?>

				<button class="tnq-btn tnq-btn-primary tnq-btn-check" type="button" disabled>
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
				<?php if ( ! empty( $q['title_icon_png'] ) ) : ?>
				<div class="tnq-question-title-icon" style="width:72px;height:72px;flex-shrink:0;display:flex;align-items:center;justify-content:center">
					<img src="<?php echo esc_url( TNQ_ASSETS_URL . $q['title_icon_png'] ); ?>"
						alt="<?php echo esc_attr( $title ); ?>"
						style="width:64px;height:64px;object-fit:contain">
				</div>
				<?php elseif ( ! empty( $q['title_icon'] ) ) : ?>
				<div class="tnq-question-title-icon" style="width:72px;height:72px;flex-shrink:0">
					<?php echo TNQ_Icons::icon( $q['title_icon'], [ 'style' => 'width:72px;height:72px' ] ); ?>
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

		// Determine if items use PNG images
		$use_png = ! empty( $items ) && ! empty( $items[0]['png'] );

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
			<!-- Drop-zone row — styled by .tnq-sequence-area in quiz.css -->
			<div class="tnq-sequence-area">
				<?php foreach ( $answer as $pos => $id ) : ?>
				<div class="tnq-sequence-slot">
					<span class="tnq-slot-number"><?php echo esc_html( $pos + 1 ); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
			<!-- Source card row — styled by .tnq-source-area in quiz.css -->
			<div class="tnq-source-area">
				<?php foreach ( $shuffled as $item ) : ?>
					<?php if ( $use_png ) : ?>
					<div class="tnq-drag-card" data-item-id="<?php echo esc_attr( $item['id'] ); ?>" draggable="true" tabindex="0" role="button" aria-label="<?php echo esc_attr( $item['label'] ?? '' ); ?>">
						<?php
						$img_path  = TNQ_ASSETS_URL . $item['png'];
						$file_path = TNQ_PLUGIN_DIR . 'public/assets/svg/' . $item['png'];
						error_log( 'TNQ img URL: ' . $img_path );
						if ( ! file_exists( $file_path ) ) {
							error_log( 'TNQ MISSING FILE: ' . $file_path );
						}
						?>
						<img src="<?php echo esc_url( $img_path ); ?>"
							 alt="<?php echo esc_attr( $item['label'] ?? '' ); ?>"
							 loading="lazy">
					</div>
					<?php else : ?>
					<div class="tnq-card" data-item-id="<?php echo esc_attr( $item['id'] ); ?>" draggable="true" tabindex="0">
						<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
						<span class="tnq-card-label"><?php echo esc_html( $item['label'] ); ?></span>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── loop-count ───────────────────────────────────────────────
	private static function render_loop_count( array $q ): string {
		$min          = (int) ( $q['min']             ?? 1 );
		$max          = (int) ( $q['max']             ?? 30 );
		$initial      = (int) ( $q['initial']         ?? $min );
		$tile_icon    = $q['tile_icon']               ?? '';
		$tile_icon_png = $q['tile_icon_png']          ?? '';
		$group_size   = (int) ( $q['tile_group_size'] ?? 0 );
		$path_svg     = $q['path_svg']                ?? '';

		// When a path illustration is used, dynamic tiles are suppressed —
		// children count from the fixed SVG visual above the counter.
		$tile_icon_url = '';
		if ( $tile_icon_png && ! $path_svg ) {
			$tile_icon_url = TNQ_PLUGIN_URL . 'public/assets/svg/' . $tile_icon_png;
		}

		// Static footprint decoration shown inside the counter row (path_svg mode)
		$footprint_img_url = '';
		if ( $path_svg && $tile_icon_png ) {
			$footprint_img_url = TNQ_PLUGIN_URL . 'public/assets/svg/' . $tile_icon_png;
		}

		// Static tile count (legacy / non-PNG mode)
		$tiles = ! $tile_icon_png ? (int) ( $q['tiles'] ?? 0 ) : 0;

		ob_start();

		// ── Path illustration (house → footprints → tap) ─────────
		if ( $path_svg === 'house-to-tap' ) :
			$_house_url    = esc_url( TNQ_ASSETS_URL . 'places/q3-house.png' );
			$_tap_url      = esc_url( TNQ_ASSETS_URL . 'places/q3-tap.png' );
			$_footstep_url = esc_url( TNQ_ASSETS_URL . 'ui/q3-footstep.png' );
		?>
		<svg viewBox="0 0 500 160" xmlns="http://www.w3.org/2000/svg"
			style="width:100%;height:160px;display:block;margin-bottom:12px"
			aria-label="Mugisha walks from his house to the water tap — count the footprints"
			role="img">

			<!-- House (80×80, left) -->
			<image href="<?php echo $_house_url; ?>" x="0" y="40" width="80" height="80"/>

			<!-- Dashed path line house→tap -->
			<line x1="80" y1="80" x2="420" y2="80"
				stroke="#b0c4de" stroke-width="3" stroke-dasharray="6 4"/>

			<!-- 5 footsteps (50×50) evenly spaced between x=80 and x=420 -->
			<!-- gap = (340 − 5×50) / 6 = 15; positions: 95, 160, 225, 290, 355 -->
			<image href="<?php echo $_footstep_url; ?>" x="95"  y="55" width="50" height="50"/>
			<image href="<?php echo $_footstep_url; ?>" x="160" y="55" width="50" height="50"/>
			<image href="<?php echo $_footstep_url; ?>" x="225" y="55" width="50" height="50"/>
			<image href="<?php echo $_footstep_url; ?>" x="290" y="55" width="50" height="50"/>
			<image href="<?php echo $_footstep_url; ?>" x="355" y="55" width="50" height="50"/>

			<!-- Tap (80×80, right) -->
			<image href="<?php echo $_tap_url; ?>" x="420" y="40" width="80" height="80"/>
		</svg>
		<?php endif; ?>

		<div class="tnq-loop-count"
			data-min="<?php echo esc_attr( $min ); ?>"
			data-max="<?php echo esc_attr( $max ); ?>"
			data-initial="<?php echo esc_attr( $initial ); ?>"
			<?php if ( $tile_icon_url ) : ?>data-tile-icon-url="<?php echo esc_url( $tile_icon_url ); ?>"<?php endif; ?>
			tabindex="0">
			<div class="tnq-loop-display">
				<?php if ( $tile_icon_url ) : ?>
				<!-- Dynamic footprint row — JS fills this on every counter change -->
				<div class="tnq-dynamic-tiles" aria-live="polite"></div>
				<?php elseif ( $tiles > 0 ) : ?>
				<div class="tnq-tiles-grid<?php echo $tile_icon ? ' tnq-tiles-grid--icons' : ''; ?>"><?php
					if ( $group_size > 0 && $tile_icon ) :
						$groups = (int) ceil( $tiles / $group_size );
						for ( $g = 0; $g < $groups; $g++ ) :
							$count = min( $group_size, $tiles - $g * $group_size );
							?><div class="tnq-tile-group"><?php
							for ( $i = 0; $i < $count; $i++ ) :
								?><div class="tnq-tile tnq-tile--icon"><?php echo TNQ_Icons::icon( $tile_icon ); ?></div><?php
							endfor;
							?></div><?php
						endfor;
					else :
						for ( $i = 0; $i < $tiles; $i++ ) :
							if ( $tile_icon ) :
								?><div class="tnq-tile tnq-tile--icon"><?php echo TNQ_Icons::icon( $tile_icon ); ?></div><?php
							else :
								?><div class="tnq-tile" style="background:var(--tnq-primary)"></div><?php
							endif;
						endfor;
					endif;
				?></div>
				<?php endif; ?>
				<div class="tnq-counter-row">
					<button class="tnq-counter-btn" data-dir="-" type="button" aria-label="Decrease"
						style="width:64px;height:64px;font-size:28px"
						<?php echo $initial <= $min ? 'disabled' : ''; ?>>&#8722;</button>
					<?php if ( $footprint_img_url ) : ?>
					<img src="<?php echo esc_url( $footprint_img_url ); ?>"
						alt="footprint"
						style="width:60px;height:60px;object-fit:contain;flex-shrink:0">
					<?php endif; ?>
					<div class="tnq-counter-value" aria-live="polite"><?php echo esc_html( $initial ); ?></div>
					<button class="tnq-counter-btn" data-dir="+" type="button" aria-label="Increase"
						style="width:64px;height:64px;font-size:28px"
						<?php echo $initial >= $max ? 'disabled' : ''; ?>>&#43;</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ── click-color ──────────────────────────────────────────────
	private static function render_click_color( array $q ): string {
		$svg_key      = $q['svg']          ?? 'leaf';
		$colors       = $q['colors']        ?? [ '#27ae60', '#f1c40f', '#795548' ];
		$color_labels = $q['color_labels']  ?? [ 'Green', 'Yellow', 'Brown' ];
		$adjacency    = $q['adjacency']     ?? [];
		$palette_pngs = $q['palette_pngs']  ?? [];

		ob_start();
		?>
		<div class="tnq-click-color" data-adjacency="<?php echo esc_attr( wp_json_encode( $adjacency ) ); ?>">
			<div class="tnq-color-workspace">
				<div class="tnq-svg-canvas" style="width:100%;max-width:400px;margin:0 auto">
					<?php
					$svg  = '<svg class="tnq-icon tnq-icon--flag-colorable tnq-colorable-svg"';
					$svg .= ' viewBox="0 0 120 80"';
					$svg .= ' style="width:100%;max-width:400px;height:auto;display:block;margin:0 auto;cursor:pointer;position:relative;z-index:10;"';
					$svg .= ' xmlns="http://www.w3.org/2000/svg">';
					$svg .= '<polygon data-region="top"    points="0,0 120,0 60,40"    fill="#d4d4d4" stroke="#bbb" stroke-width="0.75" pointer-events="all" style="cursor:pointer;"/>';
					$svg .= '<polygon data-region="right"  points="120,0 120,80 60,40" fill="#b8b8b8" stroke="#bbb" stroke-width="0.75" pointer-events="all" style="cursor:pointer;"/>';
					$svg .= '<polygon data-region="bottom" points="120,80 0,80 60,40"  fill="#c8c8c8" stroke="#bbb" stroke-width="0.75" pointer-events="all" style="cursor:pointer;"/>';
					$svg .= '<polygon data-region="left"   points="0,80 0,0 60,40"     fill="#e0e0e0" stroke="#bbb" stroke-width="0.75" pointer-events="all" style="cursor:pointer;"/>';
					$svg .= '</svg>';
					echo $svg;
					?>
				</div>
				<div class="tnq-color-palette">
					<?php foreach ( $palette_pngs as $pal ) : ?>
					<button class="tnq-color-btn" type="button"
						data-color="<?php echo esc_attr( $pal['value'] ); ?>"
						aria-label="Select colour: <?php echo esc_attr( $pal['label'] ); ?>">
						<img src="<?php echo esc_url( TNQ_ASSETS_URL . $pal['png'] ); ?>"
							 alt="<?php echo esc_attr( $pal['label'] ); ?>"
							 style="width:80px;height:80px;object-fit:cover">
					</button>
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
				<div class="tnq-pattern-slot" style="width:80px;height:80px">
					<?php echo TNQ_Icons::icon( $item['icon'] ?? '', [ 'style' => 'width:80px;height:80px' ] ); ?>
				</div>
				<?php endforeach; ?>
				<div class="tnq-pattern-blank" aria-label="What comes next?" style="width:80px;height:80px;font-size:28px">?</div>
			</div>
			<p style="font-size:15px;font-weight:600;color:#555;margin:12px 0 8px">What comes next?</p>
			<div class="tnq-choices" style="display:flex;gap:14px;flex-wrap:wrap;justify-content:center">
				<?php foreach ( $choices as $choice ) : ?>
					<?php if ( ! empty( $choice['png'] ) ) : ?>
					<div class="tnq-choice-card"
						data-choice-id="<?php echo esc_attr( $choice['id'] ); ?>"
						data-png="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . $choice['png'] ); ?>"
						data-active-png="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . ( $choice['active_png'] ?? $choice['png'] ) ); ?>"
						tabindex="0" role="button"
						aria-label="<?php echo esc_attr( $choice['label'] ?? '' ); ?>">
						<img src="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . $choice['png'] ); ?>"
							 alt="<?php echo esc_attr( $choice['label'] ?? '' ); ?>"
							 style="width:180px;height:180px;object-fit:contain">
					</div>
					<?php else : ?>
					<div class="tnq-card" data-choice-id="<?php echo esc_attr( $choice['id'] ); ?>" tabindex="0" role="button">
						<?php echo TNQ_Icons::icon( $choice['icon'] ?? '' ); ?>
					</div>
					<?php endif; ?>
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

		// Row colours for left-item dots (practice mode line drawing)
		$dot_colors = [ '#9b59b6', '#f39c12', '#27ae60' ];

		// Shuffle right side
		$right_shuffled = $right;
		shuffle( $right_shuffled );

		// Detect if items use PNG images
		$use_png = ! empty( $left ) && ! empty( $left[0]['png'] );

		ob_start();
		?>
		<div class="tnq-match-pairs" data-pairs="<?php echo esc_attr( wp_json_encode( $pairs ) ); ?>" data-mode="<?php echo esc_attr( $mode ); ?>">
			<div class="tnq-pairs-workspace" style="position:relative;display:flex;gap:16px;align-items:flex-start">
				<div class="tnq-pairs-col tnq-pairs-left" style="flex:1;display:flex;flex-direction:column;gap:12px">
					<?php foreach ( $left as $row_idx => $item ) :
						$dot_color = $dot_colors[ $row_idx % count( $dot_colors ) ];
					?>
					<?php if ( $use_png ) : ?>
					<div class="tnq-pair-card tnq-pairs-left-item"
						data-pair-id="<?php echo esc_attr( $item['id'] ); ?>"
						data-dot-color="<?php echo esc_attr( $dot_color ); ?>"
						tabindex="0" role="button"
						style="min-height:100px;min-width:0;border-radius:12px;border:2px solid var(--tnq-border);background:#fff;display:flex;align-items:center;justify-content:center;gap:8px;padding:8px;cursor:pointer;position:relative;user-select:none">
						<img src="<?php echo esc_url( TNQ_ASSETS_URL . $item['png'] ); ?>"
							 alt="<?php echo esc_attr( $item['label'] ?? '' ); ?>"
							 style="width:100px;height:100px;object-fit:contain;flex-shrink:0">
						<span class="tnq-pair-dot tnq-pair-dot-right"
							style="width:18px;height:18px;border-radius:50%;background:<?php echo esc_attr( $dot_color ); ?>;border:2px solid rgba(0,0,0,0.15);flex-shrink:0;display:block"
							aria-hidden="true"></span>
					</div>
					<?php else : ?>
					<div class="tnq-card tnq-pair-item tnq-pairs-left-item"
						data-pair-id="<?php echo esc_attr( $item['id'] ); ?>"
						data-dot-color="<?php echo esc_attr( $dot_color ); ?>"
						tabindex="0" role="button">
						<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
					</div>
					<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="tnq-pairs-connectors" style="width:40px;flex-shrink:0;position:relative;align-self:stretch"></div>
				<div class="tnq-pairs-col tnq-pairs-right" style="flex:1;display:flex;flex-direction:column;gap:12px">
					<?php foreach ( $right_shuffled as $item ) : ?>
					<?php if ( $use_png ) : ?>
					<div class="tnq-pair-card tnq-pairs-right-item"
						data-pair-id="<?php echo esc_attr( $item['id'] ); ?>"
						tabindex="0" role="button"
						style="min-height:100px;min-width:0;border-radius:12px;border:2px solid var(--tnq-border);background:#fff;display:flex;align-items:center;justify-content:center;gap:8px;padding:8px;cursor:pointer;position:relative;user-select:none">
						<span class="tnq-pair-dot tnq-pair-dot-left"
							style="width:18px;height:18px;border-radius:50%;background:#ccc;border:2px solid rgba(0,0,0,0.15);flex-shrink:0;display:block"
							aria-hidden="true"></span>
						<img src="<?php echo esc_url( TNQ_ASSETS_URL . $item['png'] ); ?>"
							 alt="<?php echo esc_attr( $item['label'] ?? '' ); ?>"
							 style="width:100px;height:100px;object-fit:contain;flex-shrink:0">
					</div>
					<?php else : ?>
					<div class="tnq-card tnq-pair-item tnq-pairs-right-item"
						data-pair-id="<?php echo esc_attr( $item['id'] ); ?>"
						tabindex="0" role="button">
						<?php echo TNQ_Icons::icon( $item['icon'] ?? '' ); ?>
					</div>
					<?php endif; ?>
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
