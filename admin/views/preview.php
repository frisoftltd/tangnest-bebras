<?php
/**
 * CT Question Preview — admin view.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

// Read filter params from GET
$filter_mode  = sanitize_text_field( $_GET['mode']  ?? 'practice' );
$filter_age   = sanitize_text_field( $_GET['age']   ?? '7-8' );
$filter_skill = sanitize_text_field( $_GET['skill'] ?? '' );

$valid_modes = [ 'practice', 'baseline', 'endline' ];
$valid_ages  = [ '7-8', '9-10', '11-12' ];
if ( ! in_array( $filter_mode, $valid_modes, true ) ) $filter_mode = 'practice';
if ( ! in_array( $filter_age,  $valid_ages,  true ) ) $filter_age  = '7-8';

// Get questions for selected set
$questions = TNQ_Question_Bank::get_questions( $filter_mode, $filter_age );

// Apply skill filter
if ( $filter_skill && in_array( $filter_skill, [ 'algorithmic', 'pattern', 'logical' ], true ) ) {
	$fs = $filter_skill;
	$questions = array_values( array_filter( $questions, function ( $q ) use ( $fs ) { return ( $q['skill'] ?? '' ) === $fs; } ) );
}

$page_url = admin_url( 'admin.php?page=tnq-preview' );
?>
<div class="wrap tnq-preview-wrap">
	<h1>CT Question Preview</h1>
	<p>All questions render as live interactive widgets. Answers submitted here are <strong>not</strong> saved to the database.</p>

	<!-- Filter form -->
	<form method="GET" action="<?php echo esc_url( $page_url ); ?>" style="background:#fff;border:1px solid #c3c4c7;border-radius:6px;padding:16px;margin-bottom:24px;display:flex;flex-wrap:wrap;gap:20px;align-items:flex-end">
		<input type="hidden" name="page" value="tnq-preview">

		<div>
			<label style="display:block;font-weight:600;margin-bottom:4px">Mode</label>
			<?php foreach ( $valid_modes as $m ) : ?>
			<label style="margin-right:12px">
				<input type="radio" name="mode" value="<?php echo esc_attr( $m ); ?>" <?php checked( $filter_mode, $m ); ?>>
				<?php echo esc_html( ucfirst( $m ) ); ?>
			</label>
			<?php endforeach; ?>
		</div>

		<div>
			<label style="display:block;font-weight:600;margin-bottom:4px">Age band</label>
			<label style="margin-right:12px">
				<input type="radio" name="age" value="7-8" <?php checked( $filter_age, '7-8' ); ?>> 7–8
			</label>
			<label style="margin-right:12px;opacity:0.5" title="Coming in M3">
				<input type="radio" name="age" value="9-10" disabled> 9–10 <em>(M3)</em>
			</label>
			<label style="opacity:0.5" title="Coming in M3">
				<input type="radio" name="age" value="11-12" disabled> 11–12 <em>(M3)</em>
			</label>
		</div>

		<div>
			<label style="display:block;font-weight:600;margin-bottom:4px">Skill</label>
			<select name="skill" style="min-height:32px">
				<option value="">All skills</option>
				<option value="algorithmic" <?php selected( $filter_skill, 'algorithmic' ); ?>>Algorithmic</option>
				<option value="pattern"     <?php selected( $filter_skill, 'pattern'     ); ?>>Pattern</option>
				<option value="logical"     <?php selected( $filter_skill, 'logical'     ); ?>>Logical</option>
			</select>
		</div>

		<div>
			<button type="submit" class="button button-primary">Show questions</button>
		</div>
	</form>

	<?php if ( empty( $questions ) ) : ?>
	<div class="notice notice-warning inline">
		<p>No questions found for the selected filters.
		<?php if ( 'baseline' === $filter_mode && '7-8' !== $filter_age ) : ?>
		Age bands 9–10 and 11–12 are coming in M3.
		<?php endif; ?>
		</p>
	</div>
	<?php else : ?>

	<p>
		Showing <strong><?php echo count( $questions ); ?></strong> question(s) —
		<strong><?php echo esc_html( ucfirst( $filter_mode ) ); ?></strong> /
		<strong><?php echo esc_html( $filter_age ); ?></strong>
		<?php if ( $filter_skill ) : ?> / <strong><?php echo esc_html( ucfirst( $filter_skill ) ); ?></strong><?php endif; ?>.
		Each question is fully interactive.
	</p>

	<?php foreach ( $questions as $i => $q ) :
		$id   = $q['id']         ?? "q-$i";
		$type = $q['type']       ?? '';
		$skill= $q['skill']      ?? '';
		$diff = $q['difficulty'] ?? '';
	?>
	<div class="tnq-question-block">
		<div class="tnq-question-block-header">
			<span><?php echo esc_html( $id ); ?></span>
			<span>&middot;</span>
			<span><?php echo esc_html( ucfirst( $skill ) ); ?></span>
			<span>&middot;</span>
			<span><?php echo esc_html( $type ); ?></span>
			<span>&middot;</span>
			<span style="text-transform:uppercase;font-size:11px;letter-spacing:0.05em"><?php echo esc_html( $diff ); ?></span>
		</div>
		<div class="tnq-question-block-body">
			<?php echo TNQ_Renderer::render_question( $q, $filter_mode, $i, true ); ?>
		</div>
	</div>
	<?php endforeach; ?>

	<?php endif; ?>
</div>
