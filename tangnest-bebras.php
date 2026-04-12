<?php
/**
 * Plugin Name: Tangnest Bebras
 * Plugin URI:  https://github.com/frisoftltd/tangnest-bebras
 * Description: Foundation plugin for Bebras-style interactive tasks with Tutor LMS integration and GitHub-based updates.
 * Version:     0.1.1
 * Author:      Tangnest
 * Author URI:  https://github.com/frisoftltd/tangnest-bebras
 * Text Domain: tangnest-bebras
 * Domain Path: /languages
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TANGNEST_BEBRAS_VERSION', '0.1.1' );
define( 'TANGNEST_BEBRAS_FILE', __FILE__ );
define( 'TANGNEST_BEBRAS_BASENAME', plugin_basename( __FILE__ ) );
define( 'TANGNEST_BEBRAS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TANGNEST_BEBRAS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Loads plugin class files.
 *
 * @return void
 */
function tangnest_bebras_load_dependencies() {
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-activator.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-settings.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-task-registry.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-tutor-lms.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-admin.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-updater.php';
	require_once TANGNEST_BEBRAS_PATH . 'includes/class-plugin.php';
}

tangnest_bebras_load_dependencies();

register_activation_hook( __FILE__, array( 'Tangnest_Bebras_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Tangnest_Bebras_Deactivator', 'deactivate' ) );

/**
 * Boots the plugin singleton.
 *
 * @return Tangnest_Bebras_Plugin
 */
function tangnest_bebras() {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new Tangnest_Bebras_Plugin();
	}

	return $plugin;
}

tangnest_bebras()->run();
