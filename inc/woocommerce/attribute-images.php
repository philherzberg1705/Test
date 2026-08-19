<?php
/**
 * §9: Attributwerte (z.B. Farbe: Grün/Beige/Terrakotta) zentral mit einem
 * Bild pflegen. Ein Term-Meta-Feld auf allen product_attribute-Taxonomien,
 * wiederverwendbar über bodywings_get_attribute_term_image_id() in
 * Produktkarten, Varianten-Auswahl, Produktseite und Filter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BODYWINGS_ATTRIBUTE_IMAGE_META_KEY = 'bodywings_attribute_image_id';

add_action( 'init', 'bodywings_register_attribute_image_fields', 20 );

/**
 * Muss nach der WooCommerce-Attribut-Taxonomie-Registrierung laufen
 * (WC registriert seine pa_*-Taxonomien selbst auf init).
 */
function bodywings_register_attribute_image_fields(): void {
	if ( ! bodywings_is_woocommerce_active() ) {
		return;
	}

	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );

		add_action( "{$taxonomy}_add_form_fields", 'bodywings_render_attribute_image_add_field' );
		add_action( "{$taxonomy}_edit_form_fields", 'bodywings_render_attribute_image_edit_field', 10, 2 );
		add_action( "created_{$taxonomy}", 'bodywings_save_attribute_image_field' );
		add_action( "edited_{$taxonomy}", 'bodywings_save_attribute_image_field' );
	}
}

function bodywings_render_attribute_image_add_field(): void {
	?>
	<div class="form-field">
		<label for="bodywings-attribute-image-id"><?php esc_html_e( 'Bild', 'bodywings' ); ?></label>
		<?php bodywings_render_attribute_image_picker( 0 ); ?>
		<p><?php esc_html_e( 'Wird auf Produktkarten, in der Variantenauswahl, der Produktseite und im Filter verwendet.', 'bodywings' ); ?></p>
	</div>
	<?php
}

function bodywings_render_attribute_image_edit_field( WP_Term $term ): void {
	?>
	<tr class="form-field">
		<th scope="row"><label for="bodywings-attribute-image-id"><?php esc_html_e( 'Bild', 'bodywings' ); ?></label></th>
		<td>
			<?php bodywings_render_attribute_image_picker( (int) $term->term_id ); ?>
			<p class="description"><?php esc_html_e( 'Wird auf Produktkarten, in der Variantenauswahl, der Produktseite und im Filter verwendet.', 'bodywings' ); ?></p>
		</td>
	</tr>
	<?php
}

function bodywings_render_attribute_image_picker( int $term_id ): void {
	$image_id  = $term_id ? bodywings_get_attribute_term_image_id( $term_id ) : 0;
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';

	wp_nonce_field( 'bodywings_attribute_image', 'bodywings_attribute_image_nonce' );
	?>
	<div class="bodywings-attribute-image-picker" data-term-id="<?php echo esc_attr( (string) $term_id ); ?>">
		<img
			src="<?php echo esc_url( $image_url ); ?>"
			class="bodywings-attribute-image-picker__preview"
			style="<?php echo $image_url ? '' : 'display:none;'; ?>max-width:80px;height:auto;display:block;margin-bottom:8px;"
			alt=""
		/>
		<input type="hidden" name="bodywings_attribute_image_id" class="bodywings-attribute-image-picker__input" value="<?php echo esc_attr( (string) $image_id ); ?>" />
		<button type="button" class="button bodywings-attribute-image-picker__select">
			<?php esc_html_e( 'Bild auswählen', 'bodywings' ); ?>
		</button>
		<button type="button" class="button bodywings-attribute-image-picker__remove" <?php echo $image_id ? '' : 'style="display:none;"'; ?>>
			<?php esc_html_e( 'Entfernen', 'bodywings' ); ?>
		</button>
	</div>
	<?php
}

function bodywings_save_attribute_image_field( int $term_id ): void {
	if ( ! isset( $_POST['bodywings_attribute_image_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bodywings_attribute_image_nonce'] ) ), 'bodywings_attribute_image' )
	) {
		return;
	}

	if ( ! current_user_can( 'manage_product_terms' ) ) {
		return;
	}

	$image_id = isset( $_POST['bodywings_attribute_image_id'] ) ? absint( $_POST['bodywings_attribute_image_id'] ) : 0;

	if ( $image_id ) {
		update_term_meta( $term_id, BODYWINGS_ATTRIBUTE_IMAGE_META_KEY, $image_id );
	} else {
		delete_term_meta( $term_id, BODYWINGS_ATTRIBUTE_IMAGE_META_KEY );
	}
}

function bodywings_get_attribute_term_image_id( int $term_id ): int {
	return (int) get_term_meta( $term_id, BODYWINGS_ATTRIBUTE_IMAGE_META_KEY, true );
}

/**
 * @return string Bild-URL oder leerer String, wenn kein Bild gepflegt ist.
 */
function bodywings_get_attribute_term_image_url( int $term_id, string $size = 'thumbnail' ): string {
	$image_id = bodywings_get_attribute_term_image_id( $term_id );

	if ( ! $image_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $image_id, $size );

	return $url ? $url : '';
}

add_action( 'admin_enqueue_scripts', 'bodywings_enqueue_attribute_image_admin_assets' );

function bodywings_enqueue_attribute_image_admin_assets( string $hook ): void {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 0 !== strpos( (string) $screen->taxonomy, 'pa_' ) ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_script(
		'bodywings-admin-attribute-image',
		BODYWINGS_URI . '/assets/js/admin/attribute-image.js',
		array( 'jquery' ),
		bodywings_asset_version( BODYWINGS_DIR . '/assets/js/admin/attribute-image.js' ),
		true
	);
}
