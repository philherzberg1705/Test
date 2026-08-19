<?php
/**
 * Template Name: BODYWINGS Wunschliste
 *
 * §7: Wunschlisten-Seite. Rendert leer + lädt die Produkte per AJAX —
 * für eingeloggte Nutzer:innen aus User-Meta (serverseitig vorbereitet),
 * für Gäste aus localStorage (siehe assets/js/ajax/wishlist.js). Damit
 * ist derselbe Rendering-Pfad für beide Fälle zuständig (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bw_initial_ids = is_user_logged_in() && function_exists( 'bodywings_get_user_wishlist_ids' )
	? bodywings_get_user_wishlist_ids()
	: array();
?>

<div class="bw-container bw-shop bw-wishlist-page">
	<header class="bw-shop__header">
		<h1 class="bw-shop__title"><?php the_title(); ?></h1>
	</header>

	<ul
		class="bw-product-grid"
		data-bw-wishlist-grid
		data-bw-wishlist-initial-ids='<?php echo esc_attr( wp_json_encode( $bw_initial_ids ) ); ?>'
	>
		<li class="bw-wishlist-page__loading bw-text-small bw-color-muted">
			<?php esc_html_e( 'Wird geladen …', 'bodywings' ); ?>
		</li>
	</ul>

	<p class="bw-wishlist-page__empty bw-color-muted" data-bw-wishlist-empty hidden>
		<?php esc_html_e( 'Deine Wunschliste ist leer.', 'bodywings' ); ?>
	</p>
</div>

<?php
get_footer();
