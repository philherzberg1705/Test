<?php
/**
 * Override von WooCommerce/templates/myaccount/form-login.php (§17).
 *
 * Login und Registrierung sind auf dieser einen Seite als Tabs
 * zusammengefasst und wechseln ohne Reload (JS: assets/js/auth-tabs.js).
 * "Passwort vergessen" führt bewusst zu WooCommerce/WordPress' eigenem
 * Lost-Password-Endpoint statt den sicherheitskritischen Reset-Flow hier
 * nachzubauen (§31) — visuell bleibt es dieselbe Tab-Leiste.
 *
 * Feldnamen/Nonces entsprechen exakt WC_Form_Handler::process_login()
 * und process_registration(), da nur die Präsentation angepasst wird,
 * nicht die Verarbeitung.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_show_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>

<div class="bw-auth" data-bw-auth>
	<div class="bw-auth__tabs" role="tablist">
		<button type="button" class="bw-auth__tab is-active" data-bw-auth-tab="login" role="tab" aria-selected="true" aria-controls="bw-auth-panel-login">
			<?php esc_html_e( 'Anmelden', 'bodywings' ); ?>
		</button>
		<?php if ( $bw_show_register ) : ?>
			<button type="button" class="bw-auth__tab" data-bw-auth-tab="register" role="tab" aria-selected="false" aria-controls="bw-auth-panel-register">
				<?php esc_html_e( 'Registrieren', 'bodywings' ); ?>
			</button>
		<?php endif; ?>
		<a class="bw-auth__tab bw-auth__tab--link" href="<?php echo esc_url( wc_lostpassword_url() ); ?>">
			<?php esc_html_e( 'Passwort vergessen', 'bodywings' ); ?>
		</a>
	</div>

	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

	<div class="bw-auth__panel is-active" id="bw-auth-panel-login" data-bw-auth-panel="login" role="tabpanel">
		<form class="woocommerce-form woocommerce-form-login login" method="post">
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="form-row">
				<label for="username"><?php esc_html_e( 'Benutzername oder E-Mail', 'bodywings' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="bw-input" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Werte-Echo nach Validierungsfehler, kein Verarbeitungscode. ?>
			</p>
			<p class="form-row">
				<label for="password"><?php esc_html_e( 'Passwort', 'bodywings' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="bw-input" type="password" name="password" id="password" autocomplete="current-password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row bw-auth__remember">
				<label>
					<input type="checkbox" name="rememberme" value="forever" />
					<?php esc_html_e( 'Angemeldet bleiben', 'bodywings' ); ?>
				</label>
			</p>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="bw-btn" name="login" value="<?php esc_attr_e( 'Anmelden', 'bodywings' ); ?>">
					<?php esc_html_e( 'Anmelden', 'bodywings' ); ?>
				</button>
				<input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" />
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</div>

	<?php if ( $bw_show_register ) : ?>
		<div class="bw-auth__panel" id="bw-auth-panel-register" data-bw-auth-panel="register" role="tabpanel" hidden>
			<form method="post" class="woocommerce-form woocommerce-form-register register">
				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
					<p class="form-row">
						<label for="reg_username"><?php esc_html_e( 'Benutzername', 'bodywings' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="text" class="bw-input" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
					</p>
				<?php endif; ?>

				<p class="form-row">
					<label for="reg_email"><?php esc_html_e( 'E-Mail-Adresse', 'bodywings' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="bw-input" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
					<p class="form-row">
						<label for="reg_password"><?php esc_html_e( 'Passwort', 'bodywings' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="password" class="bw-input" name="password" id="reg_password" autocomplete="new-password" />
					</p>
				<?php else : ?>
					<p><?php esc_html_e( 'Ein Link zur Passwortvergabe wird dir per E-Mail zugeschickt.', 'bodywings' ); ?></p>
				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="bw-btn" name="register" value="<?php esc_attr_e( 'Registrieren', 'bodywings' ); ?>">
						<?php esc_html_e( 'Registrieren', 'bodywings' ); ?>
					</button>
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div>
