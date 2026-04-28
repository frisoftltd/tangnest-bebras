/**
 * Tangnest CT Assessment — Quiz Engine
 *
 * Coordinates the assessment/practice flow:
 *   - Shows one question at a time
 *   - Drives the soft timer (assessment mode)
 *   - Handles practice feedback / hints
 *   - Submits answers via AJAX on completion
 *
 * Depends on: interactions/*.js (loaded before this file)
 */

/* global TNQData, TNQInteractions */

(function () {
	'use strict';

	window.TNQ_VERSION = '2.4.3';

	/** Namespace for interaction modules loaded from interactions/*.js */
	window.TNQInteractions = window.TNQInteractions || {};

	// ── TNQ_Timer ────────────────────────────────────────────────

	function TNQTimer(displayEl, durationSeconds) {
		this.el       = displayEl;
		this.total    = durationSeconds;
		this.remaining = durationSeconds;
		this.ticking  = false;
		this.elapsed  = 0;
		this._interval = null;
	}

	TNQTimer.prototype.start = function () {
		if (this.ticking) return;
		this.ticking = true;
		var self = this;
		this._interval = setInterval(function () {
			self.remaining = Math.max(0, self.remaining - 1);
			self.elapsed++;
			self._render();
			if (self.remaining === 0) {
				clearInterval(self._interval);
				self.ticking = false;
			}
		}, 1000);
		this._render();
	};

	TNQTimer.prototype.stop = function () {
		clearInterval(this._interval);
		this.ticking = false;
	};

	TNQTimer.prototype.reset = function (durationSeconds) {
		this.stop();
		this.remaining = durationSeconds !== undefined ? durationSeconds : this.total;
		this._render();
	};

	TNQTimer.prototype._render = function () {
		if (!this.el) return;
		var r = this.remaining;
		var mins = Math.floor(r / 60);
		var secs = r % 60;
		var timeStr = mins + ':' + (secs < 10 ? '0' : '') + secs;

		var ratio = this.total > 0 ? r / this.total : 0;
		this.el.classList.remove('is-amber', 'is-red', 'is-done');

		var msgEl = this.el.querySelector('.tnq-timer-expired-msg');

		if (r === 0) {
			this.el.classList.add('is-done');
			if (msgEl) msgEl.textContent = 'Time\'s up — take your time';
		} else if (ratio < 0.2) {
			this.el.classList.add('is-red');
			if (msgEl) msgEl.textContent = '';
		} else if (ratio < 0.5) {
			this.el.classList.add('is-amber');
			if (msgEl) msgEl.textContent = '';
		} else {
			if (msgEl) msgEl.textContent = '';
		}

		var timeDisplay = this.el.querySelector('.tnq-timer-display');
		if (timeDisplay) timeDisplay.textContent = timeStr;
	};

	// ── TNQ_Quiz ────────────────────────────────────────────────

	function TNQQuiz(container) {
		this.container    = container;
		this.mode         = container.dataset.mode || 'practice';
		this.ageBand      = container.dataset.age  || '7-8';
		this.assessType   = container.dataset.assessType || '';
		this.reviewMode   = container.dataset.review === 'true';
		this.questions    = Array.from(container.querySelectorAll('.tnq-question'));
		this.currentIdx   = 0;
		this.answers      = {};
		this.durations    = {};
		this.itemStartTime = null;
		this.timer         = null;
		this.totalElapsed  = 0;
		this._initialized   = {};  // idx -> true once interaction init() has run
		this._checkedState  = {};  // idx -> { correct: bool } once Check was pressed
		this._hasInteracted = {};  // idx -> true once tnq:interacted fired for that question
	}

	TNQQuiz.prototype.init = function () {
		if (this.questions.length === 0) return;

		// Review mode: inject banner, hide Check, add Next review button to nav
		if (this.reviewMode) {
			var firstQ = this.questions[0];
			if (firstQ) {
				var banner = document.createElement('div');
				banner.className   = 'tnq-review-banner';
				banner.textContent = 'Your score is saved \u2014 you can re-read the questions below';
				firstQ.insertBefore(banner, firstQ.firstChild);
			}
			var checkBtn = this.container.querySelector('.tnq-btn-check');
			if (checkBtn) checkBtn.style.display = 'none';

			// Add a dedicated Next button for review navigation
			var navEl = this.container.querySelector('.tnq-nav');
			if (navEl) {
				var reviewNextBtn = document.createElement('button');
				reviewNextBtn.className   = 'tnq-btn-next-review';
				reviewNextBtn.textContent = 'Next \u2192';
				reviewNextBtn.type        = 'button';
				navEl.appendChild(reviewNextBtn);
				this._reviewNextBtn = reviewNextBtn;
				var self2 = this;
				reviewNextBtn.addEventListener('click', function () {
					if (self2.currentIdx < self2.questions.length - 1) {
						self2._showQuestion(self2.currentIdx + 1);
					}
				});
			}
		}

		// Show first question
		this._showQuestion(0);

		// Wire nav buttons
		var self = this;

		var btnBack  = this.container.querySelector('.tnq-btn-back');
		var btnCheck = this.container.querySelector('.tnq-btn-check');
		var btnNext  = this.container.querySelector('.tnq-btn-next');
		var btnHint  = this.container.querySelector('.tnq-btn-hint');

		if (btnBack) {
			btnBack.addEventListener('click', function () {
				self._onBack();
			});
		}
		if (btnCheck) {
			btnCheck.addEventListener('click', function () {
				self._onCheck();
			});
		}
		if (btnNext) {
			btnNext.addEventListener('click', function () {
				self._onNext();
			});
		}
		if (btnHint) {
			btnHint.addEventListener('click', function () {
				self._onHint();
			});
		}

		// Enable "Check my answer" only after the child makes an interaction.
		// Each interaction module fires 'tnq:interacted' when the first action happens.
		// Track per-question so going Back restores the enabled state correctly.
		this.container.addEventListener('tnq:interacted', function () {
			self._hasInteracted[self.currentIdx] = true;
			var btn = self.container.querySelector('.tnq-btn-check');
			if (btn) {
				btn.disabled = false;
				btn.removeAttribute('aria-disabled');
			}
		});
	};

	TNQQuiz.prototype._showQuestion = function (idx) {
		this.questions.forEach(function (q, i) {
			q.style.display = i === idx ? '' : 'none';
		});

		this.currentIdx = idx;
		this.itemStartTime = Date.now();
		this._updateProgress();

		// Only initialise the interaction widget the first time a question is shown.
		// Re-calling init() would reset state and duplicate event listeners.
		if (!this._initialized[idx]) {
			this._initialized[idx] = true;
			this._initCurrentInteraction();
		}

		// Reset timer for this question (suppressed in review mode)
		if (this.mode !== 'practice' && !this.reviewMode) {
			this._startTimer();
		}

		// Locate nav buttons
		var btnBack  = this.container.querySelector('.tnq-btn-back');
		var btnCheck = this.container.querySelector('.tnq-btn-check');
		var btnNext  = this.container.querySelector('.tnq-btn-next');
		var btnHint  = this.container.querySelector('.tnq-btn-hint');

		// Back button visibility
		if (btnBack) {
			if (this.reviewMode) {
				// Review: always visible, disabled on Q1
				btnBack.style.display = '';
				btnBack.disabled = idx <= 0;
			} else {
				// Normal: hidden on Q1, shown from Q2
				btnBack.style.display = idx > 0 ? '' : 'none';
				btnBack.disabled = false;
			}
		}

		// Always clear feedback/hint first; restored below if navigating back to a
		// previously checked question.
		var feedbackEl    = this.container.querySelector('.tnq-feedback');
		var explanationEl = this.container.querySelector('.tnq-explanation');
		var hintBox       = this.container.querySelector('.tnq-hint-box');

		if (feedbackEl)    { feedbackEl.classList.remove('is-visible'); }
		if (explanationEl) { explanationEl.classList.remove('is-visible'); }
		if (hintBox)       { hintBox.classList.remove('is-visible'); }

		if (this.mode === 'practice') {
			var prevState = this._checkedState[idx];
			if (prevState) {
				// Restore completed state: Check was already pressed for this question.
				// Do NOT re-enable Check; show Next and the original feedback.
				if (btnCheck) { btnCheck.style.display = 'none'; }
				if (btnHint)  { btnHint.style.display  = ''; }
				if (btnNext)  {
					btnNext.style.display = '';
					btnNext.textContent   = idx < this.questions.length - 1 ? 'Next question \u2192' : 'Finish practice';
				}
				this._showFeedback(prevState.correct);
			} else {
				// Fresh / retried state
				if (btnCheck) {
					btnCheck.style.display = '';
					btnCheck.textContent   = 'Check my answer';
					// Re-enable if the child had already interacted with this question
					btnCheck.disabled = !this._hasInteracted[idx];
				}
				if (btnNext)  { btnNext.style.display  = 'none'; }
				if (btnHint)  { btnHint.style.display  = ''; }
			}
		} else {
			if (this.reviewMode) {
				// Review mode: Check hidden, hint hidden, Next review button handles navigation
				if (btnCheck) { btnCheck.style.display = 'none'; }
				if (btnHint)  { btnHint.style.display  = 'none'; }
				if (btnNext)  { btnNext.style.display   = 'none'; }
				if (this._reviewNextBtn) {
					this._reviewNextBtn.disabled = idx >= this.questions.length - 1;
				}
			} else {
				var prevState = this._checkedState[idx];
				var q         = this.questions[idx];
				var hintText  = q ? (q.dataset.hint || '') : '';

				if (prevState) {
					// Already checked — restore locked state (supports Back navigation)
					if (btnCheck) { btnCheck.style.display = 'none'; }
					if (btnNext) {
						btnNext.style.display = '';
						btnNext.textContent   = idx < this.questions.length - 1 ? 'Next question \u2192' : 'Finish';
					}
					if (btnHint) { btnHint.style.display = hintText ? '' : 'none'; }
					this._showFeedback(prevState.correct, false);
				} else {
					// Fresh — show Check (disabled until interaction), hide Next
					if (btnCheck) {
						btnCheck.style.display = '';
						btnCheck.textContent   = 'Check my answer';
						btnCheck.disabled      = !this._hasInteracted[idx];
					}
					if (btnNext)  { btnNext.style.display  = 'none'; }
					if (btnHint)  { btnHint.style.display  = hintText ? '' : 'none'; }
				}
			}
		}
	};

	TNQQuiz.prototype._initCurrentInteraction = function () {
		var q    = this.questions[this.currentIdx];
		var type = q.dataset.type;
		var interactionEl = q.querySelector('.tnq-interaction');

		if (!interactionEl) return;

		var module = this._getModule(type);
		if (module && module.init) {
			module.init(interactionEl);
		}
	};

	TNQQuiz.prototype._getModule = function (type) {
		var map = {
			'drag-sequence': TNQInteractions.dragSequence,
			'loop-count':    TNQInteractions.loopCount,
			'click-color':   TNQInteractions.clickColor,
			'pattern-next':  TNQInteractions.patternNext,
			'match-pairs':   TNQInteractions.matchPairs,
			'drag-sort':     TNQInteractions.dragSort,
		};
		return map[type] || null;
	};

	TNQQuiz.prototype._startTimer = function () {
		var timerEl = this.container.querySelector('.tnq-timer');
		if (!timerEl) return;

		timerEl.style.display = '';

		var duration = 90; // default 7-8 band
		if (this.ageBand === '9-10')  duration = 75;
		if (this.ageBand === '11-12') duration = 60;

		if (!this.timer) {
			this.timer = new TNQTimer(timerEl, duration);
		} else {
			this.timer.reset(duration);
		}
		this.timer.start();
	};

	TNQQuiz.prototype._updateProgress = function () {
		var total    = this.questions.length;
		var current  = this.currentIdx + 1;
		var pct      = ((current - 1) / total) * 100;

		var progressFill  = this.container.querySelector('.tnq-progress-fill');
		var progressLabel = this.container.querySelector('.tnq-progress-label');

		if (progressFill)  progressFill.style.width = pct + '%';
		if (progressLabel) progressLabel.textContent = 'Question ' + current + ' of ' + total;
	};

	TNQQuiz.prototype._getCurrentAnswer = function () {
		var q    = this.questions[this.currentIdx];
		var type = q.dataset.type;
		var interactionEl = q.querySelector('.tnq-interaction');
		if (!interactionEl) return null;

		var module = this._getModule(type);
		if (module && module.getAnswer) {
			return module.getAnswer(interactionEl);
		}
		return null;
	};

	TNQQuiz.prototype._isAnswerCorrect = function () {
		var q    = this.questions[this.currentIdx];
		var type = q.dataset.type;
		var correctRaw = q.dataset.answer;
		if (!correctRaw) return false;

		var correct;
		try { correct = JSON.parse(correctRaw); } catch(e) { correct = correctRaw; }

		var submitted = this._getCurrentAnswer();
		var module    = this._getModule(type);
		if (module && module.validate) {
			return module.validate(submitted, correct);
		}
		return false;
	};

	TNQQuiz.prototype._recordAnswer = function () {
		var q   = this.questions[this.currentIdx];
		var id  = q.dataset.id;
		var elapsed = Math.round((Date.now() - this.itemStartTime) / 1000);

		this.answers[id]   = this._getCurrentAnswer();
		this.durations[id] = elapsed;
		this.totalElapsed += elapsed;

		if (this.timer) {
			this.timer.stop();
		}
	};

	TNQQuiz.prototype._onCheck = function () {
		// Practice mode: show feedback
		if (this.mode === 'practice') {
			this._recordAnswer();
			var correct = this._isAnswerCorrect();
			// Record that Check was pressed so Back navigation can restore this state
			this._checkedState[this.currentIdx] = { correct: correct };
			this._showFeedback(correct);

			var btnCheck = this.container.querySelector('.tnq-btn-check');
			var btnNext  = this.container.querySelector('.tnq-btn-next');
			if (btnCheck) btnCheck.style.display = 'none';
			if (btnNext)  {
				btnNext.style.display = '';
				btnNext.textContent   = this.currentIdx < this.questions.length - 1 ? 'Next question \u2192' : 'Finish practice';
			}
		} else {
			// Assessment mode: Check → show feedback (no retry) → reveal Next button
			this._recordAnswer();
			var correct = this._isAnswerCorrect();
			this._checkedState[this.currentIdx] = { correct: correct };
			this._showFeedback(correct, false);  // false = no Try Again button

			var btnCheck = this.container.querySelector('.tnq-btn-check');
			var btnNext  = this.container.querySelector('.tnq-btn-next');
			if (btnCheck) btnCheck.style.display = 'none';
			if (btnNext) {
				btnNext.style.display = '';
				btnNext.textContent   = this.currentIdx < this.questions.length - 1
					? 'Next question \u2192'
					: 'Finish';
			}
		}
	};

	TNQQuiz.prototype._onNext = function () {
		this._advance();
	};

	TNQQuiz.prototype._onBack = function () {
		if (this.currentIdx <= 0) return;
		this._showQuestion(this.currentIdx - 1);
	};

	TNQQuiz.prototype._advance = function () {
		if (this.currentIdx < this.questions.length - 1) {
			this._showQuestion(this.currentIdx + 1);
		} else {
			this._onComplete();
		}
	};

	TNQQuiz.prototype._onHint = function () {
		var hintBox = this.container.querySelector('.tnq-hint-box');
		if (!hintBox) return;
		// BUG 4 fix: read hint text from current question's data-hint attribute
		var q = this.questions[this.currentIdx];
		var hintText = q ? (q.dataset.hint || '') : '';
		var hintSpan = hintBox.querySelector('.tnq-hint-text');
		if (hintSpan) hintSpan.textContent = hintText;
		hintBox.classList.add('is-visible');
	};

	TNQQuiz.prototype._showFeedback = function (correct, allowRetry) {
		var feedbackEl    = this.container.querySelector('.tnq-feedback');
		var explanationEl = this.container.querySelector('.tnq-explanation');

		if (feedbackEl) {
			feedbackEl.classList.remove('is-correct', 'is-wrong');
			feedbackEl.classList.add('is-visible', correct ? 'is-correct' : 'is-wrong');

			var icon = correct ? '\u2714' : '\u2718';
			var msg  = correct
				? 'Correct! Well done.'
				: ( allowRetry !== false ? 'Not quite right. Try again or see the explanation below.' : 'Not quite \u2014 but keep going!' );
			var iconEl = feedbackEl.querySelector('.tnq-feedback-icon');
			var msgEl  = feedbackEl.querySelector('.tnq-feedback-msg');
			if (iconEl) iconEl.textContent = icon;
			if (msgEl)  msgEl.textContent  = msg;

			// Remove any previous retry button
			var oldRetry = feedbackEl.querySelector('.tnq-btn-retry');
			if (oldRetry) oldRetry.remove();

			// Inject Retry button on wrong answer (practice only)
			if (!correct && allowRetry !== false) {
				var self = this;
				var retryBtn = document.createElement('button');
				retryBtn.className   = 'tnq-btn-retry';
				retryBtn.textContent = 'Try again \ud83d\udd04';
				retryBtn.style.cssText = 'background:#F39C12;color:white;border:none;border-radius:10px;' +
					'padding:12px 24px;font-size:16px;font-weight:bold;cursor:pointer;' +
					'margin-top:12px;min-height:48px;display:block;';
				retryBtn.addEventListener('click', function () { self._onRetry(); });
				feedbackEl.appendChild(retryBtn);
			}
		}

		if (explanationEl) {
			explanationEl.classList.add('is-visible');
		}
	};

	TNQQuiz.prototype._onRetry = function () {
		var feedbackEl    = this.container.querySelector('.tnq-feedback');
		var explanationEl = this.container.querySelector('.tnq-explanation');
		var hintBox       = this.container.querySelector('.tnq-hint-box');
		var btnCheck      = this.container.querySelector('.tnq-btn-check');
		var btnNext       = this.container.querySelector('.tnq-btn-next');

		// Clear the checked and interacted state so Back shows a fresh question
		delete this._checkedState[this.currentIdx];
		delete this._hasInteracted[this.currentIdx];

		// Hide feedback, explanation, hint
		if (feedbackEl)    feedbackEl.classList.remove('is-visible');
		if (explanationEl) explanationEl.classList.remove('is-visible');
		if (hintBox)       hintBox.classList.remove('is-visible');

		// Restore Check button (disabled until child interacts again)
		if (btnCheck) {
			btnCheck.style.display = '';
			btnCheck.textContent   = 'Check my answer';
			btnCheck.disabled      = true;
		}
		if (btnNext) { btnNext.style.display = 'none'; }

		// Reset the interaction
		var q    = this.questions[this.currentIdx];
		var type = q ? q.dataset.type : null;
		var interactionEl = q ? q.querySelector('.tnq-interaction') : null;
		if (interactionEl && type) {
			var module = this._getModule(type);
			if (module && module.reset) {
				module.reset(interactionEl);
			}
		}
	};

	TNQQuiz.prototype._onComplete = function () {
		if (this.mode === 'practice') {
			// Practice never submits — show a simple done message
			this._showPracticeDone();
			return;
		}

		// Assessment: submit via AJAX
		var self = this;
		var submitBtn = this.container.querySelector('.tnq-btn-check');
		if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Submitting\u2026'; }

		var data = {
			action:           'tnq_submit_assessment',
			nonce:            TNQData.nonce,
			assessment_type:  this.assessType,
			age_band:         this.ageBand,
			answers:          JSON.stringify(this.answers),
			duration_seconds: this.totalElapsed,
			tutor_course_id:  TNQData.courseId  || 0,
			tutor_lesson_id:  TNQData.lessonId  || 0,
		};

		var xhr = new XMLHttpRequest();
		xhr.open('POST', TNQData.ajaxUrl, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function () {
			if (xhr.status === 200) {
				try {
					var resp = JSON.parse(xhr.responseText);
					if (resp.success) {
						self._showResults(resp.data);
					} else {
						self._showError(resp.data && resp.data.message ? resp.data.message : 'Submission failed. Please try again.');
					}
				} catch (e) {
					self._showError('Unexpected response from server.');
				}
			} else {
				self._showError('Network error. Please check your connection.');
			}
		};
		xhr.onerror = function () {
			self._showError('Network error. Please check your connection.');
		};

		// Encode body
		var body = Object.keys(data).map(function (k) {
			return encodeURIComponent(k) + '=' + encodeURIComponent(
				typeof data[k] === 'object' ? JSON.stringify(data[k]) : data[k]
			);
		}).join('&');

		xhr.send(body);
	};

	TNQQuiz.prototype._showPracticeDone = function () {
		var html = '<div class="tnq-results-screen">' +
			'<div class="tnq-score-display">Great work!</div>' +
			'<div class="tnq-score-label">You finished all the practice questions.</div>' +
			'<div class="tnq-interpretation">Practice helps you get ready for the real assessment. Ask your teacher when you are ready to begin.</div>' +
			'</div>';

		var questionsWrap = this.container.querySelector('.tnq-questions');
		if (questionsWrap) questionsWrap.style.display = 'none';
		var navEl = this.container.querySelector('.tnq-nav');
		if (navEl) navEl.style.display = 'none';
		var timerEl = this.container.querySelector('.tnq-timer');
		if (timerEl) timerEl.style.display = 'none';

		var done = document.createElement('div');
		done.innerHTML = html;
		this.container.appendChild(done);
	};

	TNQQuiz.prototype._showResults = function (data) {
		// Diagnostic: confirm which instance is rendering and which container it owns.
		// Container selector used: document.querySelectorAll('.tnq-quiz') in boot().
		console.log('[TNQ] _showResults called — mode:', this.mode, '| container:', this.container);

		var self     = this;
		var total    = data.score_total       || 0;
		var algScore = data.score_algorithmic || 0;
		var patScore = data.score_pattern     || 0;
		var logScore = data.score_logical     || 0;
		var interp   = data.interpretation   || '';
		var growth   = data.growth;

		// 1. Capture DOM references BEFORE hiding anything
		var questionsWrap = this.container.querySelector('.tnq-questions');
		var navEl         = this.container.querySelector('.tnq-nav');
		var progressEl    = this.container.querySelector('.tnq-progress');
		var timerEl       = this.container.querySelector('.tnq-timer');

		// 2. Score colour: 0-2 red, 3-4 amber, 5+ green
		var scoreColorClass = total >= 5 ? 'tnq-score-green' : (total >= 3 ? 'tnq-score-amber' : 'tnq-score-red');

		// 3. Build HTML — button is last child inside .tnq-results-screen
		var growthHtml = '';
		if (typeof growth === 'number') {
			if (growth > 0) {
				growthHtml = '<div class="tnq-growth-line tnq-growth-positive">You improved by +' + growth + ' point' + (growth !== 1 ? 's' : '') + '!</div>';
			} else if (growth === 0) {
				growthHtml = '<div class="tnq-growth-line tnq-growth-zero">You held steady.</div>';
			} else {
				growthHtml = '<div class="tnq-growth-line tnq-growth-negative">This was a tricky one \u2014 don\'t worry, you\'re learning!</div>';
			}
		}

		var barHtml = this._skillBarHtml('Algorithmic', algScore) +
		              this._skillBarHtml('Pattern',     patScore) +
		              this._skillBarHtml('Logical',     logScore);

		var html = '<div class="tnq-results-screen">' +
			'<div class="tnq-score-display ' + scoreColorClass + '">You got ' + total + ' / 9</div>' +
			'<div class="tnq-score-label">Well done for completing the assessment!</div>' +
			'<div class="tnq-skill-bars">' + barHtml + '</div>' +
			growthHtml +
			'<div class="tnq-interpretation">' + this._esc(interp) + '</div>' +
			'</div>';

		// 4. Hide quiz chrome
		if (questionsWrap) questionsWrap.style.display = 'none';
		if (navEl)         navEl.style.display         = 'none';
		if (progressEl)    progressEl.style.display    = 'none';
		if (timerEl)       timerEl.style.display       = 'none';

		// 5. Append results to DOM
		var resultDiv = document.createElement('div');
		resultDiv.innerHTML = html;
		this.container.appendChild(resultDiv);

		// 6. Add back button programmatically (avoids template string encoding issues)
		var backBtn = document.createElement('button');
		backBtn.className   = 'tnq-results-back-btn';
		backBtn.textContent = '\u2190 Review my answers';

		var interpretation = resultDiv.querySelector('.tnq-interpretation');
		if (interpretation) {
			interpretation.after(backBtn);
		} else {
			resultDiv.appendChild(backBtn);
		}

		backBtn.addEventListener('click', function () {
			resultDiv.remove();
			if (questionsWrap) questionsWrap.style.display = '';
			if (navEl)         navEl.style.display         = '';
			if (progressEl)    progressEl.style.display    = '';
			self._showQuestion(self.questions.length - 1);
		});
	};

	TNQQuiz.prototype._skillBarHtml = function (label, score) {
		var pct = Math.round((score / 3) * 100);
		return '<div class="tnq-skill-row">' +
			'<span class="tnq-skill-name">' + label + '</span>' +
			'<div class="tnq-skill-bar-track"><div class="tnq-skill-bar-fill" style="width:' + pct + '%"></div></div>' +
			'<span class="tnq-skill-fraction">' + score + ' / 3</span>' +
			'</div>';
	};

	TNQQuiz.prototype._showError = function (msg) {
		var errDiv = document.createElement('div');
		errDiv.className = 'tnq-message';
		errDiv.textContent = msg;
		this.container.appendChild(errDiv);
	};

	TNQQuiz.prototype._esc = function (str) {
		var d = document.createElement('div');
		d.textContent = str;
		return d.innerHTML;
	};

	// ── Boot ────────────────────────────────────────────────────

	function boot() {
		var containers = Array.from(document.querySelectorAll('.tnq-quiz'));
		console.log('[TNQ] boot() found', containers.length, 'container(s):', containers.map(function (c) { return c.dataset.mode || 'no-mode'; }));
		containers.forEach(function (container) {
			var quiz = new TNQQuiz(container);
			quiz.init();
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
}());
