# CLAUDE.md — Tangnest Bebras Interactive Quiz

> Read this file before touching any code. It is the single source of truth for this project.

---

## Project

WordPress plugin for Tangnest STEM Academy, Kigali, Rwanda.
Assesses computational thinking (CT) in children aged 7–12. Integrates with Tutor LMS.

**Plugin slug:** `tangnest-bebras`
**Class prefix:** `TNQ_`
**Text domain:** `tangnest-bebras`
**Min PHP:** 7.4 | **Min WP:** 6.0

---

## Three CT Skills

| Skill | Description |
|---|---|
| Algorithmic | Organising ordered steps to reach an outcome |
| Pattern | Noticing repetition, regularity, predictable change |
| Logical | Understanding constraints, dependencies, cause-effect |

---

## Six Interaction Types

| Type | Description |
|---|---|
| drag-sequence | Order cards left-to-right |
| loop-count | Set a number with +/− buttons |
| click-color | Paint SVG regions following adjacency rules |
| pattern-next | Choose what comes next in a sequence |
| match-pairs | Connect left items to right items |
| drag-sort | Drag items into labeled bins |

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
├── legacy/                          ← DO NOT TOUCH — quarantined v1.0.0 code
│   ├── class-legacy-quiz.php
│   └── questions/
│       ├── pre-questions.php
│       └── post-questions.php
├── admin/
│   ├── class-admin.php
│   ├── class-admin-menu.php
│   ├── class-preview.php
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

**Table:** `{prefix}tnq_results` — created on activation, never modified after creation.

| Column | Type | Description |
|---|---|---|
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

| Token | Value |
|---|---|
| Primary (blue) | `#1A56A0` |
| Secondary (amber) | `#F39C12` |
| Success (green) | `#1E8449` |
| Error (red) | `#C0392B` |
| Background | `#F8F9FF` |
| Card radius | 16px |
| Button radius | 10px |
| Min touch target | 48×48px |
| Body text | 16px minimum |

**Skill colours:**
- Algorithmic → blue `#1A56A0`
- Pattern → amber `#F39C12`
- Logical → green `#1E8449`

---

## User Meta Keys (parent contact)

| Field | `wp_usermeta` key |
|---|---|
| Parent Name | `parent_name` |
| Parent Email | `parent_email` |
| WhatsApp / Phone | `phone_number` |

---

## Shortcodes

| Shortcode | Purpose | Status |
|---|---|---|
| `[tangnest_quiz type="pre"]` | Legacy pre-assessment (do not modify) | ✅ Live — DO NOT TOUCH |
| `[tangnest_quiz type="post"]` | Legacy post-assessment (do not modify) | ✅ Live — DO NOT TOUCH |
| `[tnq_practice age="7-8"]` | Practice quiz — 6 questions, age band 7–8 | ✅ M2 |
| `[tnq_assess type="baseline" age="7-8"]` | Baseline assessment — 9 questions, age band 7–8 | ✅ M2 |
| `[tnq_assess type="endline" age="7-8"]` | Endline assessment — 9 questions, age band 7–8 | ✅ M2 |
| `[tnq_results]` | Shows student score summary | ✅ M2 |
| `[tnq_practice age="9-10"]` | Practice quiz — age band 9–10 | 🔜 Future |
| `[tnq_assess type="baseline" age="9-10"]` | Baseline assessment — age band 9–10 | 🔜 Future |
| `[tnq_assess type="baseline" age="11-12"]` | Baseline assessment — age band 11–12 | 🔜 Future |

### Shortcode Notes
- `age` values: `7-8`, `9-10`, `11-12`
- `type` values for `tnq_assess`: `baseline`, `endline`
- The Overview admin page must scan for `tnq_assess` shortcode in lesson content to detect which courses have CT Assessments attached

---

## Milestone History

| Milestone | Version | Status |
|---|---|---|
| M1 — Foundation | v2.0.0 | ✅ Complete |
| M2 — Quiz Engine + Age 7–8 | v2.1.0 | ✅ Complete |
| M3A — Foundation + Overview | v2.2.0 | 🔧 Current |
| M3B — All Results table | v2.3.0 | 🔜 Next |
| M3C — Student Detail + Email/WhatsApp | v2.4.0 | 🔜 Next |
| M3D — Settings + Export | v2.5.0 | 🔜 Next |

---

