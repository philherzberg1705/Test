<?php
/**
 * Archiv für Stellenausschreibungen. Einfache serverseitige GET-Filter
 * (Beschäftigungsart/Standort) statt eines eigenen AJAX-Systems (§13 gilt
 * für den Produktfilter) — Stellenlisten ändern sich selten, ein voller
 * Seitenaufruf pro Filterwechsel ist hier bewusst die einfachere,
 * genauso schnelle und zusätzlich crawlbare Lösung (§35/§26).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bw_selected_type     = isset( $_GET['job_type'] ) ? sanitize_title( wp_unslash( $_GET['job_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$bw_selected_location = isset( $_GET['job_location'] ) ? sanitize_title( wp_unslash( $_GET['job_location'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$bw_job_types     = get_terms( array( 'taxonomy' => 'bw_job_type', 'hide_empty' => true ) );
$bw_job_locations = get_terms( array( 'taxonomy' => 'bw_job_location', 'hide_empty' => true ) );
?>

<div class="bw-container bw-shop bw-jobs-archive">
	<nav class="bw-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bodywings' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Start', 'bodywings' ); ?></a>
		<span aria-hidden="true"> / </span>
		<span><?php esc_html_e( 'Stellenangebote', 'bodywings' ); ?></span>
	</nav>

	<header class="bw-shop__header">
		<h1 class="bw-shop__title"><?php post_type_archive_title(); ?></h1>
	</header>

	<?php if ( ! is_wp_error( $bw_job_types ) && ! is_wp_error( $bw_job_locations ) && ( $bw_job_types || $bw_job_locations ) ) : ?>
		<form class="bw-jobs-filter" method="get">
			<?php if ( $bw_job_types ) : ?>
				<label class="bw-visually-hidden" for="bw-job-type"><?php esc_html_e( 'Beschäftigungsart', 'bodywings' ); ?></label>
				<select id="bw-job-type" name="job_type" class="bw-input" onchange="this.form.submit()">
					<option value=""><?php esc_html_e( 'Alle Beschäftigungsarten', 'bodywings' ); ?></option>
					<?php foreach ( $bw_job_types as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $bw_selected_type, $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>

			<?php if ( $bw_job_locations ) : ?>
				<label class="bw-visually-hidden" for="bw-job-location"><?php esc_html_e( 'Standort', 'bodywings' ); ?></label>
				<select id="bw-job-location" name="job_location" class="bw-input" onchange="this.form.submit()">
					<option value=""><?php esc_html_e( 'Alle Standorte', 'bodywings' ); ?></option>
					<?php foreach ( $bw_job_locations as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $bw_selected_location, $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>

			<noscript><button type="submit" class="bw-btn bw-btn--outline"><?php esc_html_e( 'Filtern', 'bodywings' ); ?></button></noscript>
		</form>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<ul class="bw-job-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/jobs/job-card' );
			endwhile;
			?>
		</ul>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Aktuell sind keine Stellenausschreibungen mit dieser Auswahl verfügbar.', 'bodywings' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
