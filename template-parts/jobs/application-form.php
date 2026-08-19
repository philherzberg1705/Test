<?php
/**
 * Bewerbungsformular (§15 AJAX, §32 Sicherheit). Nur ausgegeben, wenn die
 * Stelle noch offen ist (inc/jobs/helpers.php: bodywings_job_is_open()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bw_job_id = get_the_ID();
?>
<div class="bw-job-application" id="bewerben">
	<h2 class="bw-job-application__title"><?php esc_html_e( 'Jetzt bewerben', 'bodywings' ); ?></h2>

	<form
		class="bw-job-application-form"
		method="post"
		enctype="multipart/form-data"
		data-bw-job-application-form
		data-job-id="<?php echo esc_attr( (string) $bw_job_id ); ?>"
		novalidate
	>
		<div class="bw-job-application-form__row">
			<div class="bw-job-application-form__field">
				<label for="bw-app-name"><?php esc_html_e( 'Name', 'bodywings' ); ?> <span class="required">*</span></label>
				<input type="text" id="bw-app-name" name="name" class="bw-input" autocomplete="name" required />
			</div>
			<div class="bw-job-application-form__field">
				<label for="bw-app-email"><?php esc_html_e( 'E-Mail-Adresse', 'bodywings' ); ?> <span class="required">*</span></label>
				<input type="email" id="bw-app-email" name="email" class="bw-input" autocomplete="email" required />
			</div>
		</div>

		<div class="bw-job-application-form__row">
			<div class="bw-job-application-form__field">
				<label for="bw-app-phone"><?php esc_html_e( 'Telefon (optional)', 'bodywings' ); ?></label>
				<input type="tel" id="bw-app-phone" name="phone" class="bw-input" autocomplete="tel" />
			</div>
			<div class="bw-job-application-form__field">
				<label for="bw-app-cv"><?php esc_html_e( 'Lebenslauf (PDF, max. 5 MB)', 'bodywings' ); ?> <span class="required">*</span></label>
				<input type="file" id="bw-app-cv" name="cv" accept="application/pdf" required />
			</div>
		</div>

		<div class="bw-job-application-form__field">
			<label for="bw-app-message"><?php esc_html_e( 'Nachricht / Anschreiben (optional)', 'bodywings' ); ?></label>
			<textarea id="bw-app-message" name="message" class="bw-input" rows="5"></textarea>
		</div>

		<!-- Honeypot: für Menschen unsichtbar und aus Tab-Reihenfolge/AT entfernt, Bots füllen es oft blind mit aus. -->
		<div class="bw-visually-hidden" aria-hidden="true">
			<label for="bw-app-website"><?php esc_html_e( 'Website (bitte leer lassen)', 'bodywings' ); ?></label>
			<input type="text" id="bw-app-website" name="website" tabindex="-1" autocomplete="off" />
		</div>

		<label class="bw-job-application-form__consent">
			<input type="checkbox" name="consent" required />
			<span><?php esc_html_e( 'Ich stimme zu, dass meine Angaben zur Bearbeitung dieser Bewerbung gespeichert und verarbeitet werden. Nach Abschluss des Verfahrens werden die Daten gelöscht.', 'bodywings' ); ?></span>
		</label>

		<button type="submit" class="bw-btn bw-job-application-form__submit" data-bw-job-application-submit>
			<?php esc_html_e( 'Bewerbung absenden', 'bodywings' ); ?>
		</button>

		<p class="bw-job-application-form__status" data-bw-job-application-status role="status" aria-live="polite"></p>
	</form>
</div>
