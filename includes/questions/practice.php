<?php
/**
 * Practice questions — all age bands (6 questions).
 *
 * IDs: P-AT-01, P-AT-02, P-PR-01, P-PR-02, P-LR-01, P-LR-02
 * Source: design doc §7.1
 *
 * NOTE: The design doc was not available during M2 build.
 * Question content was reconstructed from context clues in the M2 briefing
 * (SVG asset lists, data structure examples, pedagogical intent).
 * Verify against the original design doc and correct if needed.
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
		'title'                => 'Getting Ready for School',
		'title_icon'           => 'school-bag',
		'instruction'          => 'Put the pictures in the right order to get ready for school.',
		'hint'                 => 'Think about what you do first when you wake up in the morning.',
		'practice_explanation' => 'First your alarm wakes you up, then you brush your teeth, then you put on your uniform, and finally you pick up your school bag. Good sequencing!',
		'items'                => [
			[ 'id' => 'a', 'icon' => 'alarm-clock', 'label' => 'Wake up'            ],
			[ 'id' => 'b', 'icon' => 'brush-teeth', 'label' => 'Brush your teeth'   ],
			[ 'id' => 'c', 'icon' => 'uniform',     'label' => 'Put on your uniform' ],
			[ 'id' => 'd', 'icon' => 'school-bag',  'label' => 'Pick up your bag'    ],
		],
		'answer'               => [ 'a', 'b', 'c', 'd' ],
	],

	// ── P-AT-02 · Algorithmic · loop-count · Easy ────────────────
	[
		'id'                   => 'P-AT-02',
		'skill'                => 'algorithmic',
		'type'                 => 'loop-count',
		'difficulty'           => 'easy',
		'title'                => 'Filling Jerrycans',
		'title_icon'           => 'jerrycan',
		'instruction'          => 'A tap fills 1 jerrycan at a time. How many times must you go to the tap to fill 3 jerrycans?',
		'hint'                 => 'Count how many jerrycans you need to fill — that tells you how many trips.',
		'practice_explanation' => 'Each trip to the tap fills 1 jerrycan. To fill 3 jerrycans you need to make 3 trips. Count the jerrycans!',
		'tiles'                => 3,
		'min'                  => 1,
		'max'                  => 10,
		'initial'              => 1,
		'answer'               => 3,
	],

	// ── P-PR-01 · Pattern · pattern-next · Easy ──────────────────
	[
		'id'                   => 'P-PR-01',
		'skill'                => 'pattern',
		'type'                 => 'pattern-next',
		'difficulty'           => 'easy',
		'title'                => 'Bead Necklace',
		'title_icon'           => 'bead-red',
		'instruction'          => 'Look at the bead pattern. Which bead comes next?',
		'hint'                 => 'Look at what colour comes after each red bead.',
		'practice_explanation' => 'The pattern is red, blue, red, blue, red... so the next bead is blue. The pattern repeats every 2 beads.',
		'pattern'              => [
			[ 'icon' => 'bead-red'  ],
			[ 'icon' => 'bead-blue' ],
			[ 'icon' => 'bead-red'  ],
			[ 'icon' => 'bead-blue' ],
			[ 'icon' => 'bead-red'  ],
		],
		'choices'              => [
			[ 'id' => 'a', 'icon' => 'bead-blue',   'label' => 'Blue'   ],
			[ 'id' => 'b', 'icon' => 'bead-red',    'label' => 'Red'    ],
			[ 'id' => 'c', 'icon' => 'bead-yellow', 'label' => 'Yellow' ],
			[ 'id' => 'd', 'icon' => 'bead-green',  'label' => 'Green'  ],
		],
		'answer'               => 'a',
	],

	// ── P-PR-02 · Pattern · drag-sort · Medium ───────────────────
	[
		'id'                   => 'P-PR-02',
		'skill'                => 'pattern',
		'type'                 => 'drag-sort',
		'difficulty'           => 'medium',
		'title'                => 'Water or Light?',
		'title_icon'           => 'water-drop',
		'instruction'          => 'Sort each thing — does it give water, or does it give light?',
		'hint'                 => 'Think about what each thing does — does it carry water or help you see in the dark?',
		'practice_explanation' => 'A tap and a jerrycan carry water. A torch and a bulb give light. Sorting by a rule is a pattern skill!',
		'items'                => [
			[ 'id' => 'tap',      'icon' => 'tap',        'label' => 'Tap',         'bin' => 0 ],
			[ 'id' => 'jerrycan', 'icon' => 'jerrycan',   'label' => 'Jerrycan',    'bin' => 0 ],
			[ 'id' => 'torch',    'icon' => 'torch',      'label' => 'Torch',       'bin' => 1 ],
			[ 'id' => 'bulb',     'icon' => 'bulb',       'label' => 'Light bulb',  'bin' => 1 ],
		],
		'bins'                 => [ 'Gives water', 'Gives light' ],
	],

	// ── P-LR-01 · Logical · match-pairs · Easy ───────────────────
	[
		'id'                   => 'P-LR-01',
		'skill'                => 'logical',
		'type'                 => 'match-pairs',
		'difficulty'           => 'easy',
		'title'                => 'What Does It Need?',
		'title_icon'           => 'bulb',
		'instruction'          => 'Match each thing on the left to what it needs to work on the right.',
		'hint'                 => 'Think: what do you put in a torch? What does a bulb connect to?',
		'practice_explanation' => 'A torch needs a battery to shine. A bulb needs an electricity plug to glow. A tap gives water. Matching shows logical thinking!',
		'left'                 => [
			[ 'id' => 'torch', 'icon' => 'torch',            'label' => 'Torch'      ],
			[ 'id' => 'bulb',  'icon' => 'bulb',             'label' => 'Light bulb' ],
			[ 'id' => 'tap',   'icon' => 'tap',              'label' => 'Tap'        ],
		],
		'right'                => [
			[ 'id' => 'battery', 'icon' => 'battery',           'label' => 'Battery'     ],
			[ 'id' => 'plug',    'icon' => 'electricity-plug',   'label' => 'Electric plug'],
			[ 'id' => 'water',   'icon' => 'water-drop',        'label' => 'Water'        ],
		],
		'pairs'                => [
			[ 'torch', 'battery' ],
			[ 'bulb',  'plug'    ],
			[ 'tap',   'water'   ],
		],
	],

	// ── P-LR-02 · Logical · click-color · Easy ───────────────────
	[
		'id'                   => 'P-LR-02',
		'skill'                => 'logical',
		'type'                 => 'click-color',
		'difficulty'           => 'easy',
		'title'                => 'Colour the Leaf',
		'title_icon'           => 'leaf',
		'instruction'          => 'Choose a colour and click each part of the leaf to paint it. Make sure no two touching parts have the same colour.',
		'hint'                 => 'Start with the leaf body, then pick a different colour for the parts that touch it.',
		'practice_explanation' => 'Two parts that touch each other must have different colours — this is called graph colouring! There are many correct answers.',
		'svg'                  => 'leaf',
		'regions'              => [ 'bg', 'body', 'vein', 'stem' ],
		'adjacency'            => [
			[ 'bg',   'body' ],
			[ 'bg',   'stem' ],
			[ 'body', 'vein' ],
			[ 'body', 'stem' ],
		],
		'colors'               => [ '#27ae60', '#f1c40f', '#795548' ],
		'color_labels'         => [ 'Green', 'Yellow', 'Brown' ],
	],

];
