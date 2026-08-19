<?php
/**
 * Bild-Helfer für Produktkarten/-seite: Transparenz-Erkennung (§11) und
 * zweites Galeriebild (§10). Ergebnis wird dauerhaft in Postmeta gecacht —
 * ein Bild wechselt seine Transparenz nach dem Upload nicht mehr, erneute
 * Berechnung bei jedem Seitenaufruf wäre unnötige Serverlast (§26).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_TRANSPARENCY_META_KEY = '_bodywings_has_transparency';

function bodywings_attachment_has_transparency( int $attachment_id ): bool {
	if ( ! $attachment_id ) {
		return false;
	}

	$cached = get_post_meta( $attachment_id, BODYWINGS_TRANSPARENCY_META_KEY, true );

	if ( '' !== $cached ) {
		return (bool) $cached;
	}

	$has_alpha = bodywings_detect_attachment_transparency( $attachment_id );
	update_post_meta( $attachment_id, BODYWINGS_TRANSPARENCY_META_KEY, $has_alpha ? 1 : 0 );

	return $has_alpha;
}

function bodywings_detect_attachment_transparency( int $attachment_id ): bool {
	// JPEG kennt keinen Alphakanal — Prüfung überspringen (§26 Performance).
	if ( ! in_array( get_post_mime_type( $attachment_id ), array( 'image/png', 'image/webp', 'image/gif' ), true ) ) {
		return false;
	}

	if ( ! function_exists( 'imagecreatefromstring' ) ) {
		return false;
	}

	$file = get_attached_file( $attachment_id );

	if ( ! $file || ! file_exists( $file ) ) {
		return false;
	}

	$data = @file_get_contents( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

	if ( ! $data ) {
		return false;
	}

	$image = @imagecreatefromstring( $data ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

	if ( ! $image ) {
		return false;
	}

	imagesavealpha( $image, true );

	$width  = imagesx( $image );
	$height = imagesy( $image );
	$step_x = max( 1, (int) ( $width / 40 ) );
	$step_y = max( 1, (int) ( $height / 40 ) );
	$found  = false;

	for ( $x = 0; $x < $width && ! $found; $x += $step_x ) {
		for ( $y = 0; $y < $height && ! $found; $y += $step_y ) {
			$rgba  = imagecolorat( $image, $x, $y );
			$alpha = ( $rgba >> 24 ) & 0x7f;

			if ( $alpha > 0 ) {
				$found = true;
			}
		}
	}

	imagedestroy( $image );

	return $found;
}

/**
 * Erste Galerie-Bild-ID als "zweites Produktbild" für den Hover-Wechsel
 * (§10). Kein zweites Bild vorhanden → 0.
 */
function bodywings_get_product_secondary_image_id( WC_Product $product ): int {
	$gallery_ids = $product->get_gallery_image_ids();

	return $gallery_ids ? (int) $gallery_ids[0] : 0;
}
