<?php
/**
 * Contact Form 7-Integration (§28 "Plugin vorhanden → Integration aktivieren,
 * Plugin nicht vorhanden → Theme funktioniert trotzdem weiter"). CF7 selbst
 * übernimmt Formularverarbeitung/Validierung/AJAX (§15/§31 — nicht selbst
 * nachbauen), hier nur: Design-System-Restyling, Danke-Seiten-Redirect nach
 * erfolgreichem Absenden und die zugehörige Theme-Einstellung.
 *
 * CF7 lädt seine eigenen Assets standardmäßig sitewide (nicht erst nach
 * Content-Scan) — das eigene Restyling-Stylesheet hängt deshalb bewusst an
 * denselben Handle statt eine eigene (fehleranfällige) "kommt ein CF7-
 * Formular auf dieser Seite vor?"-Erkennung nachzubauen (§33).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_is_cf7_active(): bool {
	return class_exists( 'WPCF7_ContactForm' );
}

add_action( 'wp_enqueue_scripts', 'bodywings_enqueue_cf7_assets', 20 );

function bodywings_enqueue_cf7_assets(): void {
	if ( ! bodywings_is_cf7_active() ) {
		return;
	}

	$css_dir = BODYWINGS_DIR . '/assets/css';
	$js_dir  = BODYWINGS_DIR . '/assets/js';

	wp_enqueue_style(
		'bodywings-forms-cf7',
		BODYWINGS_URI . '/assets/css/components/forms-cf7.css',
		array( 'bodywings-base' ),
		bodywings_asset_version( $css_dir . '/components/forms-cf7.css' )
	);

	wp_enqueue_script(
		'bodywings-cf7-redirect',
		BODYWINGS_URI . '/assets/js/cf7-redirect.js',
		array( 'contact-form-7' ),
		bodywings_asset_version( $js_dir . '/cf7-redirect.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	wp_localize_script( 'bodywings-cf7-redirect', 'bodywingsCf7', array(
		'thankYouUrl' => bodywings_get_cf7_thank_you_url(),
	) );
}

/**
 * CF7 registriert seinen Formular-Post-Type ohne REST-Unterstützung — die
 * Formularliste für den Kontakt-Block-Editor (blocks/contact/edit.js) kann
 * deshalb nicht per wp.data/core-data geladen werden (anders als z.B. die
 * Produktkategorien im Produkt-Slider-Block) und wird stattdessen serverseitig
 * mitgegeben, analog zu bodywingsProductData für Variationen.
 *
 * @return array<int,array{id:int,title:string}>
 */
function bodywings_get_cf7_forms(): array {
	if ( ! bodywings_is_cf7_active() ) {
		return array();
	}

	$posts = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );

	return array_map( static function ( WP_Post $post ): array {
		return array(
			'id'    => $post->ID,
			'title' => $post->post_title,
		);
	}, $posts );
}

add_action( 'admin_enqueue_scripts', 'bodywings_localize_cf7_editor_data' );

function bodywings_localize_cf7_editor_data(): void {
	if ( ! wp_script_is( 'bw-block-contact-edit', 'registered' ) ) {
		return;
	}

	wp_localize_script( 'bw-block-contact-edit', 'bodywingsCf7EditorData', array(
		'active' => bodywings_is_cf7_active(),
		'forms'  => bodywings_get_cf7_forms(),
	) );
}

/* ---------- Einstellungen: Danke-Seite ---------- */

const BODYWINGS_CF7_SETTINGS_OPTION = 'bodywings_cf7_settings';

function bodywings_get_cf7_settings(): array {
	return wp_parse_args( get_option( BODYWINGS_CF7_SETTINGS_OPTION, array() ), array(
		'thank_you_page_id' => 0,
	) );
}

/**
 * Leerer String = kein Redirect konfiguriert. assets/js/cf7-redirect.js
 * lässt CF7s eigene Erfolgsmeldung dann einfach stehen (§25/§37: volle
 * Funktionalität auch ohne diese optionale Einstellung).
 */
function bodywings_get_cf7_thank_you_url(): string {
	$page_id = (int) bodywings_get_cf7_settings()['thank_you_page_id'];

	if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) {
		return '';
	}

	return (string) get_permalink( $page_id );
}

add_action( 'admin_init', 'bodywings_register_cf7_settings' );

function bodywings_register_cf7_settings(): void {
	if ( ! bodywings_is_cf7_active() ) {
		return;
	}

	register_setting( 'bodywings_cf7_settings_group', BODYWINGS_CF7_SETTINGS_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'bodywings_sanitize_cf7_settings',
		'default'           => array( 'thank_you_page_id' => 0 ),
	) );
}

function bodywings_sanitize_cf7_settings( $value ): array {
	return array(
		'thank_you_page_id' => isset( $value['thank_you_page_id'] ) ? absint( $value['thank_you_page_id'] ) : 0,
	);
}

add_action( 'admin_menu', 'bodywings_register_cf7_settings_page' );

function bodywings_register_cf7_settings_page(): void {
	if ( ! bodywings_is_cf7_active() ) {
		return;
	}

	add_submenu_page(
		'bodywings-theme',
		__( 'Formulare', 'bodywings' ),
		__( 'Formulare', 'bodywings' ),
		'manage_options',
		'bodywings-forms-settings',
		'bodywings_render_cf7_settings_page'
	);
}

function bodywings_render_cf7_settings_page(): void {
	$settings = bodywings_get_cf7_settings();
	?>
	<div class="wrap bodywings-admin">
		<h1><?php esc_html_e( 'Formulare', 'bodywings' ); ?></h1>
		<p><?php esc_html_e( 'Alle Contact-Form-7-Formulare übernehmen automatisch das Theme-Design. Hier lässt sich zusätzlich eine Danke-Seite festlegen, auf die nach erfolgreichem Absenden weitergeleitet wird.', 'bodywings' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'bodywings_cf7_settings_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="bodywings-cf7-thank-you-page"><?php esc_html_e( 'Danke-Seite', 'bodywings' ); ?></label></th>
					<td>
						<?php
						wp_dropdown_pages( array(
							'name'              => BODYWINGS_CF7_SETTINGS_OPTION . '[thank_you_page_id]',
							'id'                => 'bodywings-cf7-thank-you-page',
							'selected'          => (int) $settings['thank_you_page_id'],
							'show_option_none'  => __( '— Kein Redirect (Meldung bleibt im Formular sichtbar) —', 'bodywings' ),
							'option_none_value' => 0,
						) );
						?>
						<p class="description"><?php esc_html_e( 'Gilt für alle Formulare auf der Website.', 'bodywings' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
