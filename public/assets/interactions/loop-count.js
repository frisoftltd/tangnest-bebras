/**
 * Loop-count interaction — Q3 (Count the Steps).
 *
 * Children use +/- buttons to set a numeric answer.
 * If data-tile-icon-url is set, a row of PNG icons re-renders in real time
 * to show the current count visually.
 *
 * DOM expected:
 *   .tnq-loop-count[data-min][data-max][data-initial][data-tile-icon-url?]
 *     .tnq-dynamic-tiles   (optional — filled by JS when tile-icon-url is set)
 *     .tnq-counter-row
 *       .tnq-counter-btn[data-dir="-"]
 *       .tnq-counter-value
 *       .tnq-counter-btn[data-dir="+"]
 */
window.TNQInteractions = window.TNQInteractions || {};
TNQInteractions.loopCount = (function () {

    function fireInteracted(el) {
        el.dispatchEvent(new CustomEvent('tnq:interacted', { bubbles: true }));
    }

    function init(el) {
        var valueEl  = el.querySelector('.tnq-counter-value');
        var btnMinus = el.querySelector('.tnq-counter-btn[data-dir="-"]');
        var btnPlus  = el.querySelector('.tnq-counter-btn[data-dir="+"]');
        var tilesEl  = el.querySelector('.tnq-dynamic-tiles');

        var min     = parseInt(el.dataset.min,     10) || 1;
        var max     = parseInt(el.dataset.max,     10) || 30;
        var initial = parseInt(el.dataset.initial, 10) || min;
        var current = initial;
        var iconUrl = el.dataset.tileIconUrl || '';

        var interacted = false;

        function renderTiles() {
            if (!tilesEl || !iconUrl) return;
            tilesEl.innerHTML = '';
            for (var i = 0; i < current; i++) {
                var img = document.createElement('img');
                img.src = iconUrl;
                img.alt = 'footprint ' + (i + 1);
                img.style.cssText = 'width:60px;height:60px;object-fit:contain';
                tilesEl.appendChild(img);
            }
        }

        function render() {
            if (valueEl) valueEl.textContent = current;
            if (btnMinus) btnMinus.disabled = current <= min;
            if (btnPlus)  btnPlus.disabled  = current >= max;
            renderTiles();
        }

        function change(delta) {
            var next = current + delta;
            if (next < min || next > max) return;
            current = next;
            render();
            if (!interacted) {
                interacted = true;
                fireInteracted(el);
            }
        }

        if (btnMinus) {
            btnMinus.addEventListener('click', function () { change(-1); });
        }
        if (btnPlus) {
            btnPlus.addEventListener('click', function () { change(1); });
        }

        // Keyboard support
        el.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowUp'   || e.key === '+') { change(1);  e.preventDefault(); }
            if (e.key === 'ArrowDown' || e.key === '-') { change(-1); e.preventDefault(); }
        });

        // Initial render
        render();

        // Expose reset for Retry button
        el._tnqReset = function () {
            current = initial;
            render();
            interacted = false;
        };
    }

    function reset(el) {
        if (typeof el._tnqReset === 'function') el._tnqReset();
    }

    function getAnswer(el) {
        var valueEl = el.querySelector('.tnq-counter-value');
        return valueEl ? parseInt(valueEl.textContent, 10) : 0;
    }

    function validate(submitted, correct) {
        return parseInt(submitted, 10) === parseInt(correct, 10);
    }

    return { init: init, reset: reset, getAnswer: getAnswer, validate: validate };
}());
