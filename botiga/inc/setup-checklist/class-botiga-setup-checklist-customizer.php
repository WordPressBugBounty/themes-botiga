<?php
/**
 * Botiga Setup Checklist Customizer tracking.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Customizer' ) ) {

	/**
	 * Records checklist completion from Customizer saves.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Customizer {

		/**
		 * Exact Customizer section completion mappings.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $section_completions = array();

		/**
		 * Customizer section prefix completion mappings.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $section_prefix_completions = array();

		/**
		 * Customizer panel completion mappings.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $panel_completions = array();

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		public function __construct() {
			$this->load_completion_config();

			add_action( 'customize_save_after', array( $this, 'record_completions' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_style_guide_launcher' ), 20 );
		}

		/**
		 * Loads the Style Guide launcher for its checklist action.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function enqueue_style_guide_launcher() {
			$target = isset( $_GET['botiga_setup_checklist'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				? sanitize_key( wp_unslash( $_GET['botiga_setup_checklist'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				: '';

			if ( 'style-guide' !== $target ) {
				return;
			}

			wp_enqueue_script(
				'botiga-setup-checklist-customizer',
				get_template_directory_uri() . '/assets/js/admin/botiga-setup-checklist-customizer.min.js',
				array( 'jquery', 'botiga-style-guide' ),
				BOTIGA_VERSION,
				true
			);
		}

		/**
		 * Records completion signals for sections touched by the current save.
		 *
		 * @since 2.4.9
		 *
		 * @param WP_Customize_Manager $wp_customize Customizer manager.
		 *
		 * @return void
		 */
		public function record_completions( $wp_customize ) {
			if ( ! $wp_customize instanceof WP_Customize_Manager ) {
				return;
			}

			$changed_values = $wp_customize->unsanitized_post_values(
				array(
					'exclude_post_data' => true,
					'exclude_changeset' => false,
				)
			);

			if ( empty( $changed_values ) || ! is_array( $changed_values ) ) {
				return;
			}

			$changed_settings = array_fill_keys( array_keys( $changed_values ), true );
			$completions      = array();

			foreach ( $wp_customize->controls() as $control ) {
				if ( ! $this->control_has_changed_setting( $control, $changed_settings ) ) {
					continue;
				}

				$completion = $this->get_completion_for_section(
					$control->section,
					$wp_customize
				);

				if ( '' === $completion ) {
					continue;
				}

				$completions[ $completion ] = true;
			}

			if ( empty( $completions ) ) {
				return;
			}

			$checklist = Botiga_Setup_Checklist::instance();

			foreach ( array_keys( $completions ) as $completion ) {
				$checklist->record_completion( $completion );
			}
		}

		/**
		 * Loads theme-specific Customizer completion mappings.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		private function load_completion_config() {
			$config = Botiga_Setup_Checklist_Config::get_customizer_completions();

			$this->section_completions = isset( $config['sections'] ) && is_array( $config['sections'] )
				? $config['sections']
				: array();
			$this->section_prefix_completions = isset( $config['section_prefixes'] ) && is_array( $config['section_prefixes'] )
				? $config['section_prefixes']
				: array();
			$this->panel_completions = isset( $config['panels'] ) && is_array( $config['panels'] )
				? $config['panels']
				: array();
		}

		/**
		 * Checks whether a control references a changed setting.
		 *
		 * @since 2.4.9
		 *
		 * @param WP_Customize_Control $control          Customizer control.
		 * @param array                $changed_settings Changed setting lookup.
		 *
		 * @return bool
		 */
		private function control_has_changed_setting( $control, $changed_settings ) {
			if ( empty( $control->settings ) || ! is_array( $control->settings ) ) {
				return false;
			}

			foreach ( $control->settings as $setting ) {
				$setting_id = $setting instanceof WP_Customize_Setting
					? $setting->id
					: (string) $setting;

				if ( isset( $changed_settings[ $setting_id ] ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Maps a Customizer section to a checklist completion signal.
		 *
		 * @since 2.4.9
		 *
		 * @param string               $section_id   Customizer section ID.
		 * @param WP_Customize_Manager $wp_customize Customizer manager.
		 *
		 * @return string
		 */
		private function get_completion_for_section( $section_id, $wp_customize ) {
			$section_id = (string) $section_id;

			if ( isset( $this->section_completions[ $section_id ] ) ) {
				return $this->section_completions[ $section_id ];
			}

			foreach ( $this->section_prefix_completions as $prefix => $completion ) {
				if ( 0 === strpos( $section_id, $prefix ) ) {
					return $completion;
				}
			}

			$section = $wp_customize->get_section( $section_id );

			if ( ! $section || empty( $section->panel ) ) {
				return '';
			}

			$panel_id = (string) $section->panel;

			return isset( $this->panel_completions[ $panel_id ] )
				? $this->panel_completions[ $panel_id ]
				: '';
		}
	}
}

new Botiga_Setup_Checklist_Customizer();
