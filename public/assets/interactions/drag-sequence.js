/**
 * Drag-sequence interaction — Q1 (Get Ready for School) and Q2 (Fetching Water).
 *
 * Children arrange picture cards into the correct order.
 * Supports mouse drag-and-drop and touch/pointer ghost dragging for tablets.
 *
 * DOM expected:
 *   .tnq-drag-sequence
 *     .tnq-sequence-area
 *       .tnq-sequence-slot (×N, numbered, initially empty)
 *     .tnq-source-area
 *       .tnq-drag-card[data-item-id] | .tnq-card[data-item-id]
 */
window.TNQInteractions = window.TNQInteractions || {};
TNQInteractions.dragSequence = (function () {

    function fireInteracted(el) {
        el.dispatchEvent(new CustomEvent('tnq:interacted', { bubbles: true }));
    }

    function init(el) {
        console.log('drag-sequence init', el);
        // Scope all queries to the .tnq-drag-sequence child so cards from
        // sibling questions (also hidden in the DOM) can never leak in.
        var root        = el.querySelector('.tnq-drag-sequence') || el;
        var slots       = Array.from(root.querySelectorAll('.tnq-sequence-slot'));
        var sourceArea  = root.querySelector('.tnq-source-area');
        var sourceCards = Array.from(root.querySelectorAll('.tnq-source-area [data-item-id]'));

        // slotContents[i] = itemId | null
        var slotContents = slots.map(function () { return null; });

        // itemInSlot[itemId] = slotIndex | null
        var itemInSlot = {};
        sourceCards.forEach(function (c) { itemInSlot[c.dataset.itemId] = null; });

        var interacted = false;

        // ── State mutators ────────────────────────────────────────

        function placeItem(itemId, slotIdx) {
            // Un-place from old slot (if already placed)
            var oldSlot = itemInSlot[itemId];
            if (oldSlot !== null) {
                slotContents[oldSlot] = null;
                renderSlot(oldSlot);
            }

            // Displace existing occupant of target slot
            var displaced = slotContents[slotIdx];
            if (displaced !== null && displaced !== itemId) {
                itemInSlot[displaced] = null;
                refreshSourceCard(displaced);
            }

            // Place
            slotContents[slotIdx] = itemId;
            itemInSlot[itemId]    = slotIdx;
            renderSlot(slotIdx);
            refreshSourceCard(itemId);

            if (!interacted) {
                interacted = true;
                fireInteracted(el);
            }
        }

        function removeFromAnySlot(itemId) {
            var slotIdx = itemInSlot[itemId];
            if (slotIdx === null) return;
            slotContents[slotIdx] = null;
            itemInSlot[itemId]    = null;
            renderSlot(slotIdx);
            refreshSourceCard(itemId);
        }

        function refreshSourceCard(itemId) {
            sourceCards.forEach(function (c) {
                if (c.dataset.itemId === itemId) {
                    var placed = itemInSlot[itemId] !== null;
                    c.classList.toggle('is-placed', placed);
                    // Re-enable/disable draggable and pointer events
                    c.setAttribute('draggable', placed ? 'false' : 'true');
                }
            });
        }

        function renderSlot(slotIdx) {
            var slot = slots[slotIdx];
            // Remove existing slot card (but keep the number label)
            var existing = slot.querySelector('.tnq-slot-card');
            if (existing) existing.remove();

            var itemId = slotContents[slotIdx];
            if (itemId !== null) {
                // Find source card and clone it into the slot
                var srcCard = null;
                sourceCards.forEach(function (c) {
                    if (c.dataset.itemId === itemId) srcCard = c;
                });
                if (srcCard) {
                    var clone = srcCard.cloneNode(true);
                    clone.classList.remove('is-placed', 'is-dragging');
                    clone.classList.add('tnq-slot-card');
                    clone.setAttribute('draggable', 'true');
                    clone.removeAttribute('tabindex');
                    // Position absolute so it overlays the slot's ::before spacer
                    clone.style.position     = 'absolute';
                    clone.style.top          = '0';
                    clone.style.left         = '0';
                    clone.style.width        = '100%';
                    clone.style.height       = '100%';
                    clone.style.cursor       = 'grab';
                    clone.style.pointerEvents = 'auto';

                    // ── Mouse drag from slot ──────────────────────────
                    clone.addEventListener('dragstart', function (e) {
                        dragItemId = itemId;
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', itemId);
                        // Clear slot state immediately
                        slotContents[slotIdx] = null;
                        itemInSlot[itemId]    = null;
                        clone.classList.add('is-dragging');
                        // Defer DOM cleanup so drag ghost is captured first
                        setTimeout(function () {
                            renderSlot(slotIdx);
                            refreshSourceCard(itemId);
                        }, 0);
                    });

                    clone.addEventListener('dragend', function () {
                        dragItemId = null;
                        el.querySelectorAll('.drag-over').forEach(function (z) {
                            z.classList.remove('drag-over');
                        });
                    });

                    // ── Touch / pointer drag from slot ────────────────
                    clone.addEventListener('pointerdown', function (e) {
                        if (e.pointerType === 'mouse') return;
                        e.preventDefault();
                        e.stopPropagation();

                        // Capture slot state before clearing
                        var draggedId  = itemId;
                        var fromSlot   = slotIdx;

                        // Clear slot state
                        slotContents[fromSlot] = null;
                        itemInSlot[draggedId]  = null;
                        touchCardId = draggedId;
                        touchId     = e.pointerId;

                        // Create floating ghost from clone
                        var rect = clone.getBoundingClientRect();
                        ghost = clone.cloneNode(true);
                        ghost.classList.remove('is-dragging');
                        ghost.style.position     = 'fixed';
                        ghost.style.left         = (e.clientX - rect.width / 2) + 'px';
                        ghost.style.top          = (e.clientY - rect.height / 2) + 'px';
                        ghost.style.width        = rect.width + 'px';
                        ghost.style.pointerEvents = 'none';
                        ghost.style.opacity      = '0.85';
                        ghost.style.zIndex       = '99999';
                        ghost.style.transform    = 'scale(1.06)';
                        ghost.style.boxShadow    = '0 8px 28px rgba(0,0,0,0.28)';
                        ghost.style.borderRadius = '14px';
                        document.body.appendChild(ghost);

                        // Defer slot DOM cleanup so pointerdown completes normally
                        setTimeout(function () {
                            renderSlot(fromSlot);
                            refreshSourceCard(draggedId);
                        }, 0);

                        touchMoveHandler = function (ev) {
                            if (ev.pointerId !== touchId) return;
                            ghost.style.left = (ev.clientX - rect.width / 2) + 'px';
                            ghost.style.top  = (ev.clientY - rect.height / 2) + 'px';
                        };

                        touchUpHandler = function (ev) {
                            if (ev.pointerId !== touchId) return;

                            ghost.style.display = 'none';
                            var target = document.elementFromPoint(ev.clientX, ev.clientY);
                            ghost.style.display = '';

                            if (target) {
                                var targetSlot = target.closest('.tnq-sequence-slot');
                                if (targetSlot) {
                                    var targetIdx = slots.indexOf(targetSlot);
                                    if (targetIdx >= 0) placeItem(touchCardId, targetIdx);
                                }
                            }

                            document.body.removeChild(ghost);
                            ghost = null;
                            touchCardId = null;
                            touchId     = null;

                            document.removeEventListener('pointermove', touchMoveHandler);
                            document.removeEventListener('pointerup',   touchUpHandler);
                            touchMoveHandler = null;
                            touchUpHandler   = null;
                        };

                        document.addEventListener('pointermove', touchMoveHandler);
                        document.addEventListener('pointerup',   touchUpHandler);
                    }, { passive: false });

                    slot.appendChild(clone);
                }
            }
        }

        // ── Mouse drag events ─────────────────────────────────────

        var dragItemId = null;

        sourceCards.forEach(function (card) {
            card.setAttribute('draggable', 'true');

            card.addEventListener('dragstart', function (e) {
                if (card.classList.contains('is-placed')) { e.preventDefault(); return; }
                dragItemId = card.dataset.itemId;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', dragItemId);
                card.classList.add('is-dragging');
                // Use a full-size clone as the drag ghost so it matches the card
                var ghost = e.target.cloneNode(true);
                ghost.style.width    = e.target.offsetWidth  + 'px';
                ghost.style.height   = e.target.offsetHeight + 'px';
                ghost.style.position = 'absolute';
                ghost.style.top      = '-9999px';
                document.body.appendChild(ghost);
                e.dataTransfer.setDragImage(ghost, e.target.offsetWidth / 2, e.target.offsetHeight / 2);
                setTimeout(function () { document.body.removeChild(ghost); }, 0);
            });

            card.addEventListener('dragend', function () {
                card.classList.remove('is-dragging');
                dragItemId = null;
                el.querySelectorAll('.drag-over').forEach(function (z) {
                    z.classList.remove('drag-over');
                });
            });
        });

        slots.forEach(function (slot, slotIdx) {
            slot.addEventListener('dragover', function (e) {
                e.preventDefault();           // ← required for drop to fire
                e.dataTransfer.dropEffect = 'move';
                slot.classList.add('drag-over');
            });
            slot.addEventListener('dragleave', function () {
                slot.classList.remove('drag-over');
            });
            slot.addEventListener('drop', function (e) {
                e.preventDefault();
                slot.classList.remove('drag-over');
                if (!dragItemId) return;
                placeItem(dragItemId, slotIdx);
            });
        });

        // Allow dragging back to source area
        sourceArea.addEventListener('dragover', function (e) { e.preventDefault(); });
        sourceArea.addEventListener('drop', function (e) {
            e.preventDefault();
            if (dragItemId) {
                removeFromAnySlot(dragItemId);
            }
        });

        // ── Touch / pointer (tablet) ghost drag ───────────────────

        var ghost       = null;
        var touchId     = null;
        var touchCardId = null;
        var touchMoveHandler = null;
        var touchUpHandler   = null;

        sourceCards.forEach(function (card) {
            card.addEventListener('pointerdown', function (e) {
                if (e.pointerType === 'mouse') return;
                if (card.classList.contains('is-placed')) return;
                e.preventDefault();

                touchCardId = card.dataset.itemId;
                touchId     = e.pointerId;

                // Create floating ghost clone
                var rect = card.getBoundingClientRect();
                ghost = card.cloneNode(true);
                ghost.classList.remove('is-placed', 'is-dragging');
                ghost.style.position     = 'fixed';
                ghost.style.left         = (e.clientX - rect.width / 2) + 'px';
                ghost.style.top          = (e.clientY - rect.height / 2) + 'px';
                ghost.style.width        = rect.width + 'px';
                ghost.style.pointerEvents = 'none';
                ghost.style.opacity      = '0.85';
                ghost.style.zIndex       = '99999';
                ghost.style.transform    = 'scale(1.06)';
                ghost.style.boxShadow    = '0 8px 28px rgba(0,0,0,0.28)';
                ghost.style.borderRadius = '14px';
                document.body.appendChild(ghost);
                card.classList.add('is-dragging');

                touchMoveHandler = function (ev) {
                    if (ev.pointerId !== touchId) return;
                    ghost.style.left = (ev.clientX - rect.width / 2) + 'px';
                    ghost.style.top  = (ev.clientY - rect.height / 2) + 'px';
                };

                touchUpHandler = function (ev) {
                    if (ev.pointerId !== touchId) return;

                    // Temporarily hide ghost so elementFromPoint can see beneath it
                    ghost.style.display = 'none';
                    var target = document.elementFromPoint(ev.clientX, ev.clientY);
                    ghost.style.display = '';

                    if (target) {
                        var slot = target.closest('.tnq-sequence-slot');
                        if (slot) {
                            var slotIdx = slots.indexOf(slot);
                            if (slotIdx >= 0) placeItem(touchCardId, slotIdx);
                        }
                    }

                    // Clean up
                    document.body.removeChild(ghost);
                    ghost = null;
                    card.classList.remove('is-dragging');
                    touchCardId = null;
                    touchId     = null;

                    document.removeEventListener('pointermove', touchMoveHandler);
                    document.removeEventListener('pointerup',   touchUpHandler);
                    touchMoveHandler = null;
                    touchUpHandler   = null;
                };

                document.addEventListener('pointermove', touchMoveHandler);
                document.addEventListener('pointerup',   touchUpHandler);
            }, { passive: false });
        });

        // Expose slot state for getAnswer
        el._tnqSlots = slotContents;

        // Expose reset for Retry button
        el._tnqReset = function () {
            // Clear state
            for (var i = 0; i < slotContents.length; i++) { slotContents[i] = null; }
            sourceCards.forEach(function (c) { itemInSlot[c.dataset.itemId] = null; });
            // Clear DOM
            slots.forEach(function (slot) {
                var card = slot.querySelector('.tnq-slot-card');
                if (card) card.remove();
            });
            sourceCards.forEach(function (c) {
                c.classList.remove('is-placed', 'is-dragging');
                c.setAttribute('draggable', 'true');
            });
            interacted = false;
        };
    }

    // ── Public API ────────────────────────────────────────────────

    function reset(el) {
        if (typeof el._tnqReset === 'function') el._tnqReset();
    }

    function getAnswer(el) {
        if (el._tnqSlots) {
            return el._tnqSlots.slice(); // copy
        }
        // Fallback: read from DOM (for getAnswer called before init)
        return Array.from(el.querySelectorAll('.tnq-sequence-slot')).map(function (slot) {
            var card = slot.querySelector('[data-item-id]');
            return card ? card.dataset.itemId : null;
        }).filter(Boolean);
    }

    function validate(submitted, correct) {
        if (!Array.isArray(submitted) || !Array.isArray(correct)) return false;
        if (submitted.length !== correct.length) return false;
        return submitted.every(function (v, i) { return v === correct[i]; });
    }

    return { init: init, reset: reset, getAnswer: getAnswer, validate: validate };
}());
