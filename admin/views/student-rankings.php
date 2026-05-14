<?php
/**
 * Student Rankings list view.
 *
 * Variables provided by TNQ_Admin_Student::render_rankings_list():
 *   $rankings       array — ranked student rows for current page
 *   $total_students int   — total students across all pages
 *   $paged          int   — current page number
 *   $total_pages    int   — total number of pages
 *   $per_page       int   — rows per page
 *
 * @package Tangnest_Bebras
 * @since   2.9.17
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="tnq-admin-wrap">
<main class="tnq-main-content">

	<div class="tnq-welcome-header">
		<div>
			<h1 class="tnq-welcome-title"><?php esc_html_e( 'Student Rankings', 'tangnest-bebras' ); ?></h1>
			<p class="tnq-location">
				<?php
				printf(
					/* translators: %d: total student count */
					esc_html__( '%d students ranked by XP', 'tangnest-bebras' ),
					$total_students
				);
				?>
			</p>
		</div>
	</div>

	<div class="tnq-rankings">

		<div class="tnq-rankings-header">
			<h3 class="tnq-rankings-title"><?php esc_html_e( 'All Students', 'tangnest-bebras' ); ?></h3>
			<div class="tnq-per-page-wrap">
				<label for="tnq-per-page"><?php esc_html_e( 'Show', 'tangnest-bebras' ); ?></label>
				<select id="tnq-per-page">
					<?php foreach ( [ 10, 25, 50, 100 ] as $opt ) : ?>
						<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $per_page, $opt ); ?>>
							<?php echo esc_html( $opt ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span><?php esc_html_e( 'per page', 'tangnest-bebras' ); ?></span>
			</div>
		</div>

		<?php if ( empty( $rankings ) ) : ?>
			<div class="tnq-empty-state">
				<p><?php esc_html_e( 'No student results found yet.', 'tangnest-bebras' ); ?></p>
			</div>
		<?php else : ?>
			<table class="tnq-rankings-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Rank', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Student', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'XP', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Status', 'tangnest-bebras' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'tangnest-bebras' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rankings as $r ) : ?>
					<tr class="tnq-rank-row">
						<td>
							<span class="tnq-rank-badge <?php echo esc_attr( $r['rank_class'] ); ?>">
								<?php echo esc_html( $r['rank'] ); ?>
							</span>
						</td>
						<td>
							<div class="tnq-rank-student">
								<img class="tnq-rank-avatar"
								     src="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . $r['avatar'] ); ?>"
								     alt=""
								     aria-hidden="true">
								<span><?php echo esc_html( $r['name'] ); ?></span>
							</div>
						</td>
						<td class="tnq-xp"><strong><?php echo esc_html( $r['xp'] ); ?></strong> XP</td>
						<td class="tnq-perf-label"><?php echo esc_html( $r['status'] ); ?></td>
						<td>
							<a class="tnq-btn-sm"
							   href="<?php echo esc_url( add_query_arg( [
							       'page'       => 'tnq-student-detail',
							       'student_id' => $r['student_id'],
							       'course_id'  => $r['course_id'],
							   ], admin_url( 'admin.php' ) ) ); ?>">
								<?php esc_html_e( 'View Report', 'tangnest-bebras' ); ?> &rarr;
							</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if ( $total_pages > 1 ) : ?>
		<div class="tnq-rankings-pagination">
			<?php for ( $p = 1; $p <= $total_pages; $p++ ) : ?>
				<?php if ( $p === $paged ) : ?>
					<span class="tnq-page-btn tnq-page-btn-active"><?php echo esc_html( $p ); ?></span>
				<?php else : ?>
					<a class="tnq-page-btn"
					   href="<?php echo esc_url( add_query_arg( [
					       'page'     => 'tnq-student-detail',
					       'paged'    => $p,
					       'per_page' => $per_page,
					   ], admin_url( 'admin.php' ) ) ); ?>">
						<?php echo esc_html( $p ); ?>
					</a>
				<?php endif; ?>
			<?php endfor; ?>
		</div>
		<?php endif; ?>

	</div>

</main>
</div>
