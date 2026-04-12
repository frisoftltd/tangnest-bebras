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
	 * Settings group key.
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'tangnest_bebras_settings_group';

	/**
	 * Option key.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'tangnest_bebras_settings';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const SETTINGS_PAGE = 'tangnest-bebras';

	/**
	 * Registers settings-related hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
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
		$stored = $this->get_raw_settings();

		return wp_parse_args( $stored, self::defaults() );
	}

	/**
	 * Gets the raw stored option value as an array.
	 *
	 * @return array<string, mixed>
	 */
	public function get_raw_settings() {
		$stored = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		return $stored;
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
