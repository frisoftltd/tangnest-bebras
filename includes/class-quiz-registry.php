<?php
/**
 * Quiz container registry.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores Bebras quiz containers and their tasks.
 */
class Tangnest_Bebras_Quiz_Registry {

	/**
	 * Registered quizzes.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	protected $quizzes = array();

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_default_quizzes' ), 6 );
	}

	/**
	 * Registers the built-in sample quizzes.
	 *
	 * @return void
	 */
	public function register_default_quizzes() {
		$this->quizzes = array();

		$this->register_quiz(
			'pre-course',
			array(
				'title'       => __( 'Pre-Course Bebras Quiz', 'tangnest-bebras' ),
				'quiz_type'   => 'pre',
				'description' => __( 'A short discovery quiz learners can take before starting a course.', 'tangnest-bebras' ),
				'tasks'       => array(
					array(
						'id'           => 'pre-pattern-paths',
						'title'        => __( 'Pattern Paths', 'tangnest-bebras' ),
						'task_type'    => 'multiple_choice_interactive',
						'introduction' => __( 'A robot follows repeating move patterns. Choose the best route before the course begins.', 'tangnest-bebras' ),
						'prompt'       => __( 'The robot repeats the pattern Right, Up, Right, Up. Which square will it land on after four moves if it starts at the bottom-left corner?', 'tangnest-bebras' ),
						'points'       => 5,
						'content'      => array(
							'choices' => array(
								array(
									'id'    => 'top-right',
									'label' => __( 'Top-right square', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'top-left',
									'label' => __( 'Top-left square', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'middle-right',
									'label' => __( 'Middle-right square', 'tangnest-bebras' ),
								),
							),
						),
						'solution'     => array(
							'choice_id' => 'top-right',
						),
					),
					array(
						'id'           => 'pre-code-order',
						'title'        => __( 'Code Order', 'tangnest-bebras' ),
						'task_type'    => 'sequence_order',
						'introduction' => __( 'Arrange the steps in the right order, like a beginner-friendly Bebras sequencing puzzle.', 'tangnest-bebras' ),
						'prompt'       => __( 'Put the steps in the order that makes sense for checking a secret code.', 'tangnest-bebras' ),
						'points'       => 5,
						'content'      => array(
							'items' => array(
								array(
									'id'    => 'read-clue',
									'label' => __( 'Read the clue cards', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'test-code',
									'label' => __( 'Test the code', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'build-code',
									'label' => __( 'Build the code from the clues', 'tangnest-bebras' ),
								),
							),
						),
						'solution'     => array(
							'order' => array(
								'read-clue',
								'build-code',
								'test-code',
							),
						),
					),
				),
			)
		);

		$this->register_quiz(
			'post-course',
			array(
				'title'       => __( 'Post-Course Bebras Quiz', 'tangnest-bebras' ),
				'quiz_type'   => 'post',
				'description' => __( 'A short wrap-up quiz learners can take after finishing a course.', 'tangnest-bebras' ),
				'tasks'       => array(
					array(
						'id'           => 'post-bus-stops',
						'title'        => __( 'Bus Stop Logic', 'tangnest-bebras' ),
						'task_type'    => 'multiple_choice_interactive',
						'introduction' => __( 'This placeholder task mirrors the quick logic choices common in Bebras activities.', 'tangnest-bebras' ),
						'prompt'       => __( 'A bus stops at every even-numbered station. If it starts at station 2, where does it stop next?', 'tangnest-bebras' ),
						'points'       => 5,
						'content'      => array(
							'choices' => array(
								array(
									'id'    => 'station-3',
									'label' => __( 'Station 3', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'station-4',
									'label' => __( 'Station 4', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'station-5',
									'label' => __( 'Station 5', 'tangnest-bebras' ),
								),
							),
						),
						'solution'     => array(
							'choice_id' => 'station-4',
						),
					),
					array(
						'id'           => 'post-sort-steps',
						'title'        => __( 'Sort the Strategy', 'tangnest-bebras' ),
						'task_type'    => 'sequence_order',
						'introduction' => __( 'Learners finish by organizing the reasoning steps needed to solve a small puzzle.', 'tangnest-bebras' ),
						'prompt'       => __( 'Order the strategy steps from first to last.', 'tangnest-bebras' ),
						'points'       => 5,
						'content'      => array(
							'items' => array(
								array(
									'id'    => 'spot-rules',
									'label' => __( 'Spot the rules', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'compare-options',
									'label' => __( 'Compare the options', 'tangnest-bebras' ),
								),
								array(
									'id'    => 'choose-answer',
									'label' => __( 'Choose the best answer', 'tangnest-bebras' ),
								),
							),
						),
						'solution'     => array(
							'order' => array(
								'spot-rules',
								'compare-options',
								'choose-answer',
							),
						),
					),
				),
			)
		);

		/**
		 * Allows future modules to register more quizzes.
		 */
		do_action( 'tangnest_bebras_register_quizzes', $this );

		$this->quizzes = apply_filters( 'tangnest_bebras_quizzes', $this->quizzes );
	}

	/**
	 * Registers a single quiz definition.
	 *
	 * @param string               $id   Quiz identifier.
	 * @param array<string, mixed> $quiz Quiz configuration.
	 * @return void
	 */
	public function register_quiz( $id, $quiz ) {
		$id = sanitize_key( $id );

		if ( empty( $id ) ) {
			return;
		}

		$defaults = array(
			'title'       => '',
			'quiz_type'   => 'pre',
			'description' => '',
			'tasks'       => array(),
		);

		$quiz = wp_parse_args( $quiz, $defaults );

		$quiz['id']        = $id;
		$quiz['quiz_type'] = in_array( $quiz['quiz_type'], array( 'pre', 'post' ), true ) ? $quiz['quiz_type'] : 'pre';
		$quiz['tasks']     = is_array( $quiz['tasks'] ) ? array_values( $quiz['tasks'] ) : array();

		$this->quizzes[ $id ] = $quiz;
	}

	/**
	 * Returns one quiz definition.
	 *
	 * @param string $id Quiz identifier.
	 * @return array<string, mixed>|null
	 */
	public function get_quiz( $id ) {
		$id = sanitize_key( $id );

		return isset( $this->quizzes[ $id ] ) ? $this->quizzes[ $id ] : null;
	}

	/**
	 * Returns all quiz definitions.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_quizzes() {
		return $this->quizzes;
	}
}
