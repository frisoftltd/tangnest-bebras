<?php
/**
 * Uninstall routine for Tangnest Bebras.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'tangnest_bebras_settings' );
delete_option( 'tangnest_bebras_version' );
delete_transient( 'tangnest_bebras_github_release' );
