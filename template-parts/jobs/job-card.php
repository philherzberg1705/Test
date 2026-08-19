<?php
/**
 * Job-Karte für das Stellen-Archiv. Bewusst schlank (analog §8 Produktkarte):
 * Titel, Standort/Art als Badges, optional Gehalt, Link zur Detailseite.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_job_id       = get_the_ID();
$bw_job_types    = bodywings_get_job_terms( $bw_job_id, 'bw_job_type' );
$bw_job_location = bodywings_get_job_location_names( $bw_job_id );
$bw_job_salary   = bodywings_get_job_salary_text( $bw_job_id );
?>
<li class="bw-job-card">
	<a class="bw-job-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="bw-job-card__media">
				<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => get_the_title() ) ); ?>
			</div>
		<?php endif; ?>

		<div class="bw-job-card__body">
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

			<h2 class="bw-job-card__title"><?php the_title(); ?></h2>

			<?php if ( $bw_job_salary ) : ?>
				<p class="bw-job-card__salary"><?php echo esc_html( $bw_job_salary ); ?></p>
			<?php endif; ?>

			<span class="bw-job-card__cta">
				<?php esc_html_e( 'Details ansehen', 'bodywings' ); ?>
			</span>
		</div>
	</a>
</li>
