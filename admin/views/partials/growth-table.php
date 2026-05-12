<?php
/**
 * Growth comparison table partial.
 *
 * Variables from student.php scope:
 *   $baseline    object
 *   $endline     object
 *   $first_name  string
 *
 * @package Tangnest_Bebras
 * @since   2.9.3
 */

defined( 'ABSPATH' ) || exit;

$delta       = (int) $endline->score_total     - (int) $baseline->score_total;
$delta_algo  = (int) $endline->score_algorithmic - (int) $baseline->score_algorithmic;
$delta_patt  = (int) $endline->score_pattern   - (int) $baseline->score_pattern;
$delta_logic = (int) $endline->score_logical   - (int) $baseline->score_logical;

$growth_msg  = TNQ_Admin_Student::growth_message( $first_name, $delta );

if ( $delta >= 1 ) {
	$banner_class = 'tnq-growth-banner-positive';
} elseif ( $delta < 0 ) {
	$banner_class = 'tnq-growth-banner-negative';
} else {
	$banner_class = 'tnq-growth-banner-neutral';
}

/**
 * Returns a delta cell string: "+2 ↑", "−1 ↓", "= 0".
 */
$delta_cell = function ( int $d ): string {
	if ( $d > 0 ) {
		return '<span class="tnq-delta-up">+' . $d . ' &#8593;</span>';
	}
	if ( $d < 0 ) {
		return '<span class="tnq-delta-down">' . $d . ' &#8595;</span>';
	}
	return '<span class="tnq-delta-zero">= 0</span>';
};
?>
<div class="tnq-report-card">
	<div class="tnq-report-card-header">
		<h3 class="tnq-report-card-title">📈 <?php esc_html_e( 'Growth Summary', 'tangnest-bebras' ); ?></h3>
	</div>
	<div class="tnq-report-card-body">

		<!-- Growth banner -->
		<div class="tnq-growth-banner <?php echo esc_attr( $banner_class ); ?>">
			<?php echo esc_html( $growth_msg ); ?>
		</div>

		<!-- Comparison table -->
		<table class="tnq-growth-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Skill', 'tangnest-bebras' ); ?></th>
					<th><?php esc_html_e( 'Baseline', 'tangnest-bebras' ); ?></th>
					<th><?php esc_html_e( 'Endline', 'tangnest-bebras' ); ?></th>
					<th><?php esc_html_e( 'Change', 'tangnest-bebras' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Total', 'tangnest-bebras' ); ?></td>
					<td><?php echo esc_html( $baseline->score_total ); ?>/9</td>
					<td><?php echo esc_html( $endline->score_total ); ?>/9</td>
					<td><?php echo $delta_cell( $delta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Algorithmic', 'tangnest-bebras' ); ?></td>
					<td><?php echo esc_html( $baseline->score_algorithmic ); ?>/3</td>
					<td><?php echo esc_html( $endline->score_algorithmic ); ?>/3</td>
					<td><?php echo $delta_cell( $delta_algo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Pattern', 'tangnest-bebras' ); ?></td>
					<td><?php echo esc_html( $baseline->score_pattern ); ?>/3</td>
					<td><?php echo esc_html( $endline->score_pattern ); ?>/3</td>
					<td><?php echo $delta_cell( $delta_patt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Logical', 'tangnest-bebras' ); ?></td>
					<td><?php echo esc_html( $baseline->score_logical ); ?>/3</td>
					<td><?php echo esc_html( $endline->score_logical ); ?>/3</td>
					<td><?php echo $delta_cell( $delta_logic ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
			</tbody>
		</table>

	</div><!-- .tnq-report-card-body -->
</div><!-- .tnq-report-card -->
