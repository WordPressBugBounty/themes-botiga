<?php
/**
 * Botiga Setup Checklist admin page.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$checklist            = Botiga_Setup_Checklist::instance();
$checklist_page       = new Botiga_Setup_Checklist_Page();
$sections             = $checklist->get_actionable_sections();
$promotional_sections = $checklist->get_promotional_sections();
$progress             = $checklist->get_progress();
$header_data          = $checklist_page->get_header_data();
$footer_data          = $checklist_page->get_footer_data();
$views_path           = get_template_directory() . '/inc/setup-checklist/views/';
$dismiss_url          = wp_nonce_url(
	add_query_arg(
		'action',
		'botiga_dismiss_setup_checklist',
		admin_url( 'admin-post.php' )
	),
	'botiga_dismiss_setup_checklist'
);
?>
<div class="wrap botiga-setup-checklist">
	<h1 class="screen-reader-text"><?php esc_html_e( 'Botiga Setup Checklist', 'botiga' ); ?></h1>

	<?php require $views_path . 'page-header.php'; ?>

	<main class="botiga-setup-checklist__content">
		<?php require $views_path . 'banner.php'; ?>
		<?php require $views_path . 'actionable-sections.php'; ?>
		<?php require $views_path . 'promotional-sections.php'; ?>
	</main>

	<?php require $views_path . 'page-footer.php'; ?>
</div>
