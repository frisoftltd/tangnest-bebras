<?php
/**
 * Frontend quiz shortcode template.
 *
 * @var string               $instance_id Unique instance id.
 * @var array<string, mixed> $payload     Quiz payload.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div
	id="<?php echo esc_attr( $instance_id ); ?>"
	class="tangnest-bebras-quiz"
	data-quiz-id="<?php echo esc_attr( $payload['id'] ); ?>"
	data-quiz-type="<?php echo esc_attr( $payload['quizType'] ); ?>"
>
	<div class="tangnest-bebras-quiz__app"></div>
	<script type="application/json" class="tangnest-bebras-quiz__payload"><?php echo wp_json_encode( $payload ); ?></script>
	<noscript>
		<p><?php esc_html_e( 'Tangnest Bebras quizzes require JavaScript to run this interactive quiz experience.', 'tangnest-bebras' ); ?></p>
	</noscript>
</div>
