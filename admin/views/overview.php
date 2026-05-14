<?php
/**
 * Overview page template — v2.9.0 redesign.
 *
 * Variables provided by TNQ_Admin_Overview::render():
 *   $current_user  WP_User
 *   $skills        array of skill card data
 *   $course_data   array of course card data
 *   $both_count    int  — students who completed both assessments
 *   $total_enrolled int  — total enrolled across all courses
 *   $overall_pct   int  — overall completion percentage
 *   $motivation    string
 *   $top_students  array of top student data
 *
 * @package Tangnest_Bebras
 * @since   2.9.0
 */
defined( 'ABSPATH' ) || exit;

// SVG ring helper — returns the full SVG for a circular progress ring.
// circumference for r=40 circle inside 100×100 viewBox ≈ 251.33.
$circ = 251.33;

// Donut chart constants: r=54 inside 140×140 viewBox.
$donut_circ = 339.29;
$donut_offset = $donut_circ * ( 1 - $overall_pct / 100 );
?>
<div class="tnq-admin-wrap">

	<!-- ── Main content ─────────────────────────────────────────────────── -->
	<main class="tnq-main-content">

		<!-- Welcome header -->
		<div class="tnq-welcome-header">
			<div>
				<h1 class="tnq-welcome-title">
					<?php
					printf(
						/* translators: %s: teacher display name */
						esc_html__( 'Welcome back, %s! &#128075;', 'tangnest-bebras' ),
						esc_html( $current_user->display_name )
					);
					?>
				</h1>
				<h2 class="tnq-page-title"><?php esc_html_e( 'CT Assessments — Overview', 'tangnest-bebras' ); ?></h2>
				<p class="tnq-location">&#128205; <?php esc_html_e( 'Stem Academy • Kigali', 'tangnest-bebras' ); ?></p>
			</div>
		</div>

		<!-- ── Section 1: Skill Cards ──────────────────────────────────────── -->
		<?php if ( $attempts > 0 ) : ?>
		<div class="tnq-skill-cards">
			<?php foreach ( $skills as $skill ) :
				$offset = $circ * ( 1 - $skill['pct'] / 100 );
			?>
			<div class="tnq-skill-card">
				<div class="tnq-skill-card-top">
					<div class="tnq-skill-icon">
						<img src="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . $skill['icon'] ); ?>"
						     alt=""
						     width="36"
						     height="36"
						     aria-hidden="true">
					</div>
					<div class="tnq-skill-info">
						<h3 class="tnq-skill-name" style="color:<?php echo esc_attr( $skill['color'] ); ?>">
							<?php echo esc_html( $skill['name'] ); ?>
						</h3>
						<span class="tnq-skill-label tnq-label-<?php echo esc_attr( strtolower( $skill['label'] ) ); ?>">
							<?php echo esc_html( $skill['label'] ); ?>
						</span>
					</div>
				</div>

				<div class="tnq-skill-stats">
					<div class="tnq-skill-stat">
						<span class="tnq-stat-value"><?php echo esc_html( $skill['total'] ); ?></span>
						<span class="tnq-stat-label"><?php esc_html_e( 'Total Questions', 'tangnest-bebras' ); ?></span>
					</div>
					<div class="tnq-skill-stat">
						<span class="tnq-stat-value"><?php echo esc_html( $skill['correct'] ); ?></span>
						<span class="tnq-stat-label"><?php esc_html_e( 'Correct', 'tangnest-bebras' ); ?></span>
					</div>
				</div>

				<div class="tnq-ring-wrap">
					<svg class="tnq-ring" viewBox="0 0 100 100" aria-label="<?php echo esc_attr( $skill['pct'] . '% mastery' ); ?>">
						<circle class="tnq-ring-bg" cx="50" cy="50" r="40"/>
						<circle class="tnq-ring-fill"
						        cx="50" cy="50" r="40"
						        style="stroke:<?php echo esc_attr( $skill['color'] ); ?>;stroke-dasharray:<?php echo esc_attr( $circ ); ?>;stroke-dashoffset:<?php echo esc_attr( round( $offset, 2 ) ); ?>"
						        transform="rotate(-90 50 50)"/>
						<text x="50" y="46" class="tnq-ring-pct"><?php echo esc_html( $skill['pct'] ); ?>%</text>
						<text x="50" y="60" class="tnq-ring-sub"><?php esc_html_e( 'Mastery', 'tangnest-bebras' ); ?></text>
					</svg>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<!-- ── Section 2 + 3: Course cards + Completion donut ─────────────── -->
		<div class="tnq-middle-row">

			<!-- Course cards -->
			<div class="tnq-course-section">
				<?php if ( empty( $course_data ) ) : ?>
					<div class="tnq-empty-state">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="56" height="56" fill="none" aria-hidden="true">
							<polygon points="32,6 38,24 58,24 42,36 48,54 32,42 16,54 22,36 6,24 26,24" fill="#F39C12" opacity="0.4"/>
						</svg>
						<p><?php esc_html_e( 'No classes found yet. Ask your admin to set up courses in Tutor LMS.', 'tangnest-bebras' ); ?></p>
					</div>
				<?php else : ?>
					<?php foreach ( $course_data as $c ) : ?>
					<div class="tnq-course-card-v2">
						<div class="tnq-course-card-header">
							<h3 class="tnq-course-title"><?php echo esc_html( $c['title'] ); ?></h3>
							<?php if ( $c['excerpt'] ) : ?>
								<p class="tnq-course-excerpt"><?php echo esc_html( $c['excerpt'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="tnq-course-stats">

							<!-- Enrolled -->
							<div class="tnq-progress-row">
								<div class="tnq-progress-label">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1A56A0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
									<span><?php esc_html_e( 'Students Enrolled', 'tangnest-bebras' ); ?></span>
								</div>
								<div class="tnq-progress-count"><?php echo esc_html( $c['total'] ); ?></div>
								<div class="tnq-progress-bar-wrap">
									<div class="tnq-progress-bar" style="width:100%;background:#1A56A0"></div>
								</div>
							</div>

							<!-- Baseline -->
							<div class="tnq-progress-row">
								<div class="tnq-progress-label">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1E8449" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
									<span><?php esc_html_e( 'Completed Baseline', 'tangnest-bebras' ); ?></span>
								</div>
								<div class="tnq-progress-count">
									<?php echo esc_html( $c['baseline_count'] ); ?> / <?php echo esc_html( $c['total'] ); ?>
								</div>
								<div class="tnq-progress-bar-wrap">
									<div class="tnq-progress-bar" style="width:<?php echo esc_attr( $c['baseline_pct'] ); ?>%;background:#1E8449"></div>
								</div>
								<span class="tnq-pct-badge" style="background:#1E8449"><?php echo esc_html( $c['baseline_pct'] ); ?>%</span>
							</div>

							<!-- Endline -->
							<div class="tnq-progress-row">
								<div class="tnq-progress-label">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#F39C12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
									<span><?php esc_html_e( 'Completed Endline', 'tangnest-bebras' ); ?></span>
								</div>
								<div class="tnq-progress-count">
									<?php echo esc_html( $c['endline_count'] ); ?> / <?php echo esc_html( $c['total'] ); ?>
								</div>
								<div class="tnq-progress-bar-wrap">
									<div class="tnq-progress-bar" style="width:<?php echo esc_attr( $c['endline_pct'] ); ?>%;background:#F39C12"></div>
								</div>
								<span class="tnq-pct-badge" style="background:#F39C12"><?php echo esc_html( $c['endline_pct'] ); ?>%</span>
							</div>

						</div>

						<a class="tnq-btn-v2"
						   href="<?php echo esc_url( admin_url( 'admin.php?page=tnq-results&course_id=' . (int) $c['course_id'] ) ); ?>">
							<?php esc_html_e( 'View Students', 'tangnest-bebras' ); ?> &rarr;
						</a>
					</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- Completion donut -->
			<div class="tnq-completion-card">
				<h3 class="tnq-completion-title"><?php esc_html_e( 'Assessment Completion Progress', 'tangnest-bebras' ); ?></h3>

				<div class="tnq-donut-wrap">
					<svg class="tnq-donut" viewBox="0 0 140 140" aria-label="<?php echo esc_attr( $overall_pct . '% overall completion' ); ?>">
						<circle class="tnq-donut-bg" cx="70" cy="70" r="54"/>
						<circle class="tnq-donut-fill"
						        cx="70" cy="70" r="54"
						        style="stroke-dasharray:<?php echo esc_attr( $donut_circ ); ?>;stroke-dashoffset:<?php echo esc_attr( round( $donut_offset, 2 ) ); ?>"
						        transform="rotate(-90 70 70)"/>
						<text x="70" y="65" class="tnq-donut-pct"><?php echo esc_html( $overall_pct ); ?>%</text>
						<text x="70" y="82" class="tnq-donut-sub"><?php esc_html_e( 'Complete', 'tangnest-bebras' ); ?></text>
					</svg>
				</div>

				<p class="tnq-completion-count">
					<?php
					printf(
						/* translators: 1: count fully done, 2: total enrolled */
						esc_html__( '%1$s / %2$s Students Fully Completed', 'tangnest-bebras' ),
						'<strong>' . esc_html( $both_count ) . '</strong>',
						'<strong>' . esc_html( $total_enrolled ) . '</strong>'
					);
					?>
					<br>
					<small><?php esc_html_e( '(Both Baseline &amp; Endline)', 'tangnest-bebras' ); ?></small>
				</p>

				<p class="tnq-motivation"><?php echo esc_html( $motivation ); ?></p>
			</div>

		</div>

		<!-- ── Section 4: Top Performing Students ──────────────────────────── -->
		<?php if ( ! empty( $top_students ) ) : ?>
		<div class="tnq-top-students-section">
			<div class="tnq-section-header">
				<h3 class="tnq-section-title"><?php esc_html_e( 'Top Performing Students', 'tangnest-bebras' ); ?></h3>
				<a class="tnq-link-more" href="<?php echo esc_url( admin_url( 'admin.php?page=tnq-student-detail' ) ); ?>">
					<?php esc_html_e( 'View All Rankings', 'tangnest-bebras' ); ?> &rarr;
				</a>
			</div>

			<div class="tnq-top-students">
				<?php foreach ( $top_students as $student ) : ?>
				<div class="tnq-student-card">
					<div class="tnq-student-rank" style="background:<?php echo esc_attr( $student['rank_color'] ); ?>">
						<?php echo esc_html( $student['rank'] ); ?>
					</div>
					<div class="tnq-student-avatar">
						<img src="<?php echo esc_url( TNQ_PLUGIN_URL . 'public/assets/svg/' . $student['avatar'] ); ?>"
						     alt=""
						     width="48"
						     height="48"
						     aria-hidden="true">
					</div>
					<div class="tnq-student-info">
						<span class="tnq-student-name"><?php echo esc_html( $student['name'] ); ?></span>
						<span class="tnq-student-xp">
							<strong style="color:#F39C12"><?php echo esc_html( $student['xp'] ); ?></strong> XP
						</span>
						<span class="tnq-student-perf"><?php echo esc_html( $student['perf'] ); ?></span>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

	</main><!-- .tnq-main-content -->

</div><!-- .tnq-admin-wrap -->
