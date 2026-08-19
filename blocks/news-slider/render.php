<?php
/**
 * News-Slider: Beiträge horizontal, per Scroll/Buttons weiterblätterbar
 * (dieselbe Slider-Engine wie Produkt-/Team-/Testimonial-Slider, §33).
 * Immer mindestens 3 Karten gleichzeitig sichtbar ab Desktop-Breite (siehe
 * .bw-news-slider__item in blocks.css) — auf schmalen Screens sind es
 * bewusst weniger, ein einzelnes Element ließe sich sonst nicht mehr
 * lesbar darstellen (§24: sinnvoll pro Bildschirmgröße, keine reine
 * Verkleinerung). Letzte Karte verlinkt immer auf das Beitrags-Archiv.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_heading = $attributes['heading'] ?? '';
$bw_source  = $attributes['source'] ?? 'recent';
$bw_count   = max( 3, min( 12, (int) ( $attributes['count'] ?? 6 ) ) );

$bw_query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => $bw_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'ignore_sticky_posts' => true,
);

switch ( $bw_source ) {
	case 'category':
		$bw_category_id = (int) ( $attributes['categoryId'] ?? 0 );
		if ( ! $bw_category_id ) {
			return;
		}
		$bw_query_args['cat'] = $bw_category_id;
		break;

	case 'manual':
		$bw_post_ids = array_filter( array_map( 'absint', (array) ( $attributes['postIds'] ?? array() ) ) );
		if ( ! $bw_post_ids ) {
			return;
		}
		$bw_query_args['post__in']       = $bw_post_ids;
		$bw_query_args['orderby']        = 'post__in';
		$bw_query_args['posts_per_page'] = count( $bw_post_ids );
		break;

	case 'recent':
	default:
		break;
}

$bw_query = new WP_Query( $bw_query_args );

if ( ! $bw_query->have_posts() ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-block bw-news-slider' ) ); ?><?php echo bodywings_block_bg_attr( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="bw-block__inner">
		<?php if ( $bw_heading ) : ?>
			<h2 class="bw-news-slider__heading"><?php echo wp_kses_post( $bw_heading ); ?></h2>
		<?php endif; ?>

		<div class="bw-slider" data-bw-slider>
			<ul class="bw-slider__track bw-news-slider__track" data-bw-slider-track tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Neuigkeiten, horizontal scrollbar', 'bodywings' ); ?>">
				<?php
				while ( $bw_query->have_posts() ) :
					$bw_query->the_post();
					?>
					<li class="bw-news-slider__item">
						<a class="bw-news-slider__link" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<span class="bw-news-slider__media">
									<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => get_the_title() ) ); ?>
								</span>
							<?php endif; ?>

							<span class="bw-news-slider__meta">
								<?php
								$bw_categories = get_the_category();
								if ( $bw_categories ) :
									?>
									<span class="bw-badge-pill"><?php echo esc_html( $bw_categories[0]->name ); ?></span>
								<?php endif; ?>
								<time class="bw-news-slider__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							</span>

							<span class="bw-news-slider__title"><?php the_title(); ?></span>
							<span class="bw-news-slider__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></span>
						</a>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>

				<li class="bw-news-slider__item bw-news-slider__item--all">
					<a class="bw-news-slider__link bw-news-slider__link--all" href="<?php echo esc_url( bodywings_get_blog_archive_url() ); ?>">
						<?php echo bodywings_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="bw-news-slider__title"><?php esc_html_e( 'Alle Neuigkeiten ansehen', 'bodywings' ); ?></span>
					</a>
				</li>
			</ul>

			<div class="bw-slider__nav">
				<button type="button" data-bw-slider-prev aria-label="<?php esc_attr_e( 'Zurück', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button type="button" data-bw-slider-next aria-label="<?php esc_attr_e( 'Weiter', 'bodywings' ); ?>"><?php echo bodywings_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</div>
		</div>
	</div>
</div>
