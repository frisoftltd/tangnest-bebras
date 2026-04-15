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
		'title'                => 'Get Ready for School',
		'title_icon'           => 'school-bag',
		'instruction'          => 'Drag the pictures into the right order to show how Amina gets ready for school.',
		'hint'                 => 'Think about what you do first when you wake up in the morning.',
		'practice_explanation' => 'First Amina wakes up (C), then she brushes her teeth (A), then she puts on her uniform (B), and finally she picks up her school bag (D). Good sequencing!',
		'items'                => [
			[ 'id' => 'a', 'icon' => 'brush-teeth', 'label' => 'Brush teeth'        ],
			[ 'id' => 'b', 'icon' => 'uniform',     'label' => 'Put on uniform'     ],
			[ 'id' => 'c', 'icon' => 'alarm-clock', 'label' => 'Wake up'            ],
			[ 'id' => 'd', 'icon' => 'school-bag',  'label' => 'Pick up school bag' ],
		],
		'answer'               => [ 'c', 'a', 'b', 'd' ],
	],

	// ── P-AT-02 · Algorithmic · drag-sequence · Easy ─────────────
	[
		'id'                   => 'P-AT-02',
		'skill'                => 'algorithmic',
		'type'                 => 'drag-sequence',
		'difficulty'           => 'easy',
		'title'                => 'Fetching Water',
		'title_icon'           => 'jerrycan',
		'instruction'          => 'Kagiso needs to fetch water. Put the steps in the right order.',
		'hint'                 => 'Think about what you must have before you go to the tap.',
		'practice_explanation' => 'First pick up the jerrycan (B), then walk to the tap (A), then fill the jerrycan (D), and finally carry the water home (C).',
		'items'                => [
			[ 'id' => 'a', 'icon' => 'tap',        'label' => 'Walk to the tap'    ],
			[ 'id' => 'b', 'icon' => 'jerrycan',   'label' => 'Pick up the jerrycan' ],
			[ 'id' => 'c', 'icon' => 'house',      'label' => 'Carry water home'   ],
			[ 'id' => 'd', 'icon' => 'water-drop', 'label' => 'Fill the jerrycan'  ],
		],
		'answer'               => [ 'b', 'a', 'd', 'c' ],
	],

	// ── P-PR-01 · Pattern · loop-count · Easy ───────────────────
	[
		'id'                   => 'P-PR-01',
		'skill'                => 'pattern',
		'type'                 => 'loop-count',
		'difficulty'           => 'easy',
		'title'                => 'Count the Steps',
		'title_icon'           => 'footstep',
		'instruction'          => 'Mugisha walks from his house to the water tap. How many steps does he take?',
		'hint'                 => 'Count the footsteps one by one.',
		'practice_explanation' => 'There are 5 footsteps shown — Mugisha takes 5 steps. Counting things in order is a pattern skill!',
		'tiles'                => 5,
		'tile_icon'            => 'footstep',
		'min'                  => 1,
		'max'                  => 10,
		'initial'              => 1,
		'answer'               => 5,
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
