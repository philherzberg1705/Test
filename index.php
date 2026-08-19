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
