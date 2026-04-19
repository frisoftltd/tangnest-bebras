/**
 * Match-pairs interaction — Q6 (What Makes It Work?) and assessment questions.
 *
 * Practice mode (data-mode="practice"):
 *   Dot-and-line UX — click a left item to activate it (coloured dot highlights),
 *   then click a right item to draw a line. Clicking an already-connected left
 *   item removes its line so you can change your answer.
 *
 * Assessment mode (data-mode="baseline"|"endline"):
 *   Classic click-to-select-then-confirm — correct pairs lock with a green line,
 *   incorrect pairs shake briefly.
 *
 * DOM expected (practice, PNG cards):
 *   .tnq-match-pairs[data-pairs="[[...]]"][data-mode="practice"]
 *     .tnq-pairs-workspace
 *       .tnq-pairs-col.tnq-pairs-left
 *         .tnq-pair-card.tnq-pairs-left-item[data-pair-id][data-dot-color]
 *           img.tnq-pair-img  |  tnq-icon
 *           span (label)
 *           span.tnq-pair-dot.tnq-pair-dot-right
 *       .tnq-pairs-connectors
 *       .tnq-pairs-col.tnq-pairs-right
 *         .tnq-pair-card.tnq-pairs-right-item[data-pair-id]
 *           span.tnq-pair-dot.tnq-pair-dot-left
 *           img.tnq-pair-img  |  tnq-icon
 *           span (label)
 */
