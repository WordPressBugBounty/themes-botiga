<?php
/**
 * Botiga Setup Checklist plugin state helpers.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Plugins' ) ) {

	/**
	 * Provides plugin installation and activation state for the Setup Checklist.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Plugins {

		/**
		 * Cached active states for the current request.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private static $active = array();

		/**
		 * Cached installation states for the current request.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private static $installed = array();

		/**
		 * Checks whether a plugin is active for the site or network.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_name Plugin basename.
		 *
		 * @return bool
		 */
		public static function is_active( $plugin_name ) {
			if ( '' === $plugin_name ) {
				return false;
			}

			if ( array_key_exists( $plugin_name, self::$active ) ) {
				return self::$active[ $plugin_name ];
			}

			self::load_plugin_functions();

			self::$active[ $plugin_name ] = is_plugin_active( $plugin_name ) || is_plugin_active_for_network( $plugin_name );

			return self::$active[ $plugin_name ];
		}

		/**
		 * Checks whether WordPress recognizes a plugin as installed.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_name Plugin basename.
		 *
		 * @return bool
		 */
		public static function is_installed( $plugin_name ) {
			if ( '' === $plugin_name ) {
				return false;
			}

			if ( array_key_exists( $plugin_name, self::$installed ) ) {
				return self::$installed[ $plugin_name ];
			}

			self::load_plugin_functions();

			self::$installed[ $plugin_name ] = array_key_exists( $plugin_name, get_plugins() );

			return self::$installed[ $plugin_name ];
		}

		/**
		 * Finds an installed plugin by its main file name.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_file Plugin main file name.
		 *
		 * @return string
		 */
		public static function find_by_file( $plugin_file ) {
			if ( '' === $plugin_file ) {
				return '';
			}

			self::load_plugin_functions();

			foreach ( array_keys( get_plugins() ) as $plugin_name ) {
				if ( 0 === strcasecmp( basename( $plugin_name ), $plugin_file ) ) {
					return $plugin_name;
				}
			}

			return '';
		}

		/**
		 * Gets the current plugin state.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_name Plugin basename.
		 *
		 * @return string
		 */
		public static function get_status( $plugin_name ) {
			if ( self::is_active( $plugin_name ) ) {
				return 'active';
			}

			if ( self::is_installed( $plugin_name ) ) {
				return 'inactive';
			}

			return 'not_installed';
		}

		/**
		 * Loads WordPress plugin helper functions when needed.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		private static function load_plugin_functions() {
			if ( function_exists( 'is_plugin_active' ) ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}
}
