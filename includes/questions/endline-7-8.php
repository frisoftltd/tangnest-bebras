<?php
/**
 * Endline assessment questions — Age band 7–8 (9 questions).
 *
 * IDs: E-78-AT-01 through E-78-LR-03
 * Source: user-provided specs (M2 Phase B briefing, 2026-04-15)
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

return [

	// ══ ALGORITHMIC (3) ══════════════════════════════════════════

	// ── E-78-AT-01 · Algorithmic · drag-sequence · Easy ──────────
	[
		'id'         => 'E-78-AT-01',
		'skill'      => 'algorithmic',
		'type'       => 'drag-sequence',
		'difficulty' => 'easy',
		'title'      => 'Cooking Corn on the Cob',
		'title_icon' => 'corn',
		'instruction'=> 'Mukundwa is cooking corn on the cob. Put the steps in the right order.',
		'items'      => [
			[ 'id' => 'a', 'icon' => 'pot-water',  'label' => 'Pour water in the pot' ],
			[ 'id' => 'b', 'icon' => 'fire',        'label' => 'Boil the water'        ],
			[ 'id' => 'c', 'icon' => 'corn',        'label' => 'Put the corn in the pot' ],
			[ 'id' => 'd', 'icon' => 'plate-corn',  'label' => 'Take the corn out and serve' ],
		],
		'answer'     => [ 'a', 'b', 'c', 'd' ],
	],

	// ── E-78-AT-02 · Algorithmic · drag-sequence · Medium ────────
	[
		'id'         => 'E-78-AT-02',
		'skill'      => 'algorithmic',
		'type'       => 'drag-sequence',
		'difficulty' => 'medium',
		'title'      => 'Making the Bed',
		'title_icon' => 'blanket-spread',
		'instruction'=> 'Help Cyusa make his bed in the right order.',
		'items'      => [
			[ 'id' => 'a', 'icon' => 'blanket-fold',   'label' => 'Take off the blanket'    ],
			[ 'id' => 'b', 'icon' => 'sheet-flat',     'label' => 'Straighten the sheet'    ],
			[ 'id' => 'c', 'icon' => 'blanket-spread', 'label' => 'Put the blanket back on' ],
			[ 'id' => 'd', 'icon' => 'pillow',         'label' => 'Put the pillow at the top' ],
		],
		'answer'     => [ 'a', 'b', 'c', 'd' ],
	],

	// ── E-78-AT-03 · Algorithmic · loop-count · Hard ─────────────
	[
		'id'              => 'E-78-AT-03',
		'skill'           => 'algorithmic',
		'type'            => 'loop-count',
		'difficulty'      => 'hard',
		'title'           => 'Squeezing Juice',
		'title_icon'      => 'orange',
		'instruction'     => 'Mama needs 2 oranges to make 1 glass of juice. She wants to make 3 glasses. How many oranges does she need?',
		'tiles'           => 6,
		'tile_icon'       => 'orange',
		'tile_group_size' => 2,
		'min'             => 1,
		'max'             => 20,
		'initial'         => 1,
		'answer'          => 6,
	],

	// ══ PATTERN (3) ══════════════════════════════════════════════

	// ── E-78-PR-01 · Pattern · loop-count · Easy ─────────────────
	[
		'id'              => 'E-78-PR-01',
		'skill'           => 'pattern',
		'type'            => 'loop-count',
		'difficulty'      => 'easy',
		'title'           => 'Tying Shoes',
		'title_icon'      => 'lace-pull',
		'instruction'     => 'To tie one shoe, Keza pulls the laces 3 times. She has 2 shoes. How many times does she pull the laces in total?',
		'tiles'           => 6,
		'tile_icon'       => 'lace-pull',
		'tile_group_size' => 3,
		'min'             => 1,
		'max'             => 20,
		'initial'         => 1,
		'answer'          => 6,
	],

	// ── E-78-PR-02 · Pattern · pattern-next · Medium ─────────────
	[
		'id'         => 'E-78-PR-02',
		'skill'      => 'pattern',
		'type'       => 'pattern-next',
		'difficulty' => 'medium',
		'title'      => 'Classroom Flag Colors',
		'title_icon' => 'flag-red',
		'instruction'=> 'Look at the row of small flags on the wall. What color flag comes next?',
		'pattern'    => [
			[ 'icon' => 'flag-red'    ],
			[ 'icon' => 'flag-yellow' ],
			[ 'icon' => 'flag-red'    ],
			[ 'icon' => 'flag-yellow' ],
			[ 'icon' => 'flag-red'    ],
		],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'flag-yellow', 'label' => 'Yellow' ],
			[ 'id' => 'b', 'icon' => 'flag-red',    'label' => 'Red'    ],
			[ 'id' => 'c', 'icon' => 'flag-green',  'label' => 'Green'  ],
			[ 'id' => 'd', 'icon' => 'flag-blue',   'label' => 'Blue'   ],
		],
		'answer'     => 'a',
	],

	// ── E-78-PR-03 · Pattern · drag-sort · Hard ──────────────────
	// Traffic light sequence: red → amber → green → repeat.
	// Last light shown was amber; green comes next.
	[
		'id'         => 'E-78-PR-03',
		'skill'      => 'pattern',
		'type'       => 'drag-sort',
		'difficulty' => 'hard',
		'title'      => 'Traffic Light Sequence',
		'title_icon' => 'traffic-amber',
		'instruction'=> 'A traffic light goes: red → amber → green → red → amber → green ... If the last light shown was amber, sort each color into "Comes next" or "Does NOT come next".',
		'items'      => [
			[ 'id' => 'light_green', 'icon' => 'traffic-green', 'label' => 'Green', 'bin' => 0 ],
			[ 'id' => 'light_red',   'icon' => 'traffic-red',   'label' => 'Red',   'bin' => 1 ],
			[ 'id' => 'light_amber', 'icon' => 'traffic-amber', 'label' => 'Amber', 'bin' => 1 ],
		],
		'bins'       => [ 'Comes next after amber', 'Does NOT come next' ],
	],

	// ══ LOGICAL (3) ══════════════════════════════════════════════

	// ── E-78-LR-01 · Logical · match-pairs · Easy ────────────────
	[
		'id'         => 'E-78-LR-01',
		'skill'      => 'logical',
		'type'       => 'match-pairs',
		'difficulty' => 'easy',
		'title'      => 'What a Plant Needs',
		'title_icon' => 'plant-growing',
		'instruction'=> 'Match each thing on the left to what the plant needs it for.',
		'left'       => [
			[ 'id' => 'sun',   'icon' => 'sun',        'label' => 'Sunshine' ],
			[ 'id' => 'water', 'icon' => 'water-drop', 'label' => 'Water'    ],
			[ 'id' => 'soil',  'icon' => 'soil',       'label' => 'Soil'     ],
		],
		'right'      => [
			[ 'id' => 'growth', 'icon' => 'plant-growing',  'label' => 'To grow tall'        ],
			[ 'id' => 'drink',  'icon' => 'plant-drinking', 'label' => 'To drink'             ],
			[ 'id' => 'roots',  'icon' => 'plant-roots',    'label' => 'To hold the roots'   ],
		],
		'pairs'      => [
			[ 'sun',   'growth' ],
			[ 'water', 'drink'  ],
			[ 'soil',  'roots'  ],
		],
	],

	// ── E-78-LR-02 · Logical · click-color · Medium ──────────────
	// Crested crane — Rwanda's national bird.
	// Regions: body, wing, tail, beak
	// Adjacency: body↔wing, body↔tail, body↔beak, wing↔tail
	[
		'id'          => 'E-78-LR-02',
		'skill'       => 'logical',
		'type'        => 'click-color',
		'difficulty'  => 'medium',
		'title'       => 'Color the Crested Crane',
		'title_icon'  => 'crane-colorable',
		'instruction' => 'Color the crested crane. Rule: Two parts that touch cannot be the same color.',
		'svg'         => 'crane-colorable',
		'regions'     => [ 'body', 'wing', 'tail', 'beak' ],
		'adjacency'   => [
			[ 'body', 'wing' ],
			[ 'body', 'tail' ],
			[ 'body', 'beak' ],
			[ 'wing', 'tail' ],
		],
		'colors'      => [ '#C0392B', '#1E8449', '#F1C40F' ],
		'color_labels'=> [ 'Red', 'Green', 'Yellow' ],
	],

	// ── E-78-LR-03 · Logical · pattern-next · Hard ───────────────
	// Uses pattern-next renderer with empty pattern (choices only).
	// Logical: umbrella needed only when raining AND going far.
	[
		'id'         => 'E-78-LR-03',
		'skill'      => 'logical',
		'type'       => 'pattern-next',
		'difficulty' => 'hard',
		'title'      => 'Umbrella Needed?',
		'title_icon' => 'rainy-far',
		'instruction'=> 'Mukamana goes outside. The umbrella is only needed when it is raining AND she is going far. Which picture shows when she MUST take the umbrella?',
		'pattern'    => [],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'sunny-near', 'label' => 'Sunny + going next door'   ],
			[ 'id' => 'b', 'icon' => 'rainy-near', 'label' => 'Raining + going next door' ],
			[ 'id' => 'c', 'icon' => 'sunny-far',  'label' => 'Sunny + going far to school' ],
			[ 'id' => 'd', 'icon' => 'rainy-far',  'label' => 'Raining + going far to school' ],
		],
		'answer'     => 'd',
	],

];
