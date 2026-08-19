<?php
/**
 * Fallback-Template (WordPress verlangt ein index.php im Theme-Root).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="bw-container">
	<?php
	/*
	 * §27: genau ein H1 pro Seite, auch auf diesem Fallback-Template
	 * (dient sowohl als Blog-Index als auch als Fallback für Kategorie-/
	 * Tag-/Autor-/Datums-Archive ohne eigenes archive.php).
	 */
	?>
	<header class="bw-shop__header">
		<h1 class="bw-shop__title">
			<?php
			if ( is_archive() ) {
				the_archive_title();
			} elseif ( is_home() && ! is_front_page() ) {
				single_post_title();
			} else {
				esc_html_e( 'Neuigkeiten', 'bodywings' );
			}
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="bw-entry-content"><?php the_excerpt(); ?></div>
			</article>
			<?php
		endwhile;

		the_posts_pagination();
	else :
		?>
		<p><?php esc_html_e( 'Keine Inhalte gefunden.', 'bodywings' ); ?></p>
		<?php
	endif;
	?>
</div>

<?php
get_footer();
