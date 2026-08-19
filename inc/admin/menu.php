<?php
/**
 * BODYWINGS-Theme-Backend (§29). Grundgerüst: Dashboard + Attribut-
 * Einstellungen (§8.2). Weitere Bereiche kommen bedarfsgetrieben aus
 * späteren Tasks hinzu (add_submenu_page kann von jedem Modul aus
 * aufgerufen werden) — bewusst nicht als leere Platzhalterseiten für
 * jeden in §29 genannten Punkt, siehe "gute Defaults statt jeder
 * Kleinigkeit als Option".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'bodywings_register_admin_menu' );

function bodywings_register_admin_menu(): void {
	add_menu_page(
		__( 'BODYWINGS Theme', 'bodywings' ),
		__( 'BODYWINGS', 'bodywings' ),
		'manage_options',
		'bodywings-theme',
		'bodywings_render_dashboard_page',
		'dashicons-admin-appearance',
		61
	);

	add_submenu_page(
		'bodywings-theme',
		__( 'Dashboard', 'bodywings' ),
		__( 'Dashboard', 'bodywings' ),
		'manage_options',
		'bodywings-theme',
		'bodywings_render_dashboard_page'
	);

	if ( bodywings_is_woocommerce_active() ) {
		add_submenu_page(
			'bodywings-theme',
			__( 'Produkt-Attribute', 'bodywings' ),
			__( 'Produkt-Attribute', 'bodywings' ),
			'manage_options',
			'bodywings-theme-attributes',
			'bodywings_render_attribute_settings_page'
		);
	}
}

function bodywings_render_dashboard_page(): void {
	?>
	<div class="wrap bodywings-admin">
		<h1><?php esc_html_e( 'BODYWINGS Theme', 'bodywings' ); ?></h1>
		<p><?php echo esc_html( sprintf( /* translators: %s: theme version */ __( 'Version %s', 'bodywings' ), BODYWINGS_VERSION ) ); ?></p>

		<table class="widefat striped" style="max-width:640px;">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'WooCommerce', 'bodywings' ); ?></th>
					<td>
						<?php if ( bodywings_is_woocommerce_active() ) : ?>
							<span style="color:#2d4f1e;">✓ <?php esc_html_e( 'aktiv', 'bodywings' ); ?></span>
						<?php else : ?>
							<span style="color:#b3261e;">✗ <?php esc_html_e( 'nicht aktiv — Shop-Funktionen sind deaktiviert.', 'bodywings' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'WPML', 'bodywings' ); ?></th>
					<td><?php echo function_exists( 'icl_get_languages' ) ? '✓' : '—'; ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Rank Math', 'bodywings' ); ?></th>
					<td><?php echo class_exists( 'RankMath' ) ? '✓' : '—'; ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Brevo-Newsletter', 'bodywings' ); ?></th>
					<td>
						<?php echo function_exists( 'bodywings_newsletter_brevo_api_key' ) && bodywings_newsletter_brevo_api_key()
							? esc_html__( 'konfiguriert', 'bodywings' )
							: esc_html__( 'nicht konfiguriert (lokale Speicherung aktiv)', 'bodywings' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

function bodywings_render_attribute_settings_page(): void {
	if ( ! bodywings_is_woocommerce_active() ) {
		return;
	}

	$settings   = bodywings_get_attribute_settings();
	$attributes = bodywings_get_available_attribute_taxonomies();
	?>
	<div class="wrap bodywings-admin">
		<h1><?php esc_html_e( 'Produkt-Attribute', 'bodywings' ); ?></h1>
		<p><?php esc_html_e( 'Legt fest, welche WooCommerce-Attribute auf Produktkarten, im Filter und visuell auf der Produktseite verwendet werden.', 'bodywings' ); ?></p>

		<?php if ( empty( $attributes ) ) : ?>
			<p><em><?php esc_html_e( 'Noch keine globalen Attribute angelegt (WooCommerce → Attribute).', 'bodywings' ); ?></em></p>
			<?php return; ?>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'bodywings_attribute_settings_group' ); ?>

			<table class="form-table" role="presentation">
				<?php
				$groups = array(
					'card_attributes'         => __( 'Produktkarten', 'bodywings' ),
					'filter_attributes'       => __( 'Filter', 'bodywings' ),
					'product_page_attributes' => __( 'Produktseite (visuell)', 'bodywings' ),
				);

				foreach ( $groups as $key => $label ) :
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $label ); ?></th>
						<td>
							<?php foreach ( $attributes as $taxonomy => $attr_label ) : ?>
								<label style="display:inline-block;margin:0 16px 8px 0;">
									<input
										type="checkbox"
										name="bodywings_attribute_settings[<?php echo esc_attr( $key ); ?>][]"
										value="<?php echo esc_attr( $taxonomy ); ?>"
										<?php checked( in_array( $taxonomy, $settings[ $key ], true ) ); ?>
									/>
									<?php echo esc_html( $attr_label ); ?>
								</label>
							<?php endforeach; ?>
							<p class="description">
								<?php
								if ( 'product_page_attributes' === $key ) {
									esc_html_e( 'Bildbasierte Darstellung auf der Produktdetailseite. Attributbilder werden unter WooCommerce → Attribute → [Attribut] → Begriffe gepflegt.', 'bodywings' );
								} else {
									esc_html_e( 'Attributbilder werden unter WooCommerce → Attribute → [Attribut] → Begriffe gepflegt.', 'bodywings' );
								}
								?>
							</p>
						</td>
					</tr>
					<?php
				endforeach;
				?>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
