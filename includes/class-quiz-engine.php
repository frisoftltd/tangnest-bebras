<?php
/**
 * Frontend quiz engine.
 *
 * @package TangnestBebras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders Bebras quiz containers via shortcode.
 */
class Tangnest_Bebras_Quiz_Engine {

	/**
	 * Quiz registry.
	 *
	 * @var Tangnest_Bebras_Quiz_Registry
	 */
	protected $quiz_registry;

	/**
	 * Task registry.
	 *
	 * @var Tangnest_Bebras_Task_Registry
	 */
	protected $task_registry;

	/**
	 * Constructor.
	 *
	 * @param Tangnest_Bebras_Quiz_Registry $quiz_registry Quiz registry.
	 * @param Tangnest_Bebras_Task_Registry $task_registry Task registry.
	 */
	public function __construct( Tangnest_Bebras_Quiz_Registry $quiz_registry, Tangnest_Bebras_Task_Registry $task_registry ) {
		$this->quiz_registry = $quiz_registry;
		$this->task_registry = $task_registry;
	}

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_shortcode( 'tangnest_bebras_quiz', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Registers frontend assets.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'tangnest-bebras-quiz-engine',
			TANGNEST_BEBRAS_URL . 'assets/css/quiz-engine.css',
			array(),
			TANGNEST_BEBRAS_VERSION
		);

		wp_register_script(
			'tangnest-bebras-quiz-engine',
			TANGNEST_BEBRAS_URL . 'assets/js/quiz-engine.js',
			array(),
			TANGNEST_BEBRAS_VERSION,
			true
		);

		wp_localize_script(
			'tangnest-bebras-quiz-engine',
			'tangnestBebrasQuizEngine',
			array(
				'strings' => array(
					'startQuiz'           => __( 'Start Quiz', 'tangnest-bebras' ),
					'nextTask'            => __( 'Next Task', 'tangnest-bebras' ),
					'submitQuiz'          => __( 'Submit Quiz', 'tangnest-bebras' ),
					'restartQuiz'         => __( 'Restart Quiz', 'tangnest-bebras' ),
					'quizCompleted'       => __( 'Quiz completed', 'tangnest-bebras' ),
					'finalScore'          => __( 'Final Score', 'tangnest-bebras' ),
					'taskLabel'           => __( 'Task', 'tangnest-bebras' ),
					'pointsLabel'         => __( 'Points', 'tangnest-bebras' ),
					'ofLabel'             => __( 'of', 'tangnest-bebras' ),
					'preCourseQuiz'       => __( 'Pre-Course Quiz', 'tangnest-bebras' ),
					'postCourseQuiz'      => __( 'Post-Course Quiz', 'tangnest-bebras' ),
					'unanswered'          => __( 'Please complete this task before continuing.', 'tangnest-bebras' ),
					'unsupportedTask'     => __( 'This task type is not available yet.', 'tangnest-bebras' ),
					'dragDropPlaceholder' => __( 'Drag-and-drop task foundation is ready for a future interactive renderer.', 'tangnest-bebras' ),
					'gridPlaceholder'     => __( 'Grid and logic puzzle foundation is ready for a future interactive renderer.', 'tangnest-bebras' ),
					'correctLabel'        => __( 'Correct', 'tangnest-bebras' ),
					'incorrectLabel'      => __( 'Needs review', 'tangnest-bebras' ),
				),
			)
		);
	}

	/**
	 * Renders the quiz shortcode.
	 *
	 * @param array<string, mixed> $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'tangnest_bebras_quiz'
		);

		$quiz = $this->quiz_registry->get_quiz( $atts['id'] );

		if ( empty( $quiz ) ) {
			return sprintf(
				'<div class="tangnest-bebras-quiz tangnest-bebras-quiz--error">%s</div>',
				esc_html__( 'Tangnest Bebras quiz not found.', 'tangnest-bebras' )
			);
		}

		wp_enqueue_style( 'tangnest-bebras-quiz-engine' );
		wp_enqueue_script( 'tangnest-bebras-quiz-engine' );

		$instance_id = 'tangnest-bebras-quiz-' . wp_unique_id();
		$payload     = $this->build_quiz_payload( $quiz );

		ob_start();
		require TANGNEST_BEBRAS_PATH . 'templates/quiz-shortcode.php';
		return (string) ob_get_clean();
	}

	/**
	 * Builds the frontend payload for one quiz.
	 *
	 * @param array<string, mixed> $quiz Quiz definition.
	 * @return array<string, mixed>
	 */
	protected function build_quiz_payload( $quiz ) {
		$tasks = array();

		foreach ( $quiz['tasks'] as $task ) {
			if ( empty( $task['task_type'] ) || empty( $task['id'] ) ) {
				continue;
			}

			$task_type = $this->task_registry->get_task_type( $task['task_type'] );

			$tasks[] = array(
				'id'               => sanitize_key( $task['id'] ),
				'title'            => isset( $task['title'] ) ? (string) $task['title'] : '',
				'taskType'         => sanitize_key( $task['task_type'] ),
				'points'           => isset( $task['points'] ) ? (int) $task['points'] : 0,
				'introduction'     => isset( $task['introduction'] ) ? (string) $task['introduction'] : '',
				'prompt'           => isset( $task['prompt'] ) ? (string) $task['prompt'] : '',
				'content'          => isset( $task['content'] ) && is_array( $task['content'] ) ? $task['content'] : array(),
				'solution'         => isset( $task['solution'] ) && is_array( $task['solution'] ) ? $task['solution'] : array(),
				'frontendHandler'  => ! empty( $task_type['frontend_handler'] ) ? (string) $task_type['frontend_handler'] : sanitize_key( $task['task_type'] ),
				'taskTypeLabel'    => ! empty( $task_type['label'] ) ? (string) $task_type['label'] : '',
				'taskDescription'  => ! empty( $task_type['description'] ) ? (string) $task_type['description'] : '',
				'isImplemented'    => ! empty( $task_type['is_implemented'] ),
				'supportsTutorLms' => ! empty( $task_type['supports_tutor_lms'] ),
			);
		}

		return array(
			'id'            => $quiz['id'],
			'title'         => isset( $quiz['title'] ) ? (string) $quiz['title'] : '',
			'quizType'      => isset( $quiz['quiz_type'] ) ? (string) $quiz['quiz_type'] : 'pre',
			'quizTypeLabel' => 'post' === $quiz['quiz_type'] ? __( 'Post-Course Quiz', 'tangnest-bebras' ) : __( 'Pre-Course Quiz', 'tangnest-bebras' ),
			'description'   => isset( $quiz['description'] ) ? (string) $quiz['description'] : '',
			'tasks'         => $tasks,
			'meta'          => array(
				'futureIntegration' => array(
					'tutorLmsReady' => true,
					'storageMode'   => 'hardcoded_foundation',
				),
			),
		);
	}
}
