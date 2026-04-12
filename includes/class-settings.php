<?php
/**
 * Settings registration and sanitization.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin settings.
 */
class Tangnest_Bebras_Settings {

	/**
	 * Option key.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'tangnest_bebras_settings';

	/**
	 * Registers settings-related hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'update_option_' . self::OPTION_NAME, array( $this, 'clear_update_caches' ), 10, 2 );
	}

	/**
	 * Returns default option values.
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults() {
		return array(
			'github_repo_url' => '',
			'github_branch'   => 'main',
			'enable_updates'  => 0,
		);
	}

	/**
	 * Gets merged settings.
	 *
	 * @return array<string, mixed>
	 */
	public function get_settings() {
		$stored = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return wp_parse_args( $stored, self::defaults() );
	}

	/**
	 * Returns one setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Optional default.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		$settings = $this->get_settings();

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Registers the settings fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'tangnest_bebras_settings_group',
			self::OPTION_NAME,
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'tangnest_bebras_update_settings',
			__( 'Update Settings', 'tangnest-bebras' ),
			array( $this, 'render_update_section' ),
			'tangnest-bebras'
		);

		add_settings_field(
			'github_repo_url',
			__( 'GitHub Repository URL', 'tangnest-bebras' ),
			array( $this, 'render_repo_url_field' ),
			'tangnest-bebras',
			'tangnest_bebras_update_settings'
		);

		add_settings_field(
			'github_branch',
			__( 'GitHub Branch', 'tangnest-bebras' ),
			array( $this, 'render_branch_field' ),
			'tangnest-bebras',
			'tangnest_bebras_update_settings'
		);

		add_settings_field(
			'enable_updates',
			__( 'Enable Update Checks', 'tangnest-bebras' ),
			array( $this, 'render_enable_updates_field' ),
			'tangnest-bebras',
			'tangnest_bebras_update_settings'
		);
	}

	/**
	 * Sanitizes stored settings.
	 *
	 * @param array<string, mixed> $input Raw input.
	 * @return array<string, mixed>
	 */
	public function sanitize_settings( $input ) {
		$defaults = self::defaults();

		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$output = array();

		$output['github_repo_url'] = isset( $input['github_repo_url'] ) ? esc_url_raw( trim( wp_unslash( $input['github_repo_url'] ) ) ) : $defaults['github_repo_url'];
		$output['github_branch']   = isset( $input['github_branch'] ) ? sanitize_text_field( wp_unslash( $input['github_branch'] ) ) : $defaults['github_branch'];
		$output['enable_updates']  = ! empty( $input['enable_updates'] ) ? 1 : 0;

		if ( empty( $output['github_branch'] ) ) {
			$output['github_branch'] = $defaults['github_branch'];
		}

		return wp_parse_args( $output, $defaults );
	}

	/**
	 * Renders section copy.
	 *
	 * @return void
	 */
	public function render_update_section() {
		echo '<p>' . esc_html__( 'Configure GitHub release-based updates for this plugin. For automatic updates, attach a release asset zip that contains this plugin folder.', 'tangnest-bebras' ) . '</p>';
	}

	/**
	 * Renders GitHub repository field.
	 *
	 * @return void
	 */
	public function render_repo_url_field() {
		$settings = $this->get_settings();
		?>
		<input
			type="url"
			class="regular-text"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[github_repo_url]"
			value="<?php echo esc_attr( $settings['github_repo_url'] ); ?>"
			placeholder="https://github.com/your-org/tangnest-bebras"
		/>
		<p class="description"><?php esc_html_e( 'Enter the public GitHub repository URL used for plugin releases.', 'tangnest-bebras' ); ?></p>
		<?php
	}

	/**
	 * Renders branch field.
	 *
	 * @return void
	 */
	public function render_branch_field() {
		$settings = $this->get_settings();
		?>
		<input
			type="text"
			class="regular-text"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[github_branch]"
			value="<?php echo esc_attr( $settings['github_branch'] ); ?>"
			placeholder="main"
		/>
		<p class="description"><?php esc_html_e( 'Branch used for repository metadata and future fallback workflows.', 'tangnest-bebras' ); ?></p>
		<?php
	}

	/**
	 * Renders enable-updates field.
	 *
	 * @return void
	 */
	public function render_enable_updates_field() {
		$settings = $this->get_settings();
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enable_updates]"
				value="1"
				<?php checked( ! empty( $settings['enable_updates'] ) ); ?>
			/>
			<?php esc_html_e( 'Check GitHub releases for plugin updates.', 'tangnest-bebras' ); ?>
		</label>
		<?php
	}

	/**
	 * Clears update-related caches when settings change.
	 *
	 * @param mixed $old_value Previous settings value.
	 * @param mixed $value     New settings value.
	 * @return void
	 */
	public function clear_update_caches( $old_value, $value ) {
		unset( $old_value, $value );

		delete_transient( 'tangnest_bebras_github_release' );
		delete_site_transient( 'update_plugins' );
	}
}
