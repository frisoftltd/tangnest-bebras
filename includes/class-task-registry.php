<?php
/**
 * Future task type registry.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores the plugin's task type definitions.
 */
class Tangnest_Bebras_Task_Registry {

	/**
	 * Registered task types.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	protected $task_types = array();

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_default_task_types' ), 5 );
	}

	/**
	 * Registers built-in placeholder task types.
	 *
	 * @return void
	 */
	public function register_default_task_types() {
		$this->task_types = array();

		$this->register_task_type(
			'multiple_choice_interactive',
			array(
				'label'               => __( 'Multiple Choice Interactive', 'tangnest-bebras' ),
				'description'         => __( 'Interactive choice tasks rendered by the Bebras quiz engine foundation.', 'tangnest-bebras' ),
				'render_callback'     => null,
				'evaluate_callback'   => null,
				'editor_schema'       => array(),
				'frontend_handler'    => 'multiple_choice_interactive',
				'supports_tutor_lms'  => true,
				'is_implemented'      => true,
			)
		);

		$this->register_task_type(
			'drag_and_drop',
			array(
				'label'               => __( 'Drag and Drop', 'tangnest-bebras' ),
				'description'         => __( 'Placeholder for future drag-and-drop Bebras tasks.', 'tangnest-bebras' ),
				'render_callback'     => null,
				'evaluate_callback'   => null,
				'editor_schema'       => array(),
				'frontend_handler'    => 'drag_and_drop',
				'supports_tutor_lms'  => true,
				'is_implemented'      => false,
			)
		);

		$this->register_task_type(
			'sequence_order',
			array(
				'label'               => __( 'Sequence / Order', 'tangnest-bebras' ),
				'description'         => __( 'Sequencing tasks rendered by the Bebras quiz engine foundation.', 'tangnest-bebras' ),
				'render_callback'     => null,
				'evaluate_callback'   => null,
				'editor_schema'       => array(),
				'frontend_handler'    => 'sequence_order',
				'supports_tutor_lms'  => true,
				'is_implemented'      => true,
			)
		);

		$this->register_task_type(
			'grid_logic_puzzle',
			array(
				'label'               => __( 'Grid / Logic Puzzle', 'tangnest-bebras' ),
				'description'         => __( 'Placeholder for future grid and logic puzzle tasks.', 'tangnest-bebras' ),
				'render_callback'     => null,
				'evaluate_callback'   => null,
				'editor_schema'       => array(),
				'frontend_handler'    => 'grid_logic_puzzle',
				'supports_tutor_lms'  => true,
				'is_implemented'      => false,
			)
		);

		/**
		 * Allows future task modules to register more task types.
		 */
		do_action( 'tangnest_bebras_register_task_types', $this );

		$this->task_types = apply_filters( 'tangnest_bebras_task_types', $this->task_types );
	}

	/**
	 * Registers one task type definition.
	 *
	 * @param string               $slug Task type slug.
	 * @param array<string, mixed> $args Task type configuration.
	 * @return void
	 */
	public function register_task_type( $slug, $args ) {
		$defaults = array(
			'label'              => '',
			'description'        => '',
			'render_callback'    => null,
			'evaluate_callback'  => null,
			'editor_schema'      => array(),
			'frontend_handler'   => '',
			'supports_tutor_lms' => false,
			'is_implemented'     => false,
		);

		$this->task_types[ sanitize_key( $slug ) ] = wp_parse_args( $args, $defaults );
	}

	/**
	 * Returns all task types.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_task_types() {
		return $this->task_types;
	}

	/**
	 * Returns a single task type definition.
	 *
	 * @param string $slug Task type slug.
	 * @return array<string, mixed>|null
	 */
	public function get_task_type( $slug ) {
		$slug = sanitize_key( $slug );

		return isset( $this->task_types[ $slug ] ) ? $this->task_types[ $slug ] : null;
	}
}
