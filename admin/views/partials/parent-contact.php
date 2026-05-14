<?php
/**
 * Parent / guardian contact card partial.
 *
 * Variables from student.php scope:
 *   $parent        array       — parent_name, parent_email, phone_number
 *   $student_id    int
 *   $course_id     int
 *   $display_name  string
 *   $first_name    string      — student first name
 *   $baseline      object|null — latest baseline result row
 *   $endline       object|null — latest endline result row
 *
 * @package Tangnest_Bebras
 * @since   2.9.12
 */

defined( 'ABSPATH' ) || exit;

$school_name     = get_option( 'tnq_school_name',     'Tangnest STEM Academy' );
$school_location = get_option( 'tnq_school_location', 'Kigali, Rwanda' );
$parent_name     = $parent['parent_name'] ?: __( 'Parent', 'tangnest-bebras' );

// WhatsApp URL — normalise to international format (Rwanda: 250XXXXXXXXX).
$phone_raw   = $parent['phone_number'];
$phone_clean = TNQ_Admin_Student::normalise_phone( $phone_raw );

// ── Pre-compute score values ────────────────────────────────────────────────
$make_stars = function( int $score ): string {
	if ( $score >= 7 ) return '★★★';
	if ( $score >= 4 ) return '★★☆';
	return '★☆☆';
};

$baseline_total   = 0;
$baseline_algo    = 0;
$baseline_pattern = 0;
$baseline_logical = 0;
$baseline_date    = '';
$baseline_stars   = '';
if ( $baseline ) {
	$baseline_total   = (int) $baseline->score_total;
	$baseline_algo    = (int) $baseline->score_algorithmic;
	$baseline_pattern = (int) $baseline->score_pattern;
	$baseline_logical = (int) $baseline->score_logical;
	$baseline_date    = date( 'd M Y', strtotime( $baseline->completed_at ) );
	$baseline_stars   = $make_stars( $baseline_total );
}

$endline_total   = 0;
$endline_algo    = 0;
$endline_pattern = 0;
$endline_logical = 0;
$endline_date    = '';
if ( $endline ) {
	$endline_total   = (int) $endline->score_total;
	$endline_algo    = (int) $endline->score_algorithmic;
	$endline_pattern = (int) $endline->score_pattern;
	$endline_logical = (int) $endline->score_logical;
	$endline_date    = date( 'd M Y', strtotime( $endline->completed_at ) );
}

$ref_score = $baseline ? $baseline_total : $endline_total;
if ( $ref_score >= 8 ) {
	$motivational_message = "Outstanding! {$first_name} is a CT superstar!";
} elseif ( $ref_score >= 6 ) {
	$motivational_message = "Great work! {$first_name} is making excellent progress!";
} elseif ( $ref_score >= 4 ) {
	$motivational_message = "Good effort! {$first_name} is on the right track!";
} else {
	$motivational_message = "Keep going! {$first_name} is building strong thinking skills!";
}

// ── Build WhatsApp message — urlencode each line, join with %0a ─────────────
$lines   = [];
$lines[] = urlencode( "Dear {$parent_name}," );
$lines[] = urlencode( '' );
$lines[] = urlencode( "*{$first_name}'s CT Assessment Results*" );
$lines[] = urlencode( "{$school_name}, {$school_location}" );

if ( $baseline ) {
	$lines[] = urlencode( '' );
	$lines[] = urlencode( "*Baseline Assessment ({$baseline_date}):*" );
	$lines[] = urlencode( "- Total: {$baseline_total}/9 {$baseline_stars}" );
	$lines[] = urlencode( "- [A] Algorithmic: {$baseline_algo}/3" );
	$lines[] = urlencode( "- [P] Pattern: {$baseline_pattern}/3" );
	$lines[] = urlencode( "- [L] Logical: {$baseline_logical}/3" );
}

if ( $endline ) {
	$endline_stars = $make_stars( $endline_total );
	$delta         = $endline_total - $baseline_total;
	$delta_str     = $delta >= 0 ? "+{$delta}" : "{$delta}";

	$lines[] = urlencode( '' );
	$lines[] = urlencode( "*Endline Assessment ({$endline_date}):*" );
	$lines[] = urlencode( "- Total: {$endline_total}/9 {$endline_stars}" );
	$lines[] = urlencode( "- [A] Algorithmic: {$endline_algo}/3" );
	$lines[] = urlencode( "- [P] Pattern: {$endline_pattern}/3" );
	$lines[] = urlencode( "- [L] Logical: {$endline_logical}/3" );
	$lines[] = urlencode( '' );
	$lines[] = urlencode( "*Growth: {$delta_str} points*" );
}

$lines[] = urlencode( '' );
$lines[] = urlencode( $motivational_message );
$lines[] = urlencode( '' );
$lines[] = urlencode( "For more details, contact your teacher at {$school_name}." );

$encoded = implode( '%0a', $lines );
$wa_url  = 'https://wa.me/' . $phone_clean . '?text=' . $encoded;

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
