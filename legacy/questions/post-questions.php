<?php
/**
 * Post-course Bebras question bank (10 questions), extracted from v1.0.0.
 * Returns the array consumed by TNQ_Legacy_Quiz::render().
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

function tnq_legacy_post_questions(): array {
	return [

		/* P1 – SEQUENCE: Scratch script order */
		[
			'type'        => 'drag-sequence',
			'title'       => '⌨️ Build the Script',
			'instruction' => 'Drag the Scratch blocks into the correct order so the cat moves forward and says "Hello!" when the green flag is clicked.',
			'items' => [
				[ 'id' => 'b', 'label' => '🔵 move (10) steps' ],
				[ 'id' => 'a', 'label' => '🟡 when 🏁 clicked' ],
				[ 'id' => 'd', 'label' => '🔴 stop all' ],
				[ 'id' => 'c', 'label' => '🟣 say [Hello!] for (2) secs' ],
			],
			'answer' => [ 'a', 'b', 'c', 'd' ],
			'tip'    => 'Every Scratch script starts with a yellow <strong>EVENT</strong> block. Action blocks then run top-to-bottom in sequence! 🐱',
		],

		/* P2 – PATTERN NEXT: pick the event block */
		[
			'type'        => 'pattern-next',
			'title'       => '🚦 Pick the Event Block',
			'instruction' => 'Which of these Scratch blocks is used to <strong>START</strong> a script? Click it!',
			'pattern'     => [],
			'choices'     => [
				'🔵 move (10) steps',
				'🟡 when 🏁 clicked',
				'🟣 say [Hello!] for 2 secs',
				'🔵 turn ↻ (15) degrees',
			],
			'answer' => 1,
			'tip'    => 'The yellow <strong>"when flag clicked"</strong> is an EVENT block — it tells Scratch WHEN to start the script. Without it, nothing runs! 🏁',
		],

		/* P3 – BUG FINDER: loop placement */
		[
			'type'        => 'bug-finder',
			'title'       => '🔁 Loop Trouble',
			'instruction' => 'This script should draw a square by repeating "move then turn" 4 times — but something is in the wrong place. Click the misplaced block.',
			'steps' => [
				[ 'id' => 0, 'label' => '🟡 when 🏁 clicked' ],
				[ 'id' => 1, 'label' => '🔵 move (100) steps  ← is this in the right place?' ],
				[ 'id' => 2, 'label' => '🟠 repeat (4)' ],
				[ 'id' => 3, 'label' => '🔵 turn ↻ (90) degrees' ],
				[ 'id' => 4, 'label' => '🔴 stop all' ],
			],
			'answer' => 1,
			'tip'    => '"Move (100) steps" must be INSIDE the repeat block, not before it. Both "move" and "turn" belong inside the loop to draw all 4 sides of the square! 🔁',
		],

		/* P4 – PATTERN NEXT: sprite vs stage */
		[
			'type'        => 'pattern-next',
			'title'       => '🐱 Sprite or Stage?',
			'instruction' => 'In Scratch, what do we call the CHARACTER you program to move around the stage?',
			'pattern'     => [],
			'choices'     => [
				'🖼️ Backdrop',
				'🐱 Sprite',
				'📝 Script',
				'🔊 Sound',
			],
			'answer' => 1,
			'tip'    => 'A <strong>SPRITE</strong> is any character or object you can program in Scratch. The background image is called the <strong>Backdrop</strong>. 🐱',
		],

		/* P5 – BUG FINDER: wrong block for speech bubble */
		[
			'type'        => 'bug-finder',
			'title'       => '🐛 Debug the Script',
			'instruction' => 'This script should make the cat show a <strong>speech bubble</strong> saying "Hello!" — but it\'s broken. Click the wrong block!',
			'steps' => [
				[ 'id' => 0, 'label' => '🟡 when 🏁 clicked' ],
				[ 'id' => 1, 'label' => '🔊 play sound [Meow]  ← correct block for a speech bubble?' ],
				[ 'id' => 2, 'label' => '🔴 stop all' ],
			],
			'answer' => 1,
			'tip'    => '"Play sound" plays audio — it does NOT show a speech bubble. You need <strong>"say [Hello!] for 2 secs"</strong> from the purple <strong>Looks</strong> category! 🗨️',
		],

		/* P6 – GRID NAV: centre of Scratch stage */
		[
			'type'        => 'grid-nav',
			'title'       => '📍 Find the Centre',
			'instruction' => 'In Scratch, the <strong>centre of the stage</strong> is at x:0, y:0. Click the centre cell of this mini-stage!',
			'size'   => 5,
			'start'  => [ 0, 0 ],
			'moves'  => [],
			'answer' => [ 2, 2 ],
			'direct' => true,
			'tip'    => 'The Scratch stage uses X and Y <strong>coordinates</strong>. x:0, y:0 is dead centre. Moving right increases X; moving up increases Y. 📍',
		],

		/* P7 – MATCH PAIRS: sound blocks */
		[
			'type'        => 'match-pairs',
			'title'       => '🔊 Block → Effect',
			'instruction' => 'Click a Scratch sound block on the left, then click what it does on the right.',
			'left'  => [
				[ 'id' => 'play', 'label' => '▶️ play sound [Meow]' ],
				[ 'id' => 'stop', 'label' => '⏹️ stop all sounds' ],
				[ 'id' => 'vol',  'label' => '🔉 set volume to (50%)' ],
			],
			'right' => [
				[ 'id' => 'quiet',   'label' => '📉 Makes sounds quieter' ],
				[ 'id' => 'meow',    'label' => '🐱 Makes a cat sound play' ],
				[ 'id' => 'silence', 'label' => '🤫 Silences everything' ],
			],
			'pairs' => [ [ 'play', 'meow' ], [ 'stop', 'silence' ], [ 'vol', 'quiet' ] ],
			'tip'   => 'Sound blocks control audio in your projects. "Play sound" and "stop all sounds" do very different things — always pick the right one! 🎵',
		],

		/* P8 – SEQUENCE: costume animation */
		[
			'type'        => 'drag-sequence',
			'title'       => '👋 Wave Animation',
			'instruction' => 'Put the Scratch blocks in order so the sprite waves: shows costume wave-a, waits, then switches to wave-b.',
			'items' => [
				[ 'id' => 'c', 'label' => '⏱️ wait (0.5) seconds' ],
				[ 'id' => 'a', 'label' => '🟡 when 🏁 clicked' ],
				[ 'id' => 'd', 'label' => '🟣 switch costume to [wave-b]' ],
				[ 'id' => 'b', 'label' => '🟣 switch costume to [wave-a]' ],
			],
			'answer' => [ 'a', 'b', 'c', 'd' ],
			'tip'    => 'Switching costumes quickly creates <strong>ANIMATION</strong>! The "wait" block gives time between changes so you can see each pose. 👋',
		],

		/* P9 – PATTERN NEXT: what does the script draw? */
		[
			'type'        => 'pattern-next',
			'title'       => '🔮 Read the Script',
			'instruction' => 'A Scratch script does: <em>move 10 steps → turn 15 degrees</em>, repeated <em>24 times</em>. What shape does the sprite trace?',
			'pattern'     => [],
			'choices'     => [
				'📏 A straight line',
				'⭕ A circle',
				'⬛ A square',
				'🔺 A triangle',
			],
			'answer' => 1,
			'tip'    => '24 × 15° = 360° = one full turn. Moving a little and turning a little, 24 times, traces a <strong>CIRCLE</strong>. This is exactly how Scratch draws circles! ⭕',
		],

		/* P10 – SEQUENCE: project creation workflow */
		[
			'type'        => 'drag-sequence',
			'title'       => '🚀 Create a Project',
			'instruction' => 'Put the steps for making a Scratch project in the correct order by dragging them.',
			'items' => [
				[ 'id' => 'e', 'label' => '⌨️ Write the scripts (add code blocks)' ],
				[ 'id' => 'a', 'label' => '💡 Think of your idea and plan it' ],
				[ 'id' => 'b', 'label' => '🌐 Share your project online' ],
				[ 'id' => 'c', 'label' => '🐱 Add sprites and a backdrop' ],
				[ 'id' => 'd', 'label' => '🧪 Test it and fix any bugs' ],
			],
			'answer' => [ 'a', 'c', 'e', 'd', 'b' ],
			'tip'    => 'Great programmers always: <strong>plan → build → test → debug → share!</strong> This is the software development cycle you\'ve been practising. 🚀',
		],
	];
}
