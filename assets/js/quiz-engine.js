( function () {
	'use strict';

	const config = window.tangnestBebrasQuizEngine || { strings: {} };
	const strings = config.strings || {};

	const getString = ( key, fallback ) => strings[ key ] || fallback;

	const renderers = {
		multiple_choice_interactive: {
			render( task, answer ) {
				const choices = Array.isArray( task.content.choices ) ? task.content.choices : [];
				const selected = answer && answer.choiceId ? answer.choiceId : '';

				return `
					<div class="tangnest-bebras-task__choices" role="radiogroup" aria-label="${ escapeHtml( task.title ) }">
						${ choices.map( ( choice ) => `
							<button
								type="button"
								class="tangnest-bebras-choice${ selected === choice.id ? ' is-selected' : '' }"
								data-choice-id="${ escapeHtml( choice.id ) }"
							>
								<span class="tangnest-bebras-choice__marker" aria-hidden="true"></span>
								<span class="tangnest-bebras-choice__label">${ escapeHtml( choice.label ) }</span>
							</button>
						` ).join( '' ) }
					</div>
				`;
			},
			bind( root, state, task ) {
				root.querySelectorAll( '[data-choice-id]' ).forEach( ( button ) => {
					button.addEventListener( 'click', () => {
						state.answers[ task.id ] = { choiceId: button.getAttribute( 'data-choice-id' ) };
						state.validationMessage = '';
						state.render();
					} );
				} );
			},
			isComplete( answer ) {
				return !! ( answer && answer.choiceId );
			},
			evaluate( task, answer ) {
				return !! answer && answer.choiceId === task.solution.choice_id;
			},
		},
		sequence_order: {
			render( task, answer ) {
				const items = getOrderedItems( task, answer );

				return `
					<div class="tangnest-bebras-task__sequence">
						${ items.map( ( item, index ) => `
							<div class="tangnest-bebras-sequence-item" data-item-id="${ escapeHtml( item.id ) }">
								<div class="tangnest-bebras-sequence-item__position">${ index + 1 }</div>
								<div class="tangnest-bebras-sequence-item__label">${ escapeHtml( item.label ) }</div>
								<div class="tangnest-bebras-sequence-item__controls">
									<button type="button" class="tangnest-bebras-mini-button" data-move="up" data-item-id="${ escapeHtml( item.id ) }">↑</button>
									<button type="button" class="tangnest-bebras-mini-button" data-move="down" data-item-id="${ escapeHtml( item.id ) }">↓</button>
								</div>
							</div>
						` ).join( '' ) }
					</div>
				`;
			},
			bind( root, state, task ) {
				state.answers[ task.id ] = ensureSequenceAnswer( task, state.answers[ task.id ] );

				root.querySelectorAll( '[data-move]' ).forEach( ( button ) => {
					button.addEventListener( 'click', () => {
						const answer = ensureSequenceAnswer( task, state.answers[ task.id ] );
						const itemId = button.getAttribute( 'data-item-id' );
						const direction = button.getAttribute( 'data-move' );
						const index = answer.order.indexOf( itemId );

						if ( index < 0 ) {
							return;
						}

						const targetIndex = direction === 'up' ? index - 1 : index + 1;

						if ( targetIndex < 0 || targetIndex >= answer.order.length ) {
							return;
						}

						const nextOrder = answer.order.slice();
						const movedItem = nextOrder.splice( index, 1 )[0];
						nextOrder.splice( targetIndex, 0, movedItem );

						state.answers[ task.id ] = { order: nextOrder };
						state.validationMessage = '';
						state.render();
					} );
				} );
			},
			isComplete( answer, task ) {
				return !! answer && Array.isArray( answer.order ) && answer.order.length === ( Array.isArray( task.content.items ) ? task.content.items.length : 0 );
			},
			evaluate( task, answer ) {
				if ( ! answer || ! Array.isArray( answer.order ) || ! task.solution || ! Array.isArray( task.solution.order ) ) {
					return false;
				}

				return JSON.stringify( answer.order ) === JSON.stringify( task.solution.order );
			},
		},
		drag_and_drop: {
			render() {
				return `<div class="tangnest-bebras-task__placeholder">${ escapeHtml( getString( 'dragDropPlaceholder', 'Drag-and-drop task foundation is ready for a future interactive renderer.' ) ) }</div>`;
			},
			bind() {},
			isComplete() {
				return true;
			},
			evaluate() {
				return false;
			},
		},
		grid_logic_puzzle: {
			render() {
				return `<div class="tangnest-bebras-task__placeholder">${ escapeHtml( getString( 'gridPlaceholder', 'Grid and logic puzzle foundation is ready for a future interactive renderer.' ) ) }</div>`;
			},
			bind() {},
			isComplete() {
				return true;
			},
			evaluate() {
				return false;
			},
		},
	};

	function initQuiz( container ) {
		const payloadNode = container.querySelector( '.tangnest-bebras-quiz__payload' );
		const appNode = container.querySelector( '.tangnest-bebras-quiz__app' );

		if ( ! payloadNode || ! appNode ) {
			return;
		}

		let payload = {};

		try {
			payload = JSON.parse( payloadNode.textContent || '{}' );
		} catch ( error ) {
			appNode.innerHTML = `<p>${ escapeHtml( getString( 'unsupportedTask', 'This task type is not available yet.' ) ) }</p>`;
			return;
		}

		const state = {
			stage: 'intro',
			currentIndex: 0,
			answers: {},
			results: [],
			validationMessage: '',
			render() {
				renderQuiz( appNode, payload, state );
				bindQuiz( appNode, payload, state );
			},
		};

		state.render();
	}

	function renderQuiz( appNode, payload, state ) {
		if ( state.stage === 'intro' ) {
			appNode.innerHTML = renderIntro( payload );
			return;
		}

		if ( state.stage === 'results' ) {
			appNode.innerHTML = renderResults( payload, state );
			return;
		}

		appNode.innerHTML = renderActiveTask( payload, state );
	}

	function bindQuiz( appNode, payload, state ) {
		if ( state.stage === 'intro' ) {
			const startButton = appNode.querySelector( '[data-action="start-quiz"]' );

			if ( startButton ) {
				startButton.addEventListener( 'click', () => {
					state.stage = 'active';
					state.currentIndex = 0;
					state.validationMessage = '';
					state.render();
				} );
			}

			return;
		}

		if ( state.stage === 'results' ) {
			const restartButton = appNode.querySelector( '[data-action="restart-quiz"]' );

			if ( restartButton ) {
				restartButton.addEventListener( 'click', () => {
					state.stage = 'intro';
					state.currentIndex = 0;
					state.answers = {};
					state.results = [];
					state.validationMessage = '';
					state.render();
				} );
			}

			return;
		}

		const task = payload.tasks[ state.currentIndex ];
		const renderer = renderers[ task.frontendHandler || task.taskType ];

		if ( renderer ) {
			renderer.bind( appNode, state, task );
		}

		const actionButton = appNode.querySelector( '[data-action="advance-quiz"]' );

		if ( actionButton ) {
			actionButton.addEventListener( 'click', () => {
				const answer = state.answers[ task.id ];
				const isComplete = renderer && typeof renderer.isComplete === 'function'
					? renderer.isComplete( answer, task )
					: false;

				if ( ! isComplete ) {
					state.validationMessage = getString( 'unanswered', 'Please complete this task before continuing.' );
					state.render();
					return;
				}

				state.validationMessage = '';

				if ( state.currentIndex < payload.tasks.length - 1 ) {
					state.currentIndex += 1;
					state.render();
					return;
				}

				state.results = scoreQuiz( payload, state.answers );
				state.stage = 'results';
				state.render();
			} );
		}
	}

	function renderIntro( payload ) {
		return `
			<section class="tangnest-bebras-screen tangnest-bebras-screen--intro">
				<div class="tangnest-bebras-quiz__eyebrow">${ escapeHtml( payload.quizTypeLabel || ( payload.quizType === 'post' ? getString( 'postCourseQuiz', 'Post-Course Quiz' ) : getString( 'preCourseQuiz', 'Pre-Course Quiz' ) ) ) }</div>
				<h2 class="tangnest-bebras-quiz__title">${ escapeHtml( payload.title ) }</h2>
				<p class="tangnest-bebras-quiz__description">${ escapeHtml( payload.description || '' ) }</p>
				<div class="tangnest-bebras-quiz__summary">
					<div class="tangnest-bebras-quiz__summary-item">
						<span class="tangnest-bebras-quiz__summary-value">${ payload.tasks.length }</span>
						<span class="tangnest-bebras-quiz__summary-label">${ escapeHtml( getString( 'taskLabel', 'Task' ) ) }${ payload.tasks.length === 1 ? '' : 's' }</span>
					</div>
					<div class="tangnest-bebras-quiz__summary-item">
						<span class="tangnest-bebras-quiz__summary-value">${ getTotalPoints( payload.tasks ) }</span>
						<span class="tangnest-bebras-quiz__summary-label">${ escapeHtml( getString( 'pointsLabel', 'Points' ) ) }</span>
					</div>
				</div>
				<button type="button" class="tangnest-bebras-button" data-action="start-quiz">${ escapeHtml( getString( 'startQuiz', 'Start Quiz' ) ) }</button>
			</section>
		`;
	}

	function renderActiveTask( payload, state ) {
		const task = payload.tasks[ state.currentIndex ];
		const renderer = renderers[ task.frontendHandler || task.taskType ];
		const actionLabel = state.currentIndex === payload.tasks.length - 1
			? getString( 'submitQuiz', 'Submit Quiz' )
			: getString( 'nextTask', 'Next Task' );

		let taskMarkup = `<div class="tangnest-bebras-task__placeholder">${ escapeHtml( getString( 'unsupportedTask', 'This task type is not available yet.' ) ) }</div>`;

		if ( renderer ) {
			taskMarkup = renderer.render( task, state.answers[ task.id ], state );
		}

		return `
			<section class="tangnest-bebras-screen tangnest-bebras-screen--task">
				<div class="tangnest-bebras-quiz__progress">
					<div class="tangnest-bebras-quiz__progress-label">${ escapeHtml( getString( 'taskLabel', 'Task' ) ) } ${ state.currentIndex + 1 } ${ escapeHtml( getString( 'ofLabel', 'of' ) ) } ${ payload.tasks.length }</div>
					<div class="tangnest-bebras-quiz__progress-bar"><span style="width:${ ( ( state.currentIndex + 1 ) / payload.tasks.length ) * 100 }%"></span></div>
				</div>
				<div class="tangnest-bebras-task">
						<div class="tangnest-bebras-task__meta">
							<span class="tangnest-bebras-task__type">${ escapeHtml( task.taskTypeLabel || task.taskType ) }</span>
							<span class="tangnest-bebras-task__points">${ task.points } ${ escapeHtml( getString( 'pointsLabel', 'Points' ) ) }</span>
						</div>
					<h3 class="tangnest-bebras-task__title">${ escapeHtml( task.title ) }</h3>
					<p class="tangnest-bebras-task__intro">${ escapeHtml( task.introduction || '' ) }</p>
					<p class="tangnest-bebras-task__prompt">${ escapeHtml( task.prompt || '' ) }</p>
					<div class="tangnest-bebras-task__body">${ taskMarkup }</div>
					${ state.validationMessage ? `<p class="tangnest-bebras-task__validation">${ escapeHtml( state.validationMessage ) }</p>` : '' }
					<div class="tangnest-bebras-task__actions">
						<button type="button" class="tangnest-bebras-button" data-action="advance-quiz">${ escapeHtml( actionLabel ) }</button>
					</div>
				</div>
			</section>
		`;
	}

	function renderResults( payload, state ) {
		const totalPoints = getTotalPoints( payload.tasks );
		const earnedPoints = state.results.reduce( ( total, item ) => total + item.pointsEarned, 0 );

		return `
			<section class="tangnest-bebras-screen tangnest-bebras-screen--results">
				<div class="tangnest-bebras-quiz__eyebrow">${ escapeHtml( getString( 'quizCompleted', 'Quiz completed' ) ) }</div>
				<h2 class="tangnest-bebras-quiz__title">${ escapeHtml( getString( 'finalScore', 'Final Score' ) ) }</h2>
				<div class="tangnest-bebras-quiz__score">${ earnedPoints} / ${ totalPoints }</div>
				<div class="tangnest-bebras-quiz__results-list">
					${ state.results.map( ( result, index ) => `
						<div class="tangnest-bebras-result-card${ result.isCorrect ? ' is-correct' : ' is-incorrect' }">
							<div class="tangnest-bebras-result-card__index">${ index + 1 }</div>
							<div class="tangnest-bebras-result-card__content">
								<div class="tangnest-bebras-result-card__title">${ escapeHtml( result.title ) }</div>
								<div class="tangnest-bebras-result-card__status">${ escapeHtml( result.isCorrect ? getString( 'correctLabel', 'Correct' ) : getString( 'incorrectLabel', 'Needs review' ) ) }</div>
							</div>
							<div class="tangnest-bebras-result-card__points">${ result.pointsEarned } / ${ result.pointsPossible }</div>
						</div>
					` ).join( '' ) }
				</div>
				<button type="button" class="tangnest-bebras-button tangnest-bebras-button--secondary" data-action="restart-quiz">${ escapeHtml( getString( 'restartQuiz', 'Restart Quiz' ) ) }</button>
			</section>
		`;
	}

	function scoreQuiz( payload, answers ) {
		return payload.tasks.map( ( task ) => {
			const renderer = renderers[ task.frontendHandler || task.taskType ];
			const answer = answers[ task.id ];
			const isCorrect = renderer && typeof renderer.evaluate === 'function'
				? renderer.evaluate( task, answer )
				: false;

			return {
				taskId: task.id,
				title: task.title,
				isCorrect,
				pointsPossible: task.points,
				pointsEarned: isCorrect ? task.points : 0,
			};
		} );
	}

	function getTotalPoints( tasks ) {
		return tasks.reduce( ( total, task ) => total + ( Number( task.points ) || 0 ), 0 );
	}

	function getOrderedItems( task, answer ) {
		const items = Array.isArray( task.content.items ) ? task.content.items : [];
		const normalized = ensureSequenceAnswer( task, answer );

		return normalized.order
			.map( ( itemId ) => items.find( ( item ) => item.id === itemId ) )
			.filter( Boolean );
	}

	function ensureSequenceAnswer( task, answer ) {
		const items = Array.isArray( task.content.items ) ? task.content.items : [];
		const defaultOrder = items.map( ( item ) => item.id );

		if ( answer && Array.isArray( answer.order ) && answer.order.length === defaultOrder.length ) {
			return { order: answer.order.slice() };
		}

		return { order: defaultOrder };
	}

	function escapeHtml( value ) {
		return String( value )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#039;' );
	}

	document.addEventListener( 'DOMContentLoaded', () => {
		document.querySelectorAll( '.tangnest-bebras-quiz' ).forEach( initQuiz );
	} );
}() );
