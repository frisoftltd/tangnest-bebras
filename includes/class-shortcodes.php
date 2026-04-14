<?php
/**
 * Placeholder shortcodes for the CT Assessment system (implemented in M2).
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
	}

	private function placeholder( string $name ): string {
		return sprintf(
			'<div class="tnq-placeholder" style="padding:1rem;border:1px dashed #ccc;border-radius:8px;text-align:center;color:#666;font-family:sans-serif;">[%s — coming soon in Milestone 2]</div>',
			esc_html( $name )
		);
	}

	/** [tnq_practice age="9-10"] */
	public function render_practice( $atts ): string {
		return $this->placeholder( 'tnq_practice' );
	}

	/** [tnq_assess type="baseline" age="7-8"] */
	public function render_assess( $atts ): string {
		return $this->placeholder( 'tnq_assess' );
	}

	/** [tnq_results user_id="current"] */
	public function render_results( $atts ): string {
		return $this->placeholder( 'tnq_results' );
	}

	/** [tnq_admin_results] */
	public function render_admin_results( $atts ): string {
		return $this->placeholder( 'tnq_admin_results' );
	}
}
