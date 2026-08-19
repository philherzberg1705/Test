<?php
/**
 * Stellenausschreibungen: Custom Post Type + Taxonomien (Beschäftigungsart,
 * Standort). Eigener CPT statt Seiten/Beiträge (§31-Pendant für Nicht-
 * WooCommerce-Inhalte): braucht eigene, strukturierte Meta-Felder und ein
 * eigenes Archiv/Filterverhalten, das mit normalen Posts nicht sauber
 * abzubilden wäre.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'bodywings_register_job_post_type' );

function bodywings_register_job_post_type(): void {
	register_post_type( 'bw_job', array(
		'labels'       => array(
			'name'                  => __( 'Stellenausschreibungen', 'bodywings' ),
			'singular_name'         => __( 'Stellenausschreibung', 'bodywings' ),
			'add_new_item'          => __( 'Neue Stellenausschreibung', 'bodywings' ),
			'edit_item'             => __( 'Stellenausschreibung bearbeiten', 'bodywings' ),
			'new_item'              => __( 'Neue Stellenausschreibung', 'bodywings' ),
			'view_item'             => __( 'Stellenausschreibung ansehen', 'bodywings' ),
			'search_items'          => __( 'Stellenausschreibungen durchsuchen', 'bodywings' ),
			'not_found'             => __( 'Keine Stellenausschreibungen gefunden.', 'bodywings' ),
			'all_items'             => __( 'Alle Stellenausschreibungen', 'bodywings' ),
			'menu_name'             => __( 'Jobs', 'bodywings' ),
			'featured_image'        => __( 'Titelbild', 'bodywings' ),
		),
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
		'menu_position' => 26,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'has_archive'  => 'stellenangebote',
		'rewrite'      => array( 'slug' => 'stellenangebote/stelle', 'with_front' => false ),
		'show_in_menu' => true,
	) );

	register_taxonomy( 'bw_job_type', 'bw_job', array(
		'labels'            => array(
			'name'          => __( 'Beschäftigungsarten', 'bodywings' ),
			'singular_name' => __( 'Beschäftigungsart', 'bodywings' ),
			'add_new_item'  => __( 'Neue Beschäftigungsart', 'bodywings' ),
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'stellenangebote/art' ),
	) );

	register_taxonomy( 'bw_job_location', 'bw_job', array(
		'labels'            => array(
			'name'          => __( 'Standorte', 'bodywings' ),
			'singular_name' => __( 'Standort', 'bodywings' ),
			'add_new_item'  => __( 'Neuer Standort', 'bodywings' ),
		),
		'hierarchical'      => true,
		'public'            => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'stellenangebote/standort' ),
	) );
}

/**
 * schema.org JobPosting kennt nur einen festen Enum-Satz für employmentType
 * (https://schema.org/employmentType) — bildet die frei pflegbaren
 * bw_job_type-Begriffe per Slug darauf ab. Ohne Treffer bleibt das Feld in
 * den structured data einfach weg statt einen falschen Wert zu raten.
 *
 * @return string|null
 */
function bodywings_job_type_schema_enum( string $term_slug ) {
	$map = apply_filters( 'bodywings_job_employment_type_schema_map', array(
		'vollzeit'    => 'FULL_TIME',
		'teilzeit'    => 'PART_TIME',
		'werkstudent' => 'PART_TIME',
		'praktikum'   => 'INTERN',
		'ausbildung'  => 'INTERN',
		'freelance'   => 'CONTRACTOR',
		'minijob'     => 'OTHER',
	) );

	return $map[ $term_slug ] ?? null;
}

/**
 * Default-Taxonomiebegriffe bei Theme-Aktivierung — Redakteur:innen sollen
 * sofort eine Stelle anlegen können, ohne zuerst Taxonomien zu pflegen
 * (§29 "gute Defaults"). Bestehende Begriffe werden nicht angerührt.
 */
add_action( 'after_switch_theme', 'bodywings_seed_job_taxonomies' );

function bodywings_seed_job_taxonomies(): void {
	bodywings_register_job_post_type();

	$types = array(
		'vollzeit'    => __( 'Vollzeit', 'bodywings' ),
		'teilzeit'    => __( 'Teilzeit', 'bodywings' ),
		'werkstudent' => __( 'Werkstudent', 'bodywings' ),
		'praktikum'   => __( 'Praktikum', 'bodywings' ),
		'ausbildung'  => __( 'Ausbildung', 'bodywings' ),
	);

	foreach ( $types as $slug => $name ) {
		if ( ! term_exists( $slug, 'bw_job_type' ) ) {
			wp_insert_term( $name, 'bw_job_type', array( 'slug' => $slug ) );
		}
	}

	flush_rewrite_rules();
}
