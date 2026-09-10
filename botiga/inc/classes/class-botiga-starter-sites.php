<?php
/**
 * Botiga Starter Sites state.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Starter_Sites' ) ) {

	/**
	 * Provides shared Starter Sites state for Botiga admin experiences.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Starter_Sites {

		/**
		 * Starter Sites plugin slug.
		 *
		 * @since 2.4.9
		 */
		const PLUGIN_SLUG = 'athemes-starter-sites';

		/**
		 * Starter Sites plugin basename.
		 *
		 * @since 2.4.9
		 */
		const PLUGIN_NAME = 'athemes-starter-sites/athemes-starter-sites.php';

		/**
		 * Gets the current Starter Sites state.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_state() {
			$plugin_status       = self::get_plugin_status();
			$has_wizard_state    = (bool) get_option( 'atss_wizard_state' );
			$has_current_starter = (bool) get_option( 'atss_current_starter' );
			$action              = self::get_action( $plugin_status, $has_wizard_state, $has_current_starter );

			return array(
				'plugin_slug'         => self::PLUGIN_SLUG,
				'plugin_name'         => self::PLUGIN_NAME,
				'plugin_status'       => $plugin_status,
				'action'              => $action,
				'plugin_action'       => in_array( $action, array( 'install', 'activate' ), true ) ? $action : '',
				'has_wizard_state'    => $has_wizard_state,
				'has_current_starter' => $has_current_starter,
				'wizard_url'          => add_query_arg( 'page', 'atss-onboarding-wizard', admin_url( 'admin.php' ) ),
				'customize_url'       => admin_url( 'customize.php' ),
			);
		}

		/**
		 * Gets the current Starter Sites plugin status.
		 *
		 * @since 2.4.9
		 *
		 * @return string
		 */
		private static function get_plugin_status() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( self::PLUGIN_NAME ) ) {
				return 'active';
			}

			if ( file_exists( WP_PLUGIN_DIR . '/' . self::PLUGIN_NAME ) ) {
				return 'inactive';
			}

			return 'not_installed';
		}

		/**
		 * Resolves the primary Starter Sites action.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_status       Starter Sites plugin status.
		 * @param bool   $has_wizard_state    Whether the onboarding wizard has saved state.
		 * @param bool   $has_current_starter Whether a starter site has been imported.
		 *
		 * @return string
		 */
		private static function get_action( $plugin_status, $has_wizard_state, $has_current_starter ) {
			if ( 'active' !== $plugin_status ) {
				return 'inactive' === $plugin_status ? 'activate' : 'install';
			}

			if ( $has_current_starter ) {
				return 'customize';
			}

			if ( $has_wizard_state ) {
				return 'resume';
			}

			return 'start';
		}
	}
}
