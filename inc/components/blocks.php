<?php
/**
 * Gutenberg-Content-Elemente (§5 Content-Elemente + individuelle Bausteine
 * wie Hero/Slider/FAQ). Bewusst native Blocks statt ACF Blocks/Elementor
 * (§33 keine unnötigen Dependencies, §28 "Elementor nur wenn benötigt") und
 * bewusst OHNE Build-Step (kein webpack/@wordpress/scripts im Projekt, vgl.
 * TODO-Kommentar in base.css zu Fonts) — jedes Block-edit.js registriert
 * sich direkt über die globalen wp.*-Objekte, die der Block-Editor selbst
 * mitbringt.
 *
 * Jeder Unterordner in /blocks/ ist ein eigenständiger Block (block.json +
 * render.php + edit.js). Neue Blöcke brauchen keine Änderung an dieser
 * Datei — einfach einen neuen Ordner anlegen (gleiches Prinzip wie
 * bodywings_load_module_dir() in functions.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'bodywings_register_block_category', 5 );

/**
 * Eigene Inserter-Kategorie, damit Redakteur:innen die Theme-Bausteine
 * nicht zwischen den WordPress-Standardblöcken suchen müssen.
 */
function bodywings_register_block_category(): void {
	add_filter( 'block_categories_all', function ( array $categories ): array {
		return array_merge(
			array( array( 'slug' => 'bodywings', 'title' => __( 'BODYWINGS', 'bodywings' ), 'icon' => null ) ),
			$categories
		);
	} );
}

add_action( 'init', 'bodywings_register_shared_block_assets', 5 );

/**
 * Geteilte Editor-/Frontend-Assets EINMAL registrieren (nicht pro Block),
 * damit block.json in jedem Block-Ordner per Handle statt per file:-Pfad
 * darauf verweisen kann — WordPress lädt einen Handle nur dann aus, wenn
 * mindestens einer der referenzierenden Blöcke tatsächlich auf der Seite
 * vorkommt (§26: keine unnötigen Assets), auch wenn zwölf Blöcke denselben
 * Handle referenzieren.
 */
function bodywings_register_shared_block_assets(): void {
	$css_dir = BODYWINGS_DIR . '/assets/css';
	$js_dir  = BODYWINGS_DIR . '/assets/js';

	wp_register_script(
		'bodywings-blocks-shared',
		BODYWINGS_URI . '/assets/js/blocks/shared-controls.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data', 'wp-core-data' ),
		bodywings_asset_version( $js_dir . '/blocks/shared-controls.js' ),
		true
	);

	wp_register_script(
		'bodywings-block-slider',
		BODYWINGS_URI . '/assets/js/block-slider.js',
		array(),
		bodywings_asset_version( $js_dir . '/block-slider.js' ),
		true
	);

	wp_register_script(
		'bodywings-block-before-after',
		BODYWINGS_URI . '/assets/js/block-before-after.js',
		array(),
		bodywings_asset_version( $js_dir . '/block-before-after.js' ),
		true
	);

	// Dieselben Handles wie inc/core/enqueue.php (dort für die
	// Produktkarten-Scroll-Reveals registriert) — doppelte Registrierung
	// mit identischen Argumenten ist unschädlich, WordPress enqueued den
	// Handle so oder so nur einmal. Hier zusätzlich registriert, damit das
	// Kategorien-Akkordeon GSAP/ScrollTrigger auch dann bekommt, wenn es
	// auf einer Seite ohne Produktkarten-Kontext steht.
	wp_register_script(
		'bodywings-gsap',
		BODYWINGS_URI . '/assets/js/vendor/gsap/gsap.min.js',
		array(),
		'3.15.0',
		true
	);

	wp_register_script(
		'bodywings-gsap-scrolltrigger',
		BODYWINGS_URI . '/assets/js/vendor/gsap/ScrollTrigger.min.js',
		array( 'bodywings-gsap' ),
		'3.15.0',
		true
	);

	wp_register_script(
		'bodywings-block-category-accordion',
		BODYWINGS_URI . '/assets/js/block-category-accordion.js',
		array( 'bodywings-gsap-scrolltrigger' ),
		bodywings_asset_version( $js_dir . '/block-category-accordion.js' ),
		true
	);

	wp_register_script(
		'bodywings-block-scroll-reveal',
		BODYWINGS_URI . '/assets/js/block-scroll-reveal.js',
		array( 'bodywings-gsap-scrolltrigger' ),
		bodywings_asset_version( $js_dir . '/block-scroll-reveal.js' ),
		true
	);

	wp_register_style(
		'bodywings-blocks-style',
		BODYWINGS_URI . '/assets/css/components/blocks.css',
		array( 'bodywings-base' ),
		bodywings_asset_version( $css_dir . '/components/blocks.css' )
	);

	wp_register_style(
		'bodywings-blocks-editor-style',
		BODYWINGS_URI . '/assets/css/components/blocks-editor.css',
		array( 'wp-edit-blocks', 'bodywings-blocks-style' ),
		bodywings_asset_version( $css_dir . '/components/blocks-editor.css' )
	);
}

add_action( 'init', 'bodywings_register_blocks', 10 );

/**
 * Registriert jeden Block-Ordner. edit.js wird hier (statt über block.json
 * "file:") explizit per wp_register_script() mit den nötigen wp-*-
 * Abhängigkeiten registriert, BEVOR register_block_type() das block.json
 * liest — ohne Build-Step gibt es keine automatisch generierte
 * .asset.php-Abhängigkeitsliste, block.json referenziert deshalb den
 * Handle "bw-block-{slug}-edit" als reinen String statt eines Dateipfads.
 */
function bodywings_register_blocks(): void {
	$js_dir = BODYWINGS_DIR . '/assets/js';

	foreach ( glob( BODYWINGS_DIR . '/blocks/*/block.json' ) ?: array() as $block_json ) {
		$dir  = dirname( $block_json );
		$slug = basename( $dir );

		$edit_js = $dir . '/edit.js';
		if ( file_exists( $edit_js ) ) {
			wp_register_script(
				'bw-block-' . $slug . '-edit',
				BODYWINGS_URI . '/blocks/' . $slug . '/edit.js',
				array( 'bodywings-blocks-shared' ),
				bodywings_asset_version( $edit_js ),
				true
			);
		}

		register_block_type( $dir );
	}
}

/**
 * §2.2/§5: gemeinsame Hintergrundfarben-Logik für alle Content-Elemente,
 * hier für Blöcke wiederverwendet statt pro Block neu gebaut. Erwartet ein
 * "bgColor"-Attribut mit einem der vier Palettenwerte oder "" (kein
 * Hintergrund) in jedem Block, das diesen Helfer nutzt.
 *
 * @return string z.B. ' data-bw-bg="green"' oder '' wenn keine Farbe gewählt ist.
 */
function bodywings_block_bg_attr( array $attributes ): string {
	$color = $attributes['bgColor'] ?? '';

	if ( ! in_array( $color, array( 'green', 'beige', 'terrakotta', 'grey' ), true ) ) {
		return '';
	}

	return ' data-bw-bg="' . esc_attr( $color ) . '"';
}
