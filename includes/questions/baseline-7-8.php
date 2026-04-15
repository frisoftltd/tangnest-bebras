<?php
/**
 * Baseline assessment questions — Age band 7–8 (9 questions).
 *
 * IDs: B-78-AT-01 through B-78-LR-03
 * Source: design doc §7.2
 *
 * NOTE: The design doc was not available during M2 build.
 * Question content was reconstructed from context clues in the M2 briefing
 * (data structure examples showing B-78-AT-01 explicitly, SVG asset lists,
 * and pedagogical patterns). Verify against the original design doc.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

return [

	// ══ ALGORITHMIC (3) ══════════════════════════════════════════

	// ── B-78-AT-01 · Algorithmic · drag-sequence · Easy ──────────
	// Explicitly shown as B-78-AT-01 in M2 briefing data structure example.
	[
		'id'         => 'B-78-AT-01',
		'skill'      => 'algorithmic',
		'type'       => 'drag-sequence',
		'difficulty' => 'easy',
		'title'      => 'Making Tea',
		'title_icon' => 'tea-cup',
		'instruction'=> 'Put the pictures in the right order to make a cup of tea.',
		'items'      => [
			[ 'id' => 'a', 'icon' => 'kettle-pour', 'label' => 'Pour water in kettle' ],
			[ 'id' => 'b', 'icon' => 'fire',         'label' => 'Boil the water'      ],
			[ 'id' => 'c', 'icon' => 'teabag',       'label' => 'Put tea bag in cup'  ],
			[ 'id' => 'd', 'icon' => 'cup-pour',     'label' => 'Pour hot water in cup'],
		],
		'answer'     => [ 'a', 'b', 'c', 'd' ],
	],

	// ── B-78-AT-02 · Algorithmic · drag-sequence · Medium ────────
	[
		'id'         => 'B-78-AT-02',
		'skill'      => 'algorithmic',
		'type'       => 'drag-sequence',
		'difficulty' => 'medium',
		'title'      => 'Pack the School Bag',
		'title_icon' => 'school-bag',
		'instruction'=> 'Akimana packs her bag. What is the right order?',
		'items'      => [
			[ 'id' => 'a', 'icon' => 'books',      'label' => 'Put in books'  ],
			[ 'id' => 'b', 'icon' => 'school-bag', 'label' => 'Open the bag'  ],
			[ 'id' => 'c', 'icon' => 'zip',        'label' => 'Close the zip' ],
			[ 'id' => 'd', 'icon' => 'pencil',     'label' => 'Add pencils'   ],
		],
		'answer'     => [ 'b', 'a', 'd', 'c' ],
	],

	// ── B-78-AT-03 · Algorithmic · loop-count · Hard ─────────────
	[
		'id'         => 'B-78-AT-03',
		'skill'      => 'algorithmic',
		'type'       => 'loop-count',
		'difficulty' => 'hard',
		'title'      => 'Counting Jerrycan Trips',
		'title_icon' => 'jerrycan',
		'instruction'=> 'Each trip to the river fills 2 jerrycans. How many trips are needed to fill 8 jerrycans?',
		'tiles'      => 8,
		'min'        => 1,
		'max'        => 20,
		'initial'    => 1,
		'answer'     => 4,
	],

	// ══ PATTERN (3) ══════════════════════════════════════════════

	// ── B-78-PR-01 · Pattern · pattern-next · Easy ───────────────
	[
		'id'         => 'B-78-PR-01',
		'skill'      => 'pattern',
		'type'       => 'pattern-next',
		'difficulty' => 'easy',
		'title'      => 'Shape Pattern',
		'title_icon' => 'star',
		'instruction'=> 'Look at the shapes. Which shape comes next?',
		'pattern'    => [
			[ 'icon' => 'star'   ],
			[ 'icon' => 'circle' ],
			[ 'icon' => 'star'   ],
			[ 'icon' => 'circle' ],
			[ 'icon' => 'star'   ],
		],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'circle',   'label' => 'Circle'   ],
			[ 'id' => 'b', 'icon' => 'star',     'label' => 'Star'     ],
			[ 'id' => 'c', 'icon' => 'square',   'label' => 'Square'   ],
			[ 'id' => 'd', 'icon' => 'triangle', 'label' => 'Triangle' ],
		],
		'answer'     => 'a',
	],

	// ── B-78-PR-02 · Pattern · pattern-next · Medium ─────────────
	[
		'id'         => 'B-78-PR-02',
		'skill'      => 'pattern',
		'type'       => 'pattern-next',
		'difficulty' => 'medium',
		'title'      => 'Three-Shape Pattern',
		'title_icon' => 'triangle',
		'instruction'=> 'Look carefully at the pattern of shapes. Which shape comes next?',
		'pattern'    => [
			[ 'icon' => 'square'   ],
			[ 'icon' => 'triangle' ],
			[ 'icon' => 'circle'   ],
			[ 'icon' => 'square'   ],
			[ 'icon' => 'triangle' ],
		],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'circle',   'label' => 'Circle'   ],
			[ 'id' => 'b', 'icon' => 'square',   'label' => 'Square'   ],
			[ 'id' => 'c', 'icon' => 'triangle', 'label' => 'Triangle' ],
			[ 'id' => 'd', 'icon' => 'star',     'label' => 'Star'     ],
		],
		'answer'     => 'a',
	],

	// ── B-78-PR-03 · Pattern · drag-sort · Hard ──────────────────
	// M2 briefing shows drag-sort example with clap-hand and ball-soccer
	// in bins "Gets ball next" / "Does NOT get ball next".
	[
		'id'         => 'B-78-PR-03',
		'skill'      => 'pattern',
		'type'       => 'drag-sort',
		'difficulty' => 'hard',
		'title'      => 'Who Gets the Ball?',
		'title_icon' => 'ball-soccer',
		'instruction'=> 'Ange and Bella take turns getting the ball: Ange first, Bella second, Ange third... Ange has just gone. Who gets the ball next?',
		'items'      => [
			[ 'id' => 'ange',  'icon' => 'clap-hand',  'label' => 'Ange',  'bin' => 1 ],
			[ 'id' => 'bella', 'icon' => 'clap-hand',  'label' => 'Bella', 'bin' => 0 ],
		],
		'bins'       => [ 'Gets ball next', 'Does NOT get ball next' ],
	],

	// ══ LOGICAL (3) ══════════════════════════════════════════════

	// ── B-78-LR-01 · Logical · match-pairs · Easy ────────────────
	// Explicitly shown in M2 briefing data structure example.
	[
		'id'         => 'B-78-LR-01',
		'skill'      => 'logical',
		'type'       => 'match-pairs',
		'difficulty' => 'easy',
		'title'      => 'What Opens It?',
		'title_icon' => 'door',
		'instruction'=> 'Match each thing on the left to what you use to open or work it.',
		'left'       => [
			[ 'id' => 'door',  'icon' => 'door',  'label' => 'Door'  ],
			[ 'id' => 'torch', 'icon' => 'torch', 'label' => 'Torch' ],
			[ 'id' => 'tap',   'icon' => 'tap',   'label' => 'Tap'   ],
		],
		'right'      => [
			[ 'id' => 'battery', 'icon' => 'battery', 'label' => 'Battery'         ],
			[ 'id' => 'key',     'icon' => 'key',     'label' => 'Key'             ],
			[ 'id' => 'handle',  'icon' => 'handle',  'label' => 'Turn the handle' ],
		],
		'pairs'      => [
			[ 'door',  'key'     ],
			[ 'torch', 'battery' ],
			[ 'tap',   'handle'  ],
		],
	],

	// ── B-78-LR-02 · Logical · click-color · Medium ──────────────
	// Explicitly shown in M2 briefing data structure example.
	[
		'id'         => 'B-78-LR-02',
		'skill'      => 'logical',
		'type'       => 'click-color',
		'difficulty' => 'medium',
		'title'      => 'Colour the Leaf',
		'title_icon' => 'leaf',
		'instruction'=> 'Colour the leaf so that no two parts that touch each other have the same colour. You have three colours to use.',
		'svg'        => 'leaf',
		'regions'    => [ 'bg', 'body', 'vein', 'stem' ],
		'adjacency'  => [
			[ 'bg',   'body' ],
			[ 'bg',   'stem' ],
			[ 'body', 'vein' ],
			[ 'body', 'stem' ],
		],
		'colors'      => [ '#27ae60', '#f1c40f', '#795548' ],
		'color_labels'=> [ 'Green', 'Yellow', 'Brown' ],
	],

	// ── B-78-LR-03 · Logical · pattern-next · Hard ───────────────
	[
		'id'         => 'B-78-LR-03',
		'skill'      => 'logical',
		'type'       => 'pattern-next',
		'difficulty' => 'hard',
		'title'      => 'Switch and Bulb',
		'title_icon' => 'bulb-lit',
		'instruction'=> 'When the switch is ON the bulb lights up. When the switch is OFF the bulb goes dark. Look at the pattern — what comes next?',
		'pattern'    => [
			[ 'icon' => 'switch-on'    ],
			[ 'icon' => 'switch-off'   ],
			[ 'icon' => 'switch-on'    ],
			[ 'icon' => 'switch-off'   ],
			[ 'icon' => 'switch-on'    ],
		],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'switch-off', 'label' => 'Switch OFF' ],
			[ 'id' => 'b', 'icon' => 'switch-on',  'label' => 'Switch ON'  ],
			[ 'id' => 'c', 'icon' => 'bulb-lit',   'label' => 'Bulb ON'    ],
			[ 'id' => 'd', 'icon' => 'bulb-broken', 'label' => 'Bulb OFF'  ],
		],
		'answer'     => 'a',
	],

];