## M3 Implementation Plan

### Phase 3A — v2.2.0 — Foundation + Overview
**Goal:** Shared infrastructure + first real admin page.

**Menu changes:**
- Remove `Reset` subpage from `class-admin-menu.php`
- Remove `Question Preview` subpage from `class-admin-menu.php`
- Final menu: Overview · All Results · Student Detail · Settings · Export

**New files:**
```
includes/class-tutor-helper.php     ← get_accessible_courses(), get_enrolled_students()
includes/class-student-meta.php     ← reads parent_name / parent_email / phone_number
admin/assets/admin-dashboard.css    ← all M3 admin styles, scoped to .tnq-admin-wrap
admin/class-admin-overview.php      ← Overview page controller
admin/views/overview.php            ← Overview page template
```

**Modified files:**
```
admin/class-admin-menu.php          ← remove Reset + Question Preview
admin/class-admin.php               ← register Overview controller
includes/class-plugin.php           ← hook new admin classes
tangnest-bebras.php                 ← version bump to 2.2.0
```

**Overview page behaviour:**
- One card per Tutor LMS course accessible to current user (admin = all; teacher = own)
- Card shows: course name, enrolled count, X/Y baseline done, X/Y endline done
- "View Class →" links to `?page=tnq-results&course_id=X`
- Empty state with mascot SVG if no courses found
- Use existing SVG from `/public/assets/svg/` as 64×64px mascot (top-right of header)

**Tutor LMS helper:**
```php
TNQ_Tutor_Helper::get_accessible_courses(): array
TNQ_Tutor_Helper::get_enrolled_students( int $course_id ): array
// Graceful degradation: return [] if Tutor LMS not active
// Use tutor_get_students_by_course_id() if available
// Fallback: query wp_tutor_enrolled directly
```

**Verify before tagging v2.2.0:**
- [ ] PHP lint all new/modified files
- [ ] Version string 2.2.0 consistent across all files
- [ ] Reset and Question Preview gone from menu
- [ ] Overview loads without fatal errors
- [ ] Course cards appear with real Tutor LMS data
- [ ] "View Class" link navigates correctly

---

### Phase 3B — v2.3.0 — All Results Table
**Goal:** Per-course student table with scores.

**New files:**
```
admin/class-admin-results.php       ← All Results controller
admin/views/results.php             ← Results table template
admin/views/partials/score-bars.php ← Reusable coloured score bar component
```

**Table columns:**
```
Student Name | Age Band | Baseline Score | Endline Score | Growth | Actions
```

- Baseline/Endline cells: total score + coloured skill squares (■■□ style, 3 max per skill)
- "Not taken" in grey if no result exists
- Growth cell: `+2 ↑` green / `−1 ↓` red / `= 0` grey — only when both exist
- Actions: [View Report] → `?page=tnq-student&student_id=X&course_id=Y`

**Filters:** Course dropdown + Age band (All | 7–8 | 9–10 | 11–12)
**Pagination:** 25 students per page

**Data query pattern:**
```php
// Get enrolled students for course
// LEFT JOIN tnq_results on student_id + assessment_type
// Take latest completed_at per student per type
```

**Verify before tagging v2.3.0:**
- [ ] PHP lint
- [ ] Version 2.3.0 consistent
- [ ] Table populates for a real course
- [ ] Filters work
- [ ] "Not taken" shows correctly for students with no results
- [ ] Growth column only appears when both scores exist
- [ ] View Report link works

---

### Phase 3C — v2.4.0 — Student Detail + Email + WhatsApp
**Goal:** Full 3-panel report per student + parent contact sharing.

**New files:**
```
admin/class-admin-student.php           ← Student Detail controller
admin/class-admin-email.php             ← AJAX handler for wp_mail send
admin/views/student.php                 ← Student Detail template
admin/views/partials/growth-table.php   ← Baseline vs Endline comparison table
admin/views/partials/parent-contact.php ← Parent section + send buttons
admin/assets/admin-dashboard.js        ← Email AJAX + WhatsApp link builder
```

**Three conditional panels** (show only when data exists):

Panel 1 — Baseline Report:
- Total score + star rating (0–3 ★☆☆, 4–6 ★★☆, 7–9 ★★★)
- Coloured progress bars per skill (0–9 scale)
- Interpretation string using student first name

Panel 2 — Endline Report:
- Same layout as Panel 1

