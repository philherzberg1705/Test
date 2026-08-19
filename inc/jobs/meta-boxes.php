<?php
/**
 * Meta-Box für strukturierte Stellendaten (§32: Nonce, Capability-Check,
 * Sanitization). Bewusst native Meta-Box statt eines Custom-Fields-Plugins
 * (§33 "keine unnötigen Dependencies") — die Feldmenge ist klein und fest.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_JOB_META_NONCE_ACTION = 'bodywings_save_job_meta';
const BODYWINGS_JOB_META_NONCE_NAME   = 'bodywings_job_meta_nonce';

add_action( 'add_meta_boxes', 'bodywings_register_job_meta_box' );

function bodywings_register_job_meta_box(): void {
	add_meta_box(
		'bodywings-job-details',
		__( 'Stellendetails', 'bodywings' ),
		'bodywings_render_job_meta_box',
		'bw_job',
		'normal',
		'high'
	);
}

/**
 * @return array<string,string> Feld-Key => Beschriftung, für Formular UND
 * Sanitization gemeinsam genutzt (§33), damit beide nie auseinanderlaufen.
 */
function bodywings_get_job_list_fields(): array {
	return array(
		'_bw_job_tasks'        => __( 'Aufgaben (eine Zeile je Punkt)', 'bodywings' ),
		'_bw_job_requirements' => __( 'Anforderungen (eine Zeile je Punkt)', 'bodywings' ),
		'_bw_job_benefits'     => __( 'Benefits (eine Zeile je Punkt)', 'bodywings' ),
	);
}

