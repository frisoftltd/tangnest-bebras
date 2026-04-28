<?php
/**
 * Plugin Name:  Tangnest Bebras Interactive Quiz
 * Plugin URI:   https://lms.tangnest.rw
 * Description:  Computational Thinking assessment plugin for Tangnest STEM Academy. Bebras-style visual interactive tasks for ages 7–12, integrated with Tutor LMS. Includes legacy Bebras pre/post quizzes and the new CT Assessment system.
 * Version:      2.6.5
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author:       Tangnest Ltd
 * Author URI:   https://lms.tangnest.rw
 * License:      GPL v2 or later
 * Text Domain:  tangnest-bebras
 * Domain Path:  /languages
 * GitHub Plugin URI: frisoftltd/tangnest-bebras
 */

defined( 'ABSPATH' ) || exit;

// ── Constants ────────────────────────────────────────────────────────────────
define( 'TNQ_VERSION',     '2.6.5' );
define( 'TNQ_PLUGIN_FILE',  __FILE__ );
define( 'TNQ_PLUGIN_DIR',   plugin_dir_path( __FILE__ ) );
define( 'TNQ_PLUGIN_URL',   plugin_dir_url( __FILE__ ) );
define( 'TNQ_ASSETS_URL',   plugin_dir_url( __FILE__ ) . 'public/assets/svg/' );
define( 'TNQ_MIN_WP',      '6.0' );
define( 'TNQ_MIN_PHP',     '7.4' );

// ── Module requires ──────────────────────────────────────────────────────────
require_once TNQ_PLUGIN_DIR . 'includes/class-database.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-activator.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-i18n.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-updater.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-icons.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-question-bank.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-scorer.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-storage.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-renderer.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-assessment-ajax.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-admin-reset-ajax.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once TNQ_PLUGIN_DIR . 'legacy/class-legacy-quiz.php';
require_once TNQ_PLUGIN_DIR . 'admin/class-admin.php';
require_once TNQ_PLUGIN_DIR . 'admin/class-admin-menu.php';
require_once TNQ_PLUGIN_DIR . 'admin/class-preview.php';
require_once TNQ_PLUGIN_DIR . 'public/class-public.php';
require_once TNQ_PLUGIN_DIR . 'includes/class-plugin.php';

// ── Lifecycle hooks ──────────────────────────────────────────────────────────
register_activation_hook(   __FILE__, [ 'TNQ_Activator',   'activate'   ] );
register_deactivation_hook( __FILE__, [ 'TNQ_Deactivator', 'deactivate' ] );

// ── Boot ─────────────────────────────────────────────────────────────────────
TNQ_Plugin::get_instance()->run();
