<?php
/**
 * SVG icon loader.
 *
 * Resolves icon keys (e.g. 'bead-red', 'alarm-clock') to SVG file paths
 * and inlines them so CSS color: drives fill="currentColor" regions.
 *
 * @package Tangnest_Bebras
 */

defined( 'ABSPATH' ) || exit;

class TNQ_Icons {

	/** Base directory for SVG assets. */
	private static $svg_dir = '';

	/**
	 * Map icon key → relative path under svg/.
	 * Keys without an explicit entry fall back to a directory scan.
	 */
	private static $map = [
		// objects
		'alarm-clock'       => 'objects/alarm-clock.svg',
		'ball-soccer'       => 'objects/ball-soccer.svg',
		'battery'           => 'objects/battery.svg',
		'books'             => 'objects/books.svg',
		'brush-teeth'       => 'objects/brush-teeth.svg',
		'bulb'              => 'objects/bulb.svg',
		'bulb-lit'          => 'objects/bulb-lit.svg',
		'bulb-broken'       => 'objects/bulb-broken.svg',
		'clap-hand'         => 'objects/clap-hand.svg',
		'cup-pour'          => 'objects/cup-pour.svg',
		'door'              => 'objects/door.svg',
		'electricity-plug'  => 'objects/electricity-plug.svg',
		'fire'              => 'objects/fire.svg',
		'handle'            => 'objects/handle.svg',
		'jerrycan'          => 'objects/jerrycan.svg',
		'key'               => 'objects/key.svg',
		'kettle'            => 'objects/kettle.svg',
		'kettle-pour'       => 'objects/kettle-pour.svg',
		'leaf'              => 'objects/leaf-colorable.svg',
		'leaf-colorable'    => 'objects/leaf-colorable.svg',
		'pencil'            => 'objects/pencil.svg',
		'school-bag'        => 'objects/school-bag.svg',
		'switch-on'         => 'objects/switch-on.svg',
		'switch-off'        => 'objects/switch-off.svg',
		'tea-cup'           => 'objects/tea-cup.svg',
		'teabag'            => 'objects/teabag.svg',
		'torch'             => 'objects/torch.svg',
		'uniform'           => 'objects/uniform.svg',
		'water-drop'        => 'objects/water-drop.svg',
		'zip'               => 'objects/zip.svg',
		// Phase A correction icons
		'flag-colorable'    => 'objects/flag-colorable.svg',
		'footstep'          => 'objects/footstep.svg',
		'switch-on-bulb-broken'   => 'objects/switch-on-bulb-broken.svg',
		'switch-off-bulb-good'    => 'objects/switch-off-bulb-good.svg',
		'switch-on-bulb-good'     => 'objects/switch-on-bulb-good.svg',
		'switch-off-bulb-broken'  => 'objects/switch-off-bulb-broken.svg',
		// Endline objects
		'pot-water'         => 'objects/pot-water.svg',
		'corn'              => 'objects/corn.svg',
		'plate-corn'        => 'objects/plate-corn.svg',
		'blanket-fold'      => 'objects/blanket-fold.svg',
		'sheet-flat'        => 'objects/sheet-flat.svg',
		'blanket-spread'    => 'objects/blanket-spread.svg',
		'pillow'            => 'objects/pillow.svg',
		'orange'            => 'objects/orange.svg',
		'lace-pull'         => 'objects/lace-pull.svg',
		'traffic-red'       => 'objects/traffic-red.svg',
		'traffic-amber'     => 'objects/traffic-amber.svg',
		'traffic-green'     => 'objects/traffic-green.svg',
		'sun'               => 'objects/sun.svg',
		'soil'              => 'objects/soil.svg',
		'plant-growing'     => 'objects/plant-growing.svg',
		'plant-drinking'    => 'objects/plant-drinking.svg',
		'plant-roots'       => 'objects/plant-roots.svg',
		'crane-colorable'   => 'objects/crane-colorable.svg',
		'sunny-near'        => 'objects/sunny-near.svg',
		'rainy-near'        => 'objects/rainy-near.svg',
		'sunny-far'         => 'objects/sunny-far.svg',
		'rainy-far'         => 'objects/rainy-far.svg',
		// patterns
		'bead-red'          => 'patterns/bead-red.svg',
		'bead-blue'         => 'patterns/bead-blue.svg',
		'bead-yellow'       => 'patterns/bead-yellow.svg',
		'bead-green'        => 'patterns/bead-green.svg',
		'circle'            => 'patterns/circle.svg',
		'square'            => 'patterns/square.svg',
		'star'              => 'patterns/star.svg',
		'triangle'          => 'patterns/triangle.svg',
		'flag-red'          => 'patterns/flag-red.svg',
		'flag-yellow'       => 'patterns/flag-yellow.svg',
		'flag-green'        => 'patterns/flag-green.svg',
		'flag-blue'         => 'patterns/flag-blue.svg',
		// places
		'house'             => 'places/house.svg',
		'tap'               => 'places/tap.svg',
		// ui
		'check'             => 'ui/check.svg',
		'cross'             => 'ui/cross.svg',
		'hint-bulb'         => 'ui/hint-bulb.svg',
		'timer-clock'       => 'ui/timer-clock.svg',
	];

