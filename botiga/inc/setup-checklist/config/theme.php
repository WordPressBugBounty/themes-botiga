<?php
/**
 * Botiga Setup Checklist theme integration configuration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'pro' => array(
		'version_constant'     => 'BOTIGA_PRO_VERSION',
		'plugin_path_callback' => 'botiga_get_pro_plugin_path',
		'activation_label'     => __( 'Activate Botiga Pro', 'botiga' ),
		'upgrade_label'        => __( 'Upgrade', 'botiga' ),
		'upgrade_url'          => function_exists( 'botiga_upgrade_link' )
			? botiga_upgrade_link( 'setup_checklist', 'Setup Checklist Pro Feature' )
			: 'https://athemes.com/botiga-upgrade/',
	),
);
