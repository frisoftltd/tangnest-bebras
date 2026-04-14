<?php
/**
 * Plugin Name:  Tangnest Bebras Interactive Quiz
 * Plugin URI:   https://lms.tangnest.rw
 * Description:  Bebras-style visual & interactive pre/post assessments for the Introduction to Computational Thinking course (ages 9–12). Drag-and-drop, click-to-color, grid navigation, pattern matching and more — no plain text MCQ.
 * Version:      1.0.0
 * Author:       Tangnest Ltd
 * Author URI:   https://lms.tangnest.rw
 * License:      GPL v2 or later
 * Text Domain:  tangnest-bebras
 *
 * USAGE
 * -----
 * Place either shortcode on any page / Tutor LMS lesson:
 *   [tangnest_quiz type="pre"]   ← 10 pre-course activities
 *   [tangnest_quiz type="post"]  ← 10 post-course activities
 *
 * Scores are saved to user meta (tnq_pre_score / tnq_post_score)
 * when the student is logged in.
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
 * MAIN PLUGIN CLASS
 * ================================================================== */
class Tangnest_Bebras_Quiz {

    public function __construct() {
        add_shortcode( 'tangnest_quiz', [ $this, 'render' ] );
        add_action( 'wp_ajax_tnq_save',        [ $this, 'ajax_save' ] );
        add_action( 'wp_ajax_nopriv_tnq_save', [ $this, 'ajax_save' ] );
    }

    /* ----------------------------------------------------------------
     * AJAX: persist score in user meta
     * -------------------------------------------------------------- */
    public function ajax_save(): void {
        check_ajax_referer( 'tnq_nonce', 'nonce' );
        $type  = sanitize_key( $_POST['quiz_type'] ?? 'pre' );
        $score = intval( $_POST['score'] ?? 0 );
        $total = intval( $_POST['total'] ?? 0 );
        if ( is_user_logged_in() ) {
            $uid = get_current_user_id();
            update_user_meta( $uid, "tnq_{$type}_score", $score );
            update_user_meta( $uid, "tnq_{$type}_total", $total );
            update_user_meta( $uid, "tnq_{$type}_date",  current_time( 'mysql' ) );
        }
        wp_send_json_success();
    }

