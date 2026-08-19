<?php
/**
 * Detailseite einer Stellenausschreibung (§12-Pendant für Jobs). Alle
 * strukturierten Felder werden hier UND in den JobPosting-structured-data
 * (inc/jobs/structured-data.php) aus denselben Helfern gespeist (§33),
 * damit sichtbare Seite und Schema nie auseinanderlaufen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$bw_job_id       = get_the_ID();
	$bw_job_types    = bodywings_get_job_terms( $bw_job_id, 'bw_job_type' );
	$bw_job_location = bodywings_get_job_location_names( $bw_job_id );
	$bw_job_salary   = bodywings_get_job_salary_text( $bw_job_id );
	$bw_job_start    = bodywings_get_job_start_text( $bw_job_id );
	$bw_job_deadline = bodywings_get_job_deadline_text( $bw_job_id );
	$bw_job_is_open  = bodywings_job_is_open( $bw_job_id );
	?>
	<div class="bw-container bw-job-single">
		<nav class="bw-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bodywings' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Start', 'bodywings' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'bw_job' ) ); ?>"><?php esc_html_e( 'Stellenangebote', 'bodywings' ); ?></a>
			<span aria-hidden="true"> / </span>
			<span><?php the_title(); ?></span>
		</nav>

		<header class="bw-job-single__header">
			<?php if ( $bw_job_types || $bw_job_location ) : ?>
				<div class="bw-job-card__badges">
					<?php foreach ( $bw_job_types as $type ) : ?>
						<span class="bw-badge-pill"><?php echo esc_html( $type->name ); ?></span>
					<?php endforeach; ?>
					<?php if ( $bw_job_location ) : ?>
						<span class="bw-badge-pill bw-badge-pill--outline"><?php echo esc_html( $bw_job_location ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<h1 class="bw-job-single__title"><?php the_title(); ?></h1>

			<dl class="bw-job-single__meta">
				<?php if ( $bw_job_salary ) : ?>
					<div><dt><?php esc_html_e( 'Gehalt', 'bodywings' ); ?></dt><dd><?php echo esc_html( $bw_job_salary ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $bw_job_start ) : ?>
					<div><dt><?php esc_html_e( 'Start', 'bodywings' ); ?></dt><dd><?php echo esc_html( $bw_job_start ); ?></dd></div>
				<?php endif; ?>
				<?php if ( $bw_job_deadline ) : ?>
					<div><dt><?php esc_html_e( 'Bewerbungsfrist', 'bodywings' ); ?></dt><dd><?php echo esc_html( $bw_job_deadline ); ?></dd></div>
				<?php endif; ?>
			</dl>

			<?php if ( $bw_job_is_open ) : ?>
				<a class="bw-btn bw-job-single__apply-cta" href="#bewerben"><?php esc_html_e( 'Jetzt bewerben', 'bodywings' ); ?></a>
			<?php endif; ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="bw-job-single__media">
				<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'decoding' => 'async', 'alt' => get_the_title() ) ); ?>
			</div>
		<?php endif; ?>

		<div class="bw-job-single__layout">
			<div class="bw-job-single__content">
				<div class="bw-entry-content"><?php the_content(); ?></div>

				<?php
				$bw_job_lists = array(
					'_bw_job_tasks'        => __( 'Aufgaben', 'bodywings' ),
					'_bw_job_requirements' => __( 'Anforderungen', 'bodywings' ),
					'_bw_job_benefits'     => __( 'Benefits', 'bodywings' ),
				);

				foreach ( $bw_job_lists as $meta_key => $label ) :
					$items = bodywings_get_job_list_field( $bw_job_id, $meta_key );
					if ( ! $items ) {
						continue;
					}
					?>
					<section class="bw-job-single__list-section">
						<h2><?php echo esc_html( $label ); ?></h2>
						<ul class="bw-job-single__list">
							<?php foreach ( $items as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>
			</div>

			<aside class="bw-job-single__sidebar">
				<?php if ( $bw_job_is_open ) : ?>
					<?php get_template_part( 'template-parts/jobs/application-form' ); ?>
				<?php else : ?>
					<div class="bw-job-single__closed">
						<p><?php esc_html_e( 'Die Bewerbungsfrist für diese Stelle ist leider abgelaufen.', 'bodywings' ); ?></p>
						<a class="bw-btn bw-btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'bw_job' ) ); ?>">
							<?php esc_html_e( 'Weitere Stellenangebote ansehen', 'bodywings' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</aside>
		</div>
	</div>
	<?php
endwhile;

get_footer();
