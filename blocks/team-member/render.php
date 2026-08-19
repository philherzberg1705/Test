<?php
/**
 * Ein Team-Mitglied innerhalb des Team-Sliders.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_name  = $attributes['name'] ?? '';
$bw_role  = $attributes['role'] ?? '';
$bw_photo = $attributes['photoUrl'] ?? '';
$bw_link  = $attributes['linkUrl'] ?? '';

if ( '' === trim( wp_strip_all_tags( $bw_name ) ) ) {
	return;
}
?>
<li <?php echo get_block_wrapper_attributes( array( 'class' => 'bw-team-slider__item' ) ); ?>>
	<?php if ( $bw_photo ) : ?>
		<div class="bw-team-slider__photo">
			<img src="<?php echo esc_url( $bw_photo ); ?>" alt="<?php echo esc_attr( $bw_name ); ?>" loading="lazy" decoding="async" />
		</div>
	<?php endif; ?>

	<p class="bw-team-slider__name"><?php echo esc_html( $bw_name ); ?></p>
	<?php if ( $bw_role ) : ?>
		<p class="bw-team-slider__role"><?php echo esc_html( $bw_role ); ?></p>
	<?php endif; ?>
	<?php if ( $bw_link ) : ?>
		<a class="bw-team-slider__link" href="<?php echo esc_url( $bw_link ); ?>"><?php esc_html_e( 'Profil ansehen', 'bodywings' ); ?></a>
	<?php endif; ?>
</li>
