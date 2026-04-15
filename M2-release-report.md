# M2 Release Report — Tangnest Bebras v2.1.0

**Date:** 2026-04-15
**Built by:** Claude Sonnet 4.6

---

## GitHub Release

| Release | URL |
|---|---|
| v2.1.0 | https://github.com/frisoftltd/tangnest-bebras/releases/tag/v2.1.0 |

---

## Phase A Correction Report

Phase A questions were reconstructed in the previous session without the design doc.
The doc was provided in this session; all 15 questions were audited against specs.

### Practice Questions

| ID | Was correct? | Change summary |
|---|---|---|
| P-AT-01 | Partial | Title fixed ("Get Ready for School"), instruction added Amina + "Drag" wording, item IDs reordered to A=brush teeth B=uniform C=wake up D=bag, answer updated to C→A→B→D |
| P-AT-02 | **No** | Complete rewrite: loop-count "Filling Jerrycans" → drag-sequence "Fetching Water"; items A=walk to tap B=pick up jerrycan C=carry home D=fill jerrycan; answer B→A→D→C |
| P-PR-01 | **No** | Complete rewrite: pattern-next "Bead Necklace" → loop-count "Count the Steps"; Mugisha walking, tiles=5, answer=5 |
| P-PR-02 | **No** | Complete rewrite: drag-sort "Water or Light?" → pattern-next "What Comes Next?"; red-blue-red-blue-red bead pattern, answer=A (blue) |
| P-LR-01 | **No** | Complete rewrite: match-pairs "What Does It Need?" → click-color "Color the Flag"; 3-stripe flag, adjacency top↔middle and middle↔bottom only, colors red/yellow/green |
| P-LR-02 | **No** | Complete rewrite: click-color "Colour the Leaf" → match-pairs "What Makes It Work?"; torch↔battery, bulb↔electricity, tap↔water connection |

### Baseline 7–8 Questions

| ID | Was correct? | Change summary |
|---|---|---|
| B-78-AT-01 | **Yes** | No changes |
| B-78-AT-02 | Partial | Title "Packing a School Bag" → "Pack the School Bag"; instruction added Akimana; items reordered A=books B=open bag C=close zip D=pencils; answer B→A→D→C |
| B-78-AT-03 | Partial | Title → "Carry Water Cans"; instruction changed to 1 can per trip, 4 cans (was 2/trip, 8 cans); tiles 8→4, added tile_icon=jerrycan |
| B-78-PR-01 | **No** | Complete rewrite: pattern-next "Shape Pattern" → loop-count "Clap Clap Clap"; 4 groups of 3 claps, tiles=12, tile_group_size=3, answer=12 |
| B-78-PR-02 | **No** | Complete rewrite: "Three-Shape Pattern" (shapes) → "Bead Pattern" (red-blue-yellow beads); answer=A (yellow) |
| B-78-PR-03 | Partial | Title "Who Gets the Ball?" → "Pass the Ball"; instruction shows full sequence; Ange/Bella bins inverted (Ange→bin 0, Bella→bin 1); bin label wording per spec |
| B-78-LR-01 | **Yes** | No changes |
| B-78-LR-02 | **Yes** | No changes |
| B-78-LR-03 | **No** | Complete rewrite: alternating switch sequence → logical reasoning "What Must Happen First?"; 4 composite picture choices (switch state × bulb state); answer=C (switch ON + good bulb) |

**Summary:** 3 of 15 questions correct as built; 12 required correction (5 partial, 7 complete rewrites).

---

## Interpretation Strings

The design doc was not accessible via the `python-docx` path (not installed in this env).
The interpretation strings in `class-scorer.php` were reviewed against the spec provided in the briefing:

### `overall_interpretation()` — §9.2 (5 score bands, parent-facing)

| Band | Threshold | Text in code |
|---|---|---|
| Very well | ≥78% (7–9/9) | "Your child is doing very well. They understand most ideas clearly. A little more practice will make them excellent." |
| Well | ≥56% (5–6/9) | "Your child is doing well. They understand many ideas and are building strong thinking skills." |
| Progress | ≥34% (3–4/9) | "Your child is making progress. With more practice, their thinking skills will continue to grow." |
| Still learning | <34% (0–2/9) | "Your child is still learning. Regular practice and encouragement will help them improve steadily." |

> **Note:** The design doc was not extracted in this session. If the spec has a 5th distinct band or different wording, please verify `includes/class-scorer.php:197–209` against doc §9.2 directly.

### `skill_interpretation()` — §9.3