    /* ================================================================
     * PRE-COURSE QUESTIONS  (10)
     * ============================================================== */
    private function pre_questions(): array {
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

    /* ================================================================
     * POST-COURSE QUESTIONS  (10)
     * ============================================================== */
    private function post_questions(): array {
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

    /* ================================================================
     * SHORTCODE RENDERER
     * ============================================================== */
    public function render( $atts ): string {
        $atts      = shortcode_atts( [ 'type' => 'pre' ], $atts );
        $type      = sanitize_key( $atts['type'] );
        $questions = $type === 'post' ? $this->post_questions() : $this->pre_questions();
        $uid       = 'tnq' . substr( md5( microtime() . rand() ), 0, 7 );
        $q_json    = wp_json_encode( $questions );
        $nonce     = wp_create_nonce( 'tnq_nonce' );
        $ajax_url  = esc_url( admin_url( 'admin-ajax.php' ) );
        $label     = $type === 'post' ? 'Post-Course' : 'Pre-Course';
        $n         = count( $questions );

        ob_start();
        // ── Styles (scoped) ─────────────────────────────────────────
        ?>
<style id="<?= esc_attr($uid) ?>-css">
#<?= $uid ?> *{box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
#<?= $uid ?>{--tnq-brand:#5c6bc0;--tnq-ok:#27ae60;--tnq-err:#e74c3c;--tnq-warn:#f39c12;--tnq-bg:#f8f9ff;--tnq-card:#fff;--tnq-border:#dee2f7;--tnq-text:#2d3142;--tnq-muted:#6c757d;
  max-width:720px;margin:2rem auto;padding:0 1rem;color:var(--tnq-text);}
/* Header */
.tnq-header{display:flex;align-items:center;gap:12px;margin-bottom:.75rem;flex-wrap:wrap;}
.tnq-badge{background:var(--tnq-brand);color:#fff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;white-space:nowrap;letter-spacing:.5px;}
.tnq-pbar-wrap{flex:1;min-width:120px;height:8px;background:#e0e3ff;border-radius:4px;overflow:hidden;}
.tnq-pbar-fill{height:100%;background:var(--tnq-brand);border-radius:4px;transition:width .4s ease;}
.tnq-ctr{font-size:13px;color:var(--tnq-muted);white-space:nowrap;}
.tnq-score-row{text-align:right;font-size:14px;color:var(--tnq-muted);margin-bottom:1rem;}
.tnq-score-row strong{color:var(--tnq-brand);font-size:18px;}
/* Body card */
.tnq-body{background:var(--tnq-card);border:1px solid var(--tnq-border);border-radius:16px;padding:1.5rem;margin-bottom:1rem;min-height:200px;}
.tnq-title{font-size:20px;font-weight:700;margin:0 0 .5rem;color:var(--tnq-brand);}
.tnq-instr{font-size:15px;line-height:1.6;margin:0 0 1.25rem;color:var(--tnq-text);}
/* Buttons */
.tnq-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:1.5px solid var(--tnq-border);border-radius:8px;background:#fff;color:var(--tnq-text);font-size:14px;font-weight:600;cursor:pointer;transition:.15s;}
.tnq-btn:hover{background:var(--tnq-bg);}
.tnq-btn-primary{background:var(--tnq-brand);border-color:var(--tnq-brand);color:#fff;}
.tnq-btn-primary:hover{background:#3f51b5;}
.tnq-btn-check{margin-top:1rem;width:100%;justify-content:center;font-size:16px;padding:12px;}
/* Nav */
.tnq-nav{display:flex;gap:10px;margin-top:.5rem;}
/* Tip */
.tnq-tip{border-radius:10px;padding:12px 16px;margin-top:1.25rem;font-size:14px;line-height:1.6;}
.tnq-tip-ok{background:#eafaf1;border:1px solid #a9dfbf;color:#145a32;}
.tnq-tip-err{background:#fdedec;border:1px solid #f1948a;color:#7b241c;}
/* ── DRAG SEQUENCE ── */
.tnq-seq-list{display:flex;flex-direction:column;gap:8px;}
.tnq-seq-item{background:var(--tnq-bg);border:2px solid var(--tnq-border);border-radius:10px;padding:12px 16px;font-size:15px;cursor:grab;user-select:none;transition:.15s;display:flex;align-items:center;gap:8px;}
.tnq-seq-item::before{content:'⠿';color:var(--tnq-muted);font-size:18px;}
.tnq-seq-item:hover{border-color:var(--tnq-brand);background:#eef0ff;}
.tnq-seq-item.tnq-dragging{opacity:.4;}
.tnq-seq-item.tnq-drag-over{border-color:var(--tnq-brand);background:#dde1ff;}
.tnq-seq-item.tnq-correct{background:#eafaf1;border-color:var(--tnq-ok);color:#145a32;}
.tnq-seq-item.tnq-wrong{background:#fdedec;border-color:var(--tnq-err);color:#7b241c;}
/* ── LOOP COUNT ── */
.tnq-loop-track{display:flex;gap:6px;margin:1rem 0;flex-wrap:wrap;}
.tnq-tile{width:48px;height:48px;border-radius:8px;background:var(--tnq-bg);border:2px solid var(--tnq-border);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.tnq-tile-start{background:#fff3cd;border-color:var(--tnq-warn);}
.tnq-tile-end{background:#d5f5e3;border-color:var(--tnq-ok);}
.tnq-loop-ctrl{display:flex;align-items:center;gap:12px;margin:1.25rem 0;}
.tnq-loop-val{font-size:40px;font-weight:900;color:var(--tnq-brand);min-width:60px;text-align:center;}
.tnq-loop-note{font-size:13px;color:var(--tnq-muted);}
/* ── CLICK COLOR ── */
.tnq-palette{display:flex;gap:10px;margin-bottom:1rem;align-items:center;flex-wrap:wrap;}
.tnq-swatch{width:44px;height:44px;border-radius:50%;border:3px solid transparent;cursor:pointer;transition:.15s;transform:scale(1);}
.tnq-swatch.tnq-swatch-sel{border:3px solid #222;transform:scale(1.2);box-shadow:0 0 0 3px #fff,0 0 0 5px #222;}
.tnq-pal-label{font-size:13px;color:var(--tnq-muted);}
.tnq-svg-wrap{border:2px solid var(--tnq-border);border-radius:12px;overflow:hidden;background:#fafafa;}
.tnq-region{cursor:pointer;transition:opacity .1s;}
.tnq-region:hover{opacity:.8;}
/* ── PATTERN NEXT ── */
.tnq-pat-row{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:1.25rem;}
.tnq-pat-item{font-size:32px;line-height:1;}
.tnq-pat-q{font-size:32px;font-weight:900;color:var(--tnq-brand);}
.tnq-choice-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;}
.tnq-choice-btn{padding:14px 10px;border:2px solid var(--tnq-border);border-radius:10px;background:var(--tnq-bg);font-size:18px;cursor:pointer;transition:.15s;text-align:center;}
.tnq-choice-btn:hover{border-color:var(--tnq-brand);background:#eef0ff;}
.tnq-choice-btn.tnq-correct{background:#eafaf1;border-color:var(--tnq-ok);color:#145a32;}
.tnq-choice-btn.tnq-muted-out{opacity:.4;cursor:default;}
/* ── BUG FINDER ── */
.tnq-bug-list{display:flex;flex-direction:column;gap:8px;}
.tnq-bug-step{padding:12px 16px;border-radius:10px;border:2px solid var(--tnq-border);background:var(--tnq-bg);font-size:15px;cursor:pointer;display:flex;gap:10px;align-items:center;transition:.15s;}
.tnq-bug-step:hover{border-color:var(--tnq-err);background:#fff5f5;}
.tnq-bug-step.tnq-correct{background:#eafaf1;border-color:var(--tnq-ok);cursor:default;}
.tnq-bug-step.tnq-wrong{background:#fdedec;border-color:var(--tnq-err);cursor:default;}
.tnq-step-num{font-weight:700;color:var(--tnq-brand);font-size:16px;min-width:24px;}
/* ── MATCH PAIRS ── */
.tnq-match-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.tnq-match-col{display:flex;flex-direction:column;gap:8px;}
.tnq-match-btn{padding:12px 10px;border:2px solid var(--tnq-border);border-radius:10px;background:var(--tnq-bg);font-size:14px;cursor:pointer;text-align:left;transition:.15s;line-height:1.4;}
.tnq-match-btn:hover{border-color:var(--tnq-brand);}
.tnq-match-btn.tnq-selected{border-color:var(--tnq-brand);background:#eef0ff;box-shadow:0 0 0 2px var(--tnq-brand);}
.tnq-match-btn.tnq-matched{background:#eafaf1;border-color:var(--tnq-ok);cursor:default;}
@keyframes tnq-shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-6px)}40%,80%{transform:translateX(6px)}}
.tnq-shake{animation:tnq-shake .4s ease;}
/* ── DRAG SORT ── */
.tnq-pool{display:flex;flex-wrap:wrap;gap:8px;padding:12px;border:2px dashed var(--tnq-border);border-radius:12px;min-height:60px;margin-bottom:1rem;}
.tnq-sort-item{padding:8px 14px;border:2px solid var(--tnq-border);border-radius:8px;background:#fff;font-size:15px;cursor:grab;user-select:none;}
.tnq-sort-item:hover{border-color:var(--tnq-brand);}
.tnq-sort-item.tnq-dragging{opacity:.4;}
.tnq-bins-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.tnq-bin{min-height:100px;border:2px dashed var(--tnq-border);border-radius:12px;padding:10px;background:var(--tnq-bg);}
.tnq-bin.tnq-drag-active{border-color:var(--tnq-brand);background:#eef0ff;}
.tnq-bin-label{font-weight:700;font-size:14px;margin-bottom:8px;color:var(--tnq-brand);}
/* ── GRID NAV ── */
.tnq-grid{display:grid;gap:4px;max-width:280px;margin:0 auto 1rem;}
.tnq-cell{aspect-ratio:1;border:2px solid var(--tnq-border);border-radius:8px;background:var(--tnq-bg);display:flex;align-items:center;justify-content:center;font-size:22px;cursor:pointer;transition:.15s;}
.tnq-cell:hover{border-color:var(--tnq-brand);background:#eef0ff;}
.tnq-cell.tnq-start{background:#fff3cd;border-color:var(--tnq-warn);cursor:default;}
.tnq-cell.tnq-target-reveal{background:#eafaf1;border-color:var(--tnq-ok);}
/* ── RESULTS ── */
.tnq-results{text-align:center;padding:1.5rem;}
.tnq-score-circle{font-size:64px;font-weight:900;color:var(--tnq-brand);line-height:1;}
.tnq-score-circle span{font-size:28px;color:var(--tnq-muted);}
.tnq-res-pct{font-size:18px;color:var(--tnq-muted);margin:.5rem 0;}
.tnq-res-msg{font-size:16px;margin:1rem 0 1.5rem;line-height:1.6;}
/* Responsive */
@media(max-width:480px){
  .tnq-choice-grid{grid-template-columns:1fr;}
  .tnq-match-grid{grid-template-columns:1fr;}
  .tnq-bins-row{grid-template-columns:1fr;}
  .tnq-loop-track{gap:4px;}
  .tnq-tile{width:40px;height:40px;font-size:18px;}
}
</style>

        <?php // ── HTML shell ─────────────────────────────────────── ?>
<div id="<?= esc_attr($uid) ?>">
  <div class="tnq-header">
    <span class="tnq-badge"><?= esc_html($label) ?> · <?= $n ?> Activities</span>
    <div class="tnq-pbar-wrap"><div class="tnq-pbar-fill" id="<?= $uid ?>-prog" style="width:<?= round(100/$n) ?>%"></div></div>
    <span class="tnq-ctr" id="<?= $uid ?>-ctr">1 / <?= $n ?></span>
  </div>
  <div class="tnq-score-row">Score: <strong id="<?= $uid ?>-sc">0</strong> / <?= $n ?></div>
  <div class="tnq-body" id="<?= $uid ?>-body"></div>
  <div class="tnq-nav"  id="<?= $uid ?>-nav"></div>
</div>

        <?php // ── JavaScript engine ──────────────────────────────── ?>
<script>
(function(){
'use strict';
const UID='<?= $uid ?>',AJAX='<?= $ajax_url ?>',NONCE='<?= $nonce ?>',QTYPE='<?= $type ?>';
const QS=<?= $q_json ?>;
const N=QS.length;
let cur=0,score=0,done=new Array(N).fill(false),correct=new Array(N).fill(false);

/* ── helpers ── */
function $id(id){return document.getElementById(id);}
function $qs(s,c){return (c||document).querySelector(s);}
function $all(s,c){return [...(c||document).querySelectorAll(s)];}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function shuffle(a){let b=[...a];for(let i=b.length-1;i>0;i--){let j=~~(Math.random()*(i+1));[b[i],b[j]]=[b[j],b[i]];}return b;}

function body(){return $id(UID+'-body');}
function navEl(){return $id(UID+'-nav');}

/* ── HUD ── */
function hud(){
  $id(UID+'-prog').style.width=((cur+1)/N*100)+'%';
  $id(UID+'-ctr').textContent=(cur+1)+' / '+N;
  $id(UID+'-sc').textContent=score;
}

/* ── nav buttons ── */
function renderNav(){
  navEl().innerHTML='';
  if(cur>0){
    btn('← Back','tnq-btn',()=>go(cur-1));
  }
  if(done[cur]){
    if(cur<N-1) btn('Next →','tnq-btn tnq-btn-primary',()=>go(cur+1));
    else        btn('🎉 See Results','tnq-btn tnq-btn-primary',showResults);
  }
}
function btn(txt,cls,fn){
  const b=document.createElement('button');
  b.className=cls;b.textContent=txt;b.onclick=fn;
  navEl().appendChild(b);
}

/* ── main flow ── */
function go(i){cur=i;render();}

function markDone(isCorrect){
  if(done[cur])return;
  done[cur]=true;correct[cur]=isCorrect;
  if(isCorrect)score++;
  hud();
  appendTip(QS[cur].tip,isCorrect);
  renderNav();
}

function appendTip(tip,isOk){
  const d=document.createElement('div');
  d.className='tnq-tip '+(isOk?'tnq-tip-ok':'tnq-tip-err');
  d.innerHTML='<strong>'+(isOk?'✅ Correct!':'❌ Not quite — here\'s what to know:')+'</strong><br>'+tip;
  body().appendChild(d);
}

function render(){
  const q=QS[cur]; body().innerHTML=''; hud();
  const h=document.createElement('h2');h.className='tnq-title';h.textContent=q.title;body().appendChild(h);
  const p=document.createElement('p');p.className='tnq-instr';p.innerHTML=q.instruction;body().appendChild(p);
  const isDone=done[cur];
  ({
    'drag-sequence': ()=>dragSequence(q,isDone),
    'loop-count':    ()=>loopCount(q,isDone),
    'click-color':   ()=>clickColor(q,isDone),
    'pattern-next':  ()=>patternNext(q,isDone),
    'bug-finder':    ()=>bugFinder(q,isDone),
    'match-pairs':   ()=>matchPairs(q,isDone),
    'drag-sort':     ()=>dragSort(q,isDone),
    'grid-nav':      ()=>gridNav(q,isDone),
  }[q.type]||(() => {}))();
  if(isDone)appendTip(q.tip,correct[cur]);
  renderNav();
}

/* ══════════════════════════════════════════════
   QUESTION TYPE RENDERERS
   ══════════════════════════════════════════════ */

/* ── 1. DRAG SEQUENCE ── */
function dragSequence(q,isDone){
  const items=isDone?q.items:shuffle(q.items);
  const list=document.createElement('div');list.className='tnq-seq-list';

  items.forEach((it,i)=>{
    const d=document.createElement('div');
    d.className='tnq-seq-item';d.draggable=!isDone;
    d.dataset.id=it.id;d.dataset.pos=i;
    d.innerHTML='<span>'+esc(it.label)+'</span>';

    if(isDone){
      const pos=q.answer.indexOf(it.id);
      d.classList.add(pos===items.findIndex(x=>x.id===it.id)?'tnq-correct':'tnq-wrong');
    }
    d.addEventListener('dragstart',e=>{e.dataTransfer.setData('id',it.id);d.classList.add('tnq-dragging');});
    d.addEventListener('dragend',()=>d.classList.remove('tnq-dragging'));
    d.addEventListener('dragover',e=>{e.preventDefault();d.classList.add('tnq-drag-over');});
    d.addEventListener('dragleave',()=>d.classList.remove('tnq-drag-over'));
    d.addEventListener('drop',e=>{
      e.preventDefault();d.classList.remove('tnq-drag-over');
      const fromId=e.dataTransfer.getData('id');
      const fromEl=$qs('[data-id="'+fromId+'"]',list);
      const parent=list;
      parent.insertBefore(fromEl,d);
      $all('[data-id]',list).forEach((el,i)=>el.dataset.pos=i);
    });
    list.appendChild(d);
  });
  body().appendChild(list);

  if(!isDone){
    const chk=document.createElement('button');
    chk.className='tnq-btn tnq-btn-primary tnq-btn-check';chk.textContent='✔ Check My Order';
    chk.onclick=()=>{
      const order=$all('[data-id]',list).map(e=>e.dataset.id);
      markDone(JSON.stringify(order)===JSON.stringify(q.answer));
      render();
    };
    body().appendChild(chk);
  }
}

/* ── 2. LOOP COUNT ── */
function loopCount(q,isDone){
  const track=document.createElement('div');track.className='tnq-loop-track';
  for(let i=0;i<=q.tiles;i++){
    const t=document.createElement('div');
    t.className='tnq-tile'+(i===0?' tnq-tile-start':i===q.tiles?' tnq-tile-end':'');
    t.textContent=i===0?'⭐':i===q.tiles?'🏁':'·';
    track.appendChild(t);
  }
  body().appendChild(track);

  let val=1;
  const ctrl=document.createElement('div');ctrl.className='tnq-loop-ctrl';
  const minus=document.createElement('button');minus.className='tnq-btn';minus.textContent='−';
  const disp=document.createElement('span');disp.className='tnq-loop-val';disp.textContent=val;
  const plus=document.createElement('button');plus.className='tnq-btn';plus.textContent='+';

  minus.onclick=()=>{if(val>1){val--;disp.textContent=val;}};
  plus.onclick=()=>{if(val<=14){val++;disp.textContent=val;}};
  if(isDone){minus.disabled=true;plus.disabled=true;disp.textContent=q.answer;}
  ctrl.appendChild(minus);ctrl.appendChild(disp);ctrl.appendChild(plus);
  body().appendChild(ctrl);

  const note=document.createElement('p');note.className='tnq-loop-note';
  note.textContent='How many times does the robot hop?';body().appendChild(note);

  if(!isDone){
    const chk=document.createElement('button');
    chk.className='tnq-btn tnq-btn-primary tnq-btn-check';chk.textContent='✔ Check';
    chk.onclick=()=>{markDone(val===q.answer);render();};
    body().appendChild(chk);
  }
}

/* ── 3. CLICK COLOR ── */
function clickColor(q,isDone){
  let selColor=0;
  const state={}; // rid → color index

  /* palette */
  const pal=document.createElement('div');pal.className='tnq-palette';
  const palLabel=document.createElement('span');palLabel.className='tnq-pal-label';palLabel.textContent='Pick a color:';
  pal.appendChild(palLabel);
  q.colors.forEach((c,i)=>{
    const sw=document.createElement('button');sw.className='tnq-swatch'+(i===0?' tnq-swatch-sel':'');
    sw.style.background=c;sw.title=q.color_labels[i];sw.dataset.ci=i;
    sw.onclick=()=>{selColor=i;$all('.tnq-swatch',pal).forEach(s=>s.classList.remove('tnq-swatch-sel'));sw.classList.add('tnq-swatch-sel');};
    pal.appendChild(sw);
  });
  body().appendChild(pal);

  /* SVG flower */
  const ns='http://www.w3.org/2000/svg';
  const svgWrap=document.createElement('div');svgWrap.className='tnq-svg-wrap';
  const svg=document.createElementNS(ns,'svg');
  svg.setAttribute('viewBox','0 0 300 320');svg.setAttribute('width','100%');svg.style.display='block';
  svgWrap.appendChild(svg);body().appendChild(svgWrap);

  function mkEl(tag,attrs){
    const e=document.createElementNS(ns,tag);
    Object.entries(attrs).forEach(([k,v])=>e.setAttribute(k,v));
    return e;
  }
  function addRegion(el,rid){
    el.dataset.rid=rid;el.classList.add('tnq-region');
    if(!isDone)el.addEventListener('click',()=>{
      state[rid]=selColor;
      paintRegion(rid,selColor);
    });
    svg.appendChild(el);
    state[rid]=undefined;
  }
  function paintRegion(rid,ci){
    const els=$all('[data-rid="'+rid+'"]',svg);
    els.forEach(e=>{
      if(e.tagName==='g'){[...e.children].forEach(c=>c.setAttribute('fill',ci===undefined?'#eee':q.colors[ci]));}
      else e.setAttribute('fill',ci===undefined?'#eee':q.colors[ci]);
    });
  }

  /* background */
  const bg=mkEl('rect',{x:0,y:0,width:300,height:320,fill:'#eee',stroke:'#555','stroke-width':2});
  addRegion(bg,'bg');

  /* stem */
  const stem=mkEl('rect',{x:141,y:188,width:18,height:112,fill:'#eee',stroke:'#555','stroke-width':1.5});
  addRegion(stem,'stem');

  /* leaf */
  const leaf=mkEl('ellipse',{cx:186,cy:256,rx:36,ry:16,transform:'rotate(-30 186 256)',fill:'#eee',stroke:'#555','stroke-width':1.5});
  addRegion(leaf,'leaf');

  /* petals group */
  const petalG=document.createElementNS(ns,'g');petalG.dataset.rid='petals';petalG.classList.add('tnq-region');
  for(let i=0;i<8;i++){
    const e=mkEl('ellipse',{cx:150,cy:86,rx:22,ry:44,transform:`rotate(${i*45} 150 150)`,fill:'#eee',stroke:'#555','stroke-width':1.5});
    petalG.appendChild(e);
  }
  if(!isDone)petalG.addEventListener('click',()=>{state['petals']=selColor;paintRegion('petals',selColor);});
  svg.appendChild(petalG);

  /* center */
  const center=mkEl('circle',{cx:150,cy:150,r:32,fill:'#eee',stroke:'#555','stroke-width':2});
  addRegion(center,'center');

  /* labels */
  [{rid:'bg',x:8,y:18,t:'Background'},{rid:'petals',x:72,y:30,t:'Petals'},{rid:'center',x:122,y:154,t:'Centre'},
   {rid:'stem',x:163,y:248,t:'Stem'},{rid:'leaf',x:192,y:290,t:'Leaf'}].forEach(lb=>{
    const t=mkEl('text',{x:lb.x,y:lb.y,'font-size':11,fill:'#333'});
    t.textContent=lb.t;t.style.pointerEvents='none';svg.appendChild(t);
  });

  if(!isDone){
    const chk=document.createElement('button');
    chk.className='tnq-btn tnq-btn-primary tnq-btn-check';chk.textContent='✔ Check Coloring';
    chk.onclick=()=>{
      const rids=['bg','petals','center','stem','leaf'];
      if(rids.some(r=>state[r]===undefined)){alert('Please paint all 5 regions first!');return;}
      let valid=true;
      q.regions.forEach(reg=>{
        reg.adj.forEach(adj=>{if(state[reg.id]===state[adj])valid=false;});
      });
      markDone(valid);render();
    };
    body().appendChild(chk);
  }
}

/* ── 4. PATTERN NEXT ── */
function patternNext(q,isDone){
  if(q.pattern&&q.pattern.length>0){
    const row=document.createElement('div');row.className='tnq-pat-row';
    q.pattern.forEach(p=>{const s=document.createElement('span');s.className='tnq-pat-item';s.textContent=p;row.appendChild(s);});
    const qm=document.createElement('span');qm.className='tnq-pat-q';qm.textContent='?';row.appendChild(qm);
    body().appendChild(row);
  }
  const grid=document.createElement('div');grid.className='tnq-choice-grid';
  q.choices.forEach((c,i)=>{
    const b=document.createElement('button');b.className='tnq-choice-btn';b.innerHTML=esc(c);
    if(isDone){
      if(i===q.answer)b.classList.add('tnq-correct');
      else b.classList.add('tnq-muted-out');
    } else {
      b.onclick=()=>{markDone(i===q.answer);render();};
    }
    grid.appendChild(b);
  });
  body().appendChild(grid);
}

/* ── 5. BUG FINDER ── */
function bugFinder(q,isDone){
  const list=document.createElement('div');list.className='tnq-bug-list';
  q.steps.forEach(s=>{
    const d=document.createElement('div');d.className='tnq-bug-step';
    d.innerHTML='<span class="tnq-step-num">'+(s.id+1)+'.</span>'+esc(s.label);
    if(isDone){
      if(s.id===q.answer)d.classList.add('tnq-correct');
    } else {
      d.onclick=()=>{markDone(s.id===q.answer);render();};
    }
    list.appendChild(d);
  });
  body().appendChild(list);
}

/* ── 6. MATCH PAIRS ── */
function matchPairs(q,isDone){
  let selL=null,matched={};
  const grid=document.createElement('div');grid.className='tnq-match-grid';
  const lCol=document.createElement('div');lCol.className='tnq-match-col';
  const rCol=document.createElement('div');rCol.className='tnq-match-col';

  function tryMatch(){
    if(!selL)return;
    // wait for right click
  }

  q.left.forEach(it=>{
    const b=document.createElement('button');b.className='tnq-match-btn';b.innerHTML=esc(it.label);b.dataset.lid=it.id;
    if(!isDone)b.onclick=()=>{
      $all('[data-lid]',grid).forEach(x=>{if(!x.classList.contains('tnq-matched'))x.classList.remove('tnq-selected');});
      b.classList.add('tnq-selected');selL=it.id;
    };
    lCol.appendChild(b);
  });

  q.right.forEach(it=>{
    const b=document.createElement('button');b.className='tnq-match-btn';b.innerHTML=esc(it.label);b.dataset.rid=it.id;
    if(!isDone)b.onclick=()=>{
      if(!selL)return;
      const pair=q.pairs.find(p=>p[0]===selL&&p[1]===it.id);
      const lEl=$qs('[data-lid="'+selL+'"]',grid);
      if(pair){
        lEl.classList.add('tnq-matched');b.classList.add('tnq-matched');
        lEl.onclick=null;b.onclick=null;matched[selL]=it.id;
      } else {
        lEl.classList.add('tnq-shake');b.classList.add('tnq-shake');
        setTimeout(()=>{lEl.classList.remove('tnq-shake','tnq-selected');b.classList.remove('tnq-shake');},450);
      }
      selL=null;
      if(Object.keys(matched).length===q.pairs.length){markDone(true);render();}
    };
    rCol.appendChild(b);
  });

  grid.appendChild(lCol);grid.appendChild(rCol);body().appendChild(grid);
}

/* ── 7. DRAG SORT ── */
function dragSort(q,isDone){
  const pool=document.createElement('div');pool.className='tnq-pool';
  const shuffled=isDone?q.items:shuffle(q.items);
  shuffled.forEach(it=>{
    const d=document.createElement('div');d.className='tnq-sort-item';d.textContent=it.label;
    d.draggable=!isDone;d.dataset.iid=it.id;d.dataset.bin=it.bin;d.dataset.currentBin=-1;
    d.addEventListener('dragstart',e=>{e.dataTransfer.setData('iid',it.id);d.classList.add('tnq-dragging');});
    d.addEventListener('dragend',()=>d.classList.remove('tnq-dragging'));
    pool.appendChild(d);
  });
  body().appendChild(pool);

  const binsRow=document.createElement('div');binsRow.className='tnq-bins-row';
  q.bins.forEach((label,i)=>{
    const bin=document.createElement('div');bin.className='tnq-bin';bin.dataset.bidx=i;
    const h=document.createElement('div');h.className='tnq-bin-label';h.textContent=label;bin.appendChild(h);
    bin.addEventListener('dragover',e=>{e.preventDefault();bin.classList.add('tnq-drag-active');});
    bin.addEventListener('dragleave',()=>bin.classList.remove('tnq-drag-active'));
    bin.addEventListener('drop',e=>{
      e.preventDefault();bin.classList.remove('tnq-drag-active');
      const iid=e.dataTransfer.getData('iid');
      const el=$qs('[data-iid="'+iid+'"]');
      if(el){bin.appendChild(el);el.dataset.currentBin=i;}
    });
    binsRow.appendChild(bin);
  });
  body().appendChild(binsRow);

  if(!isDone){
    const chk=document.createElement('button');
    chk.className='tnq-btn tnq-btn-primary tnq-btn-check';chk.textContent='✔ Check Sorting';
    chk.onclick=()=>{
      let ok=true;
      q.items.forEach(it=>{
        const el=$qs('[data-iid="'+it.id+'"]');
        if(!el||parseInt(el.dataset.currentBin)!==it.bin)ok=false;
      });
      markDone(ok);render();
    };
    body().appendChild(chk);
  }
}

/* ── 8. GRID NAV ── */
function gridNav(q,isDone){
  const sz=q.size;
  const target=computeTarget(q);

  const grid=document.createElement('div');
  grid.className='tnq-grid';
  grid.style.gridTemplateColumns='repeat('+sz+',1fr)';

  for(let r=0;r<sz;r++){
    for(let c=0;c<sz;c++){
      const cell=document.createElement('div');cell.className='tnq-cell';
      const isStart=r===q.start[0]&&c===q.start[1];
      const isTarget=r===target[0]&&c===target[1];
      if(isStart){cell.textContent='⭐';cell.classList.add('tnq-start');}
      if(isDone&&isTarget){cell.classList.add('tnq-target-reveal');if(!isStart)cell.textContent='✅';}
      if(!isDone&&!isStart){
        cell.addEventListener('click',()=>{markDone(r===target[0]&&c===target[1]);render();});
      }
      grid.appendChild(cell);
    }
  }
  body().appendChild(grid);

  if(!q.direct&&q.moves.length>0){
    const legend=document.createElement('p');
    legend.style.cssText='font-size:13px;color:#888;text-align:center;margin-top:.5rem;';
    legend.textContent='← = left  → = right  ↑ = up  ↓ = down';
    body().appendChild(legend);
  }
}

function computeTarget(q){
  if(q.direct)return q.answer;
  let pos=[...q.start];
  (q.moves||[]).forEach(m=>{
    if(m==='r'&&pos[1]<q.size-1)pos[1]++;
    else if(m==='l'&&pos[1]>0)pos[1]--;
    else if(m==='d'&&pos[0]<q.size-1)pos[0]++;
    else if(m==='u'&&pos[0]>0)pos[0]--;
  });
  return pos;
}

/* ══════════════════════════════════════════════
   RESULTS
   ══════════════════════════════════════════════ */
function showResults(){
  body().innerHTML='';navEl().innerHTML='';
  $id(UID+'-prog').style.width='100%';
  $id(UID+'-ctr').textContent='Done!';
  const pct=Math.round(score/N*100);
  const msg=pct>=90?'🌟 Outstanding! You\'re a true computational thinker!':
            pct>=70?'🎉 Great work! You\'ve got solid foundations!':
            pct>=50?'👍 Good effort! Review the lessons and try again.':
                    '💪 Keep going — every expert was once a beginner!';

  body().innerHTML=`
    <div class="tnq-results">
      <div class="tnq-score-circle">${score}<span>/${N}</span></div>
      <p class="tnq-res-pct">${pct}% correct</p>
      <p class="tnq-res-msg">${msg}</p>
      <button class="tnq-btn tnq-btn-primary" onclick="location.reload()">↺ Try Again</button>
    </div>`;

  /* persist to WP */
  const fd=new FormData();
  fd.append('action','tnq_save');fd.append('nonce',NONCE);
  fd.append('quiz_type',QTYPE);fd.append('score',score);fd.append('total',N);
  fetch(AJAX,{method:'POST',body:fd}).catch(()=>{});
}

/* ── BOOT ── */
render();
})();
</script>
<?php
        return ob_get_clean();
    }
}

new Tangnest_Bebras_Quiz();
