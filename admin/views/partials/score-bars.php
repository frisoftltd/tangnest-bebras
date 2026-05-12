<?php
/**
 * Score bars partial — coloured skill squares + total.
 *
 * Variables expected from calling context:
 *   $score_total   int  0–9
 *   $score_algo    int  0–3
 *   $score_pattern int  0–3
 *   $score_logical int  0–3
 *
 * @package Tangnest_Bebras
 * @since   2.9.2
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tnq-score-cell">
	<div class="tnq-score-total-val"><?php echo esc_html( $score_total ); ?>/9</div>
	<div class="tnq-sq-rows">
		<div class="tnq-sq-row tnq-sq-algo">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
			<span class="tnq-sq <?php echo $i <= $score_algo ? 'tnq-sq-filled' : 'tnq-sq-empty'; ?>"></span>
			<?php endfor; ?>
		</div>
		<div class="tnq-sq-row tnq-sq-pattern">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
			<span class="tnq-sq <?php echo $i <= $score_pattern ? 'tnq-sq-filled' : 'tnq-sq-empty'; ?>"></span>
			<?php endfor; ?>
		</div>
		<div class="tnq-sq-row tnq-sq-logical">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
			<span class="tnq-sq <?php echo $i <= $score_logical ? 'tnq-sq-filled' : 'tnq-sq-empty'; ?>"></span>
			<?php endfor; ?>
		</div>
	</div>
</div>
