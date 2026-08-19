<?php
/**
 * Wendet die GET-Filter (Beschäftigungsart/Standort) des Job-Archivs auf
 * die Haupt-Query an — selbes Prinzip wie die WooCommerce-Filter-Query
 * (inc/woocommerce/filter-query.php), nur ohne AJAX-Gegenstück (§35: hier
 * bewusst nicht nötig, siehe archive-bw_job.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'pre_get_posts', 'bodywings_apply_job_archive_filters' );

function bodywings_apply_job_archive_filters( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'bw_job' ) ) {
		return;
	}

	$tax_query = array();

	if ( ! empty( $_GET['job_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tax_query[] = array(
			'taxonomy' => 'bw_job_type',
			'field'    => 'slug',
			'terms'    => sanitize_title( wp_unslash( $_GET['job_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		);
	}

	if ( ! empty( $_GET['job_location'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tax_query[] = array(
			'taxonomy' => 'bw_job_location',
			'field'    => 'slug',
			'terms'    => sanitize_title( wp_unslash( $_GET['job_location'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		);
	}

	if ( $tax_query ) {
		$query->set( 'tax_query', array_merge( array( 'relation' => 'AND' ), $tax_query ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'DESC' );
}
