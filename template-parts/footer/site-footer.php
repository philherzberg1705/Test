<?php
/**
 * Site-Footer-Platzhalter.
 * Wird in Task 5 (Footer: Newsletter-Modul) vollständig ausgebaut.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="bw-site-footer" data-bw-bg="green">
	<div class="bw-container bw-site-footer__inner">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
	</div>
</footer>