window.TNQInteractions = window.TNQInteractions || {};
TNQInteractions.matchPairs = (function () {

    function fireInteracted(el) {
        el.dispatchEvent(new CustomEvent('tnq:interacted', { bubbles: true }));
    }

    // ─────────────────────────────────────────────────────────────
    // Practice mode: draw/remove SVG lines
    // ─────────────────────────────────────────────────────────────
    function initPractice(el) {
        var leftCards  = Array.from(el.querySelectorAll('.tnq-pairs-left-item'));
        var rightCards = Array.from(el.querySelectorAll('.tnq-pairs-right-item'));
        var workspace  = el.querySelector('.tnq-pairs-workspace');
        var connDiv    = el.querySelector('.tnq-pairs-connectors');

        // Build SVG overlay spanning the whole workspace
        var lineSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        lineSvg.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;overflow:visible;pointer-events:none;z-index:100';
        if (workspace) workspace.appendChild(lineSvg);

        // State
        var activeLeft  = null;  // leftId of currently selected left card
        var pairs       = {};    // leftId → rightId
        var lines       = {};    // leftId → SVG line element

        function dotColorFor(leftId) {
            var card = leftCards.find(function (c) { return c.dataset.pairId === leftId; });
            return card ? (card.dataset.dotColor || '#1A56A0') : '#1A56A0';
        }

        function rightDotOf(rightId) {
            var card = rightCards.find(function (c) { return c.dataset.pairId === rightId; });
            return card ? card.querySelector('.tnq-pair-dot-left') : null;
        }

        function leftDotOf(leftId) {
            var card = leftCards.find(function (c) { return c.dataset.pairId === leftId; });
            return card ? card.querySelector('.tnq-pair-dot-right') : null;
        }

        function drawLine(leftId, rightId) {
            var leftCard  = leftCards.find(function (c) { return c.dataset.pairId === leftId; });
            var rightCard = rightCards.find(function (c) { return c.dataset.pairId === rightId; });
            if (!leftCard || !rightCard || !lineSvg || !workspace) return;

            var wRect  = workspace.getBoundingClientRect();
            var lRect  = leftCard.getBoundingClientRect();
            var rRect  = rightCard.getBoundingClientRect();
            var color  = dotColorFor(leftId);

            var x1 = lRect.right  - wRect.left;
            var y1 = lRect.top    - wRect.top  + lRect.height / 2;
            var x2 = rRect.left   - wRect.left;
            var y2 = rRect.top    - wRect.top  + rRect.height / 2;

            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
            line.setAttribute('stroke', color);
            line.setAttribute('stroke-width', '3');
            line.setAttribute('stroke-linecap', 'round');
            lineSvg.appendChild(line);
            lines[leftId] = line;

            // Colour the right dot to match
            var rdot = rightDotOf(rightId);
            if (rdot) rdot.style.background = color;
        }

        function removeLine(leftId) {
            if (lines[leftId]) {
                lines[leftId].remove();
                delete lines[leftId];
            }
            // Reset right dot colour
            if (pairs[leftId]) {
                var rdot = rightDotOf(pairs[leftId]);
                if (rdot) rdot.style.background = '#ccc';
            }
        }

        function highlightDot(leftId, on) {
            var dot = leftDotOf(leftId);
            if (!dot) return;
            dot.style.transform  = on ? 'scale(1.35)' : '';
            dot.style.boxShadow  = on ? ('0 0 0 3px ' + dotColorFor(leftId) + '44') : '';
        }

        // Activate a left card, deactivate previous
        function activateLeft(leftId) {
            if (activeLeft) highlightDot(activeLeft, false);
            var leftCard = leftCards.find(function (c) { return c.dataset.pairId === leftId; });
            if (leftCard) leftCard.classList.remove('is-active-left');
            activeLeft = leftId;
            highlightDot(leftId, true);
            var aCard = leftCards.find(function (c) { return c.dataset.pairId === leftId; });
            if (aCard) aCard.classList.add('is-active-left');
        }

        function deactivateLeft() {
            if (activeLeft) {
                highlightDot(activeLeft, false);
                var aCard = leftCards.find(function (c) { return c.dataset.pairId === activeLeft; });
                if (aCard) aCard.classList.remove('is-active-left');
                activeLeft = null;
            }
        }

        var interacted = false;

        // ── Left card clicks ──────────────────────────────────────
        leftCards.forEach(function (card) {
            card.addEventListener('click', function () {
                var leftId = card.dataset.pairId;

                if (pairs[leftId]) {
                    // Already has a line → remove line, activate this card to re-pair
                    removeLine(leftId);
                    delete pairs[leftId];
                    activateLeft(leftId);
                } else if (activeLeft === leftId) {
                    // Clicking active card again → deselect
                    deactivateLeft();
                } else {
                    // Activate
                    activateLeft(leftId);
                }
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
            });
        });

        // ── Right card clicks ─────────────────────────────────────
        rightCards.forEach(function (card) {
            card.addEventListener('click', function () {
                if (!activeLeft) return;

                var rightId = card.dataset.pairId;

                // If right item already paired to someone else, unlink
                Object.keys(pairs).forEach(function (lId) {
                    if (pairs[lId] === rightId && lId !== activeLeft) {
                        removeLine(lId);
                        delete pairs[lId];
                    }
                });

                // Draw line
                pairs[activeLeft] = rightId;
                drawLine(activeLeft, rightId);
                deactivateLeft();

                if (!interacted) {
                    interacted = true;
                    fireInteracted(el);
                }
            });

            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
            });
        });

        // Expose state for getAnswer
        el._tnqPairs = pairs;

        // Expose reset for Retry button
        el._tnqReset = function () {
            // Remove all SVG lines
            while (lineSvg.firstChild) { lineSvg.removeChild(lineSvg.firstChild); }
            // Reset right-dot colours
            rightCards.forEach(function (c) {
                var dot = c.querySelector('.tnq-pair-dot-left');
                if (dot) dot.style.background = '#ccc';
            });
            // Reset left-card state
            leftCards.forEach(function (c) {
                c.classList.remove('is-active-left');
                var dot = c.querySelector('.tnq-pair-dot-right');
                if (dot) { dot.style.transform = ''; dot.style.boxShadow = ''; }
            });
            // Clear state
            Object.keys(pairs).forEach(function (k) { delete pairs[k]; });
            Object.keys(lines).forEach(function (k) { delete lines[k]; });
            activeLeft = null;
            interacted = false;
        };
    }

    // ─────────────────────────────────────────────────────────────
    // Assessment mode: lock on correct, shake on wrong
    // ─────────────────────────────────────────────────────────────
    function initAssessment(el) {
        var leftCards  = Array.from(el.querySelectorAll('.tnq-pairs-left .tnq-card, .tnq-pairs-left-item'));
        var rightCards = Array.from(el.querySelectorAll('.tnq-pairs-right .tnq-card, .tnq-pairs-right-item'));
        var connDiv    = el.querySelector('.tnq-pairs-connectors');
        var correctPairs = JSON.parse(el.dataset.pairs || '[]');

        var selectedLeft = null;
        var matched      = {};

        var lineSvg = null;
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

        function drawLine(leftCard, rightCard, color) {
            if (!lineSvg || !connDiv) return;
            var containerRect = connDiv.getBoundingClientRect();
            var leftRect  = leftCard.getBoundingClientRect();
            var rightRect = rightCard.getBoundingClientRect();

            var x1 = leftRect.right  - containerRect.left;
            var y1 = leftRect.top    - containerRect.top + leftRect.height  / 2;
            var x2 = rightRect.left  - containerRect.left;
            var y2 = rightRect.top   - containerRect.top + rightRect.height / 2;

            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1);
            line.setAttribute('y1', y1);
            line.setAttribute('x2', x2);
            line.setAttribute('y2', y2);
            line.setAttribute('stroke', color || '#1A56A0');
            line.setAttribute('stroke-width', '3');
            line.setAttribute('stroke-linecap', 'round');
            lineSvg.appendChild(line);
        }

        function lockPair(leftCard, rightCard) {
            leftCard.classList.add('is-matched', 'is-locked', 'is-correct');
            rightCard.classList.add('is-matched', 'is-locked', 'is-correct');
            matched[leftCard.dataset.pairId] = rightCard.dataset.pairId;
            leftCard.dataset.matchedTo  = rightCard.dataset.pairId;
            rightCard.dataset.matchedFrom = leftCard.dataset.pairId;
            drawLine(leftCard, rightCard, '#1E8449');
        }

        var interacted = false;

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

                var leftId  = selectedLeft.dataset.pairId;
                var rightId = card.dataset.pairId;

                if (!interacted) {
                    interacted = true;
                    fireInteracted(el);
                }

                if (isCorrectPair(leftId, rightId)) {
                    lockPair(selectedLeft, card);
                    selectedLeft.classList.remove('is-selected');
                    selectedLeft = null;

                    // Auto-complete last pair
                    var unlockedL = leftCards.filter(function (c) { return !c.classList.contains('is-locked'); });
                    var unlockedR = rightCards.filter(function (c) { return !c.classList.contains('is-locked'); });
                    if (unlockedL.length === 1 && unlockedR.length === 1) {
                        lockPair(unlockedL[0], unlockedR[0]);
                    }
                } else {
                    [selectedLeft, card].forEach(function (c) {
                        c.classList.add('tnq-shake');
                        setTimeout(function () { c.classList.remove('tnq-shake'); }, 500);
                    });
                    selectedLeft.classList.remove('is-selected');
                    selectedLeft = null;
                }
            });
        });

        el._tnqPairs = matched;

        // Assessment mode: no retry needed, but wire a no-op reset for consistency
        el._tnqReset = function () {};
    }

    // ─────────────────────────────────────────────────────────────
    // Public
    // ─────────────────────────────────────────────────────────────

    function init(el) {
        var mode = el.dataset.mode || 'practice';
        if (mode === 'practice') {
            initPractice(el);
        } else {
            initAssessment(el);
        }
    }

    /**
     * Return array of [leftId, rightId] pairs — format expected by scorer.
     */
    function getAnswer(el) {
        var pairs = el._tnqPairs || {};
        return Object.keys(pairs).map(function (leftId) {
            return [leftId, pairs[leftId]];
        });
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

    function reset(el) {
        if (typeof el._tnqReset === 'function') el._tnqReset();
    }

    return { init: init, reset: reset, getAnswer: getAnswer, validate: validate };
}());
