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
	 * Settings service.
	 *
	 * @var Tangnest_Bebras_Settings
	 */
	protected $settings;

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
	 * Whether settings were saved during this request.
	 *
	 * @var bool
	 */
	protected $settings_saved = false;

	/**
	 * Constructor.
	 *
	 * @param Tangnest_Bebras_Settings      $settings      Settings service.
	 * @param Tangnest_Bebras_Tutor_LMS     $tutor_lms     Tutor LMS service.
	 * @param Tangnest_Bebras_Task_Registry $task_registry Task registry.
	 * @param Tangnest_Bebras_Updater       $updater       Updater service.
	 */
	public function __construct( Tangnest_Bebras_Settings $settings, Tangnest_Bebras_Tutor_LMS $tutor_lms, Tangnest_Bebras_Task_Registry $task_registry, Tangnest_Bebras_Updater $updater ) {
		$this->settings      = $settings;
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

		$this->maybe_handle_settings_save();

		$task_types = $this->task_registry->get_task_types();
		$stored_settings = get_option( Tangnest_Bebras_Settings::OPTION_NAME, array() );

		if ( ! is_array( $stored_settings ) ) {
			$stored_settings = array();
		}

		$settings = array(
			'github_repo_url' => isset( $stored_settings['github_repo_url'] ) ? (string) $stored_settings['github_repo_url'] : '',
			'github_branch'   => isset( $stored_settings['github_branch'] ) && '' !== $stored_settings['github_branch'] ? (string) $stored_settings['github_branch'] : 'main',
			'enable_updates'  => ! empty( $stored_settings['enable_updates'] ) ? 1 : 0,
		);
		?>
		<div class="wrap tangnest-bebras-admin">
			<h1><?php esc_html_e( 'Tangnest Bebras', 'tangnest-bebras' ); ?></h1>
			<p><?php esc_html_e( 'Foundation settings for Bebras-style interactive learning experiences.', 'tangnest-bebras' ); ?></p>
			<?php $this->render_admin_notices(); ?>

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

			<form method="post" action="<?php echo esc_url( menu_page_url( 'tangnest-bebras', false ) ); ?>">
				<input type="hidden" name="tangnest_bebras_save_settings" value="1" />
				<?php wp_nonce_field( 'tangnest_bebras_save_settings', 'tangnest_bebras_save_settings_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="tangnest-bebras-github-repo-url"><?php esc_html_e( 'GitHub Repository URL', 'tangnest-bebras' ); ?></label>
							</th>
							<td>
								<input
									id="tangnest-bebras-github-repo-url"
									type="url"
									class="regular-text"
									name="tangnest_bebras_settings[github_repo_url]"
									value="<?php echo esc_attr( $settings['github_repo_url'] ); ?>"
									placeholder="https://github.com/your-org/tangnest-bebras"
								/>
								<p class="description"><?php esc_html_e( 'Enter the public GitHub repository URL used for plugin releases.', 'tangnest-bebras' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="tangnest-bebras-github-branch"><?php esc_html_e( 'GitHub Branch', 'tangnest-bebras' ); ?></label>
							</th>
							<td>
								<input
									id="tangnest-bebras-github-branch"
									type="text"
									class="regular-text"
									name="tangnest_bebras_settings[github_branch]"
									value="<?php echo esc_attr( $settings['github_branch'] ); ?>"
									placeholder="main"
								/>
								<p class="description"><?php esc_html_e( 'Branch used for repository metadata and future fallback workflows.', 'tangnest-bebras' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Update Checks', 'tangnest-bebras' ); ?></th>
							<td>
								<label for="tangnest-bebras-enable-updates">
									<input
										id="tangnest-bebras-enable-updates"
										type="checkbox"
										name="tangnest_bebras_settings[enable_updates]"
										value="1"
										<?php checked( $settings['enable_updates'] ); ?>
									/>
									<?php esc_html_e( 'Check GitHub releases for plugin updates.', 'tangnest-bebras' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Settings', 'tangnest-bebras' ) ); ?>
			</form>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="tangnest_bebras_check_updates" />
				<?php wp_nonce_field( 'tangnest_bebras_check_updates', 'tangnest_bebras_check_updates_nonce' ); ?>
				<?php submit_button( __( 'Check for Updates Now', 'tangnest-bebras' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handles the manual updater request.
	 *
	 * @return void
	 */
	public function handle_manual_update_check() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'tangnest-bebras' ) );
		}

		check_admin_referer( 'tangnest_bebras_check_updates', 'tangnest_bebras_check_updates_nonce' );

		$this->updater->run_manual_check();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                   => 'tangnest-bebras',
					'tangnest_bebras_notice' => 'update-check-completed',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Renders settings-page notices.
	 *
	 * @return void
	 */
	protected function render_admin_notices() {
		$notice = isset( $_GET['tangnest_bebras_notice'] ) ? sanitize_key( wp_unslash( $_GET['tangnest_bebras_notice'] ) ) : '';

		if ( $this->settings_saved ) {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Settings saved.', 'tangnest-bebras' ); ?></p>
			</div>
			<?php
		}

		if ( 'update-check-completed' !== $notice ) {
			return;
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Update check completed.', 'tangnest-bebras' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Saves settings from the current admin page request.
	 *
	 * @return void
	 */
	protected function maybe_handle_settings_save() {
		if ( 'POST' !== strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		if ( empty( $_POST['tangnest_bebras_save_settings'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'tangnest_bebras_save_settings', 'tangnest_bebras_save_settings_nonce' );

		$raw_settings = array();

		if ( isset( $_POST['tangnest_bebras_settings'] ) && is_array( $_POST['tangnest_bebras_settings'] ) ) {
			$raw_settings = wp_unslash( $_POST['tangnest_bebras_settings'] );
		}

		$sanitized_settings = $this->settings->sanitize_settings( $raw_settings );

		update_option( 'tangnest_bebras_settings', $sanitized_settings );

		$this->settings_saved = true;
	}
}
