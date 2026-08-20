<?php
/**
 * Botiga Abilities API dashboard controls.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Admin' ) ) {

	/**
	 * Adds the Botiga abilities dashboard settings.
	 */
	class Botiga_Abilities_Admin {

		/**
		 * Constructor.
		 *
		 * @since 2.4.8
		 */
		public function __construct() {
			add_filter(
				'botiga_dashboard_settings',
				array( $this, 'add_settings_tab' ),
				20
			);
		}

		/**
		 * Adds the AI MCP dashboard settings tab.
		 *
		 * @since 2.4.8
		 *
		 * @param array $settings Dashboard settings.
		 *
		 * @return array
		 */
		public function add_settings_tab( $settings ) {
			if (
				! isset( $settings['settings'] ) ||
				! is_array( $settings['settings'] )
			) {
				return $settings;
			}

			$has_white_label = array_key_exists(
				'white-label',
				$settings['settings']
			);

			$white_label = $has_white_label
				? $settings['settings']['white-label']
				: '';

			if ( $has_white_label ) {
				unset(
					$settings['settings']['white-label']
				);
			}

			$settings['settings']['ai-mcp'] = __(
				'AI MCP',
				'botiga'
			);

			if ( $has_white_label ) {
				$settings['settings']['white-label'] =
					$white_label;
			}

			return $settings;
		}
	}

	new Botiga_Abilities_Admin();
}
