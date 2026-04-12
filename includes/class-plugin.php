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
	 * Quiz registry module.
	 *
	 * @var Tangnest_Bebras_Quiz_Registry
	 */
	protected $quiz_registry;

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
	 * Frontend quiz engine.
	 *
	 * @var Tangnest_Bebras_Quiz_Engine
	 */
	protected $quiz_engine;

	/**
	 * Creates plugin services.
	 */
	public function __construct() {
		$this->task_registry = new Tangnest_Bebras_Task_Registry();
		$this->quiz_registry = new Tangnest_Bebras_Quiz_Registry();
		$this->tutor_lms     = new Tangnest_Bebras_Tutor_LMS();
		$this->updater       = new Tangnest_Bebras_Updater();
		$this->admin         = new Tangnest_Bebras_Admin( $this->tutor_lms, $this->task_registry, $this->updater );
		$this->quiz_engine   = new Tangnest_Bebras_Quiz_Engine( $this->quiz_registry, $this->task_registry );
	}

	/**
	 * Registers hooks for all modules.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		$this->task_registry->register_hooks();
		$this->quiz_registry->register_hooks();
		$this->tutor_lms->register_hooks();
		$this->admin->register_hooks();
		$this->updater->register_hooks();
		$this->quiz_engine->register_hooks();
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
