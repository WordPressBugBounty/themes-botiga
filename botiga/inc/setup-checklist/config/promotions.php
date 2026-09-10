<?php
/**
 * Botiga Setup Checklist promotional card configuration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$placeholder_icon = get_template_directory_uri() . '/assets/img/admin/setup-checklist-promo-placeholder.svg';

return array(
	'botiga-pro' => array(
		'label'                  => __( 'Do More with Botiga Pro', 'botiga' ),
		'layout'                 => 'botiga-pro',
		'footer_text'            => __( 'Plus dozens of other powerful features and elementor addons to meet all your site-building needs.', 'botiga' ),
		'footer_action_label'    => __( 'View All Features', 'botiga' ),
		'footer_action_url'      => function_exists( 'botiga_upgrade_link' )
			? botiga_upgrade_link( 'setup_checklist', 'Setup Checklist View All Features' )
			: 'https://athemes.com/botiga-upgrade/',
		'footer_action_external' => true,
		'items'                  => array(
			'product-swatches' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Product Swatches', 'botiga' ),
				'description' => __( 'Show variations as color, image, or label swatches', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/swatch.svg',
			),
			'variations-gallery' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Variations Gallery', 'botiga' ),
				'description' => __( 'Display unique galleries for each product variation', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/gallery.svg',
			),
			'template-builder' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Template Builder', 'botiga' ),
				'description' => __( 'Build fully custom page layouts', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/add-template.svg',
			),
			'video-gallery' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Video Gallery', 'botiga' ),
				'description' => __( 'Showcase products with image and video galleries', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/video.svg',
			),
			'wishlist' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Wishlist', 'botiga' ),
				'description' => __( 'Let customers save products for later', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/star-filled.svg',
			),
			'mega-menu' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Mega Menu', 'botiga' ),
				'description' => __( 'Create rich, multi-column menus with custom layouts', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/menu.svg',
			),
			'shop-filters' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Shop Filters', 'botiga' ),
				'description' => __( 'Help shoppers find products faster with filters', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/filter.svg',
			),
			'custom-sidebar' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Custom Sidebar', 'botiga' ),
				'description' => __( 'Assign custom sidebar to pages for a tailored layout', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/sidebar.svg',
			),
			'table-of-contents' => array(
				'type'        => 'theme_pro',
				'title'       => __( 'Table of Contents', 'botiga' ),
				'description' => __( 'Add automatic navigation for long content pages', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/promotion-icons/table-of-contents.svg',
			),
		),
	),
	'merchant' => array(
		'label'                => __( "Grow Your Store's Revenue", 'botiga' ),
		'layout'               => 'merchant',
		'requires_woocommerce' => true,
		'footer_text'          => __( '40+ WooCommerce modules to grow your store', 'botiga' ),
		'items'                => array(
			'product-labels' => array(
				'type'      => 'merchant_module',
				'module_id' => 'product-labels',
				'title'     => __( 'Product Labels', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/product-labels.png',
				'pro'       => false,
			),
			'payment-logos' => array(
				'type'      => 'merchant_module',
				'module_id' => 'payment-logos',
				'title'     => __( 'Payment Logos', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/payment.png',
				'pro'       => false,
			),
			'waitlist' => array(
				'type'      => 'merchant_module',
				'module_id' => 'wait-list',
				'title'     => __( 'Waitlist', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/waitlist.png',
				'pro'       => true,
			),
			'product-bundles' => array(
				'type'      => 'merchant_module',
				'module_id' => 'product-bundles',
				'title'     => __( 'Product Bundles', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/box.png',
				'pro'       => true,
			),
			'stock-scarcity' => array(
				'type'      => 'merchant_module',
				'module_id' => 'stock-scarcity',
				'title'     => __( 'Stock Scarcity', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/progress.png',
				'pro'       => true,
			),
			'bulk-discount' => array(
				'type'      => 'merchant_module',
				'module_id' => 'volume-discounts',
				'title'     => __( 'Bulk Discount', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/volume-discount.png',
				'pro'       => true,
			),
			'frequently-bought-together' => array(
				'type'      => 'merchant_module',
				'module_id' => 'frequently-bought-together',
				'title'     => __( 'Frequently Bought Together', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/add-layout.png',
				'pro'       => true,
			),
			'side-cart' => array(
				'type'      => 'merchant_module',
				'module_id' => 'side-cart',
				'title'     => __( 'Side Cart', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/side-cart.png',
				'pro'       => true,
			),
			'storewide-sale' => array(
				'type'      => 'merchant_module',
				'module_id' => 'storewide-sale',
				'title'     => __( 'Storewide Sale', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/sale.png',
				'pro'       => true,
			),
			'buy-x-get-y' => array(
				'type'      => 'merchant_module',
				'module_id' => 'buy-x-get-y',
				'title'     => __( 'Buy X Get Y', 'botiga' ),
				'icon'      => get_template_directory_uri() . '/assets/img/admin/promotion-icons/duplicate-add.png',
				'pro'       => true,
			),
		),
	),
	'recommended-tools' => array(
		'label'    => __( 'Set Up Recommended Tools', 'botiga' ),
		'layout'   => 'recommended-tools',
		'subtitle' => __( 'Trusted plugins from our team to help your site grow.', 'botiga' ),
		'items'    => array(
			'monsterinsights' => array(
				'type'        => 'plugin',
				'title'       => __( 'MonsterInsights', 'botiga' ),
				'description' => __( 'See exactly where your visitors come from and what they do on your site, all inside WordPress.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/monsterinsights.png',
				'plugin_slug' => 'google-analytics-for-wordpress',
				'plugin_name' => 'google-analytics-for-wordpress/googleanalytics.php',
			),
			'reviews-feed' => array(
				'type'        => 'plugin',
				'title'       => __( 'Reviews Feed', 'botiga' ),
				'description' => __( 'Display Google, Yelp, and Tripadvisor reviews on your site to build trust with new visitors.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/smash-balloon.png',
				'plugin_slug' => 'reviews-feed',
				'plugin_name' => 'reviews-feed/sb-reviews.php',
			),
			'optinmonster' => array(
				'type'        => 'plugin',
				'title'       => __( 'OptinMonster', 'botiga' ),
				'description' => __( 'Grow your email list and increase conversions with popups, slide-ins, and targeted campaigns.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/optinmonster.png',
				'plugin_slug' => 'optinmonster',
				'plugin_name' => 'optinmonster/optin-monster-wp-api.php',
			),
			'universally' => array(
				'type'        => 'plugin',
				'title'       => __( 'Universally', 'botiga' ),
				'description' => __( 'Translate your site into languages automatically using AI no manual translation needed.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/universally.png',
				'plugin_slug' => 'universally-language-translation-multilingual-tool',
				'plugin_name' => 'universally-language-translation-multilingual-tool/universally.php',
			),
			'push-engage' => array(
				'type'        => 'plugin',
				'title'       => __( 'Push Engage', 'botiga' ),
				'description' => __( 'Turns abandoned carts into automated recovery campaigns on web push and WhatsApp.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/push-engage.png',
				'plugin_slug' => 'pushengage',
				'plugin_name' => 'pushengage/main.php',
			),
			'easy-digital-downloads' => array(
				'type'        => 'plugin',
				'title'       => __( 'Easy Digital Downloads', 'botiga' ),
				'description' => __( 'Easily sell digital products in WordPress, from eBooks to PDF files to software.', 'botiga' ),
				'icon'        => get_template_directory_uri() . '/assets/img/admin/recommended-tools/easy-digital-downloads.png',
				'plugin_slug' => 'easy-digital-downloads',
				'plugin_name' => 'easy-digital-downloads/easy-digital-downloads.php',
			),
		),
	),
);
