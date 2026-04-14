<?php
/**
 * Fired during plugin activation.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Activator {

	public static function activate(): void {
		TNQ_Database::maybe_create_tables();
	}
}
