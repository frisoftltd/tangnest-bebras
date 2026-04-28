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
		'id'          => 'B-78-AT-01',
		'skill'       => 'algorithmic',
		'type'        => 'drag-sequence',
		'difficulty'  => 'easy',
		'title'       => 'Making Tea',
		'title_icon'  => 'tea-cup',
		'instruction' => 'Put the pictures in the right order to make a cup of tea.',
		'hint'        => 'Think about what must be in the cup before you can add the tea bag — and what do you always do after mixing something?',
		'items'       => [
			[ 'id' => 'a', 'png' => 'people/put_tea_bug_in_cup.png',        'label' => 'Put tea bag in cup'    ],
			[ 'id' => 'b', 'png' => 'people/Pour_the_hot_water_in_cup.png', 'label' => 'Pour hot water in cup' ],
			[ 'id' => 'c', 'png' => 'people/stir_the_tea.png',              'label' => 'Stir the tea'          ],
			[ 'id' => 'd', 'png' => 'people/drink_the_tea.png',             'label' => 'Drink the tea'         ],
		],
		'answer'      => [ 'b', 'a', 'c', 'd' ],
	],

	// ── B-78-AT-02 · Algorithmic · drag-sequence · Medium ────────
	[
		'id'         => 'B-78-AT-02',
		'skill'      => 'algorithmic',
		'type'       => 'drag-sequence',
		'difficulty' => 'medium',
		'title'      => 'Pack the School Bag',
		'title_icon' => 'school-bag',
		'instruction'=> 'Nyiramajyambere packs her bag. What is the right order?',
		'hint'       => 'Nyiramajyambere has to open the bag before anything can go inside. What is the very last thing you do before you leave the house?',
		'items'      => [
			[ 'id' => 'a', 'png' => 'people/put_in_books.png',  'label' => 'Put in books'  ],
			[ 'id' => 'b', 'png' => 'people/open_the_bag.png',  'label' => 'Open the bag'  ],
			[ 'id' => 'c', 'png' => 'people/close_the_zip.png', 'label' => 'Close the zip' ],
			[ 'id' => 'd', 'png' => 'people/add_pencils.png',   'label' => 'Add pencils'   ],
		],
		'answer'     => [ 'b', 'a', 'd', 'c' ],
	],

	// ── B-78-AT-03 · Algorithmic · loop-count · Hard ─────────────
	[
		'id'             => 'B-78-AT-03',
		'skill'          => 'algorithmic',
		'type'           => 'loop-count',
		'difficulty'     => 'hard',
		'title'          => 'Carry Water Cans',
		'title_icon_png' => 'objects/jerrycan.png',
		'side_image'     => 'people/carry_water_cans.png',
		'instruction'    => 'Majyambere carries one jerrycan each trip. He needs to move 4 cans. How many trips does he make?',
		'hint'           => 'Majyambere carries ONE can at a time. Count the cans on the screen — one trip for each can!',
		'tiles'          => 4,
		'tile_icon_png'  => 'objects/jerrycan.png',
		'min'            => 1,
		'max'            => 10,
		'initial'        => 1,
		'answer'         => 4,
	],

	// ══ PATTERN (3) ══════════════════════════════════════════════

	// ── B-78-PR-01 · Pattern · loop-count · Easy ────────────────
	[
		'id'              => 'B-78-PR-01',
		'skill'           => 'pattern',
		'type'            => 'loop-count',
		'difficulty'      => 'easy',
		'title'           => 'Clap Clap Clap',
		'title_icon_png'  => 'objects/clap.png',
		'side_image'      => 'people/clap_and_clap_and_clap.png',
		'instruction'     => 'The teacher claps 3 times, pauses, claps 3 times again. The class does this 4 times in total.',
		'instruction_q'   => 'How many claps in all?',
		'hint'            => 'The teacher claps 3 times, then stops, then claps 3 times again. Count how many times the whole group claps altogether.',
		'tiles'           => 12,
		'tile_icon_png'   => 'objects/clap.png',
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
		'id'            => 'B-78-PR-03',
		'skill'         => 'pattern',
		'type'          => 'drag-sort',
		'difficulty'    => 'hard',
		'title'         => 'Pass the Ball',
		'title_icon'    => 'ball-soccer',
		'side_image'    => 'people/pass_the_ball.png',
		'instruction'   => 'Look at how the ball is passed:',
		'pattern_seq'   => 'Akimana → Mugisha → Akimana → Mugisha → ?',
		'instruction_q' => 'Sort these two names: who gets the ball next?',
		'hint'          => 'Watch the pattern: Akimana, Mugisha, Akimana, Mugisha… who comes next?',
		'items'         => [
			[ 'id' => 'akimana', 'png' => 'people/akimana.png', 'label' => 'Akimana', 'bin' => 0 ],
			[ 'id' => 'mugisha', 'png' => 'people/mugisha.png', 'label' => 'Mugisha', 'bin' => 1 ],
		],
		'bins'          => [ 'Gets the ball NEXT', 'Does NOT get the ball next' ],
	],

	// ══ LOGICAL (3) ══════════════════════════════════════════════

	// ── B-78-LR-01 · Logical · match-pairs · Easy ────────────────
	[
		'id'               => 'B-78-LR-01',
		'skill'            => 'logical',
		'type'             => 'match-pairs',
		'difficulty'       => 'easy',
		'title'            => 'Help Nyiramajyambere at Home!',
		'title_icon_png'   => 'people/Nyiramajyambere_at_home.png',
		'char_image'       => 'people/Nyiramajyambere_at_home.png',
		'instruction'      => 'Nyiramajyambere wants to use these things. Match each thing with the right tool.',
		'col_left_label'   => 'Things at Home',
		'col_right_label'  => 'Tools',
		'hint'             => 'A door needs something you put in a lock. To turn on a light you use a switch on the wall. What do you turn to get water out of a tap?',
		'left'             => [
			[ 'id' => 'door',  'png' => 'objects/door_is_closed.png', 'label' => 'Door is closed' ],
			[ 'id' => 'light', 'png' => 'objects/light_is_off.png',   'label' => 'Light is off'   ],
			[ 'id' => 'tap',   'png' => 'objects/tap_is_closed.png',  'label' => 'Tap is closed'  ],
		],
		'right'            => [
			[ 'id' => 'key',    'png' => 'objects/key.png',    'label' => 'Key'    ],
			[ 'id' => 'switch', 'png' => 'objects/switch.png', 'label' => 'Switch' ],
			[ 'id' => 'handle', 'png' => 'objects/handle.png', 'label' => 'Handle' ],
		],
		'pairs'            => [
			[ 'door',  'key'    ],
			[ 'light', 'switch' ],
			[ 'tap',   'handle' ],
		],
	],

	// ── B-78-LR-02 · Logical · click-color · Medium ──────────────
	[
		'id'           => 'B-78-LR-02',
		'skill'        => 'logical',
		'type'         => 'click-color',
		'difficulty'   => 'medium',
		'title'        => 'Colour the Leaf',
		'title_icon'   => 'leaf',
		'instruction'  => 'Colour the leaf so that no two parts that touch each other have the same colour. You have three colours to use.',
		'hint'         => 'The left and right sides both touch each other at the middle. The bottom part touches both sides. You need all three colours!',
		'svg'          => 'leaf-colorable',
		'regions'      => [ 'left', 'right', 'bottom' ],
		'adjacency'    => [
			[ 'left',  'right'  ],
			[ 'left',  'bottom' ],
			[ 'right', 'bottom' ],
		],
		'palette_pngs' => [
			[ 'png' => 'ui/leaf_blue.png',   'label' => 'Blue',   'value' => '#3498db' ],
			[ 'png' => 'ui/leaf_red.png',    'label' => 'Red',    'value' => '#e74c3c' ],
			[ 'png' => 'ui/leaf_yellow.png', 'label' => 'Yellow', 'value' => '#f1c40f' ],
		],
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
			[ 'id' => 'a', 'png' => 'objects/switch_on_bulb_broken.png',  'label' => 'Switch ON + broken bulb'  ],
			[ 'id' => 'b', 'png' => 'objects/switch_off_bulb_good.png',   'label' => 'Switch OFF + good bulb'   ],
			[ 'id' => 'c', 'png' => 'objects/switch_on_bulb_good.png',    'label' => 'Switch ON + good bulb'    ],
			[ 'id' => 'd', 'png' => 'objects/switch_off_bulb_broken.png', 'label' => 'Switch OFF + broken bulb' ],
		],
		'answer'     => 'c',
	],

];
