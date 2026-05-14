<?php
/**
 * Settings page controller.
 *
 * @package Tangnest_Bebras
 * @since   2.9.9
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Settings {

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'tangnest-bebras' ) );
		}

		$saved = false;

		if (
			isset( $_POST['tnq_settings_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tnq_settings_nonce'] ) ), 'tnq_save_settings' )
		) {
			update_option( 'tnq_school_logo_url',  isset( $_POST['tnq_school_logo_url'] )  ? sanitize_url( wp_unslash( $_POST['tnq_school_logo_url'] ) )            : '' );
			update_option( 'tnq_school_name',      isset( $_POST['tnq_school_name'] )      ? sanitize_text_field( wp_unslash( $_POST['tnq_school_name'] ) )          : '' );
			update_option( 'tnq_school_location',  isset( $_POST['tnq_school_location'] )  ? sanitize_text_field( wp_unslash( $_POST['tnq_school_location'] ) )      : '' );
			update_option( 'tnq_active_age_bands', array_map( 'sanitize_text_field', (array) ( isset( $_POST['tnq_active_age_bands'] ) ? wp_unslash( $_POST['tnq_active_age_bands'] ) : [] ) ) );
			update_option( 'tnq_timer_visible',    isset( $_POST['tnq_timer_visible'] ) ? '1' : '0' );
			$saved = true;
		}

		include __DIR__ . '/views/settings.php';
	}
}
