<?php
/**
 * Self-hosted GitHub release update checker.
 * Adds a "Check for Update" action link on the Plugins page.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Updater {

	/** @var \YahnisElsts\PluginUpdateChecker\v5\Plugin\UpdateChecker|null */
	private $checker = null;

	public function init(): void {
		$puc_bootstrap = TNQ_PLUGIN_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
		if ( ! file_exists( $puc_bootstrap ) ) {
			return;
		}

		require_once $puc_bootstrap;

		$this->checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			'https://github.com/frisoftltd/tangnest-bebras',
			TNQ_PLUGIN_FILE,
			'tangnest-bebras'
		);

		// Fetch the release asset zip rather than the raw source zip.
		$this->checker->getVcsApi()->enableReleaseAssets();

		// Treat 'main' as the stable branch.
		$this->checker->setBranch( 'main' );

		add_filter(
			'plugin_action_links_' . plugin_basename( TNQ_PLUGIN_FILE ),
			[ $this, 'add_action_link' ]
		);

		add_action( 'admin_action_tnq_check_update', [ $this, 'handle_check_update' ] );
		add_action( 'admin_notices',                 [ $this, 'show_checked_notice' ] );
	}

	/**
	 * Append "Check for Update" to the plugin row action links.
	 */
	public function add_action_link( array $links ): array {
		$url = wp_nonce_url(
			add_query_arg( 'action', 'tnq_check_update', admin_url( 'admin.php' ) ),
			'tnq_check_update'
		);
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Check for Update', 'tangnest-bebras' ) . '</a>';
		return $links;
	}

	/**
	 * Handle the admin_action_tnq_check_update request.
	 * Fires when admin.php?action=tnq_check_update is visited.
	 */
	public function handle_check_update(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'tangnest-bebras' ) );
		}

		check_admin_referer( 'tnq_check_update' );

		if ( $this->checker ) {
			$this->checker->checkForUpdates();
		}

		wp_safe_redirect(
			add_query_arg( 'tnq_update_checked', '1', self_admin_url( 'plugins.php' ) )
		);
		exit;
	}

	/**
	 * Show a success admin notice after the update check redirect.
	 */
	public function show_checked_notice(): void {
		if ( empty( $_GET['tnq_update_checked'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		echo '<div class="notice notice-success is-dismissible"><p>' .
			esc_html__( 'Tangnest Bebras: Update check complete.', 'tangnest-bebras' ) .
			'</p></div>';
	}
}
