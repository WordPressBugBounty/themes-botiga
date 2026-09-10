<?php
/**
 * Botiga Setup Checklist Customizer completion configuration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'sections' => array(
		'botiga_section_main_header'   => 'header_customized',
		'botiga_section_mobile_header' => 'header_customized',
		'colors'                       => 'brand_style_customized',
		'botiga_section_layout'         => 'page_layout_customized',
		'botiga_section_shop_cart'      => 'cart_customized',
		'botiga_section_blog_archives'  => 'blog_customized',
		'botiga_section_blog_singles'   => 'blog_customized',
	),
	'section_prefixes' => array(
		'botiga_section_hb_'         => 'header_customized',
		'botiga_section_fb_'         => 'footer_customized',
		'botiga_section_footer_'     => 'footer_customized',
		'botiga_section_typography_' => 'brand_style_customized',
	),
	'panels' => array(
		'botiga_panel_shop_archive'   => 'shop_customized',
		'botiga_panel_single_product' => 'product_customized',
	),
);
