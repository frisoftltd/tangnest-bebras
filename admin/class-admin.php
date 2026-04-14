<?php
/**
 * Admin-area bootstrap — enqueues admin assets.
 * Full implementation deferred to M2.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin {

	public function init(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets( string $hook ): void {
		// Only load on CT Assessments admin pages.
		if ( strpos( $hook, 'tnq-' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'tnq-admin',
			TNQ_PLUGIN_URL . 'admin/assets/admin.css',
			[],
			TNQ_VERSION
		);

		wp_enqueue_script(
			'tnq-admin',
			TNQ_PLUGIN_URL . 'admin/assets/admin.js',
			[],
			TNQ_VERSION,
			true
		);
	}
}
