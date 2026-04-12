<?php
/**
 * Admin UI and plugin page.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the plugin admin experience.
 */
class Tangnest_Bebras_Admin {
	/**
	 * Tutor LMS service.
	 *
	 * @var Tangnest_Bebras_Tutor_LMS
	 */
	protected $tutor_lms;

	/**
	 * Task registry service.
	 *
	 * @var Tangnest_Bebras_Task_Registry
	 */
	protected $task_registry;

	/**
	 * Updater service.
	 *
	 * @var Tangnest_Bebras_Updater
	 */
	protected $updater;

	/**
	 * Page hook suffix.
	 *
	 * @var string
	 */
	protected $page_hook = '';

	/**
	 * Constructor.
	 *
	 * @param Tangnest_Bebras_Tutor_LMS     $tutor_lms     Tutor LMS service.
	 * @param Tangnest_Bebras_Task_Registry $task_registry Task registry.
	 * @param Tangnest_Bebras_Updater       $updater       Updater service.
	 */
	public function __construct( Tangnest_Bebras_Tutor_LMS $tutor_lms, Tangnest_Bebras_Task_Registry $task_registry, Tangnest_Bebras_Updater $updater ) {
		$this->tutor_lms     = $tutor_lms;
		$this->task_registry = $task_registry;
		$this->updater       = $updater;
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_tangnest_bebras_check_updates', array( $this, 'handle_manual_update_check' ) );
		add_action( 'admin_notices', array( $this, 'render_update_check_notice' ) );
		add_filter( 'plugin_action_links_' . TANGNEST_BEBRAS_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Registers the plugin top-level menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		$this->page_hook = add_menu_page(
			__( 'Tangnest Bebras', 'tangnest-bebras' ),
			__( 'Tangnest Bebras', 'tangnest-bebras' ),
			'manage_options',
			'tangnest-bebras',
			array( $this, 'render_settings_page' ),
			'dashicons-welcome-learn-more',
			58
		);
	}

	/**
	 * Enqueues admin assets for the plugin screen.
	 *
	 * @param string $hook_suffix Current admin screen.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( $hook_suffix !== $this->page_hook ) {
			return;
		}

		wp_enqueue_style(
			'tangnest-bebras-admin',
			TANGNEST_BEBRAS_URL . 'assets/css/admin.css',
			array(),
			TANGNEST_BEBRAS_VERSION
		);

		wp_enqueue_script(
			'tangnest-bebras-admin',
			TANGNEST_BEBRAS_URL . 'assets/js/admin.js',
			array(),
			TANGNEST_BEBRAS_VERSION,
			true
		);
	}

	/**
	 * Renders the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$task_types = $this->task_registry->get_task_types();
		?>
		<div class="wrap tangnest-bebras-admin">
			<h1><?php esc_html_e( 'Tangnest Bebras', 'tangnest-bebras' ); ?></h1>
			<p><?php esc_html_e( 'Foundation overview for Bebras-style interactive learning experiences.', 'tangnest-bebras' ); ?></p>

			<div class="tangnest-bebras-admin__meta">
				<div class="tangnest-bebras-card">
					<h2><?php esc_html_e( 'Tutor LMS Status', 'tangnest-bebras' ); ?></h2>
					<p>
						<span class="tangnest-bebras-status <?php echo $this->tutor_lms->is_active() ? 'is-active' : 'is-inactive'; ?>">
							<?php echo esc_html( $this->tutor_lms->is_active() ? __( 'Active', 'tangnest-bebras' ) : __( 'Not Active', 'tangnest-bebras' ) ); ?>
						</span>
					</p>
					<p><?php esc_html_e( 'Tangnest Bebras will integrate with Tutor LMS without modifying Tutor LMS core files.', 'tangnest-bebras' ); ?></p>
				</div>

				<div class="tangnest-bebras-card">
					<h2><?php esc_html_e( 'Planned Task Types', 'tangnest-bebras' ); ?></h2>
					<ul class="tangnest-bebras-task-list">
						<?php foreach ( $task_types as $task_type ) : ?>
							<li>
								<strong><?php echo esc_html( $task_type['label'] ); ?></strong>
								<span><?php echo esc_html( $task_type['description'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Adds plugin row actions on the Plugins screen.
	 *
	 * @param array<string, string> $actions Existing action links.
	 * @return array<string, string>
	 */
	public function add_plugin_action_links( $actions ) {
		if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_options' ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=tangnest_bebras_check_updates' ),
			'tangnest_bebras_check_updates'
		);

		$actions['tangnest_bebras_check_updates'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			esc_html__( 'Check for Updates', 'tangnest-bebras' )
		);

		return $actions;
	}

	/**
	 * Handles the manual updater request.
	 *
	 * @return void
	 */
	public function handle_manual_update_check() {
		if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'tangnest-bebras' ) );
		}

		check_admin_referer( 'tangnest_bebras_check_updates' );
		delete_site_transient( 'update_plugins' );
		$result = $this->updater->run_manual_check();

		$notice = 'update-check-completed';

		if ( ! empty( $result['status'] ) && is_string( $result['status'] ) ) {
			$notice = $result['status'];
		}

		wp_safe_redirect(
			add_query_arg(
				'tangnest_bebras_notice',
				$notice,
				admin_url( 'plugins.php' )
			)
		);
		exit;
	}

	/**
	 * Renders the update-check notice on the Plugins screen.
	 *
	 * @return void
	 */
	public function render_update_check_notice() {
		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $current_screen || 'plugins' !== $current_screen->id ) {
			return;
		}

		$notice = isset( $_GET['tangnest_bebras_notice'] ) ? sanitize_key( wp_unslash( $_GET['tangnest_bebras_notice'] ) ) : '';

		if ( empty( $notice ) ) {
			return;
		}

		if ( 'update-available' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Update available. The Plugins page has been refreshed with the latest update data.', 'tangnest-bebras' ); ?></p>
			</div>
			<?php
			return;
		}

		if ( 'no-update' === $notice ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p><?php esc_html_e( 'No update available.', 'tangnest-bebras' ); ?></p>
			</div>
			<?php
			return;
		}

		if ( 'check-failed' === $notice ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p><?php esc_html_e( 'Update check could not be completed. Please try again.', 'tangnest-bebras' ); ?></p>
			</div>
			<?php
			return;
		}

		if ( 'update-check-completed' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Update check completed.', 'tangnest-bebras' ); ?></p>
			</div>
			<?php
		}
	}
}
