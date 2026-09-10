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

if ( ! class_exists( 'Botiga_Setup_Checklist_Banner' ) ) {

	/**
	 * Builds the starter-site banner displayed on the Setup Checklist.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Banner {

		/**
		 * Gets the banner display data.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_data() {
			$user          = wp_get_current_user();
			$starter_sites = Botiga_Starter_Sites::get_state();
			$action        = $starter_sites['action'];
			$action_url    = 'customize' === $action
				? $starter_sites['customize_url']
				: $starter_sites['wizard_url'];
			$action_label  = __( 'Start Building With Templates', 'botiga' );

			if ( 'resume' === $action ) {
				$action_label = __( 'Resume Site Wizard', 'botiga' );
			} elseif ( 'customize' === $action ) {
				$action_label = __( 'Customize', 'botiga' );
			}

			return array(
				'user_name'     => $user->display_name,
				'title'         => __( 'Welcome to Botiga', 'botiga' ),
				'description'   => __( 'Botiga is now installed and ready to go. To help you with the next step, we’ve gathered together on this page all the resources you might need. We hope you enjoy using Botiga.', 'botiga' ),
				'badge'         => defined( 'BOTIGA_PRO_VERSION' ) ? __( 'PRO', 'botiga' ) : __( 'FREE', 'botiga' ),
				'is_pro'        => defined( 'BOTIGA_PRO_VERSION' ),
				'image_url'     => get_template_directory_uri() . '/assets/img/dashboard/welcome-banner@2x.png',
				'action_label'  => $action_label,
				'action_url'    => $action_url,
				'plugin_action' => $starter_sites['plugin_action'],
				'plugin_slug'   => $starter_sites['plugin_slug'],
				'plugin_name'   => $starter_sites['plugin_name'],
			);
		}
	}
}
