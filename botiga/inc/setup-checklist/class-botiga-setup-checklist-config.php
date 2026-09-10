<?php
/**
 * Botiga Setup Checklist configuration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Config' ) ) {

	/**
	 * Loads theme-specific Setup Checklist configuration.
	 *
	 * The checklist engine intentionally keeps theme-specific task definitions,
	 * Customizer mappings, promotional content, and theme integrations in config
	 * files so sibling themes can reuse the runtime logic with minimal changes.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Config {

		/**
		 * Gets the actionable checklist sections.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_actionable_sections() {
			return self::load_config( 'actionable' );
		}

		/**
		 * Gets the promotional checklist sections.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_promotional_sections() {
			return self::load_config( 'promotions' );
		}

		/**
		 * Gets the Customizer completion mappings.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_customizer_completions() {
			return self::load_config( 'customizer' );
		}

		/**
		 * Gets theme integration settings.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public static function get_theme_config() {
			return self::load_config( 'theme' );
		}

		/**
		 * Builds a Customizer section URL.
		 *
		 * @since 2.4.9
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return string
		 */
		public static function get_customizer_section_url( $section_id ) {
			return add_query_arg(
				'autofocus[section]',
				$section_id,
				admin_url( 'customize.php' )
			);
		}

		/**
		 * Builds a Customizer panel URL.
		 *
		 * @since 2.4.9
		 *
		 * @param string $panel_id Customizer panel ID.
		 *
		 * @return string
		 */
		public static function get_customizer_panel_url( $panel_id ) {
			return add_query_arg(
				'autofocus[panel]',
				$panel_id,
				admin_url( 'customize.php' )
			);
		}

		/**
		 * Builds the Style Guide URL.
		 *
		 * @since 2.4.9
		 *
		 * @return string
		 */
		public static function get_style_guide_url() {
			return add_query_arg(
				'botiga_setup_checklist',
				'style-guide',
				admin_url( 'customize.php' )
			);
		}

		/**
		 * Builds the Review Pages acknowledgement URL.
		 *
		 * @since 2.4.9
		 *
		 * @return string
		 */
		public static function get_review_pages_url() {
			$url = add_query_arg(
				array(
					'post_type'                       => 'page',
					'botiga_setup_checklist_complete' => 'review_pages',
				),
				admin_url( 'edit.php' )
			);

			return wp_nonce_url(
				$url,
				'botiga_setup_checklist_complete_review_pages'
			);
		}

		/**
		 * Loads one Setup Checklist config file.
		 *
		 * @since 2.4.9
		 *
		 * @param string $name Config file name without extension.
		 *
		 * @return array
		 */
		private static function load_config( $name ) {
			$path = get_template_directory() . '/inc/setup-checklist/config/' . sanitize_file_name( $name ) . '.php';

			if ( ! is_readable( $path ) ) {
				return array();
			}

			$config = require $path;

			return is_array( $config ) ? $config : array();
		}
	}
}
