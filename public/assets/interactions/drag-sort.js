/**
 * Drag-sort interaction.
 *
 * Students drag items from a source pool into labelled bins.
 * Fallback: click-to-select, click-bin to place.
 *
 * DOM expected:
 *   .tnq-drag-sort
 *     .tnq-sort-source
 *       .tnq-card[data-item-id="ange"] ...
 *     .tnq-sort-bins
 *       .tnq-bin[data-bin-index="0"]
 *         .tnq-bin-label ...
 *         .tnq-bin-items
 *       .tnq-bin[data-bin-index="1"] ...
 */
TNQInteractions.dragSort = (function () {

	function init(el) {
		const source   = el.querySelector('.tnq-sort-source');
		const bins     = el.querySelectorAll('.tnq-bin');
		let dragSrc    = null;
		let selected   = null;
		let hasDragged = false;

		function getDropZones() {
			return Array.from(bins).map(function (b) { return b.querySelector('.tnq-bin-items'); }).concat(source);
		}

		// ── Drag (mouse / pointer) ───────────────────────────────
		function onDragStart(e) {
			hasDragged = true;
			dragSrc = e.currentTarget;
			dragSrc.classList.add('is-dragging');
			e.dataTransfer.effectAllowed = 'move';
		}

		function onDragEnd() {
			if (dragSrc) dragSrc.classList.remove('is-dragging');
			el.querySelectorAll('.drag-over').forEach(function (z) { z.classList.remove('drag-over'); });
			dragSrc = null;
		}

		function onDragOver(e) {
			e.preventDefault();
			e.currentTarget.classList.add('drag-over');
		}

		function onDragLeave() {
			this.classList.remove('drag-over');
		}

		function onDrop(e) {
			e.preventDefault();
			e.currentTarget.classList.remove('drag-over');
			if (dragSrc) {
				e.currentTarget.appendChild(dragSrc);
			}
		}

		// Wire up cards in source
		function wireCard(card) {
			card.setAttribute('draggable', 'true');
			card.addEventListener('dragstart', onDragStart);
			card.addEventListener('dragend', onDragEnd);
		}

		el.querySelectorAll('.tnq-card').forEach(wireCard);

		// Wire drop zones (bin-items + source)
		getDropZones().forEach(function (zone) {
			zone.addEventListener('dragover', onDragOver);
			zone.addEventListener('dragleave', onDragLeave);
			zone.addEventListener('drop', onDrop);
		});

		// ── Click-to-select / click-bin fallback ─────────────────
		el.querySelectorAll('.tnq-card').forEach(function (card) {
			card.addEventListener('click', function () {
				if (hasDragged) return;
				if (selected === card) {
					card.classList.remove('is-selected');
					selected = null;
					return;
				}
				el.querySelectorAll('.tnq-card.is-selected').forEach(function (c) { c.classList.remove('is-selected'); });
				selected = card;
				card.classList.add('is-selected');
				showDragHint(el, true);
			});
		});

		bins.forEach(function (bin) {
			const binItems = bin.querySelector('.tnq-bin-items');
			bin.addEventListener('click', function () {
				if (hasDragged || !selected) return;
				binItems.appendChild(selected);
				selected.classList.remove('is-selected');
				selected = null;
				showDragHint(el, false);
			});
		});

		// Also allow clicking source to return items
		source.addEventListener('click', function () {
			if (hasDragged || !selected) return;
			source.appendChild(selected);
			selected.classList.remove('is-selected');
			selected = null;
		});

		// Show fallback hint after 5s if no drag
		setTimeout(function () {
			if (!hasDragged) showDragHint(el, true);
		}, 5000);
	}

	function showDragHint(el, show) {
		const hint = el.querySelector('.tnq-drag-hint');
		if (hint) hint.classList.toggle('is-visible', show);
	}

	/**
	 * Return object mapping itemId → binIndex (number).
	 */
	function getAnswer(el) {
		const result = {};
		el.querySelectorAll('.tnq-bin').forEach(function (bin) {
			const binIndex = parseInt(bin.dataset.binIndex, 10);
			bin.querySelectorAll('.tnq-card').forEach(function (card) {
				result[card.dataset.itemId] = binIndex;
			});
		});
		return result;
	}

	/**
	 * Validate: every item must be placed in its correct bin.
	 * correct = object mapping itemId → binIndex (from question data).
	 */
	function validate(submitted, correct) {
		if (!submitted || !correct) return false;
		return Object.keys(correct).every(function (id) {
			return submitted[id] === correct[id];
		});
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
