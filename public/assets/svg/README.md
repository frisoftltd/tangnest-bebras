# SVG Asset Catalog — Tangnest CT Assessment

## Attribution

All SVGs in this directory are **hand-authored** for the Tangnest CT Assessment plugin. They were not sourced from any third-party icon library — no external attribution is required.

Design constraints followed:
- `viewBox="0 0 64 64"`, `width="64"`, `height="64"`
- Flat / line-art style, 2–3 colors per icon
- Strokes 2–3 px at 64×64 for legibility at small sizes
- No text inside SVGs (labels rendered separately)
- `fill="currentColor"` used where CSS color override is needed
- No external references, no `<image>` embeds

## Directory structure

```
svg/
├── _template.svg          Base template for new icons
├── objects/               Real-world objects used in questions
├── patterns/              Beads and shapes for pattern questions
├── people/                Human silhouettes (culturally neutral)
├── places/                Buildings and locations
└── ui/                    Interface elements (timer, hint, check, cross)
```

## Icon catalog

### objects/

| Key | File | Used in |
|---|---|---|
| `alarm-clock` | objects/alarm-clock.svg | P-AT-01 practice |
| `ball-soccer` | objects/ball-soccer.svg | B-78-PR-03 |
| `battery` | objects/battery.svg | P-LR-01, B-78-LR-01 |
| `books` | objects/books.svg | B-78-AT-02 |
| `brush-teeth` | objects/brush-teeth.svg | P-AT-01 |
| `bulb` | objects/bulb.svg | P-LR-01 |
| `bulb-lit` | objects/bulb-lit.svg | B-78-LR-03 |
| `bulb-broken` | objects/bulb-broken.svg | B-78-LR-03 |
| `clap-hand` | objects/clap-hand.svg | B-78-PR-03 |
| `cup-pour` | objects/cup-pour.svg | B-78-AT-01 |
| `door` | objects/door.svg | B-78-LR-01 |
| `electricity-plug` | objects/electricity-plug.svg | P-LR-01 |
| `fire` | objects/fire.svg | B-78-AT-01 |
| `handle` | objects/handle.svg | B-78-LR-01 |
| `jerrycan` | objects/jerrycan.svg | P-AT-02, B-78-AT-03 |
| `key` | objects/key.svg | B-78-LR-01 |
| `kettle` | objects/kettle.svg | P-AT-01 (practice ref) |
| `kettle-pour` | objects/kettle-pour.svg | B-78-AT-01 |
| `leaf-colorable` | objects/leaf-colorable.svg | B-78-LR-02 (click-color) |
| `pencil` | objects/pencil.svg | B-78-AT-02 |
| `school-bag` | objects/school-bag.svg | P-AT-01 |
| `switch-off` | objects/switch-off.svg | B-78-LR-03 |
| `switch-on` | objects/switch-on.svg | B-78-LR-03 |
| `tea-cup` | objects/tea-cup.svg | B-78-AT-01 (result) |
| `teabag` | objects/teabag.svg | B-78-AT-01 |
| `torch` | objects/torch.svg | P-LR-01, B-78-LR-01 |
| `uniform` | objects/uniform.svg | P-AT-01 |
| `water-drop` | objects/water-drop.svg | P-LR-01 |
| `zip` | objects/zip.svg | B-78-AT-02 |

### patterns/

| Key | File | Used in |
|---|---|---|
| `bead-red` | patterns/bead-red.svg | P-PR-01, B-78-PR-02 |
| `bead-blue` | patterns/bead-blue.svg | P-PR-01, B-78-PR-02 |
| `bead-yellow` | patterns/bead-yellow.svg | P-PR-01 choices |
| `bead-green` | patterns/bead-green.svg | P-PR-01 choices |
| `circle` | patterns/circle.svg | B-78-PR-01 |
| `square` | patterns/square.svg | B-78-PR-01 |
| `star` | patterns/star.svg | B-78-PR-01 |
| `triangle` | patterns/triangle.svg | B-78-PR-01 |

### places/

| Key | File | Used in |
|---|---|---|
| `house` | places/house.svg | P-PR-02 |
| `tap` | places/tap.svg | P-LR-01, B-78-LR-01 |

### ui/

| Key | File | Used in |
|---|---|---|
| `check` | ui/check.svg | Feedback (correct) |
| `cross` | ui/cross.svg | Feedback (wrong) |
| `hint-bulb` | ui/hint-bulb.svg | Hint button |
| `timer-clock` | ui/timer-clock.svg | Assessment timer |

## Adding new icons

1. Copy `_template.svg` to the appropriate category folder.
2. Name the file using kebab-case matching the icon key used in question PHP arrays.
3. Keep the viewBox `0 0 64 64` and preserve thick strokes (2–3 px).
4. Add an entry to this README.
5. Test via the preview page: CT Assessments → Question Preview.
