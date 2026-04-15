/**
 * Click-color interaction.
 *
 * Students select a color from a palette then click SVG regions to paint them.
 * Validation is by adjacency rule (no two adjacent regions share a color).
 *
 * DOM expected:
 *   .tnq-click-color
 *     .tnq-color-palette
 *       .tnq-color-item
 *         .tnq-color-swatch[data-color="#hex"]
 *     .tnq-svg-canvas
 *       <svg> with [data-region="..."] elements
 */
TNQInteractions.clickColor = (function () {

	function init(el) {
		const swatches    = el.querySelectorAll('.tnq-color-swatch');
		const svgCanvas   = el.querySelector('.tnq-svg-canvas svg');
		const regions     = svgCanvas ? svgCanvas.querySelectorAll('[data-region]') : [];

		let activeColor   = null;

		// Select color from palette
		swatches.forEach(function (swatch) {
			swatch.addEventListener('click', function () {
				swatches.forEach(function (s) { s.classList.remove('is-active'); });
				swatch.classList.add('is-active');
				activeColor = swatch.dataset.color;
			});
		});

		// Paint region on click
		regions.forEach(function (region) {
			region.style.cursor = 'pointer';
			region.addEventListener('click', function () {
				if (!activeColor) return;
				const regionName = region.dataset.region;
				if (!regionName || regionName === 'bg') {
					// Paint background — find the bg rect/path
					if (regionName === 'bg') {
						region.setAttribute('fill', activeColor);
					}
					return;
				}
				region.setAttribute('fill', activeColor);
			});

			// Touch support
			region.addEventListener('touchend', function (e) {
				e.preventDefault();
				if (!activeColor) return;
				region.setAttribute('fill', activeColor);
			}, { passive: false });
		});

		// Also handle bg click
		regions.forEach(function (region) {
			if (region.dataset.region === 'bg') {
				region.addEventListener('click', function () {
					if (activeColor) region.setAttribute('fill', activeColor);
				});
			}
		});
	}

	/**
	 * Return object mapping region name → current fill color.
	 */
	function getAnswer(el) {
		const svgCanvas = el.querySelector('.tnq-svg-canvas svg');
		if (!svgCanvas) return {};

		const result = {};
		svgCanvas.querySelectorAll('[data-region]').forEach(function (region) {
			const name = region.dataset.region;
			if (name && !result[name]) {
				// Use computed fill or attribute
				const fill = region.getAttribute('fill');
				result[name] = fill || '';
			}
		});
		return result;
	}

	/**
	 * Validate by adjacency rule: every region must be painted AND
	 * no two adjacent regions share the same color.
	 *
	 * correct = adjacency array from question data (passed as JSON string in data-answer).
	 * submitted = object from getAnswer().
	 */
	function validate(submitted, correct) {
		// correct is the adjacency pairs array: [['bg','body'], ...]
		if (!correct || !Array.isArray(correct)) return false;

		// All regions must have a color
		const values = Object.values(submitted);
		if (values.some(function (v) { return !v || v === 'transparent' || v === 'none'; })) return false;

		// No adjacent pair may share a color
		return correct.every(function (pair) {
			const colorA = submitted[pair[0]];
			const colorB = submitted[pair[1]];
			return colorA && colorB && colorA !== colorB;
		});
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
