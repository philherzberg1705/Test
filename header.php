<?php
/**
 * Theme-Shell: <head> + Body-Öffnung + Site-Header.
 * Die eigentliche Header-Komponente (Logo, 6 Icons, Mobile-Offcanvas) lebt
 * in template-parts/header/site-header.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="bw-visually-hidden bw-skip-link" href="#bw-main">
	<?php esc_html_e( 'Zum Inhalt springen', 'bodywings' ); ?>
</a>

<?php get_template_part( 'template-parts/header/site-header' ); ?>

<main id="bw-main">
