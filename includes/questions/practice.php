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

	// ── P-PR-02 · Pattern · pattern-next · Easy ─────────────────
	[
		'id'                   => 'P-PR-02',
		'skill'                => 'pattern',
		'type'                 => 'pattern-next',
		'difficulty'           => 'easy',
		'title'                => 'What Comes Next?',
		'title_icon'           => 'bead-red',
		'instruction'          => 'Look at the bead pattern. What bead comes next?',
		'hint'                 => 'Look at the two colours and which one comes after red.',
		'practice_explanation' => 'The pattern is red, blue, red, blue, red... the next bead is blue. The pattern repeats every 2 beads!',
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

	// ── P-LR-01 · Logical · click-color · Easy ──────────────────
	[
		'id'                   => 'P-LR-01',
		'skill'                => 'logical',
		'type'                 => 'click-color',
		'difficulty'           => 'easy',
		'title'                => 'Color the Flag',
		'title_icon'           => 'flag-colorable',
		'instruction'          => 'Color each part of the flag. Rule: Two touching parts cannot be the same color.',
		'hint'                 => 'The top and bottom bands do not touch each other — only middle touches both.',
		'practice_explanation' => 'The middle band touches both the top and bottom, so it must be different from both. The top and bottom can be the same color since they do not touch. Many correct answers exist!',
		'svg'                  => 'flag-colorable',
		'regions'              => [ 'top', 'middle', 'bottom' ],
		'adjacency'            => [
			[ 'top',    'middle' ],
			[ 'middle', 'bottom' ],
		],
		'colors'               => [ '#C0392B', '#f1c40f', '#1E8449' ],
		'color_labels'         => [ 'Red', 'Yellow', 'Green' ],
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
