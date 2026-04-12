<?php
/**
 * Plugin lifecycle hooks.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation.
 */
class Tangnest_Bebras_Activator {

	/**
	 * Runs on plugin activation.
	 *
	 * @return void
	 */
	public static function activate() {
		$defaults = Tangnest_Bebras_Settings::defaults();
		$current  = get_option( Tangnest_Bebras_Settings::OPTION_NAME, array() );

		if ( ! is_array( $current ) ) {
			$current = array();
		}

		update_option( Tangnest_Bebras_Settings::OPTION_NAME, wp_parse_args( $current, $defaults ) );
		update_option( 'tangnest_bebras_version', TANGNEST_BEBRAS_VERSION );

		delete_transient( 'tangnest_bebras_github_release' );
		flush_rewrite_rules();
	}
}

/**
 * Handles plugin deactivation.
 */
class Tangnest_Bebras_Deactivator {

	/**
	 * Runs on plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		delete_transient( 'tangnest_bebras_github_release' );
		flush_rewrite_rules();
	}
}
