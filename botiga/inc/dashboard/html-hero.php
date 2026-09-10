<?php

/**
 *
 * Hero
 * @package Dashboard
 *
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

global $pagenow;

$screen = get_current_screen(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$user   = wp_get_current_user(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$is_products_filter_page = isset( $_GET['tab'] ) && 'products-filter' === $_GET['tab'] ? true : false;
$is_templates_builder_page = isset( $_GET['tab'] ) && 'templates-builder' === $_GET['tab'] ? true : false;
$starter_sites = Botiga_Starter_Sites::get_state(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$starter_action = $starter_sites['action']; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

if ( $is_products_filter_page || $is_templates_builder_page ) {
	return;
}

$starter_actions = array(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$starter_notice  = ''; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

if ( in_array( $starter_action, array( 'install', 'activate' ), true ) ) {
	$starter_actions[] = array(
		'label'      => 'activate' === $starter_action ? __( 'Activate', 'botiga' ) : __( 'Let’s Get Started', 'botiga' ),
		'url'        => $starter_sites['wizard_url'],
		'class'      => 'button button-primary botiga-dashboard-plugin-ajax-button botiga-ajax-success-redirect',
		'attributes' => array(
			'data-type' => $starter_action,
			'data-path' => $starter_sites['plugin_name'],
			'data-slug' => $starter_sites['plugin_slug'],
		),
	);

	if ( 'install' === $starter_action ) {
		$starter_notice = __( 'Clicking "Let’s Get Started" button will install and activate the Botiga "aThemes Starter Sites" plugin.', 'botiga' );
	}
} elseif ( 'start' === $starter_action ) {
	$starter_actions[] = array(
		'label' => __( 'Let’s Get Started', 'botiga' ),
		'url'   => $starter_sites['wizard_url'],
		'class' => 'button button-primary botiga-dashboard-hero-button',
	);
} elseif ( 'resume' === $starter_action ) {
	$starter_actions[] = array(
		'label' => __( 'Resume wizard', 'botiga' ),
		'url'   => $starter_sites['wizard_url'],
		'class' => 'button button-primary botiga-dashboard-hero-button',
	);
} else {
	$starter_actions[] = array(
		'label'  => __( 'Start Customizing', 'botiga' ),
		'url'    => $starter_sites['customize_url'],
		'class'  => 'button button-primary',
		'target' => '_blank',
	);

	if ( $this->settings['menu_slug'] !== ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' ) ) {
		$starter_actions[] = array(
			'label' => __( 'Theme Dashboard', 'botiga' ),
			'url'   => add_query_arg( 'page', $this->settings['menu_slug'], admin_url( 'admin.php' ) ),
			'class' => 'button button-secondary',
		);
	}
}

$starter_banner = array( // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	'root_tag'               => 'div',
	'greeting'               => __( 'Hello, ', 'botiga' ) . $user->display_name . __( '👋🏻', 'botiga' ),
	'title'                  => $this->settings['hero_title'],
	'title_allow_html'       => true,
	'description'            => $this->settings['hero_desc'],
	'description_allow_html' => true,
	'badge'                  => array(
		'label' => $this->settings['has_pro'] ? 'pro' : 'free',
		'tag'   => 'sup',
		'class' => 'botiga-dashboard-hero-badge ' . ( $this->settings['has_pro'] ? 'botiga-dashboard-hero-badge-pro' : 'botiga-dashboard-hero-badge-free' ),
	),
	'actions'                => $starter_actions,
	'notice'                 => $starter_notice,
	'image_url'              => $this->settings['hero_image'],
	'classes'                => array(
		'root'        => 'botiga-dashboard-hero',
		'content'     => 'botiga-dashboard-hero-content',
		'greeting'    => 'botiga-dashboard-hero-hello',
		'heading'     => 'botiga-dashboard-hero-title',
		'description' => 'botiga-dashboard-hero-desc',
		'actions'     => 'botiga-dashboard-hero-actions',
		'notice'      => 'botiga-dashboard-hero-notion',
		'image'       => 'botiga-dashboard-hero-image',
	),
);

require get_template_directory() . '/inc/components/starter-banner.php';
