<?php
/**
 * Geschäftssuche (Content-Block "Geschäftssuche" + Konto-Bereich): welche
 * WordPress-Rolle wird als "Geschäftspartner" behandelt? Nur Mitglieder
 * dieser Rolle erscheinen im Block und bekommen im Konto den Punkt
 * "Geschäftsdaten" (§29 "gute Defaults" — bewusst eine einzige, bestehende
 * Rolle statt einer neuen Rollen-Verwaltung, siehe inc/business-search/profile.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_BUSINESS_SEARCH_OPTION = 'bodywings_business_search_settings';

function bodywings_get_business_search_settings(): array {
	return wp_parse_args( get_option( BODYWINGS_BUSINESS_SEARCH_OPTION, array() ), array(
		'role' => '',
	) );
}

/**
 * Leerer String = Feature nicht konfiguriert. Block rendert dann nichts im
 * Frontend, Konto bekommt keinen zusätzlichen Punkt (§25/§37 — Theme bleibt
 * ohne diese optionale Einstellung vollständig funktionsfähig).
 */
function bodywings_get_business_search_role(): string {
	$role = bodywings_get_business_search_settings()['role'];

	return $role && wp_roles()->is_role( $role ) ? $role : '';
}

function bodywings_current_user_is_business_partner(): bool {
	$role = bodywings_get_business_search_role();

	return $role && is_user_logged_in() && in_array( $role, (array) wp_get_current_user()->roles, true );
}

add_action( 'admin_init', 'bodywings_register_business_search_settings' );

function bodywings_register_business_search_settings(): void {
	register_setting( 'bodywings_business_search_settings_group', BODYWINGS_BUSINESS_SEARCH_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'bodywings_sanitize_business_search_settings',
		'default'           => array( 'role' => '' ),
	) );
}

function bodywings_sanitize_business_search_settings( $value ): array {
	$role = isset( $value['role'] ) ? sanitize_key( $value['role'] ) : '';

	return array(
		'role' => wp_roles()->is_role( $role ) ? $role : '',
	);
}

add_action( 'admin_menu', 'bodywings_register_business_search_settings_page' );

function bodywings_register_business_search_settings_page(): void {
	add_submenu_page(
		'bodywings-theme',
		__( 'Geschäftssuche', 'bodywings' ),
		__( 'Geschäftssuche', 'bodywings' ),
		'manage_options',
		'bodywings-business-search-settings',
		'bodywings_render_business_search_settings_page'
	);
}

add_action( 'admin_enqueue_scripts', 'bodywings_localize_business_search_editor_data' );

function bodywings_localize_business_search_editor_data(): void {
	if ( ! wp_script_is( 'bw-block-business-search-edit', 'registered' ) ) {
		return;
	}

	wp_localize_script( 'bw-block-business-search-edit', 'bodywingsBusinessSearchEditorData', array(
		'roleConfigured' => (bool) bodywings_get_business_search_role(),
	) );
}

function bodywings_render_business_search_settings_page(): void {
	$settings = bodywings_get_business_search_settings();
	$roles    = wp_roles()->get_names();
	?>
	<div class="wrap bodywings-admin">
		<h1><?php esc_html_e( 'Geschäftssuche', 'bodywings' ); ?></h1>
		<p><?php esc_html_e( 'Legt fest, welche Nutzerrolle im Geschäftssuche-Content-Element auf der Karte erscheint. Nutzer:innen mit dieser Rolle erhalten im Konto zusätzlich den Punkt "Geschäftsdaten" zur Pflege ihrer Angaben.', 'bodywings' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'bodywings_business_search_settings_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="bodywings-business-search-role"><?php esc_html_e( 'Rolle', 'bodywings' ); ?></label></th>
					<td>
						<select id="bodywings-business-search-role" name="<?php echo esc_attr( BODYWINGS_BUSINESS_SEARCH_OPTION ); ?>[role]">
							<option value=""><?php esc_html_e( '— Keine (Feature deaktiviert) —', 'bodywings' ); ?></option>
							<?php foreach ( $roles as $role_key => $role_label ) : ?>
								<option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( $settings['role'], $role_key ); ?>>
									<?php echo esc_html( translate_user_role( $role_label ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Nur Nutzer:innen mit einer vollständig ausgefüllten Adresse (siehe Konto → Geschäftsdaten) erscheinen auf der Karte.', 'bodywings' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
