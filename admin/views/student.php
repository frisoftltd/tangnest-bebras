<?php
/**
 * Student Detail page template.
 *
 * Variables provided by TNQ_Admin_Student::render():
 *   $student_id    int
 *   $course_id     int
 *   $display_name  string
 *   $first_name    string
 *   $course_title  string
 *   $avatar_color  string  — hex colour
 *   $initials      string  — 1–2 chars
 *   $baseline      object|null
 *   $endline       object|null
 *   $age_band      string
 *   $back_url      string
 *   $parent        array   — parent_name, parent_email, phone_number
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

// Helper: skill bar row HTML.
$bar_row = function ( string $label, int $score, string $css_modifier ) {
	$pct = round( $score / 3 * 100 );
	?>
	<div class="tnq-skill-bar-row">
		<span class="tnq-skill-bar-label"><?php echo esc_html( $label ); ?></span>
		<div class="tnq-bar-track">
			<div class="tnq-bar-fill tnq-bar-fill-<?php echo esc_attr( $css_modifier ); ?>"
			     data-width="<?php echo esc_attr( $pct . '%' ); ?>"></div>
		</div>
		<span class="tnq-bar-fraction"><?php echo esc_html( $score ); ?>/3</span>
	</div>
	<?php
};
?>
<div class="tnq-admin-wrap tnq-student-page">
<div class="tnq-main-content">

	<!-- ── Back link ────────────────────────────────────────────────────── -->
	<a href="<?php echo esc_url( $back_url ); ?>" class="tnq-student-back">
		&#8592; <?php esc_html_e( 'Back to Class', 'tangnest-bebras' ); ?>
	</a>

	<!-- ── Student header ───────────────────────────────────────────────── -->
	<div class="tnq-student-header">
		<div class="tnq-avatar" style="background-color:<?php echo esc_attr( $avatar_color ); ?>">
			<?php echo esc_html( $initials ); ?>
		</div>
		<div class="tnq-student-identity">
			<h2 class="tnq-student-display-name"><?php echo esc_html( $display_name ); ?></h2>
			<?php if ( $course_title ) : ?>
			<p class="tnq-student-course">
				<?php esc_html_e( 'Course:', 'tangnest-bebras' ); ?>
				<?php echo esc_html( $course_title ); ?>
			</p>
			<?php endif; ?>
			<?php if ( $age_band ) : ?>
			<span class="tnq-age-badge">
				<?php esc_html_e( 'Age', 'tangnest-bebras' ); ?> <?php echo esc_html( $age_band ); ?>
			</span>
			<?php endif; ?>
		</div>
	</div>

	<!-- ── Report panels ────────────────────────────────────────────────── -->
	<div class="tnq-report-panels">

	<?php
	// Render a single assessment panel (baseline or endline).
	$render_panel = function ( object $result, string $type ) use ( $first_name, $bar_row ) {
		$is_baseline   = ( $type === 'baseline' );
		$icon          = $is_baseline ? '📋' : '✅';
		$label         = $is_baseline
			? __( 'Baseline Assessment', 'tangnest-bebras' )
			: __( 'Endline Assessment',  'tangnest-bebras' );
		$score_total   = (int) $result->score_total;
		$score_algo    = (int) $result->score_algorithmic;
		$score_pattern = (int) $result->score_pattern;
		$score_logical = (int) $result->score_logical;
		$date          = wp_date( 'd M Y', strtotime( $result->completed_at ) );
		?>
		<div class="tnq-report-card">
			<div class="tnq-report-card-header">
				<h3 class="tnq-report-card-title">
					<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — literal emoji ?>
					<?php echo esc_html( $label ); ?>
				</h3>
				<span class="tnq-report-date"><?php echo esc_html( $date ); ?></span>
			</div>
			<div class="tnq-report-card-body">

				<!-- Score hero -->
				<div class="tnq-score-hero">
					<div class="tnq-score-big"><?php echo esc_html( $score_total ); ?> / 9</div>
					<?php echo TNQ_Admin_Student::stars( $score_total ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — HTML from trusted method ?>
				</div>

				<!-- Skill bars -->
				<div class="tnq-skill-bars">
					<?php $bar_row( 'Algorithmic', $score_algo,    'algo' ); ?>
					<?php $bar_row( 'Pattern',     $score_pattern, 'pattern' ); ?>
					<?php $bar_row( 'Logical',     $score_logical, 'logical' ); ?>
				</div>

				<!-- Insight message -->
				<div class="tnq-insight">
					<?php echo esc_html( TNQ_Admin_Student::insight( $first_name, $score_total ) ); ?>
				</div>

			</div><!-- .tnq-report-card-body -->
		</div><!-- .tnq-report-card -->
		<?php
	};
	?>

	<?php if ( $baseline ) : $render_panel( $baseline, 'baseline' ); endif; ?>
	<?php if ( $endline )  : $render_panel( $endline,  'endline'  ); endif; ?>

	<!-- Growth panel — only when both results exist -->
	<?php if ( $baseline && $endline ) :
		include TNQ_PLUGIN_DIR . 'admin/views/partials/growth-table.php';
	endif; ?>

	</div><!-- .tnq-report-panels -->

	<!-- ── Parent contact card ──────────────────────────────────────────── -->
	<?php if ( $parent['parent_name'] || $parent['parent_email'] ) :
		include TNQ_PLUGIN_DIR . 'admin/views/partials/parent-contact.php';
	endif; ?>

</div><!-- .tnq-main-content -->
</div><!-- .tnq-admin-wrap -->
