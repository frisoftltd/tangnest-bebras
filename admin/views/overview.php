<?php
/**
 * Overview page template.
 *
 * Expects: $course_data — array of course info from TNQ_Admin_Overview::render().
 *
 * @since 2.8.0
 */
defined( 'ABSPATH' ) || exit;

// Inline star SVG (amber, 64×64) used as mascot.
$star_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64" fill="none" aria-hidden="true"><polygon points="32,6 38,24 58,24 42,36 48,54 32,42 16,54 22,36 6,24 26,24" fill="#F39C12" stroke="#d68910" stroke-width="2" stroke-linejoin="round"/></svg>';
$star_svg_lg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="96" height="96" fill="none" aria-hidden="true"><polygon points="32,6 38,24 58,24 42,36 48,54 32,42 16,54 22,36 6,24 26,24" fill="#F39C12" stroke="#d68910" stroke-width="2" stroke-linejoin="round"/></svg>';
?>
<div class="tnq-admin-wrap">

	<div class="tnq-page-header">
		<div class="tnq-mascot"><?php echo $star_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<div>
			<h1><?php esc_html_e( 'CT Assessments — Overview', 'tangnest-bebras' ); ?></h1>
			<p><?php esc_html_e( 'Stem Academy · Kigali', 'tangnest-bebras' ); ?></p>
		</div>
	</div>

	<?php if ( empty( $course_data ) ) : ?>

		<div class="tnq-empty-state">
			<div class="tnq-mascot-lg"><?php echo $star_svg_lg; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p><?php esc_html_e( 'No classes found yet. Ask your admin to set up courses in Tutor LMS.', 'tangnest-bebras' ); ?></p>
		</div>

	<?php else : ?>

		<div class="tnq-course-grid">
			<?php foreach ( $course_data as $c ) : ?>
				<div class="tnq-course-card">
					<h2><?php echo esc_html( $c['title'] ); ?></h2>
					<hr>
					<p class="tnq-stat">&#128101;&nbsp; <?php echo esc_html( $c['total'] ); ?> <?php esc_html_e( 'students enrolled', 'tangnest-bebras' ); ?></p>
					<p class="tnq-stat">&#128203;&nbsp; <?php echo esc_html( $c['baseline_count'] ); ?> / <?php echo esc_html( $c['total'] ); ?> <?php esc_html_e( 'completed Baseline', 'tangnest-bebras' ); ?></p>
					<p class="tnq-stat">&#9989;&nbsp; <?php echo esc_html( $c['endline_count'] ); ?> / <?php echo esc_html( $c['total'] ); ?> <?php esc_html_e( 'completed Endline', 'tangnest-bebras' ); ?></p>
					<a class="tnq-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=tnq-results&course_id=' . (int) $c['course_id'] ) ); ?>">
						<?php esc_html_e( 'View Class', 'tangnest-bebras' ); ?> &rarr;
					</a>
				</div>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

</div>
