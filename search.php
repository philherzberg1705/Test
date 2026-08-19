<?php
/**
 * Volle Suchergebnisseite (§14 "Enter → vollständige Suchergebnisseite").
 * Produkte nutzen dieselbe Produktkarte wie Shop/Filter (§33), andere
 * Inhaltstypen fallen minimal auf Titel+Link zurück.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="bw-container bw-shop">
	<header class="bw-shop__header">
		<h1 class="bw-shop__title">
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( 'Suchergebnisse für „%s“', 'bodywings' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<ul class="bw-product-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( 'product' === get_post_type() && bodywings_is_woocommerce_active() ) {
					$bw_product = wc_get_product( get_the_ID() );

					if ( $bw_product ) {
						bodywings_render_product_card( $bw_product );
						continue;
					}
				}
				?>
				<li class="bw-search-result">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
				<?php
			endwhile;
			?>
		</ul>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Keine Ergebnisse gefunden.', 'bodywings' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
