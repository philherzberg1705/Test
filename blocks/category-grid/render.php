<?php
/**
 * Kategorien-Liste.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! bodywings_is_woocommerce_active() ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<p class="bw-block bw-block__admin-notice">' . esc_html__( 'Kategorien-Liste: WooCommerce ist nicht aktiv.', 'bodywings' ) . '</p>';
	}
	return;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_ids     = array_filter( array_map( 'absint', (array) ( $attributes['categoryIds'] ?? array() ) ) );
$bw_count   = max( 1, min( 12, (int) ( $attributes['count'] ?? 6 ) ) );

$bw_term_args = array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => $bw_ids ? '' : 0,
	'number'     => $bw_count,
);

if ( $bw_ids ) {
	$bw_term_args['include'] = $bw_ids;
	$bw_term_args['orderby'] = 'include';
}

$bw_terms = get_terms( $bw_term_args );

if ( is_wp_error( $bw_terms ) || ! $bw_terms ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-category-grid' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-category-grid__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<ul class="bw-category-grid__list">
			<?php foreach ( $bw_terms as $bw_term ) : ?>
				<?php
				$bw_thumb_id = get_term_meta( $bw_term->term_id, 'thumbnail_id', true );
				$bw_image    = $bw_thumb_id ? wp_get_attachment_image_url( $bw_thumb_id, 'bodywings-product-card' ) : '';
				?>
				<li class="bw-category-grid__item">
					<a class="bw-category-grid__link" href="<?php echo esc_url( get_term_link( $bw_term ) ); ?>">
						<span class="bw-category-grid__media">
							<?php if ( $bw_image ) : ?>
								<img src="<?php echo esc_url( $bw_image ); ?>" alt="<?php echo esc_attr( $bw_term->name ); ?>" loading="lazy" decoding="async" />
							<?php endif; ?>
						</span>
						<span class="bw-category-grid__name"><?php echo esc_html( $bw_term->name ); ?></span>
						<span class="bw-category-grid__count">
							<?php
							printf(
								/* translators: %d: number of products in this category */
								esc_html( _n( '%d Produkt', '%d Produkte', $bw_term->count, 'bodywings' ) ),
								(int) $bw_term->count
							);
							?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
