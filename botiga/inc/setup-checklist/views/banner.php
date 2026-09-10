<?php
/**
 * Botiga Setup Checklist starter banner.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$banner             = Botiga_Setup_Checklist_Banner::get_data();
$badge_class        = 'botiga-setup-checklist__banner-badge' . ( $banner['is_pro'] ? ' is-pro' : '' );
$can_manage_plugins = current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
$banner_action      = array(
	'label' => $banner['action_label'],
	'url'   => $banner['action_url'],
	'class' => 'button button-primary',
);

if ( $banner['plugin_action'] ) {
	$banner_action = array();

	if ( $can_manage_plugins ) {
		$banner_action = array(
			'type'       => 'button',
			'label'      => $banner['action_label'],
			'class'      => 'button button-primary botiga-install-plugin',
			'attributes' => array(
				'data-plugin-slug'   => $banner['plugin_slug'],
				'data-plugin-name'   => $banner['plugin_name'],
				'data-plugin-action' => $banner['plugin_action'],
				'data-redirect-to'   => $banner['action_url'],
			),
		);
	}
}

$starter_banner = array(
	'root_tag'          => 'section',
	'greeting'          => sprintf(
		/* translators: %s: current user's display name. */
		__( 'Hello, %s 👋', 'botiga' ),
		$banner['user_name']
	),
	'title'             => $banner['title'],
	'title_tag'         => 'h2',
	'description'       => $banner['description'],
	'description_tag'   => 'p',
	'badge'             => array(
		'label' => $banner['badge'],
		'class' => $badge_class,
	),
	'actions'           => $banner_action ? array( $banner_action ) : array(),
	'image_url'         => $banner['image_url'],
	'image_alt'         => '',
	'image_aria_hidden' => true,
	'classes'           => array(
		'root'        => 'botiga-setup-checklist__banner',
		'content'     => 'botiga-setup-checklist__banner-content',
		'greeting'    => 'botiga-setup-checklist__banner-greeting',
		'heading'     => 'botiga-setup-checklist__banner-heading',
		'description' => 'botiga-setup-checklist__banner-description',
		'actions'     => 'botiga-setup-checklist__banner-actions',
		'image'       => 'botiga-setup-checklist__banner-image',
	),
);

require get_template_directory() . '/inc/components/starter-banner.php';
