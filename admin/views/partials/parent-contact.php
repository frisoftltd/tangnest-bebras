<?php
/**
 * Parent / guardian contact card partial.
 *
 * Variables from student.php scope:
 *   $parent        array       — parent_name, parent_email, phone_number
 *   $student_id    int
 *   $course_id     int
 *   $display_name  string
 *   $baseline      object|null — latest baseline result row
 *   $endline       object|null — latest endline result row
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

// WhatsApp URL — normalise to international format (Rwanda: 250XXXXXXXXX).
$phone_raw   = $parent['phone_number'];
$phone_clean = TNQ_Admin_Student::normalise_phone( $phone_raw );

// Build structured WhatsApp message with actual scores.
$wa_lines   = [];
$wa_lines[] = "Hello, here is {$display_name}'s CT Assessment result from Tangnest STEM Academy.";
$wa_lines[] = '';

if ( $baseline ) {
	$b_date      = wp_date( 'd M Y', strtotime( $baseline->completed_at ) );
	$wa_lines[]  = "📋 Baseline Assessment ({$b_date}):";
	$wa_lines[]  = "  Total:        {$baseline->score_total}/9";
	$wa_lines[]  = "  Algorithmic:  {$baseline->score_algorithmic}/3";
	$wa_lines[]  = "  Pattern:      {$baseline->score_pattern}/3";
	$wa_lines[]  = "  Logical:      {$baseline->score_logical}/3";
	$wa_lines[]  = '';
}

if ( $endline ) {
	$e_date      = wp_date( 'd M Y', strtotime( $endline->completed_at ) );
	$wa_lines[]  = "✅ Endline Assessment ({$e_date}):";
	$wa_lines[]  = "  Total:        {$endline->score_total}/9";
	$wa_lines[]  = "  Algorithmic:  {$endline->score_algorithmic}/3";
	$wa_lines[]  = "  Pattern:      {$endline->score_pattern}/3";
	$wa_lines[]  = "  Logical:      {$endline->score_logical}/3";
	$wa_lines[]  = '';
}

if ( $baseline && $endline ) {
	$wa_delta    = (int) $endline->score_total - (int) $baseline->score_total;
	$wa_sign     = $wa_delta > 0 ? '+' : '';
	$wa_lines[]  = "📈 Growth: {$wa_sign}{$wa_delta} points overall.";
	$wa_lines[]  = '';
}

$wa_lines[] = 'For more details, contact Tangnest STEM Academy.';
$wa_message = implode( "\n", $wa_lines );
$wa_url     = 'https://wa.me/' . $phone_clean . '?text=' . rawurlencode( $wa_message );

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
