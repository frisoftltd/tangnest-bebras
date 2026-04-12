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
	 * Page hook suffix.
	 *
	 * @var string
	 */
	protected $page_hook = '';

	/**
	 * Constructor.
	 *
	 * @param Tangnest_Bebras_Settings      $settings      Settings service.
	 * @param Tangnest_Bebras_Tutor_LMS     $tutor_lms     Tutor LMS service.
	 * @param Tangnest_Bebras_Task_Registry $task_registry Task registry.
	 */
	public function __construct( Tangnest_Bebras_Settings $settings, Tangnest_Bebras_Tutor_LMS $tutor_lms, Tangnest_Bebras_Task_Registry $task_registry ) {
		$this->settings      = $settings;
		$this->tutor_lms     = $tutor_lms;
		$this->task_registry = $task_registry;
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
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
			<p><?php esc_html_e( 'Foundation settings for Bebras-style interactive learning experiences.', 'tangnest-bebras' ); ?></p>

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

			<form method="post" action="options.php">
				<?php
				settings_fields( 'tangnest_bebras_settings_group' );
				do_settings_sections( 'tangnest-bebras' );
				submit_button( __( 'Save Settings', 'tangnest-bebras' ) );
				?>
			</form>
		</div>
		<?php
	}
}
