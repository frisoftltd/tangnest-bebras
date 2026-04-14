<?php
/**
 * Loads the plugin text domain for translations.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_I18n {

	public function load(): void {
		load_plugin_textdomain(
			'tangnest-bebras',
			false,
			dirname( plugin_basename( TNQ_PLUGIN_FILE ) ) . '/languages'
		);
	}
}
