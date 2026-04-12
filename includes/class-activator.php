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
		update_option( 'tangnest_bebras_version', TANGNEST_BEBRAS_VERSION );

		delete_transient( 'tangnest_bebras_github_release' );
		delete_site_transient( 'update_plugins' );
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
		delete_site_transient( 'update_plugins' );
		flush_rewrite_rules();
	}
}
