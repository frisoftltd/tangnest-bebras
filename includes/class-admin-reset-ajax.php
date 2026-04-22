<?php
/**
 * Admin-only AJAX endpoint to delete a student's assessment result.
 *
 * Allows re-testing during development when a 0 score is locked.
 * Only available to users with manage_options capability.
 *
 * Endpoint: wp_ajax_tnq_admin_reset_result
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Reset_Ajax {

    public function init(): void {
        add_action( 'wp_ajax_tnq_admin_reset_result', [ $this, 'handle_reset' ] );
    }

    public function handle_reset(): void {
        if ( ! check_ajax_referer( 'tnq_admin_reset_nonce', 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Permission denied.' ], 403 );
        }

        $student_id      = (int) ( $_POST['student_id']      ?? 0 );
        $assessment_type = sanitize_text_field( $_POST['assessment_type'] ?? '' );
        $age_band        = sanitize_text_field( $_POST['age_band']        ?? '7-8' );

        if ( ! $student_id || ! in_array( $assessment_type, [ 'baseline', 'endline' ], true ) ) {
            wp_send_json_error( [ 'message' => 'Invalid parameters.' ], 400 );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'tnq_results';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $deleted = $wpdb->delete(
            $table,
            [
                'student_id'      => $student_id,
                'assessment_type' => $assessment_type,
                'age_band'        => $age_band,
            ],
            [ '%d', '%s', '%s' ]
        );

        if ( false === $deleted ) {
            wp_send_json_error( [ 'message' => 'Database error.' ], 500 );
        }

        wp_send_json_success( [
            'message'  => "Deleted {$deleted} result row(s) for student {$student_id} ({$assessment_type} / {$age_band}).",
            'deleted'  => (int) $deleted,
        ] );
    }
}
