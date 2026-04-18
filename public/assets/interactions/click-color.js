/**
 * Click-color interaction — Q5 (Color the Flag).
 *
 * Children select a colour from a PNG palette then click SVG regions to paint.
 * Validation is by adjacency rule (no two adjacent regions share a color).
 *
 * If no colour is selected when a region is clicked, the palette shakes
 * to signal "pick a colour first".
 *
 * DOM expected:
 *   .tnq-click-color[data-adjacency="[['top','left'],...]"]
 *     .tnq-color-workspace
 *       .tnq-svg-canvas
 *         <svg> with [data-region="..."] elements
 *       .tnq-color-palette
 *         .tnq-color-item
 *           .tnq-color-btn[data-color="red"]   ← PNG palette button
 *           OR .tnq-color-swatch[data-color="#hex"] ← legacy hex swatch
 */
window.TNQInteractions = window.TNQInteractions || {};
TNQInteractions.clickColor = (function () {

    function fireInteracted(el) {
        el.dispatchEvent(new CustomEvent('tnq:interacted', { bubbles: true }));
    }

    function init(el) {
        // Support both PNG buttons (.tnq-color-btn) and legacy hex swatches (.tnq-color-swatch)
        var paletteItems = Array.from(el.querySelectorAll('.tnq-color-btn, .tnq-color-swatch'));
        var svgCanvas    = el.querySelector('.tnq-svg-canvas svg');
        var regions      = svgCanvas ? Array.from(svgCanvas.querySelectorAll('[data-region]')) : [];
        var paletteEl    = el.querySelector('.tnq-color-palette');

        var activeColor  = null;
        var interacted   = false;

        // ── Palette selection ─────────────────────────────────────
        paletteItems.forEach(function (btn) {
            btn.addEventListener('click', function () {
                paletteItems.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                activeColor = btn.dataset.color;
            });

            // Keyboard support
            btn.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); btn.click(); }
            });
        });

        // ── Palette shake helper ──────────────────────────────────
        function shakePalette() {
            if (!paletteEl) return;
            paletteEl.classList.remove('tnq-palette-shake');
            // Force reflow so animation restarts
            void paletteEl.offsetWidth;
            paletteEl.classList.add('tnq-palette-shake');
            paletteEl.addEventListener('animationend', function handler() {
                paletteEl.classList.remove('tnq-palette-shake');
                paletteEl.removeEventListener('animationend', handler);
            });
        }

        // ── Paint regions on click / touch ────────────────────────
        regions.forEach(function (region) {
            var regionName = region.dataset.region;
            if (!regionName || regionName === 'bg') return;

            region.style.cursor = 'pointer';

            function paint() {
                if (!activeColor) {
                    shakePalette();
                    return;
                }
                region.setAttribute('fill', activeColor);

                if (!interacted) {
                    interacted = true;
                    fireInteracted(el);
                }
            }

            region.addEventListener('click', paint);

            region.addEventListener('touchend', function (e) {
                e.preventDefault();
                paint();
            }, { passive: false });
        });
    }

    /**
     * Return object mapping region name → current fill colour value.
     */
    function getAnswer(el) {
        var svgCanvas = el.querySelector('.tnq-svg-canvas svg');
        if (!svgCanvas) return {};

        var result = {};
        svgCanvas.querySelectorAll('[data-region]').forEach(function (region) {
            var name = region.dataset.region;
            if (name && name !== 'bg' && !result[name]) {
                result[name] = region.getAttribute('fill') || '';
            }
        });
        return result;
    }

    /**
     * Validate by adjacency rule:
     *   - every region must have a colour
     *   - no two adjacent regions share the same colour
     *
     * correct = adjacency pairs array from data-answer, e.g.
     *   [["top","left"],["top","right"],["bottom","left"],["bottom","right"]]
     */
    function validate(submitted, correct) {
        if (!correct || !Array.isArray(correct)) return false;

        // All regions in submitted must have a non-empty colour
        var values = Object.values(submitted);
        if (values.length === 0) return false;
        if (values.some(function (v) { return !v || v === 'transparent' || v === 'none'; })) return false;

        // No adjacent pair may share a colour
        return correct.every(function (pair) {
            var colorA = submitted[pair[0]];
            var colorB = submitted[pair[1]];
            return colorA && colorB && colorA !== colorB;
        });
    }

    return { init: init, getAnswer: getAnswer, validate: validate };
}());
