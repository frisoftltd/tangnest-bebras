/**
 * Pattern-next interaction.
 *
 * Student views a pattern and selects what comes next.
 *
 * DOM expected:
 *   .tnq-pattern-next
 *     .tnq-pattern-row   (pattern items + blank slot)
 *     .tnq-choices
 *       .tnq-card[data-choice-id="a"] ...
 */
TNQInteractions.patternNext = (function () {

	function init(el) {
		const cards = el.querySelectorAll('.tnq-choices .tnq-card');
		const blank = el.querySelector('.tnq-pattern-blank');

		cards.forEach(function (card) {
			card.addEventListener('click', function () {
				// Deselect all
				cards.forEach(function (c) { c.classList.remove('is-selected'); });
				card.classList.add('is-selected');

				// Preview in the blank slot
				if (blank) {
					const icon = card.querySelector('.tnq-icon, .tnq-icon-missing');
					blank.innerHTML = '';
					if (icon) {
						blank.appendChild(icon.cloneNode(true));
					}
				}
			});
		});
	}

	function getAnswer(el) {
		const selected = el.querySelector('.tnq-choices .tnq-card.is-selected');
		return selected ? selected.dataset.choiceId : null;
	}

	function validate(submitted, correct) {
		return submitted === correct;
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
