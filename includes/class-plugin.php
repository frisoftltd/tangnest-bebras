<?php
/**
 * Core plugin bootstrap — wires up all modules.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Plugin {

	/** @var TNQ_Plugin|null */
	private static $instance = null;

	public static function get_instance(): TNQ_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function run(): void {
		require_once TNQ_PLUGIN_DIR . 'includes/class-tutor-helper.php';
		require_once TNQ_PLUGIN_DIR . 'includes/class-student-meta.php';
		( new TNQ_I18n() )->load();
		( new TNQ_Updater() )->init();
		( new TNQ_Legacy_Quiz() )->init();
		( new TNQ_Assessment_Ajax() )->init();
		( new TNQ_Admin_Reset_Ajax() )->init();
		( new TNQ_Shortcodes() )->init();
		if ( is_admin() ) {
			( new TNQ_Admin() )->init();
			( new TNQ_Admin_Menu() )->init();
		} else {
			( new TNQ_Public() )->init();
		}
	}
}