function bodywings_render_job_meta_box( WP_Post $post ): void {
	wp_nonce_field( BODYWINGS_JOB_META_NONCE_ACTION, BODYWINGS_JOB_META_NONCE_NAME );

	$salary_min  = get_post_meta( $post->ID, '_bw_job_salary_min', true );
	$salary_max  = get_post_meta( $post->ID, '_bw_job_salary_max', true );
	$salary_unit = get_post_meta( $post->ID, '_bw_job_salary_unit', true ) ?: 'YEAR';
	$is_remote   = (bool) get_post_meta( $post->ID, '_bw_job_is_remote', true );
	$start_asap  = (bool) get_post_meta( $post->ID, '_bw_job_start_asap', true );
	$start_date  = get_post_meta( $post->ID, '_bw_job_start_date', true );
	$deadline    = get_post_meta( $post->ID, '_bw_job_application_deadline', true );
	$contact_name  = get_post_meta( $post->ID, '_bw_job_contact_name', true );
	$contact_email = get_post_meta( $post->ID, '_bw_job_contact_email', true );
	?>
	<style>
		.bw-job-meta-box .field { margin-bottom: 16px; }
		.bw-job-meta-box label { display: block; font-weight: 600; margin-bottom: 4px; }
		.bw-job-meta-box textarea { width: 100%; min-height: 90px; }
		.bw-job-meta-box .field-row { display: flex; gap: 16px; flex-wrap: wrap; }
		.bw-job-meta-box .field-row .field { flex: 1; min-width: 160px; }
		.bw-job-meta-box .description { color: #646970; font-size: 12px; margin-top: 2px; }
	</style>
	<div class="bw-job-meta-box">
		<?php foreach ( bodywings_get_job_list_fields() as $meta_key => $label ) : ?>
			<div class="field">
				<label for="<?php echo esc_attr( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label>
				<textarea id="<?php echo esc_attr( $meta_key ); ?>" name="<?php echo esc_attr( $meta_key ); ?>"><?php
					$values = (array) get_post_meta( $post->ID, $meta_key, true );
					echo esc_textarea( implode( "\n", $values ) );
				?></textarea>
			</div>
		<?php endforeach; ?>

		<div class="field-row">
			<div class="field">
				<label for="_bw_job_salary_min"><?php esc_html_e( 'Gehalt von (€, optional)', 'bodywings' ); ?></label>
				<input type="number" min="0" step="1" id="_bw_job_salary_min" name="_bw_job_salary_min" value="<?php echo esc_attr( $salary_min ); ?>" class="regular-text" />
			</div>
			<div class="field">
				<label for="_bw_job_salary_max"><?php esc_html_e( 'Gehalt bis (€, optional)', 'bodywings' ); ?></label>
				<input type="number" min="0" step="1" id="_bw_job_salary_max" name="_bw_job_salary_max" value="<?php echo esc_attr( $salary_max ); ?>" class="regular-text" />
			</div>
			<div class="field">
				<label for="_bw_job_salary_unit"><?php esc_html_e( 'Zeitraum', 'bodywings' ); ?></label>
				<select id="_bw_job_salary_unit" name="_bw_job_salary_unit">
					<option value="YEAR" <?php selected( $salary_unit, 'YEAR' ); ?>><?php esc_html_e( 'pro Jahr', 'bodywings' ); ?></option>
					<option value="MONTH" <?php selected( $salary_unit, 'MONTH' ); ?>><?php esc_html_e( 'pro Monat', 'bodywings' ); ?></option>
					<option value="HOUR" <?php selected( $salary_unit, 'HOUR' ); ?>><?php esc_html_e( 'pro Stunde', 'bodywings' ); ?></option>
				</select>
			</div>
		</div>

		<div class="field-row">
			<div class="field">
				<label for="_bw_job_start_date"><?php esc_html_e( 'Startdatum', 'bodywings' ); ?></label>
				<input type="date" id="_bw_job_start_date" name="_bw_job_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
				<label class="description">
					<input type="checkbox" name="_bw_job_start_asap" value="1" <?php checked( $start_asap ); ?> />
					<?php esc_html_e( 'ab sofort', 'bodywings' ); ?>
				</label>
			</div>
			<div class="field">
				<label for="_bw_job_application_deadline"><?php esc_html_e( 'Bewerbungsfrist (optional)', 'bodywings' ); ?></label>
				<input type="date" id="_bw_job_application_deadline" name="_bw_job_application_deadline" value="<?php echo esc_attr( $deadline ); ?>" />
			</div>
			<div class="field">
				<label for="_bw_job_is_remote"><?php esc_html_e( 'Remote möglich', 'bodywings' ); ?></label>
				<label class="description">
					<input type="checkbox" id="_bw_job_is_remote" name="_bw_job_is_remote" value="1" <?php checked( $is_remote ); ?> />
					<?php esc_html_e( 'Diese Stelle ist (teilweise) remote möglich', 'bodywings' ); ?>
				</label>
			</div>
		</div>

		<div class="field-row">
			<div class="field">
				<label for="_bw_job_contact_name"><?php esc_html_e( 'Ansprechpartner:in (optional)', 'bodywings' ); ?></label>
				<input type="text" id="_bw_job_contact_name" name="_bw_job_contact_name" value="<?php echo esc_attr( $contact_name ); ?>" class="regular-text" />
			</div>
			<div class="field">
				<label for="_bw_job_contact_email"><?php esc_html_e( 'Benachrichtigungs-E-Mail für Bewerbungen', 'bodywings' ); ?></label>
				<input type="email" id="_bw_job_contact_email" name="_bw_job_contact_email" value="<?php echo esc_attr( $contact_email ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
				<p class="description"><?php esc_html_e( 'Leer lassen, um die Standard-Administrator-E-Mail zu verwenden.', 'bodywings' ); ?></p>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'save_post_bw_job', 'bodywings_save_job_meta' );

function bodywings_save_job_meta( int $post_id ): void {
	if ( ! isset( $_POST[ BODYWINGS_JOB_META_NONCE_NAME ] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ BODYWINGS_JOB_META_NONCE_NAME ] ) ), BODYWINGS_JOB_META_NONCE_ACTION )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( bodywings_get_job_list_fields() as $meta_key => $label ) {
		$raw   = isset( $_POST[ $meta_key ] ) ? (string) wp_unslash( $_POST[ $meta_key ] ) : '';
		$lines = array_filter( array_map( 'sanitize_text_field', array_map( 'trim', explode( "\n", $raw ) ) ) );
		update_post_meta( $post_id, $meta_key, array_values( $lines ) );
	}

	$salary_min = isset( $_POST['_bw_job_salary_min'] ) && '' !== $_POST['_bw_job_salary_min']
		? absint( $_POST['_bw_job_salary_min'] )
		: '';
	$salary_max = isset( $_POST['_bw_job_salary_max'] ) && '' !== $_POST['_bw_job_salary_max']
		? absint( $_POST['_bw_job_salary_max'] )
		: '';
	update_post_meta( $post_id, '_bw_job_salary_min', $salary_min );
	update_post_meta( $post_id, '_bw_job_salary_max', $salary_max );

	$salary_unit = isset( $_POST['_bw_job_salary_unit'] ) ? sanitize_key( wp_unslash( $_POST['_bw_job_salary_unit'] ) ) : 'YEAR';
	update_post_meta( $post_id, '_bw_job_salary_unit', in_array( $salary_unit, array( 'YEAR', 'MONTH', 'HOUR' ), true ) ? $salary_unit : 'YEAR' );

	update_post_meta( $post_id, '_bw_job_is_remote', ! empty( $_POST['_bw_job_is_remote'] ) ? 1 : 0 );
	update_post_meta( $post_id, '_bw_job_start_asap', ! empty( $_POST['_bw_job_start_asap'] ) ? 1 : 0 );

	foreach ( array( '_bw_job_start_date', '_bw_job_application_deadline' ) as $date_key ) {
		$raw_date = isset( $_POST[ $date_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $date_key ] ) ) : '';
		$valid    = (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw_date );
		update_post_meta( $post_id, $date_key, $valid ? $raw_date : '' );
	}

	update_post_meta( $post_id, '_bw_job_contact_name', isset( $_POST['_bw_job_contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['_bw_job_contact_name'] ) ) : '' );

	$contact_email = isset( $_POST['_bw_job_contact_email'] ) ? sanitize_email( wp_unslash( $_POST['_bw_job_contact_email'] ) ) : '';
	update_post_meta( $post_id, '_bw_job_contact_email', $contact_email );
}