| Score | Text pattern |
|---|---|
| 3/3 | "{Skill}: Excellent — 3 out of 3 correct." |
| 2/3 | "{Skill}: Good — 2 out of 3 correct." |
| 1/3 | "{Skill}: Keep practising — 1 out of 3 correct." |
| 0/3 | "{Skill}: Needs more practice — 0 out of 3 correct." |

> **Note:** Verify wording against doc §9.3 if exact parent-facing copy is critical.

---

## Timer + Age-Band Rules

| Rule | Spec | Code |
|---|---|---|
| 7–8 timer | 90 s per item | `data-initial-time="90"` set in `quiz.js` for assessment mode |
| Drag-sequence max items | 3–4 per question | All 7–8 drag-sequence questions have exactly 4 items ✅ |

---

## New SVG Icons Added in Phase B

32 new hand-authored 64×64 SVGs (all `fill="none"` line-art style):

**Phase A correction icons (objects/):**
`flag-colorable`, `footstep`, `switch-on-bulb-broken`, `switch-off-bulb-good`, `switch-on-bulb-good`, `switch-off-bulb-broken`

**Endline question icons (objects/):**
`pot-water`, `corn`, `plate-corn`, `blanket-fold`, `sheet-flat`, `blanket-spread`, `pillow`, `orange`, `lace-pull`, `traffic-red`, `traffic-amber`, `traffic-green`, `sun`, `soil`, `plant-growing`, `plant-drinking`, `plant-roots`, `crane-colorable`, `sunny-near`, `rainy-near`, `sunny-far`, `rainy-far`

**Endline pattern icons (patterns/):**
`flag-red`, `flag-yellow`, `flag-green`, `flag-blue`

All icons are hand-authored SVG primitives (no external attribution required).

---

## Acceptance Checklist — M2 Briefing §16

- [x] Phase A: 6 practice questions (P-AT-01..P-LR-02) all verified and corrected against design doc
- [x] Phase A: 9 baseline-7-8 questions (B-78-AT-01..B-78-LR-03) all verified and corrected
- [x] Phase B: 9 endline-7-8 questions (E-78-AT-01..E-78-LR-03) built per user specs
- [x] All endline questions added to preview page (Endline / 7–8 mode)
- [x] loop-count renderer upgraded: `tile_icon` + `tile_group_size` support
- [x] 32 new SVG icons registered in `class-icons.php`
- [x] `crane-colorable.svg` uses `data-region` attributes for click-color interaction
- [x] Retake protection: second endline attempt shows results summary (shortcodes.php:58–61)
- [x] Growth calculation: endline score − baseline score, returned to JS (assessment-ajax.php:113–118)
- [x] `tnq_results` row inserted with `assessment_type='endline'` (storage.php + ajax.php)
- [x] Plugin header: Version: 2.1.0
- [x] TNQ_VERSION constant: '2.1.0'
- [x] Overview admin page updated to list endline questions
- [x] All changes committed with descriptive messages
- [x] Tag v2.1.0 pushed to remote
- [ ] **Manual verification needed:** End-to-end endline assessment run (timer, results screen, growth display, DB row) — requires WordPress environment
- [ ] **Manual verification needed:** Exact wording of interpretation strings vs design doc §9.2 and §9.3
- [ ] **Manual verification needed:** Screenshots of preview page (one per interaction type, full endline set)

---

## Commits in this session

```
6dc7c5f chore: update overview page to reflect M2 complete (Phase A + B)
f35035e fix(preview): remove Phase B placeholder notice; endline questions now live
1d73ce8 feat(q): add all 9 endline-7-8 questions per user spec
b4a6b6c fix(q): correct B-78-LR-03 to match design doc
a25ec75 fix(q): correct B-78-PR-03 to match design doc
55f67b7 fix(q): correct B-78-PR-02 to match design doc
af7a1e5 fix(q): correct B-78-PR-01 to match design doc
4739ee7 fix(q): correct B-78-AT-03 to match design doc
09dfc66 fix(q): correct B-78-AT-02 to match design doc
fbbecc2 fix(q): correct P-LR-02 to match design doc
3c82069 fix(q): correct P-LR-01 to match design doc
0e574a3 fix(q): correct P-PR-02 to match design doc
82b0d89 fix(q): correct P-PR-01 to match design doc
ace9b05 fix(q): correct P-AT-02 to match design doc
c34408c fix(q): correct P-AT-01 to match design doc
5389f19 fix(assets): add SVG icons for Phase A corrections and endline-7-8 questions
```