Panel 3 — Growth Report (only when both panels exist):
- Side-by-side table: Baseline | Endline | Change per skill + total
- Growth message:
```php
$delta = $endline_total - $baseline_total;
if      ( $delta >= 3 ) $msg = "{name} made excellent progress!";
elseif  ( $delta >= 1 ) $msg = "Great improvement! {name} improved by {delta} points.";
elseif  ( $delta === 0 ) $msg = "{name} maintained their score. Keep practising!";
else                     $msg = "{name} needs extra support. Review with teacher.";
```

**Parent Contact section:**
- Reads `parent_name`, `parent_email`, `phone_number` from `wp_usermeta`
- Email button → AJAX → `wp_mail()` to `parent_email`
  - Subject: `"{Student Name}'s CT Assessment Report — Tangnest STEM Academy"`
  - Body: plain text score summary
  - Show inline success/error after send
- WhatsApp button → anchor tag:
```php
$phone = preg_replace('/[^0-9]/', '', get_user_meta($student_id, 'phone_number', true));
$url   = "https://wa.me/{$phone}?text=" . urlencode($message);
```

**Verify before tagging v2.4.0:**
- [ ] PHP lint
- [ ] Version 2.4.0 consistent
- [ ] Student Detail loads for a student with baseline only (shows 1 panel)
- [ ] Student Detail loads for a student with both (shows 3 panels)
- [ ] Growth message correct for +, 0, − deltas
- [ ] Email sends and success notice appears
- [ ] WhatsApp link opens correct number
- [ ] Panel hidden when data missing (not empty box — fully absent)

---

### Phase 3D — v2.5.0 — Settings + Export
**Goal:** Working settings persistence + CSV and PDF export.

**New files:**
```
admin/class-admin-settings.php      ← Settings controller (replaces stub)
admin/class-admin-export.php        ← Export controller (replaces stub)
admin/views/settings.php            ← Settings template
admin/views/export.php              ← Export template
```

**Settings fields:**

| Setting | Type | Default | WP Option Key |
|---|---|---|---|
| Active age bands | Checkboxes (7–8, 9–10, 11–12) | 7–8 only | `tnq_active_age_bands` |
| Timer visible to student | Toggle | On | `tnq_timer_visible` |

Save with `update_option()`. Use nonce. Simple layout — no kids UI needed here.

**CSV Export:**
- Filter: course + age band
- One row per student per assessment
- Columns: Student Name, Email, Course, Age Band, Type, Date, Total, Algorithmic, Pattern, Logical
- PHP `Content-Disposition: attachment` download

**PDF Report Card:**
- URL: `?page=tnq-export&action=pdf&student_id=X`
- Print-friendly HTML page — NO external PDF library
- Use `@media print` CSS
- Content: student name, school, date, score bars, growth table
- Trigger with `window.print()` via JS button

**Verify before tagging v2.5.0:**
- [ ] PHP lint
- [ ] Version 2.5.0 consistent
- [ ] Settings save and persist across page reloads
- [ ] CSV downloads with correct columns and data
- [ ] PDF print view renders cleanly
- [ ] Export respects course + age band filters

---

## Hard Constraints — Apply to Every Phase

```
NEVER invent question content — no new questions in M3
NEVER modify tnq_results table schema
NEVER touch any file in /legacy/
NEVER remove or modify existing shortcodes
NEVER use external PHP libraries for PDF
NEVER use localStorage or sessionStorage

Parent meta keys are EXACTLY:
  parent_name / parent_email / phone_number

WhatsApp URL format:
  https://wa.me/{digits_only}?text={urlencode(message)}

All new admin CSS must be scoped to .tnq-admin-wrap
SVG over emoji everywhere in user-facing UI
Score bars use: blue=Algorithmic, amber=Pattern, green=Logical
```

---

## Release Workflow (every phase)

```bash
# 1. PHP lint
find . -name "*.php" | xargs -I{} php -l {}

# 2. Check version consistency
bash scripts/check-version-consistency.sh

# 3. Push to main
git add . && git commit -m "feat: M3X description" && git push origin main

# 4. Watch GitHub Actions in browser

# 5. Tag release (after Actions pass)
git tag v2.X.0 && git push origin v2.X.0

# 6. WordPress live test (verify checklist above)
```

Do not tag a release until the WordPress live checklist for that phase passes.
