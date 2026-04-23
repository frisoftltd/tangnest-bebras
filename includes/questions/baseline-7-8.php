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
		'hint'       => 'Think about what you do first when you want to boil water — you need to put the water IN before you can heat it up!',
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
		'hint'       => 'Akimana has to open the bag before anything can go inside. What is the very last thing you do before you leave the house?',
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
		'title'      => 'Carry Water Cans',
		'title_icon' => 'jerrycan',
		'instruction'=> 'Habimana carries one jerrycan each trip. He needs to move 4 cans. How many trips does he make?',
		'hint'       => 'Habimana carries ONE can at a time. Count the cans on the screen — one trip for each can!',
		'tiles'      => 4,
		'tile_icon'  => 'jerrycan',
		'min'        => 1,
		'max'        => 10,
		'initial'    => 1,
		'answer'     => 4,
	],

	// ══ PATTERN (3) ══════════════════════════════════════════════

	// ── B-78-PR-01 · Pattern · loop-count · Easy ────────────────
	[
		'id'              => 'B-78-PR-01',
		'skill'           => 'pattern',
		'type'            => 'loop-count',
		'difficulty'      => 'easy',
		'title'           => 'Clap Clap Clap',
		'title_icon'      => 'clap-hand',
		'instruction'     => 'The teacher claps 3 times, pauses, claps 3 times again. The class does this 4 times total. How many claps in all?',
		'hint'            => 'The teacher claps 3 times, then stops, then claps 3 times again. Count how many times the whole group claps altogether.',
		'tiles'           => 12,
		'tile_icon'       => 'clap-hand',
		'tile_group_size' => 3,
		'min'             => 1,
		'max'             => 20,
		'initial'         => 1,
		'answer'          => 12,
	],

	// ── B-78-PR-02 · Pattern · pattern-next · Medium ─────────────
	[
		'id'         => 'B-78-PR-02',
		'skill'      => 'pattern',
		'type'       => 'pattern-next',
		'difficulty' => 'medium',
		'title'      => 'Bead Pattern',
		'title_icon' => 'bead-red',
		'instruction'=> 'Uwase is making a necklace. What color bead comes next?',
		'hint'       => 'Uwase always repeats the same three colours in order: Red, Blue, Yellow — then Red again. What comes after Blue?',
		'pattern'    => [
			[ 'icon' => 'bead-red'    ],
			[ 'icon' => 'bead-blue'   ],
			[ 'icon' => 'bead-yellow' ],
			[ 'icon' => 'bead-red'    ],
			[ 'icon' => 'bead-blue'   ],
		],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'bead-yellow', 'label' => 'Yellow' ],
			[ 'id' => 'b', 'icon' => 'bead-red',    'label' => 'Red'    ],
			[ 'id' => 'c', 'icon' => 'bead-blue',   'label' => 'Blue'   ],
			[ 'id' => 'd', 'icon' => 'bead-green',  'label' => 'Green'  ],
		],
		'answer'     => 'a',
	],

	// ── B-78-PR-03 · Pattern · drag-sort · Hard ──────────────────
	[
		'id'         => 'B-78-PR-03',
		'skill'      => 'pattern',
		'type'       => 'drag-sort',
		'difficulty' => 'hard',
		'title'      => 'Pass the Ball',
		'title_icon' => 'ball-soccer',
		'instruction'=> 'Look at how the ball is passed: Ange → Bella → Ange → Bella → ? Sort these two names: who gets the ball next?',
		'hint'       => 'Watch the pattern: Ange, Bella, Ange, Bella... who comes next in this sequence?',
		'items'      => [
			[ 'id' => 'ange',  'icon' => 'clap-hand', 'label' => 'Ange',  'bin' => 0 ],
			[ 'id' => 'bella', 'icon' => 'clap-hand', 'label' => 'Bella', 'bin' => 1 ],
		],
		'bins'       => [ 'Gets the ball NEXT', 'Does NOT get the ball next' ],
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
		'hint'       => 'A door needs something you put in a lock. A torch needs something with power inside it. What do you turn to get water out of a tap?',
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
		'hint'       => 'Two parts that are touching each other cannot be the same colour. Start with the big body of the leaf, then pick a different colour for anything it touches.',
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
	// Uses pattern-next renderer with empty pattern (choices only).
	// Logical reasoning: switch ON AND bulb not broken → bulb turns on.
	[
		'id'         => 'B-78-LR-03',
		'skill'      => 'logical',
		'type'       => 'pattern-next',
		'difficulty' => 'hard',
		'title'      => 'What Must Happen First?',
		'title_icon' => 'bulb-lit',
		'instruction'=> 'The light bulb will only turn on if the switch is flipped AND the bulb is not broken. Which picture shows when the bulb turns ON?',
		'hint'       => 'The bulb only turns on when TWO things are both true at the same time: the switch must be ON AND the bulb must not be broken. Which picture shows both?',
		'pattern'    => [],
		'choices'    => [
			[ 'id' => 'a', 'icon' => 'switch-on-bulb-broken',  'label' => 'Switch ON + broken bulb'  ],
			[ 'id' => 'b', 'icon' => 'switch-off-bulb-good',   'label' => 'Switch OFF + good bulb'   ],
			[ 'id' => 'c', 'icon' => 'switch-on-bulb-good',    'label' => 'Switch ON + good bulb'    ],
			[ 'id' => 'd', 'icon' => 'switch-off-bulb-broken', 'label' => 'Switch OFF + broken bulb' ],
		],
		'answer'     => 'c',
	],

];
