<?php
/**
 * Optionale Firmen-Angaben fürs JobPosting-Schema (§27). Ohne diese
 * Einstellung fällt bodywings_get_site_logo_url()/get_bloginfo('name')
 * bereits sinnvoll zurück (§29 "gute Defaults") — hier nur für den Fall,
 * dass die ausschreibende Organisation nicht identisch mit dem
 * Seitentitel/-logo sein soll (z.B. eine Recruiting-Marke).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_JOB_SETTINGS_OPTION = 'bodywings_job_settings';

function bodywings_get_job_settings(): array {
	return wp_parse_args( get_option( BODYWINGS_JOB_SETTINGS_OPTION, array() ), array(
		'org_name'    => '',
		'org_logo_id' => 0,
	) );
}

function bodywings_get_job_org_name(): string {
	$name = bodywings_get_job_settings()['org_name'];

	return $name ? $name : get_bloginfo( 'name' );
}

function bodywings_get_job_org_logo_url(): string {
	$logo_id = (int) bodywings_get_job_settings()['org_logo_id'];

	if ( $logo_id ) {
		$src = wp_get_attachment_image_src( $logo_id, 'medium' );
		if ( $src ) {
			return $src[0];
		}
	}

	return bodywings_get_site_logo_url();
}

add_action( 'admin_init', 'bodywings_register_job_settings' );

function bodywings_register_job_settings(): void {
	register_setting( 'bodywings_job_settings_group', BODYWINGS_JOB_SETTINGS_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'bodywings_sanitize_job_settings',
		'default'           => array(
			'org_name'    => '',
			'org_logo_id' => 0,
		),
	) );
}

function bodywings_sanitize_job_settings( $value ): array {
	return array(
		'org_name'    => isset( $value['org_name'] ) ? sanitize_text_field( $value['org_name'] ) : '',
		'org_logo_id' => isset( $value['org_logo_id'] ) ? absint( $value['org_logo_id'] ) : 0,
	);
}

add_action( 'admin_menu', 'bodywings_register_job_settings_page' );

function bodywings_register_job_settings_page(): void {
	$hook = add_submenu_page(
		'edit.php?post_type=bw_job',
		__( 'Job-Einstellungen', 'bodywings' ),
		__( 'Einstellungen', 'bodywings' ),
		'manage_options',
		'bodywings-job-settings',
		'bodywings_render_job_settings_page'
	);

	add_action( 'load-' . $hook, 'bodywings_enqueue_job_settings_assets' );
}

function bodywings_enqueue_job_settings_assets(): void {
	wp_enqueue_media();

	// Nutzt bewusst dasselbe generische (Klassen-basierte) Bild-Picker-
	// Skript wie die Attributbilder (§9) statt eines eigenen — die Logik
	// "ein Bild wählen/entfernen, ID in ein Hidden-Field schreiben" ist
	// identisch (§33).
	wp_enqueue_script(
		'bodywings-admin-attribute-image',
		BODYWINGS_URI . '/assets/js/admin/attribute-image.js',
		array( 'jquery' ),
		bodywings_asset_version( BODYWINGS_DIR . '/assets/js/admin/attribute-image.js' ),
		true
	);
}

function bodywings_render_job_settings_page(): void {
	$settings  = bodywings_get_job_settings();
	$logo_url  = $settings['org_logo_id'] ? wp_get_attachment_image_url( (int) $settings['org_logo_id'], 'thumbnail' ) : '';
	?>
	<div class="wrap bodywings-admin">
		<h1><?php esc_html_e( 'Job-Einstellungen', 'bodywings' ); ?></h1>
		<p><?php esc_html_e( 'Diese Angaben werden als "hiringOrganization" in den strukturierten Daten (JobPosting-Schema) jeder Stellenausschreibung verwendet.', 'bodywings' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'bodywings_job_settings_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="bodywings-job-org-name"><?php esc_html_e( 'Firmenname', 'bodywings' ); ?></label></th>
					<td>
						<input
							type="text"
							id="bodywings-job-org-name"
							name="<?php echo esc_attr( BODYWINGS_JOB_SETTINGS_OPTION ); ?>[org_name]"
							value="<?php echo esc_attr( $settings['org_name'] ); ?>"
							class="regular-text"
							placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						/>
						<p class="description"><?php esc_html_e( 'Leer lassen, um den Seitentitel zu verwenden.', 'bodywings' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Logo', 'bodywings' ); ?></th>
					<td>
						<div class="bodywings-attribute-image-picker" data-term-id="0">
							<img
								src="<?php echo esc_url( $logo_url ); ?>"
								class="bodywings-attribute-image-picker__preview"
								style="<?php echo $logo_url ? '' : 'display:none;'; ?>max-width:80px;height:auto;display:block;margin-bottom:8px;"
								alt=""
							/>
							<input
								type="hidden"
								name="<?php echo esc_attr( BODYWINGS_JOB_SETTINGS_OPTION ); ?>[org_logo_id]"
								class="bodywings-attribute-image-picker__input"
								value="<?php echo esc_attr( (string) $settings['org_logo_id'] ); ?>"
							/>
							<button type="button" class="button bodywings-attribute-image-picker__select"><?php esc_html_e( 'Bild auswählen', 'bodywings' ); ?></button>
							<button type="button" class="button bodywings-attribute-image-picker__remove" <?php echo $logo_url ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Entfernen', 'bodywings' ); ?></button>
						</div>
						<p class="description"><?php esc_html_e( 'Leer lassen, um das Custom Logo bzw. Site-Icon zu verwenden.', 'bodywings' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
