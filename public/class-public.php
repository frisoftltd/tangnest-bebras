<?php
/**
 * Frontend asset enqueues.
 *
 * Note: quiz assets are enqueued on-demand by TNQ_Shortcodes when a
 * CT Assessment shortcode is used on the page. This class handles any
 * global frontend concerns (none currently in M2).
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Public {

	public function init(): void {
		// Quiz CSS/JS are enqueued by TNQ_Shortcodes when shortcodes render.
		// No global frontend assets required in M2.
	}
}
