<?php
/**
 * Site-Footer: Newsletter-Modul (§19) + Footer-Navigation + Copyright.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="bw-site-footer" data-bw-bg="green">
	<div class="bw-container bw-site-footer__inner">
		<div class="bw-site-footer__top">
			<div class="bw-newsletter">
				<h2 class="bw-newsletter__title"><?php esc_html_e( 'Newsletter', 'bodywings' ); ?></h2>
				<?php get_template_part( 'template-parts/newsletter-form' ); ?>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="bw-site-footer__nav" aria-label="<?php esc_attr_e( 'Footer-Navigation', 'bodywings' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'bw-site-footer__nav-list',
						'fallback_cb'    => false,
					) );
					?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="bw-site-footer__bottom">
			<p class="bw-text-small">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
		</div>
	</div>
</footer>
