<?php
/**
 * Standard-Seitentemplate.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'bw-container' ); ?> id="post-<?php the_ID(); ?>">
		<h1><?php the_title(); ?></h1>
		<div class="bw-entry-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
