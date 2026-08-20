<?php
/**
 * Base Botiga ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Ability' ) ) {

	/**
	 * Provides shared dependencies and permissions for Botiga abilities.
	 */
	abstract class Botiga_Ability {

		/**
		 * Prefix used by Botiga Customizer sections.
		 *
		 * @since 2.4.8
		 *
		 * @var string
		 */
		private const SECTION_ID_PREFIX = 'botiga_section_';
		
		/**
		 * Core Customizer sections supported by Botiga abilities.
		 *
		 * @since 2.4.8
		 *
		 * @var string[]
		 */
		private const SUPPORTED_CORE_SECTION_IDS = array(
			'title_tagline',
		);

		/**
		 * Sections provided by Botiga Pro.
		 * 
		 * @since 2.4.8
		 *
		 * @var string[]
		 */
		private const PRO_SECTION_IDS = array(
			'botiga_section_adtcnotif',
			'botiga_section_buy_now',
			'botiga_section_fb_component__button2',
			'botiga_section_fb_component__footer_menu',
			'botiga_section_fb_component__html2',
			'botiga_section_fb_component__shortcode',
			'botiga_section_free_shipping_progress_bar',
			'botiga_section_hb_component__button2',
			'botiga_section_hb_component__html2',
			'botiga_section_hb_component__login_register',
			'botiga_section_hb_component__shortcode',
			'botiga_section_hb_component__shortcode2',
			'botiga_section_hb_component__shortcode3',
			'botiga_section_hooks_content',
			'botiga_section_hooks_footer',
			'botiga_section_hooks_general',
			'botiga_section_hooks_header',
			'botiga_section_hooks_sidebar',
			'botiga_section_hooks_woocommerce_general',
			'botiga_section_hooks_woocommerce_shop_archive',
			'botiga_section_hooks_woocommerce_shop_cart',
			'botiga_section_hooks_woocommerce_shop_checkout',
			'botiga_section_hooks_woocommerce_shop_myaccount',
			'botiga_section_hooks_woocommerce_shop_single',
			'botiga_section_modal_popup',
			'botiga_section_product_swatches',
			'botiga_section_product_swatches_swatch_button',
			'botiga_section_product_swatches_swatch_color',
			'botiga_section_product_swatches_swatch_image',
			'botiga_section_product_swatches_swatch_select',
			'botiga_section_sidebar',
			'botiga_section_single_product_advanced_reviews',
			'botiga_section_single_product_linked_variations',
			'botiga_section_single_product_size_chart',
			'botiga_section_single_product_sticky_add_to_cart',
			'botiga_section_wishlist',
		);

		/**
		 * Settings registry.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Settings_Registry
		 */
		protected $registry;

		/**
		 * Constructor.
		 *
		 * @since 2.4.8
		 *
		 * @param Botiga_Abilities_Settings_Registry $registry Settings registry.
		 */
		public function __construct( $registry ) {
			$this->registry = $registry;
		}

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		abstract public function register();

		/**
		 * Checks whether the current user can manage theme settings.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		public function can_manage_theme() {
			return current_user_can( 'edit_theme_options' );
		}

		/**
		 * Checks whether a Customizer section is supported by Botiga abilities.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return bool
		 */
		protected function is_supported_section( $section_id ) {
			if (
				0 === strpos(
					$section_id,
					self::SECTION_ID_PREFIX
				)
			) {
				return true;
			}

			return in_array(
				$section_id,
				self::SUPPORTED_CORE_SECTION_IDS,
				true
			);
		}

		/**
		 * Checks whether a section requires Botiga Pro.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return bool
		 */
		protected function requires_pro( $section_id ) {
			return in_array(
				$section_id,
				self::PRO_SECTION_IDS,
				true
			);
		}

		/**
		 * Checks whether Botiga Pro is active.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		protected function is_pro_active() {
			return defined( 'BOTIGA_PRO_VERSION' );
		}

		/**
		 * Validates a Customizer section ID.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return true|WP_Error
		 */
		protected function validate_section_id( $section_id ) {
			if ( ! $this->is_supported_section( $section_id ) ) {
				return new WP_Error(
					'botiga_invalid_section',
					__(
						'The requested section is not a supported Botiga settings section.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			if (
				$this->requires_pro( $section_id ) &&
				! $this->is_pro_active()
			) {
				return new WP_Error(
					'botiga_pro_section_unavailable',
					__(
						'The requested section requires Botiga Pro to be active.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			return true;
		}
	}
}
