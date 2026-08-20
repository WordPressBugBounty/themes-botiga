<?php
/**
 * Botiga Abilities API access controls.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Access' ) ) {

	/**
	 * Provides access settings for Botiga abilities.
	 */
	class Botiga_Abilities_Access {

		/**
		 * Master abilities option.
		 *
		 * @since 2.4.8
		 *
		 * @var string
		 */
		const ENABLED_OPTION = 'botiga_abilities_enabled';

		/**
		 * Edit abilities option.
		 *
		 * @since 2.4.8
		 *
		 * @var string
		 */
		const EDIT_ENABLED_OPTION = 'botiga_edit_abilities_enabled';

		/**
		 * Checks whether the edit abilities preference is enabled.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		public static function is_edit_enabled() {
			return self::get_boolean_option(
				self::EDIT_ENABLED_OPTION
			);
		}

		/**
		 * Checks whether Botiga abilities are enabled.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		public static function is_enabled() {
			return self::get_boolean_option(
				self::ENABLED_OPTION
			);
		}

		/**
		 * Checks whether Botiga write abilities are enabled.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		public static function can_edit() {
			if ( ! self::is_enabled() ) {
				return false;
			}

			return self::is_edit_enabled();
		}

		/**
		 * Returns a stored option as a boolean.
		 *
		 * @since 2.4.8
		 *
		 * @param string $option_name Option name.
		 *
		 * @return bool
		 */
		private static function get_boolean_option( $option_name ) {
			return in_array(
				get_option(
					$option_name,
					false
				),
				array(
					true,
					1,
					'1',
					'yes',
				),
				true
			);
		}
	}
}
