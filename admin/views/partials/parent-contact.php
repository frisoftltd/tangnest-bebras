<?php
/**
 * Parent / guardian contact card partial.
 *
 * Variables from student.php scope:
 *   $parent        array   — parent_name, parent_email, phone_number
 *   $student_id    int
 *   $course_id     int
 *   $display_name  string
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

// WhatsApp URL — strip all non-digit characters from phone number.
$phone_raw   = $parent['phone_number'];
$phone_clean = preg_replace( '/[^0-9]/', '', $phone_raw );

$wa_message = sprintf(
	/* translators: student display name */
	"Hello, here is %s's CT Assessment result from Tangnest STEM Academy. Please log in to view the full report or contact us for details.",
	$display_name
);
$wa_url = 'https://wa.me/' . $phone_clean . '?text=' . rawurlencode( $wa_message );

$email_nonce = wp_create_nonce( 'tnq_email_nonce' );
?>
<div class="tnq-parent-card">
	<h3 class="tnq-parent-title">👨‍👩‍👧 <?php esc_html_e( 'Parent / Guardian', 'tangnest-bebras' ); ?></h3>

	<div class="tnq-parent-info">
		<?php if ( $parent['parent_name'] ) : ?>
		<div class="tnq-parent-name"><?php echo esc_html( $parent['parent_name'] ); ?></div>
		<?php endif; ?>
		<?php if ( $parent['parent_email'] ) : ?>
		<div class="tnq-parent-email">📧 <?php echo esc_html( $parent['parent_email'] ); ?></div>
		<?php endif; ?>
		<?php if ( $phone_raw ) : ?>
		<div class="tnq-parent-phone">📱 <?php echo esc_html( $phone_raw ); ?></div>
		<?php endif; ?>
	</div>

	<div class="tnq-contact-actions">

		<?php if ( $parent['parent_email'] ) : ?>
		<button id="tnq-email-btn"
		        class="tnq-btn-email"
		        type="button"
		        data-student-id="<?php echo esc_attr( $student_id ); ?>"
		        data-course-id="<?php echo esc_attr( $course_id ); ?>"
		        data-nonce="<?php echo esc_attr( $email_nonce ); ?>">
			📧 <?php esc_html_e( 'Email Report', 'tangnest-bebras' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $phone_clean ) : ?>
		<a href="<?php echo esc_url( $wa_url ); ?>"
		   class="tnq-btn-whatsapp"
		   target="_blank"
		   rel="noopener noreferrer">
			💬 <?php esc_html_e( 'WhatsApp', 'tangnest-bebras' ); ?>
		</a>
		<?php endif; ?>

	</div><!-- .tnq-contact-actions -->

	<div id="tnq-email-status" class="tnq-email-status" role="alert"></div>

</div><!-- .tnq-parent-card -->
