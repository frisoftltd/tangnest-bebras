<?php
/**
 * Pre-course Bebras question bank (10 questions), extracted from v1.0.0.
 * Returns the array consumed by TNQ_Legacy_Quiz::render().
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

function tnq_legacy_pre_questions(): array {
	return [

		/* Q1 – SEQUENCE: sandwich steps */
		[
			'type'        => 'drag-sequence',
			'title'       => '🥪 Put It in Order',
			'instruction' => 'Drag the steps into the CORRECT ORDER to make a peanut-butter sandwich. The first step is already shown — arrange the rest!',
			'items'       => [
				[ 'id' => 'b', 'label' => '🥜 Spread peanut butter on one slice' ],
				[ 'id' => 'a', 'label' => '🍞 Get two slices of bread' ],
				[ 'id' => 'c', 'label' => '🥪 Press the two slices together' ],
				[ 'id' => 'd', 'label' => '🍽️ Place sandwich on a plate' ],
			],
			'answer' => [ 'a', 'b', 'c', 'd' ],
			'tip'    => 'Computers need instructions in exactly the right order — just like making a sandwich! This is called a <strong>SEQUENCE</strong>. 🧠',
		],

		/* Q2 – LOOP COUNT: robot hops */
		[
			'type'        => 'loop-count',
			'title'       => '🔁 Count the Hops',
			'instruction' => 'The robot does ONE hop each move. It starts at ⭐ and must reach 🏁. How many times does it hop? Use + and − to set your answer, then check!',
			'tiles'       => 6,
			'answer'      => 6,
			'tip'         => 'Doing the same thing many times is called a <strong>LOOP</strong>! A computer can say "repeat 6 times: hop forward" instead of writing each step. 🔁',
		],

		/* Q3 – CLICK-TO-COLOR: flower graph coloring */
		[
			'type'        => 'click-color',
			'title'       => '🎨 Color the Flower',
			'instruction' => 'Paint every region. <strong>Rule: two touching regions cannot share the same color.</strong> Pick a color from the palette, then click a region to paint it.',
			'colors'      => [ '#e74c3c', '#3498db', '#27ae60' ],
			'color_labels'=> [ 'Red', 'Blue', 'Green' ],
			'regions'     => [
				[ 'id' => 'bg',     'label' => 'Background', 'adj' => [ 'petals', 'leaf', 'stem' ] ],
				[ 'id' => 'petals', 'label' => 'Petals',     'adj' => [ 'bg', 'center' ] ],
				[ 'id' => 'center', 'label' => 'Centre',     'adj' => [ 'petals' ] ],
				[ 'id' => 'stem',   'label' => 'Stem',       'adj' => [ 'bg', 'leaf' ] ],
				[ 'id' => 'leaf',   'label' => 'Leaf',       'adj' => [ 'bg', 'stem' ] ],
			],
			'tip' => 'There is more than one correct coloring! Computers solve problems like this with <strong>GRAPH COLORING</strong> algorithms — used for map drawing and scheduling. 🗺️',
		],

		/* Q4 – PATTERN NEXT */
		[
			'type'        => 'pattern-next',
			'title'       => '🔮 What Comes Next?',
			'instruction' => 'Study the pattern and click what should come next.',
			'pattern'     => [ '🔴', '🔵', '🔴', '🔵', '🔴' ],
			'choices'     => [ '🔵 Blue', '🔴 Red', '⭐ Star', '🟢 Green' ],
			'answer'      => 0,
			'tip'         => 'Red, blue, red, blue, red → next is <strong>BLUE</strong>! Spotting and continuing patterns is a core skill for coding. 🎯',
		],

		/* Q5 – BUG FINDER: watering plant */
		[
			'type'        => 'bug-finder',
			'title'       => '🐛 Find the Bug!',
			'instruction' => 'These instructions for watering a plant have a step in the <strong>WRONG place</strong>. Click the step that does not belong where it is.',
			'steps' => [
				[ 'id' => 0, 'label' => '🪣 Get the watering can' ],
				[ 'id' => 1, 'label' => '🌿 Water the plant' ],
				[ 'id' => 2, 'label' => '💧 Fill it with water' ],
				[ 'id' => 3, 'label' => '🔙 Put the watering can back' ],
			],
			'answer' => 1,
			'tip'    => 'You can\'t water the plant before filling the can! Finding mistakes in instructions is called <strong>DEBUGGING</strong>. Every programmer does it! 🐛→✅',
		],

		/* Q6 – MATCH PAIRS: animal abilities */
		[
			'type'        => 'match-pairs',
			'title'       => '🔗 Make a Match',
			'instruction' => 'Click an animal on the left, then click what it does best on the right to connect them.',
			'left'  => [
				[ 'id' => 'bird',   'label' => '🐦 Bird' ],
				[ 'id' => 'fish',   'label' => '🐟 Fish' ],
				[ 'id' => 'monkey', 'label' => '🐒 Monkey' ],
			],
			'right' => [
				[ 'id' => 'swim',  'label' => '🌊 Swim' ],
				[ 'id' => 'fly',   'label' => '✈️ Fly' ],
				[ 'id' => 'climb', 'label' => '🌲 Climb trees' ],
			],
			'pairs' => [ [ 'bird', 'fly' ], [ 'fish', 'swim' ], [ 'monkey', 'climb' ] ],
			'tip'   => 'Matching things that belong together is how computers organise data — this is the idea behind a <strong>DATABASE</strong>! 🗃️',
		],

		/* Q7 – DRAG SORT: living vs non-living */
		[
			'type'        => 'drag-sort',
			'title'       => '📦 Sort It Out',
			'instruction' => 'Drag each item into the correct bin — <strong>Living</strong> or <strong>Non-Living</strong>.',
			'bins'  => [ '🟢 Living', '⚪ Non-Living' ],
			'items' => [
				[ 'id' => 'dog',    'label' => '🐕 Dog',    'bin' => 0 ],
				[ 'id' => 'chair',  'label' => '🪑 Chair',  'bin' => 1 ],
				[ 'id' => 'tree',   'label' => '🌳 Tree',   'bin' => 0 ],
				[ 'id' => 'book',   'label' => '📚 Book',   'bin' => 1 ],
				[ 'id' => 'flower', 'label' => '🌸 Flower', 'bin' => 0 ],
				[ 'id' => 'pencil', 'label' => '✏️ Pencil', 'bin' => 1 ],
			],
			'tip' => 'Grouping things into categories is called <strong>CLASSIFICATION</strong>. Computers do this every time they sort emails, photos, or files! 📂',
		],

		/* Q8 – GRID NAV: robot path */
		[
			'type'        => 'grid-nav',
			'title'       => '🤖 Follow the Robot',
			'instruction' => 'The robot starts at ⭐ (top-left). It follows: → → ↓ → ↓. Click the cell where it ends up!',
			'size'   => 4,
			'start'  => [ 0, 0 ],
			'moves'  => [ 'r', 'r', 'd', 'r', 'd' ],
			'answer' => [ 2, 3 ],
			'direct' => false,
			'tip'    => 'The robot ends at row 3, column 4 (index 2,3). Following step-by-step instructions is called <strong>SEQUENTIAL EXECUTION</strong> — the heart of every program! 💻',
		],

		/* Q9 – SEQUENCE: brushing teeth (decomposition) */
		[
			'type'        => 'drag-sequence',
			'title'       => '🦷 Break It Down',
			'instruction' => 'Put these steps for brushing your teeth into the correct order by dragging them.',
			'items' => [
				[ 'id' => 'a', 'label' => '🦷 Brush all teeth for 2 minutes' ],
				[ 'id' => 'b', 'label' => '🚿 Rinse your mouth with water' ],
				[ 'id' => 'c', 'label' => '🪥 Get your toothbrush' ],
				[ 'id' => 'd', 'label' => '🧴 Put toothpaste on the brush' ],
			],
			'answer' => [ 'c', 'd', 'a', 'b' ],
			'tip'    => 'Breaking a big task into small steps is called <strong>DECOMPOSITION</strong> — one of the four superpowers of computational thinking! 💪',
		],

		/* Q10 – MATCH PAIRS: input → output */
		[
			'type'        => 'match-pairs',
			'title'       => '⚡ Input → Output',
			'instruction' => 'Click a button on the left, then click what it causes on the right to match them.',
			'left'  => [
				[ 'id' => 'red',  'label' => '🔴 Red button' ],
				[ 'id' => 'blue', 'label' => '🔵 Blue button' ],
				[ 'id' => 'star', 'label' => '⭐ Star button' ],
			],
			'right' => [
				[ 'id' => 'light',  'label' => '💡 Turns on a light' ],
				[ 'id' => 'music',  'label' => '🎵 Plays music' ],
				[ 'id' => 'door',   'label' => '🚪 Opens the door' ],
			],
			'pairs' => [ [ 'red', 'light' ], [ 'blue', 'music' ], [ 'star', 'door' ] ],
			'tip'   => 'Computers react to <strong>INPUTS</strong> (button presses, mouse clicks) and produce <strong>OUTPUTS</strong> (sounds, lights, movement). That\'s programming! ⚡',
		],
	];
}
