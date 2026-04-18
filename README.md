# Tangnest Bebras Interactive Quiz

**Computational Thinking Assessment Plugin for WordPress**

Built for [Tangnest STEM Academy](https://lms.tangnest.rw) — Kigali, Rwanda

---

## Overview

A WordPress plugin that assesses three computational thinking skills in children aged 7–12 using child-friendly, Rwanda-grounded visual interactions. Integrates with Tutor LMS.

**Three CT skills measured:**
- **Algorithmic Thinking** — organising ordered steps to reach an outcome
- **Pattern Recognition** — noticing repetition, regularity, and predictable change
- **Logical Reasoning** — understanding constraints, dependencies, and cause-effect

**Six interaction types:**
- Drag Sequence — order cards left-to-right
- Loop Count — set a number with +/− buttons
- Click-to-Color — paint SVG regions following adjacency rules
- Pattern Next — choose what comes next in a sequence
- Match Pairs — connect left items to right items
- Drag Sort — drag items into labeled bins

---

## Development Status

| Milestone | Version | Status | Description |
|-----------|---------|--------|-------------|
| M1 — Foundation | v2.0.0 | ✅ Complete | Modular plugin skeleton, DB table, admin menu stubs, GitHub self-update mechanism |
| M2 — Quiz Engine + Age 7–8 | v2.1.0 | 🔧 In Progress | Full quiz engine, 6 interaction types, practice + baseline + endline for age 7–8. Code built — pending WordPress testing & release. |
| M3 — Age 9–10, 11–12 + Reporting | — | 🔜 Next | Remaining age bands, parent email, Tutor LMS gradebook, admin dashboard, CSV export |

### M2 Progress

- [x] Design system CSS
- [x] SVG asset pipeline (~67 icons, no emoji in user-facing UI)
- [x] 6 interaction type JS modules (native drag/pointer, no external libraries)
- [x] Quiz engine (timer, progress, practice feedback, AJAX submit, results screen)
- [x] PHP backend (question bank, renderer, scorer, storage, AJAX handlers)
- [x] 6 practice questions — verified against design doc
- [x] 9 baseline 7–8 questions — verified against design doc
- [x] 9 endline 7–8 questions — built from user specs
- [x] Admin Question Preview page
- [x] PHP lint clean (33 files)
- [ ] WordPress live testing
- [ ] Tag v2.1.0 release
- [ ] End-to-end assessment flow verified
- [ ] Interpretation strings verified against design doc §9.2/§9.3

---

## Current Live Features (v2.0.0)

### Foundation
- Modular plugin structure (14 PHP classes)
- `tnq_results` database table created on activation
- CT Assessments admin menu with 6 subpages (content coming in M3)
- GitHub self-update: "Check for Update" link on Plugins page, one-click update from GitHub Releases

### Legacy Support
- Original `[tangnest_quiz]` shortcode from v1.0.0 fully preserved
- Existing user meta (`tnq_pre_score`, `tnq_post_score`, etc.) untouched
- Legacy quiz lives in `/legacy/` — quarantined from new development

---

## Shortcodes

| Shortcode | Status | Description |
|-----------|--------|-------------|
| `[tangnest_quiz type="pre"]` | ✅ Live | Legacy pre-course Bebras quiz (10 items) |
| `[tangnest_quiz type="post"]` | ✅ Live | Legacy post-course Bebras quiz (10 items) |
| `[tnq_practice age="7-8"]` | 🔧 M2 | 6 practice items with feedback |
| `[tnq_assess type="baseline" age="7-8"]` | 🔧 M2 | 9-item baseline assessment |
| `[tnq_assess type="endline" age="7-8"]` | 🔧 M2 | 9-item endline assessment |
| `[tnq_results]` | 🔧 M2 | Student's own results summary |
| `[tnq_practice age="9-10"]` | 🔜 M3 | Practice for age 9–10 |
| `[tnq_assess type="baseline" age="9-10"]` | 🔜 M3 | Baseline for age 9–10 |
| `[tnq_assess type="baseline" age="11-12"]` | 🔜 M3 | Baseline for age 11–12 |
| `[tnq_admin_results]` | 🔜 M3 | Admin results table |

---

## Plugin Structure

```
tangnest-bebras/
├── tangnest-bebras.php              ← bootstrap (~50 lines)
├── uninstall.php
├── includes/
│   ├── class-plugin.php             ← TNQ_Plugin: singleton, hook registration
│   ├── class-activator.php          ← creates tnq_results table
│   ├── class-deactivator.php        ← no-op
│   ├── class-database.php           ← dbDelta wrapper, schema versioning
│   ├── class-shortcodes.php         ← registers [tnq_*] shortcodes
│   ├── class-updater.php            ← GitHub release update checker (PUC v5.5)
│   ├── class-i18n.php               ← text domain loader
│   ├── class-question-bank.php      ← returns questions by mode/age/skill
│   ├── class-renderer.php           ← outputs quiz HTML shell
│   ├── class-icons.php              ← SVG loader helper
│   ├── class-scorer.php             ← server-side answer validation
│   ├── class-storage.php            ← writes to tnq_results table
│   ├── class-assessment-ajax.php    ← AJAX handlers
│   └── questions/
│       ├── practice.php
│       ├── baseline-7-8.php
│       └── endline-7-8.php
├── legacy/
│   ├── class-legacy-quiz.php        ← preserved v1.0.0 Bebras engine
│   └── questions/
│       ├── pre-questions.php
│       └── post-questions.php
├── admin/
│   ├── class-admin.php
│   ├── class-admin-menu.php
│   ├── class-preview.php            ← Question Preview page
│   ├── views/
│   └── assets/
├── public/
│   ├── class-public.php
│   └── assets/
│       ├── quiz.css
│       ├── quiz.js
│       ├── interactions/            ← one JS file per interaction type
│       └── svg/                     ← ~67 SVG icons
├── vendor/plugin-update-checker/
├── scripts/
│   ├── build-plugin-zip.sh
│   └── check-version-consistency.sh
└── .github/workflows/
    ├── build.yml
    └── release.yml
```

---

## Database

**Table:** `{prefix}tnq_results`

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK | Row ID |
| student_id | BIGINT | WordPress user ID |
| assessment_type | ENUM | practice, baseline, endline |
| age_band | ENUM | 7-8, 9-10, 11-12 |
| score_total | TINYINT | Total out of 9 |
| score_algorithmic | TINYINT | 0–3 |
| score_pattern | TINYINT | 0–3 |
| score_logical | TINYINT | 0–3 |
| answers_json | LONGTEXT | Raw answers for audit |
| duration_seconds | SMALLINT | Time taken |
| completed_at | DATETIME | UTC timestamp |
| tutor_course_id | BIGINT | Tutor LMS course ID |
| tutor_lesson_id | BIGINT | Tutor LMS lesson ID |

---

## Design System

| Element | Value |
|---------|-------|
| Primary | `#1A56A0` (Tangnest blue) |
| Secondary | `#F39C12` (warm amber) |
| Success | `#1E8449` (green) |
| Error | `#C0392B` (red) |
| Background | `#F8F9FF` |
| Card radius | 16px |
| Button radius | 10px |
| Min touch target | 48×48px |
| Body text | 16px minimum |

---

## Release History

| Version | Date | Changes |
|---------|------|---------|
| v2.1.0 | April 2026 | *Pending release* — Quiz engine, 6 interaction types, practice + baseline + endline for age 7–8, 67 SVG icons, admin preview page |
| v2.0.1 | April 2026 | Update-flow smoke test (version bump only) |
| v2.0.0 | April 2026 | Modular refactor, DB table, admin menu skeleton, GitHub self-update mechanism |
| v1.0.0 | — | Original single-file Bebras plugin (legacy, preserved in /legacy/) |

---

## Coming in M3

- Age bands 9–10 and 11–12 (18 baseline + 18 endline questions)
- Parent email reports on assessment completion
- Tutor LMS gradebook integration
- Admin results dashboard with filtering
- Student detail view (item-by-item breakdown for teachers)
- CSV export
- Admin endline reset per student

---

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Tutor LMS plugin (optional — graceful degradation if absent)

---

## License

GPL v2 or later

---

*Tangnest Ltd — Kigali, Rwanda — [lms.tangnest.rw](https://lms.tangnest.rw)*
