<?php
/**
 * CT Assessment shortcodes.
 *
 * [tnq_practice age="7-8"]                → 6 practice items with feedback
 * [tnq_assess type="baseline" age="7-8"]  → 9-item baseline assessment
 * [tnq_assess type="endline"  age="7-8"]  → 9-item endline assessment
 * [tnq_results user_id="current"]         → student results summary
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Shortcodes {

	public function init(): void {
		add_shortcode( 'tnq_practice',      [ $this, 'render_practice' ] );
		add_shortcode( 'tnq_assess',        [ $this, 'render_assess' ] );
		add_shortcode( 'tnq_results',       [ $this, 'render_results' ] );
		add_shortcode( 'tnq_admin_results', [ $this, 'render_admin_results' ] );

		// Proactively enqueue on TutorLMS pages (shortcode detection won't fire there)
		add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_assets' ] );
	}

	public function maybe_enqueue_assets(): void {
		if ( ! $this->should_enqueue() ) return;
		$this->enqueue_quiz_assets();
	}

	private function should_enqueue(): bool {
		// Always load on TutorLMS lesson/course pages
		if ( function_exists( 'tutor' ) ) {
			if ( is_singular( 'lesson' ) || is_singular( 'courses' ) ) {
				return true;
			}
			if ( function_exists( 'tutor_utils' ) && tutor_utils()->is_tutor_page() ) {
				return true;
			}
		}
		// Also load if any tnq shortcode is present (for non-Tutor pages)
		global $post;
		if ( $post && (
			has_shortcode( $post->post_content, 'tnq_practice' ) ||
			has_shortcode( $post->post_content, 'tnq_assess'   ) ||
			has_shortcode( $post->post_content, 'tnq_results'  )
		) ) {
			return true;
		}
		return false;
	}

	/** [tnq_practice age="7-8"] */
	public function render_practice( $atts ): string {
		$atts = shortcode_atts( [ 'age' => '7-8' ], $atts, 'tnq_practice' );
		$age  = $this->sanitize_age( $atts['age'] );

		if ( ! is_user_logged_in() ) {
			return $this->login_prompt();
		}

		$questions = TNQ_Question_Bank::get_questions( 'practice', $age );
		if ( empty( $questions ) ) {
			return '<div class="tnq-quiz"><div class="tnq-message">Practice questions are not yet available. Check back soon.</div></div>';
		}

		return $this->enqueue_and_render( TNQ_Renderer::render_quiz( $questions, 'practice', $age ) );
	}

	/** [tnq_assess type="baseline" age="7-8"] */
	public function render_assess( $atts ): string {
		$atts = shortcode_atts( [ 'type' => 'baseline', 'age' => '7-8' ], $atts, 'tnq_assess' );
		$type = sanitize_text_field( $atts['type'] );
		$age  = $this->sanitize_age( $atts['age'] );

		if ( ! in_array( $type, [ 'baseline', 'endline' ], true ) ) {
			$type = 'baseline';
		}

		if ( ! is_user_logged_in() ) {
			return $this->login_prompt();
		}

		$student_id = get_current_user_id();

		// Check if already completed
		$existing = TNQ_Storage::get_result( $student_id, $type, $age );
		if ( $existing ) {
			// Show their results summary instead of the assessment
			return $this->already_completed_screen( $existing, $type, $age );
		}

		$questions = TNQ_Question_Bank::get_questions( $type, $age );
		if ( empty( $questions ) ) {
			return '<div class="tnq-quiz"><div class="tnq-message">This assessment is not yet available. Please check with your teacher.</div></div>';
		}

		return $this->enqueue_and_render( TNQ_Renderer::render_quiz( $questions, $type, $age ) );
	}

	/** [tnq_results user_id="current"] */
	public function render_results( $atts ): string {
		if ( ! is_user_logged_in() ) {
			return $this->login_prompt();
		}
		$student_id = get_current_user_id();
		return $this->enqueue_style() . TNQ_Renderer::render_results( $student_id );
	}

	/** [tnq_admin_results] — placeholder for M3 */
	public function render_admin_results( $atts ): string {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '';
		}
		return '<div class="tnq-message">[tnq_admin_results] — Full admin results table coming in M3.</div>';
	}

	// ── Helpers ──────────────────────────────────────────────────

	private function sanitize_age( string $age ): string {
		return in_array( $age, [ '7-8', '9-10', '11-12' ], true ) ? $age : '7-8';
	}

	private function login_prompt(): string {
		$login_url = wp_login_url( get_permalink() );
		return sprintf(
			'<div class="tnq-quiz"><div class="tnq-message">Please <a href="%s">log in</a> to access this assessment.</div></div>',
			esc_url( $login_url )
		);
	}

	private function already_completed_screen( object $result, string $type, string $age ): string {
		// Show only the result for THIS mode — never call render_results() here as it
		// outputs ALL modes (baseline + endline) stacked, causing the double-card bug.
		return $this->enqueue_style() . TNQ_Renderer::render_single_result( $result, $type );
	}

	/**
	 * Enqueue assets and return HTML.
	 * Assets must be enqueued before wp_head is called (i.e., during shortcode
	 * execution in the_content). We queue them here; WP will print them in head/footer.
	 */
	private function enqueue_and_render( string $html ): string {
		$this->enqueue_quiz_assets();
		return $html;
	}

	private function enqueue_style(): string {
		wp_enqueue_style( 'tnq-quiz', TNQ_PLUGIN_URL . 'public/assets/quiz.css', [], TNQ_VERSION );
		return '';
	}

	private function enqueue_quiz_assets(): void {
		wp_enqueue_style(
			'tnq-quiz',
			TNQ_PLUGIN_URL . 'public/assets/quiz.css',
			[],
			TNQ_VERSION
		);

		$interactions = [
			'drag-sequence',
			'loop-count',
			'click-color',
			'pattern-next',
			'match-pairs',
			'drag-sort',
		];

		foreach ( $interactions as $name ) {
			wp_enqueue_script(
				"tnq-interaction-$name",
				TNQ_PLUGIN_URL . "public/assets/interactions/$name.js",
				[],
				TNQ_VERSION,
				true
			);
		}

		$deps = array_map( function ( $n ) { return "tnq-interaction-$n"; }, $interactions );

		wp_enqueue_script(
			'tnq-quiz',
			TNQ_PLUGIN_URL . 'public/assets/quiz.js',
			$deps,
			TNQ_VERSION,
			true
		);

		$tutor_course_id = 0;
		$tutor_lesson_id = 0;
		if ( function_exists( 'get_post' ) ) {
			// Attempt to detect Tutor LMS context
			$tutor_course_id = (int) apply_filters( 'tnq_tutor_course_id', 0 );
			$tutor_lesson_id = (int) apply_filters( 'tnq_tutor_lesson_id', 0 );
		}

		wp_localize_script( 'tnq-quiz', 'TNQData', [
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'tnq_assessment_nonce' ),
			'courseId' => $tutor_course_id,
			'lessonId' => $tutor_lesson_id,
		] );
	}
}
