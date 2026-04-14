<?php
/**
 * Fires when the plugin is deleted via WordPress admin (Plugins → Delete).
 *
 * Drops the tnq_results table and removes all tnq_* plugin options.
 * Does NOT delete user meta (tnq_pre_score, tnq_post_score, etc.) —
 * student assessment records are preserved even if the plugin is uninstalled.
 *
 * @package Tangnest_Bebras
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-database.php';

// Drop the CT Assessment results table.
TNQ_Database::drop_tables();

// Remove all plugin options. User meta (tnq_pre_* / tnq_post_*) is intentionally preserved.
$option_keys = [
	'tnq_db_version',
];

foreach ( $option_keys as $key ) {
	delete_option( $key );
}

// Remove any options matching the tnq_ prefix that may have been added by the updater.
global $wpdb;
$wpdb->query(
	"DELETE FROM {$wpdb->options} WHERE option_name LIKE 'tnq\_%' OR option_name LIKE '%tangnest-bebras%'"
);
