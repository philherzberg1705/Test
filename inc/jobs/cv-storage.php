<?php
/**
 * Sichere Ablage hochgeladener Lebensläufe (§32). Bewusst NICHT über die
 * normale Mediathek (wp_insert_attachment) — die wäre uploads-URL-basiert
 * und damit für jede:n mit der URL direkt abrufbar. Stattdessen:
 *
 * - eigener Ordner außerhalb der Mediathek, mit .htaccess "Deny from all"
 *   (Apache) als erste Verteidigungslinie,
 * - pro Bewerbung ein kryptografisch zufälliger, nicht erratbarer
 *   Unterordner als zweite Verteidigungslinie (wirkt serverunabhängig),
 * - Download ausschließlich über einen capability-geprüften
 *   admin-post.php-Handler, nie über einen direkten Medien-Link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bodywings_job_applications_base_dir(): string {
	$upload_dir = wp_upload_dir();
	$dir        = trailingslashit( $upload_dir['basedir'] ) . 'bw-job-applications';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$htaccess = $dir . '/.htaccess';
	if ( ! file_exists( $htaccess ) ) {
		file_put_contents( $htaccess, "<Files \"*\">\n\tRequire all denied\n\tDeny from all\n</Files>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	$index = $dir . '/index.php';
	if ( ! file_exists( $index ) ) {
		file_put_contents( $index, "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	return $dir;
}

/**
 * Validiert + speichert einen hochgeladenen Lebenslauf.
 *
 * @param array $file Ein Eintrag aus $_FILES.
 * @return array{relpath:string,filename:string}|WP_Error
 */
function bodywings_store_job_application_cv( array $file ) {
	if ( empty( $file['tmp_name'] ) || UPLOAD_ERR_OK !== ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) ) {
		return new WP_Error( 'bw_upload_failed', __( 'Der Lebenslauf konnte nicht hochgeladen werden.', 'bodywings' ) );
	}

	$max_bytes = apply_filters( 'bodywings_job_cv_max_bytes', 5 * MB_IN_BYTES );
	if ( $file['size'] > $max_bytes ) {
		return new WP_Error( 'bw_upload_too_large', __( 'Die Datei ist zu groß (max. 5 MB).', 'bodywings' ) );
	}

	$filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], array( 'pdf' => 'application/pdf' ) );

	if ( 'pdf' !== $filetype['ext'] || 'application/pdf' !== $filetype['type'] ) {
		return new WP_Error( 'bw_upload_invalid_type', __( 'Bitte den Lebenslauf als PDF hochladen.', 'bodywings' ) );
	}

	// Zusätzliche Signaturprüfung gegen MIME-Spoofing über den Dateinamen.
	$handle  = fopen( $file['tmp_name'], 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	$header  = $handle ? fread( $handle, 5 ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread
	if ( $handle ) {
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	}
	if ( '%PDF-' !== $header ) {
		return new WP_Error( 'bw_upload_invalid_signature', __( 'Die Datei scheint kein gültiges PDF zu sein.', 'bodywings' ) );
	}

	$base_dir = bodywings_job_applications_base_dir();
	$token    = bin2hex( random_bytes( 16 ) );
	$dest_dir = $base_dir . '/' . $token;

	if ( ! wp_mkdir_p( $dest_dir ) ) {
		return new WP_Error( 'bw_upload_dir_failed', __( 'Der Upload konnte nicht gespeichert werden.', 'bodywings' ) );
	}

	$dest_path = $dest_dir . '/lebenslauf.pdf';

	if ( ! move_uploaded_file( $file['tmp_name'], $dest_path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_move_uploaded_file
		return new WP_Error( 'bw_upload_move_failed', __( 'Der Upload konnte nicht gespeichert werden.', 'bodywings' ) );
	}

	return array(
		'relpath'  => $token . '/lebenslauf.pdf',
		'filename' => sanitize_file_name( $file['name'] ),
	);
}

function bodywings_get_job_application_cv_download_url( int $application_id ): string {
	$relpath = get_post_meta( $application_id, '_bw_cv_relpath', true );

	if ( ! $relpath ) {
		return '';
	}

	return wp_nonce_url(
		add_query_arg(
			array(
				'action'         => 'bodywings_download_job_cv',
				'application_id' => $application_id,
			),
			admin_url( 'admin-post.php' )
		),
		'bodywings_download_job_cv_' . $application_id
	);
}

add_action( 'admin_post_bodywings_download_job_cv', 'bodywings_handle_job_cv_download' );

function bodywings_handle_job_cv_download(): void {
	$application_id = isset( $_GET['application_id'] ) ? absint( $_GET['application_id'] ) : 0;

	if ( ! $application_id
		|| ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bodywings_download_job_cv_' . $application_id )
	) {
		wp_die( esc_html__( 'Ungültige Anfrage.', 'bodywings' ), 400 );
	}

	if ( ! current_user_can( 'edit_post', $application_id ) || 'bw_job_application' !== get_post_type( $application_id ) ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'bodywings' ), 403 );
	}

	$relpath = get_post_meta( $application_id, '_bw_cv_relpath', true );
	$base_dir = bodywings_job_applications_base_dir();
	$path    = $relpath ? $base_dir . '/' . $relpath : '';

	// Path-Traversal-Schutz: aufgelöster Pfad muss weiterhin innerhalb des
	// geschützten Basisordners liegen.
	if ( ! $path || ! file_exists( $path ) || 0 !== strpos( realpath( $path ), realpath( $base_dir ) ) ) {
		wp_die( esc_html__( 'Datei nicht gefunden.', 'bodywings' ), 404 );
	}

	$filename = get_post_meta( $application_id, '_bw_cv_filename', true ) ?: 'lebenslauf.pdf';

	nocache_headers();
	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	exit;
}
