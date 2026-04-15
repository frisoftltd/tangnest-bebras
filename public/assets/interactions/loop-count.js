/**
 * Loop-count interaction.
 *
 * Students use +/- buttons to set a numeric answer.
 *
 * DOM expected:
 *   .tnq-loop-count
 *     .tnq-counter-btn[data-dir="-"]
 *     .tnq-counter-value
 *     .tnq-counter-btn[data-dir="+"]
 *
 * data-min / data-max / data-initial on the container.
 */
TNQInteractions.loopCount = (function () {

	function init(el) {
		const valueEl = el.querySelector('.tnq-counter-value');
		const btnMinus = el.querySelector('.tnq-counter-btn[data-dir="-"]');
		const btnPlus  = el.querySelector('.tnq-counter-btn[data-dir="+"]');

		const min     = parseInt(el.dataset.min, 10) || 1;
		const max     = parseInt(el.dataset.max, 10) || 30;
		let   current = parseInt(el.dataset.initial, 10) || min;

		function render() {
			valueEl.textContent = current;
			if (btnMinus) btnMinus.disabled = current <= min;
			if (btnPlus)  btnPlus.disabled  = current >= max;
		}

		if (btnMinus) {
			btnMinus.addEventListener('click', function () {
				if (current > min) { current--; render(); }
			});
		}
		if (btnPlus) {
			btnPlus.addEventListener('click', function () {
				if (current < max) { current++; render(); }
			});
		}

		// Keyboard support when counter has focus
		el.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowUp'   || e.key === '+') { if (current < max) { current++; render(); e.preventDefault(); } }
			if (e.key === 'ArrowDown' || e.key === '-') { if (current > min) { current--; render(); e.preventDefault(); } }
		});

		render();
	}

	function getAnswer(el) {
		const valueEl = el.querySelector('.tnq-counter-value');
		return valueEl ? parseInt(valueEl.textContent, 10) : 0;
	}

	function validate(submitted, correct) {
		return parseInt(submitted, 10) === parseInt(correct, 10);
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
