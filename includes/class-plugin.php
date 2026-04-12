<?php
/**
 * Main plugin bootstrap.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates the plugin modules.
 */
class Tangnest_Bebras_Plugin {

	/**
	 * Settings module.
	 *
	 * @var Tangnest_Bebras_Settings
	 */
	protected $settings;

	/**
	 * Task registry module.
	 *
	 * @var Tangnest_Bebras_Task_Registry
	 */
	protected $task_registry;

	/**
	 * Tutor LMS integration module.
	 *
	 * @var Tangnest_Bebras_Tutor_LMS
	 */
	protected $tutor_lms;

	/**
	 * Admin UI module.
	 *
	 * @var Tangnest_Bebras_Admin
	 */
	protected $admin;

	/**
	 * GitHub updater module.
	 *
	 * @var Tangnest_Bebras_Updater
	 */
	protected $updater;

	/**
	 * Creates plugin services.
	 */
	public function __construct() {
		$this->settings      = new Tangnest_Bebras_Settings();
		$this->task_registry = new Tangnest_Bebras_Task_Registry();
		$this->tutor_lms     = new Tangnest_Bebras_Tutor_LMS();
		$this->admin         = new Tangnest_Bebras_Admin( $this->settings, $this->tutor_lms, $this->task_registry );
		$this->updater       = new Tangnest_Bebras_Updater( $this->settings );
	}

	/**
	 * Registers hooks for all modules.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		$this->settings->register_hooks();
		$this->task_registry->register_hooks();
		$this->tutor_lms->register_hooks();
		$this->admin->register_hooks();
		$this->updater->register_hooks();
	}

	/**
	 * Loads plugin translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'tangnest-bebras',
			false,
			dirname( TANGNEST_BEBRAS_BASENAME ) . '/languages'
		);
	}
}
