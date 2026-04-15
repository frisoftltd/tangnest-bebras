/**
 * Drag-sequence interaction.
 *
 * Students arrange cards into the correct order by dragging.
 * Fallback: click-to-select then click-to-place when no drag is detected.
 *
 * DOM expected:
 *   .tnq-drag-sequence
 *     .tnq-sequence-area    (drop zone with ordered slots)
 *     .tnq-source-area      (shuffled source cards)
 *
 * Each card: <div class="tnq-card" data-item-id="a" draggable="true">
 */
TNQInteractions.dragSequence = (function () {

	function init(el) {
		const sequenceArea = el.querySelector('.tnq-sequence-area');
		const sourceArea   = el.querySelector('.tnq-source-area');
		const cards        = el.querySelectorAll('.tnq-card');

		let dragSrc  = null;
		let selected = null;    // click-to-select fallback state
		let hasDragged = false; // track whether any drag started

		// ── Drag events (mouse) ─────────────────────────────────
		cards.forEach(function (card) {
			card.setAttribute('draggable', 'true');

			card.addEventListener('dragstart', function (e) {
				hasDragged = true;
				dragSrc = card;
				card.classList.add('is-dragging');
				e.dataTransfer.effectAllowed = 'move';
				e.dataTransfer.setData('text/plain', card.dataset.itemId);
			});

			card.addEventListener('dragend', function () {
				card.classList.remove('is-dragging');
				el.querySelectorAll('.drag-over').forEach(function (z) {
					z.classList.remove('drag-over');
				});
				dragSrc = null;
			});
		});

		// Dragover / drop on slots and areas
		[sequenceArea].concat(Array.from(el.querySelectorAll('.tnq-sequence-slot'))).forEach(function (zone) {
			zone.addEventListener('dragover', function (e) {
				e.preventDefault();
				e.dataTransfer.dropEffect = 'move';
				zone.classList.add('drag-over');
			});
			zone.addEventListener('dragleave', function () {
				zone.classList.remove('drag-over');
			});
			zone.addEventListener('drop', function (e) {
				e.preventDefault();
				zone.classList.remove('drag-over');
				if (!dragSrc) return;

				const slot = zone.closest('.tnq-sequence-slot') || zone;
				const existingCard = slot.querySelector('.tnq-card');

				if (existingCard && existingCard !== dragSrc) {
					// Swap: move existing card to dragSrc's previous parent
					const srcParent = dragSrc.parentElement;
					srcParent.appendChild(existingCard);
				}

				slot.appendChild(dragSrc);
			});
		});

		// Also allow dropping back to source
		sourceArea.addEventListener('dragover', function (e) {
			e.preventDefault();
			sourceArea.classList.add('drag-over');
		});
		sourceArea.addEventListener('dragleave', function () {
			sourceArea.classList.remove('drag-over');
		});
		sourceArea.addEventListener('drop', function (e) {
			e.preventDefault();
			sourceArea.classList.remove('drag-over');
			if (dragSrc) sourceArea.appendChild(dragSrc);
		});

		// ── Click-to-select / click-to-place fallback ───────────
		function handleClick(card) {
			if (hasDragged) return; // user already dragging, skip

			if (!selected) {
				// Select this card
				selected = card;
				card.classList.add('is-selected');
				showDragHint(el, true);
			} else if (selected === card) {
				// Deselect
				card.classList.remove('is-selected');
				selected = null;
			} else {
				// Place selected card into this card's slot, and swap
				const aParent  = selected.parentElement;
				const bParent  = card.parentElement;
				const aNext    = selected.nextSibling;

				bParent.insertBefore(selected, card);
				if (aNext) {
					aParent.insertBefore(card, aNext);
				} else {
					aParent.appendChild(card);
				}

				selected.classList.remove('is-selected');
				selected = null;
				showDragHint(el, false);
			}
		}

		cards.forEach(function (card) {
			card.addEventListener('click', function () {
				handleClick(card);
			});
		});

		// Fallback hint: show after 5s if no drag
		setTimeout(function () {
			if (!hasDragged) {
				showDragHint(el, true);
			}
		}, 5000);
	}

	function showDragHint(el, show) {
		const hint = el.querySelector('.tnq-drag-hint');
		if (hint) {
			hint.classList.toggle('is-visible', show);
		}
	}

	/**
	 * Return the current order as array of item IDs.
	 * Only counts cards inside .tnq-sequence-slot elements.
	 */
	function getAnswer(el) {
		const slots = el.querySelectorAll('.tnq-sequence-slot');
		if (slots.length === 0) {
			// Flat sequence area — read order directly
			return Array.from(el.querySelectorAll('.tnq-sequence-area .tnq-card')).map(function (c) {
				return c.dataset.itemId;
			});
		}
		return Array.from(slots).map(function (slot) {
			const card = slot.querySelector('.tnq-card');
			return card ? card.dataset.itemId : null;
		}).filter(Boolean);
	}

	function validate(submitted, correct) {
		if (!Array.isArray(submitted) || !Array.isArray(correct)) return false;
		if (submitted.length !== correct.length) return false;
		return submitted.every(function (v, i) { return v === correct[i]; });
	}

	return { init: init, getAnswer: getAnswer, validate: validate };
}());
