<?php
/**
 * JobPosting-Structured-Data (schema.org, §27) für Stellenausschreibungen —
 * eigenständig neben WooCommerce-Produktdaten, da Google for Jobs eigene
 * Pflicht-/Empfehlungsfelder verlangt (title, description, datePosted,
 * hiringOrganization, jobLocation/jobLocationType, validThrough, ...).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'bodywings_output_job_structured_data' );

function bodywings_output_job_structured_data(): void {
	if ( ! is_singular( 'bw_job' ) ) {
		return;
	}

	$job_id = get_the_ID();
	$data   = bodywings_build_job_schema( $job_id );

	if ( ! $data ) {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() escaped bereits alle Werte.
}

/**
 * @return array<string,mixed>|null
 */
function bodywings_build_job_schema( int $job_id ): ?array {
	$post = get_post( $job_id );

	if ( ! $post ) {
		return null;
	}

	$org_name = bodywings_get_job_org_name();

	$schema = array(
		'@context'          => 'https://schema.org/',
		'@type'             => 'JobPosting',
		'title'             => get_the_title( $job_id ),
		'description'       => wp_kses_post( apply_filters( 'the_content', $post->post_content ) ),
		'datePosted'        => get_the_date( 'c', $job_id ),
		'identifier'        => array(
			'@type' => 'PropertyValue',
			'name'  => $org_name,
			'value' => (string) $job_id,
		),
		'hiringOrganization' => array_filter( array(
			'@type' => 'Organization',
			'name'  => $org_name,
			'sameAs' => home_url( '/' ),
			'logo'  => bodywings_get_job_org_logo_url(),
		) ),
	);

	$deadline = get_post_meta( $job_id, '_bw_job_application_deadline', true );
	if ( $deadline ) {
		$schema['validThrough'] = gmdate( 'c', strtotime( $deadline . ' 23:59:59' ) );
	}

	$type_terms = bodywings_get_job_terms( $job_id, 'bw_job_type' );
	$employment_types = array_values( array_filter( array_map(
		static fn( WP_Term $term ) => bodywings_job_type_schema_enum( $term->slug ),
		$type_terms
	) ) );
	if ( $employment_types ) {
		// schema.org erlaubt ein Array, wenn mehrere Beschäftigungsarten
		// zutreffen (z.B. "Vollzeit oder Teilzeit möglich").
		$schema['employmentType'] = 1 === count( $employment_types ) ? $employment_types[0] : $employment_types;
	}

	$country = apply_filters( 'bodywings_job_default_country', 'DE' );
	$location_terms = bodywings_get_job_terms( $job_id, 'bw_job_location' );

	if ( $location_terms ) {
		$schema['jobLocation'] = array_map(
			static fn( WP_Term $term ) => array(
				'@type'   => 'Place',
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => $term->name,
					'addressCountry'  => $country,
				),
			),
			$location_terms
		);
	}

	if ( bodywings_job_is_remote( $job_id ) ) {
		$schema['jobLocationType'] = 'TELECOMMUTE';
		$schema['applicantLocationRequirements'] = array(
			'@type' => 'Country',
			'name'  => $country,
		);
	}

	$salary = bodywings_get_job_salary( $job_id );
	if ( $salary ) {
		$schema['baseSalary'] = array(
			'@type'    => 'MonetaryAmount',
			'currency' => 'EUR',
			'value'    => array(
				'@type'    => 'QuantitativeValue',
				'minValue' => $salary['min'],
				'maxValue' => $salary['max'],
				'unitText' => $salary['unit'],
			),
		);
	}

	return $schema;
}

function bodywings_get_site_logo_url(): string {
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		$src = wp_get_attachment_image_src( $custom_logo_id, 'medium' );
		if ( $src ) {
			return $src[0];
		}
	}

	$site_icon = get_site_icon_url();

	return $site_icon ? $site_icon : '';
}
