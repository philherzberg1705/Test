<?php
/**
 * Bewerbungen (§7-Pendant für Jobs): eigener, NICHT-öffentlicher Post-Type
 * zur Ablage eingegangener Bewerbungen. Bewusst kein REST/kein Frontend-
 * Zugriff — Bewerberdaten sind personenbezogene Daten (§32), nur im
 * Backend mit passender Capability einsehbar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'bodywings_register_job_application_post_type' );

function bodywings_register_job_application_post_type(): void {
	register_post_type( 'bw_job_application', array(
		'labels'        => array(
			'name'          => __( 'Bewerbungen', 'bodywings' ),
			'singular_name' => __( 'Bewerbung', 'bodywings' ),
			'all_items'     => __( 'Bewerbungen', 'bodywings' ),
			'search_items'  => __( 'Bewerbungen durchsuchen', 'bodywings' ),
			'not_found'     => __( 'Keine Bewerbungen gefunden.', 'bodywings' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => false,
		'show_in_menu'  => 'edit.php?post_type=bw_job',
		'supports'      => array( 'title' ),
		'capability_type' => 'page',
		'map_meta_cap'  => true,
	) );
}

add_filter( 'manage_bw_job_application_posts_columns', 'bodywings_job_application_columns' );

function bodywings_job_application_columns( array $columns ): array {
	$new = array(
		'cb'          => $columns['cb'],
		'applicant'   => __( 'Bewerber:in', 'bodywings' ),
		'job'         => __( 'Stelle', 'bodywings' ),
		'status'      => __( 'Status', 'bodywings' ),
		'cv'          => __( 'Lebenslauf', 'bodywings' ),
		'date'        => $columns['date'],
	);

	return $new;
}

add_action( 'manage_bw_job_application_posts_custom_column', 'bodywings_render_job_application_column', 10, 2 );

function bodywings_render_job_application_column( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'applicant':
			$name  = get_post_meta( $post_id, '_bw_applicant_name', true );
			$email = get_post_meta( $post_id, '_bw_applicant_email', true );
			echo esc_html( $name );
			if ( $email ) {
				echo '<br /><a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>';
			}
			break;

		case 'job':
			$job_id = (int) get_post_meta( $post_id, '_bw_job_id', true );
			if ( $job_id && get_post( $job_id ) ) {
				echo '<a href="' . esc_url( get_edit_post_link( $job_id ) ) . '">' . esc_html( get_the_title( $job_id ) ) . '</a>';
			}
			break;

		case 'status':
			echo esc_html( bodywings_get_job_application_status_label( get_post_meta( $post_id, '_bw_status', true ) ) );
			break;

		case 'cv':
			$download_url = bodywings_get_job_application_cv_download_url( $post_id );
			if ( $download_url ) {
				echo '<a href="' . esc_url( $download_url ) . '">' . esc_html__( 'Herunterladen', 'bodywings' ) . '</a>';
			} else {
				echo '—';
			}
			break;
	}
}

function bodywings_get_job_application_status_label( string $status ): string {
	$labels = array(
		'new'       => __( 'Neu', 'bodywings' ),
		'reviewing' => __( 'In Prüfung', 'bodywings' ),
		'rejected'  => __( 'Abgelehnt', 'bodywings' ),
		'hired'     => __( 'Eingestellt', 'bodywings' ),
	);

	return $labels[ $status ] ?? $labels['new'];
}

add_action( 'add_meta_boxes', 'bodywings_register_job_application_meta_box' );

function bodywings_register_job_application_meta_box(): void {
	add_meta_box(
		'bodywings-job-application-details',
		__( 'Bewerbungsdetails', 'bodywings' ),
		'bodywings_render_job_application_meta_box',
		'bw_job_application',
		'normal',
		'high'
	);
}

function bodywings_render_job_application_meta_box( WP_Post $post ): void {
	$name    = get_post_meta( $post->ID, '_bw_applicant_name', true );
	$email   = get_post_meta( $post->ID, '_bw_applicant_email', true );
	$phone   = get_post_meta( $post->ID, '_bw_applicant_phone', true );
	$job_id  = (int) get_post_meta( $post->ID, '_bw_job_id', true );
	$status  = get_post_meta( $post->ID, '_bw_status', true ) ?: 'new';
	$cv_name = get_post_meta( $post->ID, '_bw_cv_filename', true );
	$download_url = bodywings_get_job_application_cv_download_url( $post->ID );

	wp_nonce_field( 'bodywings_save_job_application_status', 'bodywings_job_application_status_nonce' );
	?>
	<p><strong><?php esc_html_e( 'Name:', 'bodywings' ); ?></strong> <?php echo esc_html( $name ); ?></p>
	<p><strong><?php esc_html_e( 'E-Mail:', 'bodywings' ); ?></strong> <a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
	<?php if ( $phone ) : ?>
		<p><strong><?php esc_html_e( 'Telefon:', 'bodywings' ); ?></strong> <?php echo esc_html( $phone ); ?></p>
	<?php endif; ?>
	<?php if ( $job_id ) : ?>
		<p><strong><?php esc_html_e( 'Stelle:', 'bodywings' ); ?></strong> <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>"><?php echo esc_html( get_the_title( $job_id ) ); ?></a></p>
	<?php endif; ?>
	<?php if ( $download_url ) : ?>
		<p><strong><?php esc_html_e( 'Lebenslauf:', 'bodywings' ); ?></strong> <a href="<?php echo esc_url( $download_url ); ?>"><?php echo esc_html( $cv_name ?: __( 'Herunterladen', 'bodywings' ) ); ?></a></p>
	<?php endif; ?>
	<p>
		<label for="bw-application-status"><strong><?php esc_html_e( 'Status:', 'bodywings' ); ?></strong></label><br />
		<select id="bw-application-status" name="bw_application_status">
			<?php foreach ( array( 'new', 'reviewing', 'rejected', 'hired' ) as $value ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>>
					<?php echo esc_html( bodywings_get_job_application_status_label( $value ) ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class="description">
		<?php esc_html_e( 'Nachricht/Anschreiben steht im Titel-Feld darüber im Inhaltsbereich.', 'bodywings' ); ?>
	</p>
	<?php
}

add_action( 'save_post_bw_job_application', 'bodywings_save_job_application_status' );

function bodywings_save_job_application_status( int $post_id ): void {
	if ( ! isset( $_POST['bodywings_job_application_status_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bodywings_job_application_status_nonce'] ) ), 'bodywings_save_job_application_status' )
	) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$status = isset( $_POST['bw_application_status'] ) ? sanitize_key( wp_unslash( $_POST['bw_application_status'] ) ) : 'new';
	$status = in_array( $status, array( 'new', 'reviewing', 'rejected', 'hired' ), true ) ? $status : 'new';

	update_post_meta( $post_id, '_bw_status', $status );

	// Zeitstempel für die automatische Löschfrist (§32/DSGVO, siehe
	// inc/jobs/retention.php) — läuft erst ab einem abgeschlossenen
	// Verfahren, nicht ab Eingang der Bewerbung. Bei Rückstufung auf
	// "new"/"reviewing" (z.B. Korrektur) wird die Frist wieder aufgehoben.
	if ( in_array( $status, array( 'rejected', 'hired' ), true ) ) {
		if ( ! get_post_meta( $post_id, '_bw_status_concluded_at', true ) ) {
			update_post_meta( $post_id, '_bw_status_concluded_at', current_time( 'mysql' ) );
		}
	} else {
		delete_post_meta( $post_id, '_bw_status_concluded_at' );
	}
}
