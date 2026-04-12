<?php
/**
 * GitHub release updater scaffold.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds GitHub release update support to WordPress.
 */
class Tangnest_Bebras_Updater {

	/**
	 * Registers updater hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update' ) );
		add_filter( 'site_transient_update_plugins', array( $this, 'inject_update' ) );
		add_filter( 'plugins_api', array( $this, 'inject_plugin_information' ), 20, 3 );
		add_filter( 'upgrader_source_selection', array( $this, 'rename_plugin_source_directory' ), 10, 4 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_cached_release_data' ), 10, 2 );
	}

	/**
	 * Adds update details to the plugin update transient.
	 *
	 * @param stdClass $transient Existing update transient.
	 * @return stdClass
	 */
	public function inject_update( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		if ( empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
			$transient->checked = array(
				TANGNEST_BEBRAS_BASENAME => TANGNEST_BEBRAS_VERSION,
			);
		}

		return $this->merge_update_into_transient( $transient );
	}

	/**
	 * Merges this plugin's update payload into the update_plugins transient.
	 *
	 * @param stdClass $transient Existing update transient.
	 * @return stdClass
	 */
	protected function merge_update_into_transient( $transient ) {
		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}

		if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
			$transient->no_update = array();
		}

		unset( $transient->response[ TANGNEST_BEBRAS_BASENAME ] );
		unset( $transient->no_update[ TANGNEST_BEBRAS_BASENAME ] );

		$installed_version = $this->get_installed_version( $transient );
		$update_offer      = $this->build_update_offer( $installed_version );

		if ( $update_offer ) {
			$transient->response[ TANGNEST_BEBRAS_BASENAME ] = $update_offer;
			$this->log_debug(
				'Update offer injected into update_plugins transient.',
				array(
					'basename'          => TANGNEST_BEBRAS_BASENAME,
					'installed_version' => $installed_version,
					'new_version'       => $update_offer->new_version,
				)
			);

			return $transient;
		}

		$transient->no_update[ TANGNEST_BEBRAS_BASENAME ] = $this->build_no_update_item( $installed_version );
		$this->log_debug(
			'No update offer available for current installed version.',
			array(
				'basename'          => TANGNEST_BEBRAS_BASENAME,
				'installed_version' => $installed_version,
			)
		);

		return $transient;
	}

	/**
	 * Builds the WordPress plugin update object when a newer release exists.
	 *
	 * @param string $installed_version Installed plugin version.
	 * @return object|null
	 */
	protected function build_update_offer( $installed_version ) {
		$release = $this->get_latest_release();

		if ( empty( $release ) ) {
			$this->log_debug( 'GitHub release lookup returned no release payload.' );
			return null;
		}

		$version = $this->normalize_version( $release['tag_name'] );

		if ( empty( $version ) ) {
			$this->log_debug(
				'GitHub release tag could not be normalized to a plugin version.',
				array(
					'tag_name' => isset( $release['tag_name'] ) ? (string) $release['tag_name'] : '',
				)
			);
			return null;
		}

		if ( version_compare( $version, $installed_version, '<=' ) ) {
			return null;
		}

		$package_url = $this->get_release_package_url( $release );

		if ( empty( $package_url ) ) {
			$this->log_debug(
				'GitHub release did not provide a usable package URL.',
				array(
					'tag_name' => isset( $release['tag_name'] ) ? (string) $release['tag_name'] : '',
				)
			);
			return null;
		}

		return (object) array(
			'id'          => $this->get_repository_url() . '#tangnest-bebras',
			'slug'        => dirname( TANGNEST_BEBRAS_BASENAME ),
			'plugin'      => TANGNEST_BEBRAS_BASENAME,
			'new_version' => $version,
			'url'         => $this->get_repository_url(),
			'package'     => $package_url,
			'icons'       => array(),
			'banners'     => array(),
			'banners_rtl' => array(),
			'tested'      => '',
			'requires'    => '',
			'requires_php'=> '',
			'compatibility' => new stdClass(),
		);
	}

	/**
	 * Builds the WordPress no_update object for this plugin.
	 *
	 * @param string $installed_version Installed plugin version.
	 * @return object
	 */
	protected function build_no_update_item( $installed_version ) {
		return (object) array(
			'id'          => $this->get_repository_url() . '#tangnest-bebras',
			'slug'        => dirname( TANGNEST_BEBRAS_BASENAME ),
			'plugin'      => TANGNEST_BEBRAS_BASENAME,
			'new_version' => $installed_version,
			'url'         => $this->get_repository_url(),
			'package'     => '',
			'icons'       => array(),
			'banners'     => array(),
			'banners_rtl' => array(),
			'tested'      => '',
			'requires'    => '',
			'requires_php'=> '',
			'compatibility' => new stdClass(),
		);
	}

	/**
	 * Determines the installed plugin version from the transient when possible.
	 *
	 * @param stdClass $transient Update transient.
	 * @return string
	 */
	protected function get_installed_version( $transient ) {
		if ( ! empty( $transient->checked[ TANGNEST_BEBRAS_BASENAME ] ) ) {
			return (string) $transient->checked[ TANGNEST_BEBRAS_BASENAME ];
		}

		return TANGNEST_BEBRAS_VERSION;
	}

	/**
	 * Adds plugin information to the updates modal.
	 *
	 * @param false|object|array $result Existing result.
	 * @param string             $action API action.
	 * @param object             $args   API arguments.
	 * @return false|object|array
	 */
	public function inject_plugin_information( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || dirname( TANGNEST_BEBRAS_BASENAME ) !== $args->slug ) {
			return $result;
		}

		$release = $this->get_latest_release();

		if ( empty( $release ) ) {
			return $result;
		}

		$version     = $this->normalize_version( $release['tag_name'] );
		$package_url = $this->get_release_package_url( $release );
		$sections    = array(
			'description'  => wp_kses_post( wpautop( __( 'Tangnest Bebras provides a modular WordPress foundation for future Bebras-style tasks and Tutor LMS integration.', 'tangnest-bebras' ) ) ),
			'installation' => wp_kses_post(
				wpautop(
					sprintf(
						/* translators: %s: GitHub branch name. */
						__( 'Upload the plugin zip in WordPress admin, activate the plugin, then use the Plugins screen to run a manual update check if needed. The plugin is configured to use the %s branch for repository metadata.', 'tangnest-bebras' ),
						esc_html( $this->get_repository_branch() )
					)
				)
			),
			'changelog'   => wp_kses_post( wpautop( ! empty( $release['body'] ) ? $release['body'] : __( 'Release notes are not available for this version yet.', 'tangnest-bebras' ) ) ),
		);

		return (object) array(
			'name'          => __( 'Tangnest Bebras', 'tangnest-bebras' ),
			'slug'          => dirname( TANGNEST_BEBRAS_BASENAME ),
			'version'       => ! empty( $version ) ? $version : TANGNEST_BEBRAS_VERSION,
			'author'        => '<a href="' . esc_url( $this->get_repository_url() ) . '">Tangnest</a>',
			'homepage'      => $this->get_repository_url(),
			'download_link' => $package_url,
			'sections'      => $sections,
			'banners'       => array(),
		);
	}

	/**
	 * Renames the extracted source folder when a GitHub package uses a tag-based directory name.
	 *
	 * @param string      $source        Extracted source path.
	 * @param string      $remote_source Remote source path.
	 * @param WP_Upgrader $upgrader      Upgrader instance.
	 * @param array       $hook_extra    Upgrade context.
	 * @return string|WP_Error
	 */
	public function rename_plugin_source_directory( $source, $remote_source, $upgrader, $hook_extra ) {
		unset( $remote_source, $upgrader );

		if ( empty( $hook_extra['plugin'] ) || TANGNEST_BEBRAS_BASENAME !== $hook_extra['plugin'] ) {
			return $source;
		}

		$expected_dir = trailingslashit( dirname( $source ) ) . dirname( TANGNEST_BEBRAS_BASENAME );

		if ( wp_normalize_path( $source ) === wp_normalize_path( $expected_dir ) ) {
			return $source;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			return $source;
		}

		if ( $wp_filesystem->exists( $expected_dir ) ) {
			$wp_filesystem->delete( $expected_dir, true );
		}

		if ( ! $wp_filesystem->move( $source, $expected_dir, true ) ) {
			return new WP_Error(
				'tangnest_bebras_update_failed',
				__( 'Tangnest Bebras could not prepare the updated plugin package.', 'tangnest-bebras' )
			);
		}

		return $expected_dir;
	}

	/**
	 * Clears cached release metadata after upgrades.
	 *
	 * @param WP_Upgrader $upgrader_object Upgrader instance.
	 * @param array       $options         Upgrade options.
	 * @return void
	 */
	public function clear_cached_release_data( $upgrader_object, $options ) {
		unset( $upgrader_object );

		if ( empty( $options['type'] ) || 'plugin' !== $options['type'] ) {
			return;
		}

		delete_transient( 'tangnest_bebras_github_release' );
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * Runs a manual update check for this plugin.
	 *
	 * @return array<string, string|bool>
	 */
	public function run_manual_check() {
		delete_transient( 'tangnest_bebras_github_release' );
		delete_site_transient( 'update_plugins' );

		if ( ! function_exists( 'wp_update_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/update.php';
		}

		if ( function_exists( 'wp_clean_plugins_cache' ) ) {
			wp_clean_plugins_cache( true );
		}

		wp_update_plugins();

		$transient = get_site_transient( 'update_plugins' );

		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		if ( empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
			$transient->checked = array(
				TANGNEST_BEBRAS_BASENAME => TANGNEST_BEBRAS_VERSION,
			);
		}

		$transient = $this->merge_update_into_transient( $transient );
		set_site_transient( 'update_plugins', $transient );

		$has_update = ! empty( $transient->response[ TANGNEST_BEBRAS_BASENAME ] );

		$this->log_debug(
			'Manual update check completed.',
			array(
				'basename'              => TANGNEST_BEBRAS_BASENAME,
				'has_update'            => $has_update,
				'transient_response_key'=> isset( $transient->response[ TANGNEST_BEBRAS_BASENAME ] ),
				'transient_no_update_key' => isset( $transient->no_update[ TANGNEST_BEBRAS_BASENAME ] ),
			)
		);

		if ( $has_update ) {
			return array(
				'status'     => 'update-available',
				'has_update' => true,
			);
		}

		return array(
			'status'     => 'no-update',
			'has_update' => false,
		);
	}

	/**
	 * Returns the configured repository URL.
	 *
	 * @return string
	 */
	protected function get_repository_url() {
		return TANGNEST_BEBRAS_GITHUB_REPO_URL;
	}

	/**
	 * Returns the configured repository branch.
	 *
	 * @return string
	 */
	protected function get_repository_branch() {
		return TANGNEST_BEBRAS_GITHUB_BRANCH;
	}

	/**
	 * Fetches the latest GitHub release with transient caching.
	 *
	 * @return array<string, mixed>
	 */
	protected function get_latest_release() {
		$cached = get_transient( 'tangnest_bebras_github_release' );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$repo = $this->parse_repository();

		if ( empty( $repo ) ) {
			return array();
		}

		$response = wp_remote_get(
			sprintf(
				'https://api.github.com/repos/%1$s/%2$s/releases/latest',
				rawurlencode( $repo['owner'] ),
				rawurlencode( $repo['repo'] )
			),
			array(
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'Tangnest-Bebras/' . TANGNEST_BEBRAS_VERSION,
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			$this->log_debug(
				'GitHub latest release request failed.',
				array(
					'status_code' => is_wp_error( $response ) ? 'wp_error' : (string) wp_remote_retrieve_response_code( $response ),
				)
			);
			return array();
		}

		$release = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $release ) || empty( $release['tag_name'] ) ) {
			$this->log_debug( 'GitHub latest release payload was invalid or missing tag_name.' );
			return array();
		}

		set_transient( 'tangnest_bebras_github_release', $release, 12 * HOUR_IN_SECONDS );

		return $release;
	}

	/**
	 * Returns repository owner and repo name.
	 *
	 * @return array<string, string>
	 */
	protected function parse_repository() {
		$url = untrailingslashit( $this->get_repository_url() );

		if ( empty( $url ) ) {
			return array();
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );

		if ( empty( $path ) ) {
			return array();
		}

		$parts = array_values( array_filter( explode( '/', trim( $path, '/' ) ) ) );

		if ( 2 > count( $parts ) ) {
			return array();
		}

		return array(
			'owner' => sanitize_text_field( $parts[0] ),
			'repo'  => sanitize_text_field( preg_replace( '/\.git$/', '', $parts[1] ) ),
		);
	}

	/**
	 * Normalizes a Git tag to a plugin version.
	 *
	 * @param string $tag Tag name.
	 * @return string
	 */
	protected function normalize_version( $tag ) {
		$version = ltrim( trim( (string) $tag ), 'vV' );

		return preg_match( '/^\d+(\.\d+){0,3}$/', $version ) ? $version : '';
	}

	/**
	 * Returns the preferred downloadable package URL from a release.
	 *
	 * @param array<string, mixed> $release Release payload.
	 * @return string
	 */
	protected function get_release_package_url( $release ) {
		if ( ! empty( $release['assets'] ) && is_array( $release['assets'] ) ) {
			foreach ( $release['assets'] as $asset ) {
				if ( empty( $asset['name'] ) || empty( $asset['browser_download_url'] ) ) {
					continue;
				}

				$name = strtolower( (string) $asset['name'] );

				if ( preg_match( '/^tangnest-bebras(?:-[0-9.]+)?\.zip$/', $name ) ) {
					return esc_url_raw( $asset['browser_download_url'] );
				}
			}
		}

		return ! empty( $release['zipball_url'] ) ? esc_url_raw( $release['zipball_url'] ) : '';
	}

	/**
	 * Writes updater debug details to error_log when debug logging is enabled.
	 *
	 * @param string               $message Log message.
	 * @param array<string, mixed> $context Optional context.
	 * @return void
	 */
	protected function log_debug( $message, $context = array() ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$line = '[Tangnest Bebras Updater] ' . $message;

		if ( ! empty( $context ) ) {
			$line .= ' ' . wp_json_encode( $context );
		}

		error_log( $line );
	}
}
