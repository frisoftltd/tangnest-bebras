<?php
/**
 * CT Question Preview admin page.
 *
 * Capability: manage_options
 * Menu: CT Assessments → Question Preview
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Preview {

	public function init(): void {
		add_action( 'admin_menu',             [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_assets' ] );
	}

	public function register_menu(): void {
		add_submenu_page(
			'tnq-overview',
			__( 'Question Preview', 'tangnest-bebras' ),
			__( 'Question Preview', 'tangnest-bebras' ),
			'manage_options',
			'tnq-preview',
			[ $this, 'render_page' ]
		);
	}

	public function enqueue_assets( string $hook ): void {
		if ( strpos( $hook, 'tnq-preview' ) === false ) {
			return;
		}
		// Quiz CSS + JS for live interaction
		wp_enqueue_style(
			'tnq-quiz',
			TNQ_PLUGIN_URL . 'public/assets/quiz.css',
			[],
			TNQ_VERSION
		);

		// Interaction modules (order matters)
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

		wp_enqueue_script(
			'tnq-quiz',
			TNQ_PLUGIN_URL . 'public/assets/quiz.js',
			array_map( function ( $n ) { return "tnq-interaction-$n"; }, $interactions ),
			TNQ_VERSION,
			true
		);

		// Pass no AJAX data in preview (submissions blocked server-side).
		wp_localize_script( 'tnq-quiz', 'TNQData', [
			'ajaxUrl'  => '',
			'nonce'    => '',
			'courseId' => 0,
			'lessonId' => 0,
		] );
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'tangnest-bebras' ) );
		}
		require TNQ_PLUGIN_DIR . 'admin/views/preview.php';
	}
}
