<?php
/**
 * Override von WooCommerce/templates/myaccount/form-lost-password.php.
 * Zeigt dieselbe Tab-Leiste wie form-login.php (§17: optisch ein
 * gemeinsamer Auth-Bereich), "Passwort vergessen" ist hier aktiv. Login/
 * Registrieren führen per Link zurück zur Account-Seite — die eigentliche
 * Passwort-Reset-Logik bleibt vollständig WordPress/WooCommerce (§31).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_account_url = wc_get_page_permalink( 'myaccount' );
?>

<div class="bw-auth" data-bw-auth>
	<div class="bw-auth__tabs" role="tablist">
		<a class="bw-auth__tab bw-auth__tab--link" href="<?php echo esc_url( $bw_account_url ); ?>">
			<?php esc_html_e( 'Anmelden', 'bodywings' ); ?>
		</a>
		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<a class="bw-auth__tab bw-auth__tab--link" href="<?php echo esc_url( $bw_account_url . '#register' ); ?>">
				<?php esc_html_e( 'Registrieren', 'bodywings' ); ?>
			</a>
		<?php endif; ?>
		<span class="bw-auth__tab is-active" aria-current="page">
			<?php esc_html_e( 'Passwort vergessen', 'bodywings' ); ?>
		</span>
	</div>

	<?php do_action( 'woocommerce_before_lost_password_form' ); ?>

	<div class="bw-auth__panel is-active">
		<form method="post" class="woocommerce-ResetPassword lost_reset_password">
			<p>
				<?php
				echo wp_kses_post( apply_filters(
					'woocommerce_lost_password_message',
					esc_html__( 'Passwort vergessen? Bitte Benutzername oder E-Mail-Adresse eingeben. Du erhältst per E-Mail einen Link zum Erstellen eines neuen Passworts.', 'bodywings' )
				) );
				?>
			</p>

			<p class="form-row">
				<label for="user_login"><?php esc_html_e( 'Benutzername oder E-Mail', 'bodywings' ); ?></label>
				<input class="bw-input" type="text" name="user_login" id="user_login" autocomplete="username" />
			</p>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
				<button type="submit" class="bw-btn" value="<?php esc_attr_e( 'Passwort zurücksetzen', 'bodywings' ); ?>">
					<?php esc_html_e( 'Passwort zurücksetzen', 'bodywings' ); ?>
				</button>
			</p>
		</form>
	</div>

	<?php do_action( 'woocommerce_after_lost_password_form' ); ?>
</div>
