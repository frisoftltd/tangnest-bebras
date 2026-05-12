<?php
/**
 * Registers the CT Assessments top-level admin menu and all subpages.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Admin_Menu {

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'register_menus' ] );
	}

	public function register_menus(): void {
		add_menu_page(
			__( 'CT Assessments', 'tangnest-bebras' ),
			__( 'CT Assessments', 'tangnest-bebras' ),
			'manage_options',
			'tnq-overview',
			[ $this, 'render_overview' ],
			'dashicons-welcome-learn-more',
			30
		);

		add_submenu_page(
			'tnq-overview',
			__( 'Overview', 'tangnest-bebras' ),
			__( 'Overview', 'tangnest-bebras' ),
			'manage_options',
			'tnq-overview',
			[ $this, 'render_overview' ]
		);

		add_submenu_page(
			'tnq-overview',
			__( 'All Results', 'tangnest-bebras' ),
			__( 'All Results', 'tangnest-bebras' ),
			'manage_options',
			'tnq-results',
			[ $this, 'render_results' ]
		);

		add_submenu_page(
			'tnq-overview',
			__( 'Student Detail', 'tangnest-bebras' ),
			__( 'Student Detail', 'tangnest-bebras' ),
			'manage_options',
			'tnq-student-detail',
			[ $this, 'render_student_detail' ]
		);

		add_submenu_page(
			'tnq-overview',
			__( 'Settings', 'tangnest-bebras' ),
			__( 'Settings', 'tangnest-bebras' ),
			'manage_options',
			'tnq-settings',
			[ $this, 'render_settings' ]
		);

		add_submenu_page(
			'tnq-overview',
			__( 'Export', 'tangnest-bebras' ),
			__( 'Export', 'tangnest-bebras' ),
			'manage_options',
			'tnq-export',
			[ $this, 'render_export' ]
		);

	}

	public function render_overview(): void {
		( new TNQ_Admin_Overview() )->render();
	}

	public function render_results(): void {
		( new TNQ_Admin_Results() )->render();
	}

	public function render_student_detail(): void {
		require TNQ_PLUGIN_DIR . 'admin/views/student-detail.php';
	}

	public function render_settings(): void {
		require TNQ_PLUGIN_DIR . 'admin/views/settings.php';
	}

	public function render_export(): void {
		require TNQ_PLUGIN_DIR . 'admin/views/export.php';
	}

}
