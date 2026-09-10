<?php
/**
 * Botiga Setup Checklist actionable task configuration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'basics' => array(
		'label' => __( 'Set up the Basics', 'botiga' ),
		'items' => array(
			'homepage' => array(
				'title'        => __( 'Set Up Your Homepage', 'botiga' ),
				'description'  => __( 'Set a dedicated homepage so visitors land exactly where you want them.', 'botiga' ),
				'completion'   => 'homepage',
				'action_label' => __( 'Edit Homepage', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'static_front_page' ),
			),
			'navigation-menu' => array(
				'title'        => __( 'Set Up Navigation Menu', 'botiga' ),
				'description'  => __( 'Guide visitors to the right places with a clear, well-organized menu.', 'botiga' ),
				'completion'   => 'navigation_menu',
				'action_label' => __( 'Setup Menu', 'botiga' ),
				'action_url'   => admin_url( 'nav-menus.php' ),
			),
			'header' => array(
				'title'        => __( 'Customize Header', 'botiga' ),
				'description'  => __( 'Set the layout of your header, logo position, navigation, buttons, and more.', 'botiga' ),
				'completion'   => 'header_customized',
				'action_label' => __( 'Customize Header', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'botiga_section_hb_wrapper' ),
			),
			'footer' => array(
				'title'        => __( 'Customize Footer', 'botiga' ),
				'description'  => __( 'Add social links, contact info, copyright text, or widgets to build a professional closing section.', 'botiga' ),
				'completion'   => 'footer_customized',
				'action_label' => __( 'Customize Footer', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'botiga_section_fb_wrapper' ),
			),
			'contact-form' => array(
				'title'        => __( 'Set Up a Contact Form', 'botiga' ),
				'description'  => __( 'Add a contact form so visitors can reach you directly. Set up spam protection and email notifications.', 'botiga' ),
				'completion'   => 'contact_form',
				'action_label' => __( 'Install WPForms', 'botiga' ),
				'plugin_slug'  => 'wpforms-lite',
				'plugin_name'  => 'wpforms-lite/wpforms.php',
			),
			'review-pages' => array(
				'title'        => __( 'Review Your Key Pages', 'botiga' ),
				'description'  => __( 'Check that your key pages: About, Services, Contact are in place and ready.', 'botiga' ),
				'completion'   => 'review_pages',
				'action_label' => __( 'Review Pages', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_review_pages_url(),
			),
			'woocommerce-onboarding' => array(
				'title'                => __( 'Complete WooCommerce Onboarding', 'botiga' ),
				'description'          => __( 'Walk through WooCommerce setup to configure your store location, currency, and payment basics.', 'botiga' ),
				'completion'           => 'woocommerce_onboarding',
				'action_label'         => __( 'Complete Onboarding', 'botiga' ),
				'action_url'           => add_query_arg(
					array(
						'page' => 'wc-admin',
						'path' => '/setup-wizard',
					),
					admin_url( 'admin.php' )
				),
				'requires_woocommerce' => true,
			),
		),
	),
	'design' => array(
		'label' => __( 'Design Your Store', 'botiga' ),
		'items' => array(
			'brand-style' => array(
				'title'        => __( 'Update Your Brand Style', 'botiga' ),
				'description'  => __( 'Set your default colors, fonts, and design rules so every page looks consistent.', 'botiga' ),
				'completion'   => 'brand_style_customized',
				'action_label' => __( 'Update Style Guide', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_style_guide_url(),
			),
			'shop-page' => array(
				'title'                => __( 'Customize Shop Page', 'botiga' ),
				'description'          => __( 'Control how your product grid looks, columns, spacing, card style, and what information to display.', 'botiga' ),
				'completion'           => 'shop_customized',
				'action_label'         => __( 'Customize Shop', 'botiga' ),
				'action_url'           => Botiga_Setup_Checklist_Config::get_customizer_panel_url( 'botiga_panel_shop_archive' ),
				'requires_woocommerce' => true,
			),
			'product-page' => array(
				'title'                => __( 'Customize Product Page', 'botiga' ),
				'description'          => __( 'Design your individual product page, layout, image gallery style, add-to-cart placement, and more.', 'botiga' ),
				'completion'           => 'product_customized',
				'action_label'         => __( 'Customize Product', 'botiga' ),
				'action_url'           => Botiga_Setup_Checklist_Config::get_customizer_panel_url( 'botiga_panel_single_product' ),
				'requires_woocommerce' => true,
			),
			'cart-page' => array(
				'title'                => __( 'Customize Cart Page', 'botiga' ),
				'description'          => __( 'Style your cart page to match your store design and reduce drop-off before checkout.', 'botiga' ),
				'completion'           => 'cart_customized',
				'action_label'         => __( 'Customize Cart', 'botiga' ),
				'action_url'           => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'botiga_section_shop_cart' ),
				'requires_woocommerce' => true,
			),
			'page-layout' => array(
				'title'        => __( 'Set Up Your Page Layout', 'botiga' ),
				'description'  => __( 'Set the default layout for your inner pages, full width, sidebar left, or sidebar right.', 'botiga' ),
				'completion'   => 'page_layout_customized',
				'action_label' => __( 'Customize Pages', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'botiga_section_layout' ),
			),
			'blog-options' => array(
				'title'        => __( 'Customize Blog Options', 'botiga' ),
				'description'  => __( 'Control how your blog posts appear: layout, style, width, and more.', 'botiga' ),
				'completion'   => 'blog_customized',
				'action_label' => __( 'Customize Blog', 'botiga' ),
				'action_url'   => Botiga_Setup_Checklist_Config::get_customizer_section_url( 'botiga_section_blog_archives' ),
			),
		),
	),
	'launch' => array(
		'label' => __( 'Get Ready to Launch', 'botiga' ),
		'items' => array(
			'email-delivery' => array(
				'title'        => __( 'Make Sure Your Emails Deliver', 'botiga' ),
				'description'  => __( 'Make sure your WordPress emails always reach the inbox and more.', 'botiga' ),
				'completion'   => 'email_delivery',
				'action_label' => __( 'Install WP Mail SMTP', 'botiga' ),
				'plugin_slug'  => 'wp-mail-smtp',
				'plugin_name'  => 'wp-mail-smtp/wp_mail_smtp.php',
			),
			'site-backup' => array(
				'title'        => __( 'Enable Site Backup', 'botiga' ),
				'description'  => __( 'Set up automatic backups so your site is always recoverable if something goes wrong.', 'botiga' ),
				'completion'   => 'site_backup',
				'action_label' => __( 'Install Duplicator', 'botiga' ),
				'plugin_slug'  => 'duplicator',
				'plugin_name'  => 'duplicator/duplicator.php',
			),
			'seo' => array(
				'title'        => __( 'Improve Your SEO', 'botiga' ),
				'description'  => __( 'Improve your rankings, set meta titles and descriptions, and get found on Google.', 'botiga' ),
				'completion'   => 'seo',
				'action_label' => __( 'Install AIOSEO', 'botiga' ),
				'plugin_slug'  => 'all-in-one-seo-pack',
				'plugin_name'  => 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			),
			'privacy-compliance' => array(
				'title'        => __( 'Set Up Privacy Compliance', 'botiga' ),
				'description'  => __( 'Add a cookie consent banner and manage GDPR, CCPA, and privacy compliance for your visitors.', 'botiga' ),
				'completion'   => 'privacy_compliance',
				'action_label' => __( 'Install WPConsent', 'botiga' ),
				'plugin_slug'  => 'wpconsent-cookies-banner-privacy-suite',
				'plugin_name'  => 'wpconsent-cookies-banner-privacy-suite/wpconsent.php',
			),
		),
	),
);
