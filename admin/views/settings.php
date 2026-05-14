<?php
/**
 * Settings page template.
 *
 * Variables from TNQ_Admin_Settings::render():
 *   $saved  bool — true when settings were just saved
 *
 * @package Tangnest_Bebras
 * @since   2.9.9
 */

defined( 'ABSPATH' ) || exit;

$logo_url        = get_option( 'tnq_school_logo_url', '' );
$school_name     = get_option( 'tnq_school_name',     'Tangnest STEM Academy' );
$school_location = get_option( 'tnq_school_location', 'Kigali, Rwanda' );
$active_bands    = (array) get_option( 'tnq_active_age_bands', [ '7-8' ] );
$timer_visible   = get_option( 'tnq_timer_visible', '1' );
?>
<div class="tnq-admin-wrap">
<div class="tnq-main-content">

	<h1 class="tnq-page-title"><?php esc_html_e( 'Settings', 'tangnest-bebras' ); ?></h1>

	<?php if ( ! empty( $saved ) ) : ?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Settings saved.', 'tangnest-bebras' ); ?></p>
	</div>
	<?php endif; ?>

	<form method="post">
		<?php wp_nonce_field( 'tnq_save_settings', 'tnq_settings_nonce' ); ?>

		<table class="form-table" role="presentation">

			<tr>
				<th scope="row">
					<label for="tnq_school_logo_url"><?php esc_html_e( 'School Logo URL', 'tangnest-bebras' ); ?></label>
				</th>
				<td>
					<input type="url"
					       id="tnq_school_logo_url"
					       name="tnq_school_logo_url"
					       value="<?php echo esc_attr( $logo_url ); ?>"
					       class="regular-text"
					       placeholder="https://">
					<p class="description"><?php esc_html_e( 'Paste the full URL of your school logo image (used in email reports).', 'tangnest-bebras' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="tnq_school_name"><?php esc_html_e( 'School Name', 'tangnest-bebras' ); ?></label>
				</th>
				<td>
					<input type="text"
					       id="tnq_school_name"
					       name="tnq_school_name"
					       value="<?php echo esc_attr( $school_name ); ?>"
					       class="regular-text">
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="tnq_school_location"><?php esc_html_e( 'School Location', 'tangnest-bebras' ); ?></label>
				</th>
				<td>
					<input type="text"
					       id="tnq_school_location"
					       name="tnq_school_location"
					       value="<?php echo esc_attr( $school_location ); ?>"
					       class="regular-text">
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Active Age Bands', 'tangnest-bebras' ); ?></th>
				<td>
					<?php foreach ( [ '7-8', '9-10', '11-12' ] as $band ) : ?>
					<label style="margin-right:16px;">
						<input type="checkbox"
						       name="tnq_active_age_bands[]"
						       value="<?php echo esc_attr( $band ); ?>"
						       <?php checked( in_array( $band, $active_bands, true ) ); ?>>
						<?php echo esc_html( $band ); ?>
					</label>
					<?php endforeach; ?>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Timer Visible', 'tangnest-bebras' ); ?></th>
				<td>
					<label>
						<input type="checkbox"
						       name="tnq_timer_visible"
						       value="1"
						       <?php checked( $timer_visible, '1' ); ?>>
						<?php esc_html_e( 'Show timer to students during quiz', 'tangnest-bebras' ); ?>
					</label>
				</td>
			</tr>

		</table>

		<?php submit_button( __( 'Save Settings', 'tangnest-bebras' ) ); ?>
	</form>

</div><!-- .tnq-main-content -->
</div><!-- .tnq-admin-wrap -->
