<?php
/**
 * Botiga Abilities Customizer manager.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Customizer_Manager' ) ) {

	/**
	 * Prepares and populates the Customizer manager for abilities.
	 */
	class Botiga_Abilities_Customizer_Manager {

		/**
		 * Class instance.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Customizer_Manager
		 */
		private static $instance;

		/**
		 * Customizer manager.
		 *
		 * @since 2.4.8
		 *
		 * @var WP_Customize_Manager|null
		 */
		private $wp_customize = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		/**
		 * Whether Customizer settings have been registered.
		 *
		 * @since 2.4.8
		 *
		 * @var bool
		 */
		private $registered = false;

		/**
		 * Botiga sections captured during Customizer registration.
		 *
		 * @since 2.4.8
		 *
		 * @var WP_Customize_Section[]
		 */
		private $captured_sections = array();

		/**
		 * Core Site Identity section ID.
		 *
		 * @since 2.4.8
		 *
		 * @var string
		 */
		private const SITE_IDENTITY_SECTION_ID = 'title_tagline';

		/**
		 * Core controls belonging to Site Identity.
		 *
		 * @since 2.4.8
		 *
		 * @var string[]
		 */
		private const SITE_IDENTITY_CONTROL_IDS = array(
			'blogname',
			'blogdescription',
			'display_header_text',
			'custom_logo',
			'site_icon',
		);

		/**
		 * Returns the class instance.
		 *
		 * @since 2.4.8
		 *
		 * @return Botiga_Abilities_Customizer_Manager
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.4.8
		 */
		private function __construct() {
			/*
			 * Only bootstrap the Customizer for an Abilities API REST request.
			 *
			 * The initialization must happen early (before other code decides,
			 * based on the Customizer/preview context, whether to register its
			 * own Customizer controls) so that WooCommerce and the Header/Footer
			 * Builder register the controls the abilities read and reassign.
			 *
			 * It must NOT run on ordinary front-end or admin page loads:
			 * setup_theme() puts the manager into "previewing" state, which
			 * makes is_customize_preview() return true and causes Botiga (and
			 * other code) to render Customizer-only markup such as partial edit
			 * shortcuts on the live site.
			 */
			if ( ! $this->is_abilities_rest_request() ) {
				return;
			}

			add_action(
				'after_setup_theme',
				array( $this, 'prepare' ),
				-9999
			);
		}

		/**
		 * Checks whether the current request targets the Abilities API.
		 *
		 * Runs at construction time, before REST_REQUEST is defined, so it
		 * inspects the requested route/URI for the Abilities API namespace.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		private function is_abilities_rest_request() {
			$target = '';

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['rest_route'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$target = sanitize_text_field(
					wp_unslash( $_GET['rest_route'] )
				);
			}

			if ( '' === $target && isset( $_SERVER['REQUEST_URI'] ) ) {
				$target = sanitize_text_field(
					wp_unslash( $_SERVER['REQUEST_URI'] )
				);
			}

			return '' !== $target &&
				false !== strpos( $target, 'wp-abilities' );
		}

		/**
		 * Prepares the Customizer manager without registering settings.
		 *
		 * Hooked early on after_setup_theme, but only for Abilities API REST
		 * requests (see the constructor). Never runs on a normal page load, so
		 * the manager is never put into preview state on the front end.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function prepare() {
			global $wp_customize;

			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return;
			}

			if ( $wp_customize instanceof WP_Customize_Manager ) {
				$this->wp_customize = $wp_customize;

				return;
			}

			if ( ! class_exists( 'WP_Customize_Manager' ) ) {
				require_once ABSPATH .
					WPINC .
					'/class-wp-customize-manager.php';
			}

			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$wp_customize = new WP_Customize_Manager(
				array(
					'settings_previewed' => false,
				)
			);

			remove_action(
				'wp_loaded',
				array( $wp_customize, 'wp_loaded' )
			);

			$wp_customize->setup_theme();

			$this->wp_customize = $wp_customize;
		}

		/**
		 * Returns the populated Customizer manager.
		 *
		 * @since 2.4.8
		 *
		 * @return WP_Customize_Manager|WP_Error
		 */
		public function get_manager() {
			if ( ! $this->wp_customize instanceof WP_Customize_Manager ) {
				return new WP_Error(
					'botiga_customizer_manager_unavailable',
					__(
						'The Botiga settings registry is unavailable.',
						'botiga'
					)
				);
			}

			if ( $this->registered ) {
				return $this->wp_customize;
			}
			
			$this->registered = true;
			
			/*
			 * Register WordPress core controls before manually firing the
			 * customize_register callbacks. This matches WordPress core's
			 * programmatic Customizer initialization order and ensures that
			 * theme callbacks can access core settings such as blogname,
			 * blogdescription, custom_logo, and site_icon.
			 */
			remove_action(
				'customize_register',
				array(
					$this->wp_customize,
					'register_controls',
				)
			);
			
			$this->wp_customize->register_controls();

			$site_identity_section = $this->wp_customize->get_section(
				self::SITE_IDENTITY_SECTION_ID
			);
			
			add_action(
				'customize_register',
				array( $this, 'capture_sections' ),
				PHP_INT_MAX
			);

			$this->wp_customize->wp_loaded();

			remove_action(
				'customize_register',
				array( $this, 'capture_sections' ),
				PHP_INT_MAX
			);

			$this->restore_sections();

			$this->restore_site_identity_section( $site_identity_section );

			return $this->wp_customize;
		}

		/**
		 * Restores the core Site Identity section for ability discovery.
		 *
		 * Botiga's Header/Footer Builder moves the core controls to its
		 * logo component section. That section is UI-specific and may not
		 * remain registered in the programmatic Customizer manager used by
		 * abilities, leaving the controls attached to a missing section.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Section|null $section Core Site Identity section.
		 *
		 * @return void
		 */
		private function restore_site_identity_section( $section ) {
			if ( ! $section instanceof WP_Customize_Section ) {
				return;
			}

			if (
				! $this->wp_customize->get_section(
					self::SITE_IDENTITY_SECTION_ID
				)
			) {
				$this->wp_customize->add_section(
					$section
				);
			}

			foreach (
				self::SITE_IDENTITY_CONTROL_IDS
				as $control_id
			) {
				$control = $this->wp_customize->get_control(
					$control_id
				);

				if ( ! $control instanceof WP_Customize_Control ) {
					continue;
				}

				$control->section =
					self::SITE_IDENTITY_SECTION_ID;
			}
		}

		/**
		 * Captures registered Botiga sections before they are removed.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Manager $wp_customize Customizer manager.
		 *
		 * @return void
		 */
		public function capture_sections( $wp_customize ) {
			if ( $wp_customize !== $this->wp_customize ) {
				return;
			}

			foreach (
				$wp_customize->sections()
				as $section_id => $section
			) {
				if (
					0 !== strpos(
						$section_id,
						'botiga_section_'
					)
				) {
					continue;
				}

				$this->captured_sections[ $section_id ] =
					$section;
			}
		}

		/**
		 * Restores captured Botiga sections for ability discovery.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		private function restore_sections() {
			foreach (
				$this->captured_sections
				as $section_id => $section
			) {
				if (
					$this->wp_customize->get_section(
						$section_id
					)
				) {
					continue;
				}

				$this->wp_customize->add_section(
					$section
				);
			}
		}
	}
}
