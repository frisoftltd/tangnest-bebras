<?php
/**
 * All Results page template.
 *
 * Variables provided by TNQ_Admin_Results::render():
 *   $courses             array  — [ ['course_id' => int, 'title' => string], ... ]
 *   $selected_course_id  int
 *   $selected_age_band   string
 *   $students_page       array  — slice of enrolled students for current page
 *   $results_by_student  array  — [ student_id => [ 'baseline' => obj, 'endline' => obj ] ]
 *   $total_students      int
 *   $current_page        int
 *   $total_pages         int
 *
 * @package Tangnest_Bebras
 * @since   2.9.2
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tnq-admin-wrap">
<div class="tnq-main-content">

	<div class="tnq-welcome-header">
		<h2 class="tnq-page-title"><?php esc_html_e( 'All Results', 'tangnest-bebras' ); ?></h2>
		<p class="tnq-location"><?php esc_html_e( 'CT Assessments — Student scores by course', 'tangnest-bebras' ); ?></p>
	</div>

	<!-- ── Filters ──────────────────────────────────────────────────────── -->
	<form method="get" class="tnq-results-filters">
		<input type="hidden" name="page" value="tnq-results">

		<select name="course_id" class="tnq-filter-select">
			<option value=""><?php esc_html_e( '— Select a course —', 'tangnest-bebras' ); ?></option>
			<?php foreach ( $courses as $course ) : ?>
			<option value="<?php echo esc_attr( $course['course_id'] ); ?>"
				<?php selected( $selected_course_id, (int) $course['course_id'] ); ?>>
				<?php echo esc_html( $course['title'] ); ?>
			</option>
			<?php endforeach; ?>
		</select>

		<select name="age_band" class="tnq-filter-select">
			<option value=""><?php esc_html_e( 'All Ages', 'tangnest-bebras' ); ?></option>
			<?php foreach ( [ '7-8', '9-10', '11-12' ] as $band ) : ?>
			<option value="<?php echo esc_attr( $band ); ?>"
				<?php selected( $selected_age_band, $band ); ?>>
				<?php echo esc_html( $band ); ?>
			</option>
			<?php endforeach; ?>
		</select>

		<button type="submit" class="tnq-btn-v2"><?php esc_html_e( 'Filter', 'tangnest-bebras' ); ?></button>
	</form>

	<!-- ── Content ──────────────────────────────────────────────────────── -->
	<?php if ( empty( $courses ) ) : ?>

	<div class="tnq-empty-state">
		<p><?php esc_html_e( 'No CT Assessment courses found. Attach a [tnq_assess] shortcode to a lesson to get started.', 'tangnest-bebras' ); ?></p>
	</div>

	<?php elseif ( ! $selected_course_id ) : ?>

	<div class="tnq-empty-state">
		<p><?php esc_html_e( 'Select a course above to view results.', 'tangnest-bebras' ); ?></p>
	</div>

	<?php elseif ( empty( $students_page ) ) : ?>

	<div class="tnq-empty-state">
		<p><?php esc_html_e( 'No students enrolled in this course yet.', 'tangnest-bebras' ); ?></p>
	</div>

	<?php else : ?>

	<div class="tnq-results-card">

		<div class="tnq-results-meta">
			<?php
			$first = ( $current_page - 1 ) * 25 + 1;
			$last  = min( $current_page * 25, $total_students );
			printf(
				/* translators: 1: first student number, 2: last student number, 3: total students */
				esc_html__( 'Showing %1$d–%2$d of %3$d students', 'tangnest-bebras' ),
				(int) $first,
				(int) $last,
				(int) $total_students
			);
			?>
		</div>

		<div class="tnq-table-wrap">
			<table class="tnq-results-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Student Name', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Age Band', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Baseline Score', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Endline Score', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Growth', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'tangnest-bebras' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $students_page as $student ) :
					$uid      = (int) $student['user_id'];
					$baseline = $results_by_student[ $uid ]['baseline'] ?? null;
					$endline  = $results_by_student[ $uid ]['endline']  ?? null;

					// Age band from whichever result exists (baseline preferred).
					$age_display = $baseline
						? esc_html( $baseline->age_band )
						: ( $endline ? esc_html( $endline->age_band ) : '—' );

					$report_url = add_query_arg(
						[
							'page'       => 'tnq-student-detail',
							'student_id' => $uid,
							'course_id'  => $selected_course_id,
						],
						admin_url( 'admin.php' )
					);
				?>
				<tr>
					<td class="tnq-col-name"><?php echo esc_html( $student['display_name'] ); ?></td>
					<td class="tnq-col-age"><?php echo $age_display; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped above ?></td>

					<td class="tnq-col-score">
					<?php if ( $baseline ) :
						$score_total   = (int) $baseline->score_total;
						$score_algo    = (int) $baseline->score_algorithmic;
						$score_pattern = (int) $baseline->score_pattern;
						$score_logical = (int) $baseline->score_logical;
						include TNQ_PLUGIN_DIR . 'admin/views/partials/score-bars.php';
					else : ?>
						<span class="tnq-not-taken"><?php esc_html_e( 'Not taken', 'tangnest-bebras' ); ?></span>
					<?php endif; ?>
					</td>

					<td class="tnq-col-score">
					<?php if ( $endline ) :
						$score_total   = (int) $endline->score_total;
						$score_algo    = (int) $endline->score_algorithmic;
						$score_pattern = (int) $endline->score_pattern;
						$score_logical = (int) $endline->score_logical;
						include TNQ_PLUGIN_DIR . 'admin/views/partials/score-bars.php';
					else : ?>
						<span class="tnq-not-taken"><?php esc_html_e( 'Not taken', 'tangnest-bebras' ); ?></span>
					<?php endif; ?>
					</td>

					<td class="tnq-col-growth">
					<?php if ( $baseline && $endline ) :
						$delta = (int) $endline->score_total - (int) $baseline->score_total;
						if ( $delta > 0 ) :
					?>
						<span class="tnq-growth tnq-growth-up">+<?php echo esc_html( $delta ); ?> ↑</span>
					<?php elseif ( $delta < 0 ) : ?>
						<span class="tnq-growth tnq-growth-down"><?php echo esc_html( $delta ); ?> ↓</span>
					<?php else : ?>
						<span class="tnq-growth tnq-growth-zero">= 0</span>
					<?php endif; endif; ?>
					</td>

					<td class="tnq-col-actions">
						<a href="<?php echo esc_url( $report_url ); ?>" class="tnq-btn-sm">
							<?php esc_html_e( 'View Report', 'tangnest-bebras' ); ?>
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div><!-- .tnq-table-wrap -->

		<?php if ( $total_pages > 1 ) :
			$base_url = add_query_arg(
				[
					'page'      => 'tnq-results',
					'course_id' => $selected_course_id,
					'age_band'  => $selected_age_band,
				],
				admin_url( 'admin.php' )
			);
		?>
		<div class="tnq-pagination">
			<?php for ( $p = 1; $p <= $total_pages; $p++ ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'paged', $p, $base_url ) ); ?>"
			   class="tnq-page-btn <?php echo $p === $current_page ? 'tnq-page-btn-active' : ''; ?>">
				<?php echo esc_html( $p ); ?>
			</a>
			<?php endfor; ?>
		</div>
		<?php endif; ?>

	</div><!-- .tnq-results-card -->

	<?php endif; ?>

</div><!-- .tnq-main-content -->
</div><!-- .tnq-admin-wrap -->
