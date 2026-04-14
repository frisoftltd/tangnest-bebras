<?php
/**
 * Fired during plugin deactivation.
 * No-op — table and data are preserved on deactivation.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Deactivator {

	public static function deactivate(): void {
		// Intentionally empty. Table and user data survive deactivation.
	}
}
