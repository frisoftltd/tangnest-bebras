# M1 Release Report — Tangnest Bebras v2.0.0

**Date:** 2026-04-14
**Built by:** Claude Sonnet 4.6

---

## GitHub Releases

| Release | URL | Zip asset |
|---|---|---|
| v2.0.0 (install this first) | https://github.com/frisoftltd/tangnest-bebras/releases/tag/v2.0.0 | `tangnest-bebras-2.0.0.zip` (192 KB) |
| v2.0.1 (smoke-test target) | https://github.com/frisoftltd/tangnest-bebras/releases/tag/v2.0.1 | `tangnest-bebras-2.0.1.zip` (192 KB) |

**Direct download — install this on WordPress first:**
```
https://github.com/frisoftltd/tangnest-bebras/releases/download/v2.0.0/tangnest-bebras-2.0.0.zip
```

---

## Workflow Results

| Workflow | Trigger | Result |
|---|---|---|
| Build Plugin | push to `main` (M1 scaffold) | ✅ success |
| Build Plugin | push to `main` (version script fix) | ✅ success |
| Release Plugin | tag `v2.0.0` (first attempt) | ❌ failed — version script wrote info to stdout, breaking `GITHUB_OUTPUT` |
| Release Plugin | tag `v2.0.0` (fixed, retagged) | ✅ success |
| Build Plugin | push to `main` (2.0.1 bump) | ✅ success |
| Release Plugin | tag `v2.0.1` | ✅ success |

**One issue encountered and fixed:** `scripts/check-version-consistency.sh` was sending a human-readable verification message to stdout alongside the bare version number. `release.yml` captures stdout as the `VERSION` variable, so the multiline output caused the `GITHUB_OUTPUT` write to fail. Fixed by redirecting all informational messages to stderr (`>&2`), leaving only the bare version number on stdout.

---

## Zip Contents Verification (v2.0.0)

| Check | Result |
|---|---|
| Top-level folder | `tangnest-bebras/` ✅ |
| `tangnest-bebras/tangnest-bebras.php` present | ✅ |
| `Version: 2.0.0` in plugin header | ✅ |
| `GitHub Plugin URI: frisoftltd/tangnest-bebras` in header | ✅ |
| `tangnest-bebras/vendor/plugin-update-checker/` present | ✅ |
| `tangnest-bebras/legacy/class-legacy-quiz.php` present | ✅ |
| `tangnest-bebras/uninstall.php` present | ✅ |

---

## M1 Acceptance Checklist — User to verify on WordPress test site

### Install v2.0.0 (fresh install path)
- [ ] Uploaded `tangnest-bebras-2.0.0.zip` via Plugins → Add New → Upload
- [ ] Activated without PHP errors
- [ ] `wp_tnq_results` table exists with 13 columns (check via phpMyAdmin or `wp db query "DESCRIBE wp_tnq_results;"`)

### Legacy Bebras quiz still works
- [ ] Page with `[tangnest_quiz type="pre"]` renders the quiz
- [ ] Page with `[tangnest_quiz type="post"]` renders the quiz
- [ ] Completing a quiz saves `tnq_pre_score` / `tnq_post_score` to user meta
  - Check Users → Edit User → scroll to custom fields, or: `wp user meta list <user_id> | grep tnq_`

### New CT Assessment foundation
- [ ] "CT Assessments" menu appears in admin sidebar
- [ ] All 6 subpages load and show "Coming in Milestone 3"
- [ ] `[tnq_practice]` renders the dashed placeholder box
- [ ] `[tnq_assess]` renders the dashed placeholder box
- [ ] `[tnq_results]` renders the dashed placeholder box
- [ ] `[tnq_admin_results]` renders the dashed placeholder box

### Uninstall safety
- [ ] Deactivate plugin → `wp_tnq_results` table still exists
- [ ] User meta `tnq_pre_score` etc. still exists
- [ ] Reactivate → no errors, no duplicate table creation warnings

### Update flow — THE critical M1 deliverable
- [ ] On Plugins page, a "Check for Update" action link appears under "Tangnest Bebras"
- [ ] Clicking it redirects back to Plugins page
- [ ] Success notice shows: "Tangnest Bebras: Update check complete."
- [ ] WordPress now shows "Update available" notice (because v2.0.1 exists on GitHub)
- [ ] Clicking "update now" updates to v2.0.1 without errors
- [ ] After update: CT Assessments → Overview shows "Plugin version: 2.0.1"
- [ ] After update: `[tangnest_quiz type="pre"]` still works
- [ ] After update: `wp_tnq_results` table still exists (update did not wipe data)

### Quality gate
- [ ] Enable `WP_DEBUG` in wp-config.php, exercise every feature above
- [ ] `/wp-content/debug.log` shows no PHP notices/warnings/errors from `tangnest-bebras`
