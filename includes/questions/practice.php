<?php
/**
 * Practice questions — age band 7–8 (6 questions).
 *
 * IDs: P-AT-01, P-AT-02, P-PR-01, P-PR-02, P-LR-01, P-LR-02
 * Source: design doc §7.1 + M2 briefing asset spec.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

return [

	// ── P-AT-01 · Algorithmic · drag-sequence · Easy ─────────────
	[
		'id'                   => 'P-AT-01',
		'skill'                => 'algorithmic',
		'type'                 => 'drag-sequence',
		'difficulty'           => 'easy',
		'title'                => 'Get Ready for School',
		'title_icon'           => 'school-bag',
		'instruction'          => 'Drag the pictures into the right order to show how Amina gets ready for school.',
		'hint'                 => "Amina's mum says: you can't brush your teeth before you wake up! And the very last thing before walking out the door is always grabbing your school bag.",
		'practice_explanation' => 'First Amina wakes up, then she brushes her teeth, then she puts on her uniform, and finally she picks up her school bag. Good sequencing!',
		'items'                => [
			[ 'id' => 'q1-wake-up',        'png' => 'people/q1-wake-up.png',        'label' => 'Wake up'        ],
			[ 'id' => 'q1-brush-teeth',    'png' => 'people/q1-brush-teeth.png',    'label' => 'Brush teeth'    ],
			[ 'id' => 'q1-put-on-uniform', 'png' => 'people/q1-put-on-uniform.png', 'label' => 'Put on uniform' ],
			[ 'id' => 'q1-pick-up-bag',    'png' => 'people/q1-pick-up-bag.png',    'label' => 'Pick up bag'    ],
		],
		'answer'               => [ 'q1-wake-up', 'q1-brush-teeth', 'q1-put-on-uniform', 'q1-pick-up-bag' ],
	],

	// ── P-AT-02 · Algorithmic · drag-sequence · Easy ─────────────
	[
		'id'                   => 'P-AT-02',
		'skill'                => 'algorithmic',
		'type'                 => 'drag-sequence',
		'difficulty'           => 'easy',
		'title'                => 'Fetching Water',
		'title_icon'           => 'jerrycan',
		'instruction'          => 'Kalisa needs to fetch water. Put the steps in the right order.',
		'hint'                 => "Kalisa's grandmother says: you must walk to the tap before you can fill anything! And you can only carry the jerrycan home once it is full and lifted up.",
		'practice_explanation' => 'First Kalisa walks to the tap, then fills the jerrycan, then picks it up, and finally carries it home. Every step must happen in order!',
		'items'                => [
			[ 'id' => 'q2-walk-to-tap',      'png' => 'people/q2-walk-to-tap.png',      'label' => 'Walk to tap'      ],
			[ 'id' => 'q2-fill-jerrycan',    'png' => 'people/q2-fill-jerrycan.png',    'label' => 'Fill jerrycan'    ],
			[ 'id' => 'q2-pick-up-jerrycan', 'png' => 'people/q2-pick-up-jerrycan.png', 'label' => 'Pick up jerrycan' ],
			[ 'id' => 'q2-carry-home',       'png' => 'people/q2-carry-home.png',       'label' => 'Carry home'       ],
		],
		'answer'               => [ 'q2-walk-to-tap', 'q2-fill-jerrycan', 'q2-pick-up-jerrycan', 'q2-carry-home' ],
	],

	// ── P-PR-01 · Pattern · loop-count · Easy ───────────────────
	[
		'id'                   => 'P-PR-01',
		'skill'                => 'pattern',
		'type'                 => 'loop-count',
		'difficulty'           => 'easy',
		'title'                => 'Count the Steps',
		'title_icon'           => 'footstep',
		'title_icon_png'       => 'ui/q3-count-the-steps.png',
		'instruction'          => 'Mugisha walks from his house to the water tap. How many steps does he take?',
		'hint'                 => 'Touch each footprint one by one and count out loud with Mugisha as he walks to the tap — one, two, three, four, five!',
		'practice_explanation' => 'There are 5 footsteps shown — Mugisha takes 5 steps. Counting things in order is a pattern skill!',
		'tile_icon_png'        => 'ui/q3-count-the-steps.png',
		'path_svg'             => 'house-to-tap',
		'min'                  => 1,
		'max'                  => 10,
		'initial'              => 1,
		'answer'               => 5,
	],

	// ── P-PR-02 · Pattern · pattern-next · Easy ─────────────────
	[
		'id'                   => 'P-PR-02',
		'skill'                => 'pattern',
		'type'                 => 'pattern-next',
		'difficulty'           => 'easy',
		'title'                => 'What Comes Next?',
		'title_icon'           => 'bead-red',
		'instruction'          => 'Look at the bead pattern. What bead comes next?',
		'hint'                 => 'Aisha made a bead necklace — she always puts one red bead, then one blue bead, then red again, then blue again. What colour always comes after red?',
		'practice_explanation' => 'The pattern is red, blue, red, blue, red... the next bead is blue. The pattern repeats every 2 beads!',
		'pattern'              => [
			[ 'icon' => 'bead-red'  ],
			[ 'icon' => 'bead-blue' ],
			[ 'icon' => 'bead-red'  ],
			[ 'icon' => 'bead-blue' ],
			[ 'icon' => 'bead-red'  ],
		],
		'choices'              => [
			[ 'id' => 'blue',   'png' => 'patterns/q4-blue.png',   'active_png' => 'patterns/q4-blue-active.png',   'label' => 'Blue'   ],
			[ 'id' => 'red',    'png' => 'patterns/q4-red.png',    'active_png' => 'patterns/q4-red-active.png',    'label' => 'Red'    ],
			[ 'id' => 'yellow', 'png' => 'patterns/q4-yellow.png', 'active_png' => 'patterns/q4-yellow-active.png', 'label' => 'Yellow' ],
			[ 'id' => 'green',  'png' => 'patterns/q4-green.png',  'active_png' => 'patterns/q4-green-active.png',  'label' => 'Green'  ],
		],
		'answer'               => 'blue',
	],

	// ── P-LR-01 · Logical · click-color · Easy ──────────────────
	[
		'id'                   => 'P-LR-01',
		'skill'                => 'logical',
		'type'                 => 'click-color',
		'difficulty'           => 'easy',
		'title'                => 'Color the Flag',
		'title_icon'           => 'flag-colorable',
		'instruction'          => 'Color each part of the flag. Rule: Two touching parts cannot be the same color.',
		'hint'                 => 'Imagine painting houses on a street — two neighbours can never paint their house the same colour. Look at which parts of the flag are touching each other!',
		'practice_explanation' => 'The Top and Bottom do not touch each other, and Left and Right do not touch each other — but all other pairs do touch. Many correct colour combinations exist!',
		'svg'                  => 'flag-colorable',
		'regions'              => [ 'top', 'bottom', 'left', 'right' ],
		'adjacency'            => [
			[ 'top',    'left'  ],
			[ 'top',    'right' ],
			[ 'bottom', 'left'  ],
			[ 'bottom', 'right' ],
		],
		'palette_pngs'         => [
			[ 'png' => 'ui/q5-red.png',    'label' => 'Red',    'value' => 'red'    ],
			[ 'png' => 'ui/q5-yellow.png', 'label' => 'Yellow', 'value' => 'yellow' ],
			[ 'png' => 'ui/q5-green.png',  'label' => 'Green',  'value' => 'green'  ],
		],
	],

	// ── P-LR-02 · Logical · match-pairs · Easy ──────────────────
	[
		'id'                   => 'P-LR-02',
		'skill'                => 'logical',
		'type'                 => 'match-pairs',
		'difficulty'           => 'easy',
		'title'                => 'What Makes It Work?',
		'title_icon'           => 'torch',
		'instruction'          => 'Match each object on the left to what it needs on the right to work.',
		'hint'                 => 'A torch goes dark when the small round thing inside it runs out of power. A tap needs pipes in the wall. What does a light bulb need from the socket?',
		'practice_explanation' => 'A torch needs a battery to shine. A light bulb needs electricity. A tap needs a water connection. Matching by logic shows clear thinking!',
		'left'                 => [
			[ 'id' => 'torch',      'png' => 'objects/q6-torch.png',     'label' => 'Torch'      ],
			[ 'id' => 'light_bulb', 'png' => 'objects/q6-light-bulb.png','label' => 'Light bulb' ],
			[ 'id' => 'tap',        'png' => 'objects/q6-tap.png',       'label' => 'Tap'        ],
		],
		'right'                => [
			[ 'id' => 'battery',     'png' => 'objects/q6-battery.png',          'label' => 'Battery'          ],
			[ 'id' => 'electricity', 'png' => 'objects/q6-electricity.png',      'label' => 'Electricity'      ],
			[ 'id' => 'water',       'png' => 'objects/q6-water-connection.png', 'label' => 'Water connection' ],
		],
		'pairs'                => [
			[ 'torch',      'battery'     ],
			[ 'light_bulb', 'electricity' ],
			[ 'tap',        'water'       ],
		],
	],

];
