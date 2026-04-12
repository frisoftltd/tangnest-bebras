<?php
/**
 * Tutor LMS integration checks.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Tutor LMS detection and notices.
 */
class Tangnest_Bebras_Tutor_LMS {

	/**
	 * Known Tutor LMS plugin basenames.
	 *
	 * @var string[]
	 */
	protected $known_plugins = array(
		'tutor/tutor.php',
		'tutor-lms/tutor.php',
	);

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_notices', array( $this, 'maybe_render_missing_notice' ) );
	}

	/**
	 * Returns whether Tutor LMS appears to be active.
	 *
	 * @return bool
	 */
	public function is_active() {
		if ( defined( 'TUTOR_VERSION' ) || class_exists( 'TUTOR\Input' ) ) {
			return true;
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );

		foreach ( $this->known_plugins as $plugin_file ) {
			if ( in_array( $plugin_file, $active_plugins, true ) ) {
				return true;
			}
		}

		if ( is_multisite() ) {
			$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $this->known_plugins as $plugin_file ) {
				if ( isset( $network_plugins[ $plugin_file ] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Displays an admin notice when Tutor LMS is missing.
	 *
	 * @return void
	 */
	public function maybe_render_missing_notice() {
		if ( $this->is_active() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Tangnest Bebras works best with Tutor LMS. Tutor LMS does not appear to be active, so Tutor-specific features will remain unavailable until it is installed and activated.', 'tangnest-bebras' ); ?>
			</p>
		</div>
		<?php
	}
}