	private static function svg_dir(): string {
		if ( '' === self::$svg_dir ) {
			self::$svg_dir = TNQ_PLUGIN_DIR . 'public/assets/svg/';
		}
		return self::$svg_dir;
	}

	/**
	 * Return inlined SVG markup for the given icon key.
	 *
	 * @param string $key    Icon key (e.g. 'bead-red').
	 * @param array  $attrs  Extra HTML attributes to inject into the <svg> tag.
	 *                       'class' defaults to 'tnq-icon'; pass an explicit
	 *                       'class' to override (it will be merged).
	 */
	public static function icon( string $key, array $attrs = [] ): string {
		$path = self::resolve_path( $key );

		if ( null === $path || ! file_exists( $path ) ) {
			return '<span class="tnq-icon-missing" title="' . esc_attr( "Missing icon: $key" ) . '" aria-hidden="true">?</span>';
		}

		$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $svg ) {
			return '<span class="tnq-icon-missing" title="' . esc_attr( "Unreadable icon: $key" ) . '" aria-hidden="true">?</span>';
		}

		return self::inject_attrs( $svg, $attrs, $key );
	}

	/**
	 * Return the absolute filesystem path for an icon key, or null.
	 */
	public static function resolve_path( string $key ): ?string {
		if ( isset( self::$map[ $key ] ) ) {
			return self::svg_dir() . self::$map[ $key ];
		}
		// Fallback: search each category directory.
		foreach ( [ 'objects', 'patterns', 'places', 'people', 'ui' ] as $dir ) {
			$candidate = self::svg_dir() . $dir . '/' . $key . '.svg';
			if ( file_exists( $candidate ) ) {
				return $candidate;
			}
		}
		return null;
	}

	/**
	 * Inject HTML attributes into the root <svg> element.
	 */
	private static function inject_attrs( string $svg, array $attrs, string $key ): string {
		$class = 'tnq-icon tnq-icon--' . esc_attr( preg_replace( '/[^a-z0-9\-]/', '-', $key ) );
		if ( ! empty( $attrs['class'] ) ) {
			$class .= ' ' . $attrs['class'];
		}
		$attrs['class'] = $class;

		// Remove width/height so CSS controls sizing.
		$svg = preg_replace( '/\s+(width|height)="[^"]*"/', '', $svg );

		// Build extra attribute string (skip class, handled separately).
		$extra = '';
		foreach ( $attrs as $name => $value ) {
			if ( 'class' !== $name ) {
				$extra .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Inject class + extra into the opening <svg tag.
		$svg = preg_replace(
			'/<svg\b/',
			'<svg class="' . esc_attr( $class ) . '"' . $extra,
			$svg,
			1
		);

		return $svg;
	}
}
