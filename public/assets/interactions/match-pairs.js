/**
 * Match-pairs interaction.
 *
 * Click a left item, then click a right item to create a pair.
 * Correct pairs lock green; wrong pairs shake briefly in practice mode.
 *
 * DOM expected:
 *   .tnq-match-pairs[data-mode="practice|assessment"]
 *     .tnq-pairs-workspace
 *       .tnq-pairs-col.tnq-pairs-left
 *         .tnq-card[data-pair-id="door"] ...
 *       .tnq-pairs-connectors
 *         svg (line canvas, injected by JS)
 *       .tnq-pairs-col.tnq-pairs-right
 *         .tnq-card[data-pair-id="key"] ...
 */
TNQInteractions.matchPairs = (function () {

	function init(el) {
		const leftCards  = Array.from(el.querySelectorAll('.tnq-pairs-left .tnq-card'));
		const rightCards = Array.from(el.querySelectorAll('.tnq-pairs-right .tnq-card'));
		const connDiv    = el.querySelector('.tnq-pairs-connectors');
		const mode       = el.closest('.tnq-quiz') ? el.closest('.tnq-quiz').dataset.mode : 'assessment';

		// Correct pairs data from data-pairs attribute (JSON)
		const correctPairs = JSON.parse(el.dataset.pairs || '[]');

		// State
		let selectedLeft = null;
		let matched      = {}; // left-id → right-id

		// SVG canvas for connection lines
		let lineSvg = null;
		if (connDiv) {
			lineSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
			lineSvg.setAttribute('overflow', 'visible');
			lineSvg.style.width  = '100%';
			lineSvg.style.height = '100%';
			connDiv.appendChild(lineSvg);
		}

		function isCorrectPair(leftId, rightId) {
			return correctPairs.some(function (pair) {
				return pair[0] === leftId && pair[1] === rightId;
			});
		}

		function lockPair(leftCard, rightCard, correct) {
			leftCard.classList.add('is-matched', 'is-locked', correct ? 'is-correct' : '');
			rightCard.classList.add('is-matched', 'is-locked', correct ? 'is-correct' : '');
			matched[leftCard.dataset.pairId] = rightCard.dataset.pairId;
			drawLine(leftCard, rightCard, correct ? '#1E8449' : '#C0392B');
		}

		function drawLine(leftCard, rightCard, color) {
			if (!lineSvg || !connDiv) return;

			const containerRect = connDiv.getBoundingClientRect();
			const leftRect  = leftCard.getBoundingClientRect();
			const rightRect = rightCard.getBoundingClientRect();

			const x1 = leftRect.right  - containerRect.left;
			const y1 = leftRect.top    - containerRect.top + leftRect.height  / 2;
			const x2 = rightRect.left  - containerRect.left;
			const y2 = rightRect.top   - containerRect.top + rightRect.height / 2;

			const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
			line.setAttribute('x1', x1);
			line.setAttribute('y1', y1);
			line.setAttribute('x2', x2);
			line.setAttribute('y2', y2);
			line.setAttribute('stroke', color || '#1A56A0');
			line.setAttribute('stroke-width', '3');
			line.setAttribute('stroke-linecap', 'round');
			lineSvg.appendChild(line);
		}

		leftCards.forEach(function (card) {
			card.addEventListener('click', function () {
				if (card.classList.contains('is-locked')) return;
				leftCards.forEach(function (c) { c.classList.remove('is-selected'); });
				selectedLeft = card;
				card.classList.add('is-selected');
			});
		});

		rightCards.forEach(function (card) {
			card.addEventListener('click', function () {
				if (!selectedLeft) return;
				if (card.classList.contains('is-locked')) return;

				const leftId  = selectedLeft.dataset.pairId;
				const rightId = card.dataset.pairId;
				const correct = isCorrectPair(leftId, rightId);

				if (correct) {
					lockPair(selectedLeft, card, true);
					selectedLeft.classList.remove('is-selected');
					selectedLeft = null;

					// Auto-complete: if only one pair remains, lock it
					const unlockedLeft  = leftCards.filter(function (c) { return !c.classList.contains('is-locked'); });
					const unlockedRight = rightCards.filter(function (c) { return !c.classList.contains('is-locked'); });
					if (unlockedLeft.length === 1 && unlockedRight.length === 1) {
						lockPair(unlockedLeft[0], unlockedRight[0], true);
					}
				} else {
					if (mode === 'practice') {
						// Shake both
						[selectedLeft, card].forEach(function (c) {
							c.classList.add('tnq-shake');
							setTimeout(function () { c.classList.remove('tnq-shake'); }, 500);
						});
					}
					selectedLeft.classList.remove('is-selected');
					selectedLeft = null;
				}
			});
		});
	}

	/**
	 * Return array of [leftId, rightId] pairs.
	 */
	function getAnswer(el) {
		const result = [];
		el.querySelectorAll('.tnq-pairs-left .tnq-card.is-matched').forEach(function (card) {
			const leftId  = card.dataset.pairId;
			const rightId = card.dataset.matchedTo;
			if (leftId && rightId) result.push([leftId, rightId]);
		});

		// Alternative: derive from locked pairs by checking which right card
		// shares a connector line — use matched state stored on cards.
		el.querySelectorAll('.tnq-pairs-left .tnq-card.is-locked').forEach(function (lCard) {
			if (lCard.dataset.matchedTo) return; // already handled
			const leftId = lCard.dataset.pairId;
			// Find the corresponding right card with same matched marker
			el.querySelectorAll('.tnq-pairs-right .tnq-card.is-locked').forEach(function (rCard) {
				if (rCard.dataset.matchedFrom === leftId) {
					result.push([leftId, rCard.dataset.pairId]);
				}
			});
		});

		return result;
	}

	function validate(submitted, correct) {
		if (!Array.isArray(submitted) || !Array.isArray(correct)) return false;
		if (submitted.length !== correct.length) return false;
		return correct.every(function (correctPair) {
			return submitted.some(function (sub) {
				return sub[0] === correctPair[0] && sub[1] === correctPair[1];
			});
		});
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
