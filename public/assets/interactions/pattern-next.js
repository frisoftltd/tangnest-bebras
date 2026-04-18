/**
 * Pattern-next interaction — Q4 (What Comes Next?).
 *
 * Children view a repeating bead pattern and select the next item.
 * Choice cards may use PNG images (data-png / data-active-png) or SVG icons.
 * Selecting a card swaps to the active/highlighted image version.
 *
 * DOM expected:
 *   .tnq-pattern-next
 *     .tnq-pattern-row   (pattern beads + blank slot)
 *     [.tnq-choices] or inline choices
 *       .tnq-choice-card[data-choice-id][data-png?][data-active-png?] ...
 *       OR .tnq-card[data-choice-id] ...
 */
window.TNQInteractions = window.TNQInteractions || {};
TNQInteractions.patternNext = (function () {

    function fireInteracted(el) {
        el.dispatchEvent(new CustomEvent('tnq:interacted', { bubbles: true }));
    }

    function init(el) {
        // Support both .tnq-choice-card (PNG) and .tnq-card (SVG) choice elements
        var cards = Array.from(el.querySelectorAll('.tnq-choice-card, .tnq-choices .tnq-card'));
        var blank = el.querySelector('.tnq-pattern-blank');

        var interacted = false;

        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                // Deselect all cards, reset images to non-active
                cards.forEach(function (c) {
                    c.classList.remove('is-selected');
                    var img = c.querySelector('img');
                    if (img && c.dataset.png) {
                        img.src = c.dataset.png;
                    }
                });

                // Select this card
                card.classList.add('is-selected');

                // Swap to active image
                var img = card.querySelector('img');
                if (img && card.dataset.activePng) {
                    img.src = card.dataset.activePng;
                }

                // Preview selected item in the blank slot
                if (blank) {
                    blank.innerHTML = '';
                    if (card.dataset.png) {
                        var preview = document.createElement('img');
                        preview.src = card.dataset.png;
                        preview.alt = card.dataset.choiceId || '';
                        preview.style.cssText = 'width:64px;height:64px;object-fit:contain';
                        blank.appendChild(preview);
                    } else {
                        var icon = card.querySelector('.tnq-icon, .tnq-icon-missing');
                        if (icon) blank.appendChild(icon.cloneNode(true));
                    }
                }

                if (!interacted) {
                    interacted = true;
                    fireInteracted(el);
                }
            });

            // Keyboard: enter/space activates the card
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    card.click();
                }
            });
        });

        // Expose reset for Retry button
        el._tnqReset = function () {
            cards.forEach(function (c) {
                c.classList.remove('is-selected');
                var img = c.querySelector('img');
                if (img && c.dataset.png) { img.src = c.dataset.png; }
            });
            if (blank) blank.innerHTML = '';
            interacted = false;
        };
    }

    function reset(el) {
        if (typeof el._tnqReset === 'function') el._tnqReset();
    }

    function getAnswer(el) {
        var selected = el.querySelector('.tnq-choice-card.is-selected, .tnq-choices .tnq-card.is-selected');
        return selected ? selected.dataset.choiceId : null;
    }

    function validate(submitted, correct) {
        return submitted === correct;
    }

    return { init: init, reset: reset, getAnswer: getAnswer, validate: validate };
}());
