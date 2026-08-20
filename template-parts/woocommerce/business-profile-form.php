<?php
/**
 * Formular für den Konto-Punkt "Geschäftsdaten" (§17-Optik-Vorbild: gleiche
 * .form-row-Struktur wie die Auth-Formulare, hier aber klassisches POST
 * statt AJAX — eine selten genutzte Einstellungsseite braucht keinen
 * eigenen AJAX-Handler, ein normaler Redirect ist hier einfacher und
 * genauso schnell (§15 "kein Selbstzweck").
 *
 * $args['profile']: array aus bodywings_get_business_profile().
 * $args['notice']: string|null Erfolgsmeldung nach dem Speichern.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_profile = $args['profile'];
$bw_notice  = $args['notice'];
?>
<?php if ( $bw_notice ) : ?>
	<p class="woocommerce-message" role="status" aria-live="polite"><?php echo esc_html( $bw_notice ); ?></p>
<?php endif; ?>

<?php if ( null === $bw_profile['lat'] ) : ?>
	<p class="bw-business-form__hint bw-color-muted">
		<?php esc_html_e( 'Sobald Straße, PLZ und Ort ausgefüllt sind, wird der Standort automatisch auf der Karte lokalisiert.', 'bodywings' ); ?>
	</p>
<?php endif; ?>

<form class="bw-business-form" method="post">
	<div class="bw-business-form__field">
		<label for="bw-business-name"><?php esc_html_e( 'Firmenname', 'bodywings' ); ?> <span class="required">*</span></label>
		<input type="text" id="bw-business-name" name="name" class="bw-input" value="<?php echo esc_attr( $bw_profile['name'] ); ?>" required />
	</div>

	<div class="bw-business-form__row">
		<div class="bw-business-form__field">
			<label for="bw-business-street"><?php esc_html_e( 'Straße und Hausnummer', 'bodywings' ); ?></label>
			<input type="text" id="bw-business-street" name="street" class="bw-input" value="<?php echo esc_attr( $bw_profile['street'] ); ?>" autocomplete="street-address" />
		</div>
		<div class="bw-business-form__field">
			<label for="bw-business-zip"><?php esc_html_e( 'PLZ', 'bodywings' ); ?></label>
			<input type="text" id="bw-business-zip" name="zip" class="bw-input" value="<?php echo esc_attr( $bw_profile['zip'] ); ?>" autocomplete="postal-code" />
		</div>
	</div>

	<div class="bw-business-form__field">
		<label for="bw-business-city"><?php esc_html_e( 'Ort', 'bodywings' ); ?></label>
		<input type="text" id="bw-business-city" name="city" class="bw-input" value="<?php echo esc_attr( $bw_profile['city'] ); ?>" autocomplete="address-level2" />
	</div>

	<div class="bw-business-form__row">
		<div class="bw-business-form__field">
			<label for="bw-business-phone"><?php esc_html_e( 'Telefon', 'bodywings' ); ?></label>
			<input type="tel" id="bw-business-phone" name="phone" class="bw-input" value="<?php echo esc_attr( $bw_profile['phone'] ); ?>" autocomplete="tel" />
		</div>
		<div class="bw-business-form__field">
			<label for="bw-business-email"><?php esc_html_e( 'E-Mail', 'bodywings' ); ?></label>
			<input type="email" id="bw-business-email" name="email" class="bw-input" value="<?php echo esc_attr( $bw_profile['email'] ); ?>" autocomplete="email" />
		</div>
	</div>

	<div class="bw-business-form__field">
		<label for="bw-business-website"><?php esc_html_e( 'Website', 'bodywings' ); ?></label>
		<input type="url" id="bw-business-website" name="website" class="bw-input" value="<?php echo esc_attr( $bw_profile['website'] ); ?>" placeholder="https://" autocomplete="url" />
	</div>

	<?php wp_nonce_field( 'bodywings_business_profile', 'bodywings_business_profile_nonce' ); ?>
	<button type="submit" name="bodywings_business_profile_submit" value="1" class="bw-btn">
		<?php esc_html_e( 'Speichern', 'bodywings' ); ?>
	</button>
</form>
