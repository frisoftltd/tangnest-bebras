<?php
/**
 * Student parent contact meta reader.
 *
 * Meta keys confirmed in wp_usermeta:
 *   parent_name   → parent / guardian name
 *   parent_email  → parent email address
 *   phone_number  → WhatsApp / phone number
 *
 * @package Tangnest_Bebras
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Student_Meta {

	/**
	 * Get parent contact data for a student.
	 *
	 * @since  2.8.0
	 * @param  int $user_id WordPress user ID of the student.
	 * @return array { parent_name, parent_email, phone_number }
	 */
	public static function get( int $user_id ): array {
		return [
			'parent_name'  => sanitize_text_field( get_user_meta( $user_id, 'parent_name',  true ) ),
			'parent_email' => sanitize_email(      get_user_meta( $user_id, 'parent_email', true ) ),
			'phone_number' => sanitize_text_field( get_user_meta( $user_id, 'phone_number', true ) ),
		];
	}
}
