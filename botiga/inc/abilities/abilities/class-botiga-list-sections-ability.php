<?php
/**
 * Botiga list-sections ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_List_Sections_Ability' ) ) {

	/**
	 * Registers and executes the list-sections ability.
	 */
	class Botiga_List_Sections_Ability extends Botiga_Ability {

		/**
		 * Blog section IDs.
		 *
		 * @var string[]
		 */
		private const BLOG_SECTION_IDS = array(
			'botiga_section_blog_archives',
			'botiga_section_blog_singles',
		);

		/**
		 * WooCommerce section IDs.
		 *
		 * @var string[]
		 */
		private const WOOCOMMERCE_SECTION_IDS = array(
			'botiga_section_adtcnotif',
			'botiga_section_buy_now',
			'botiga_section_catalog_general',
			'botiga_section_free_shipping_progress_bar',
			'botiga_section_hb_component__woo_icons',
			'botiga_section_hooks_woocommerce_general',
			'botiga_section_hooks_woocommerce_shop_archive',
			'botiga_section_hooks_woocommerce_shop_cart',
			'botiga_section_hooks_woocommerce_shop_checkout',
			'botiga_section_hooks_woocommerce_shop_myaccount',
			'botiga_section_hooks_woocommerce_shop_single',
			'botiga_section_product_swatches',
			'botiga_section_product_swatches_swatch_button',
			'botiga_section_product_swatches_swatch_color',
			'botiga_section_product_swatches_swatch_image',
			'botiga_section_product_swatches_swatch_select',
			'botiga_section_shop_archive_categories',
			'botiga_section_shop_archive_product_card',
			'botiga_section_shop_archive_sale_tag',
			'botiga_section_shop_cart',
			'botiga_section_shop_search',
			'botiga_section_single_product_advanced_reviews',
			'botiga_section_single_product_layout',
			'botiga_section_single_product_linked_variations',
			'botiga_section_single_product_size_chart',
			'botiga_section_single_product_sticky_add_to_cart',
			'botiga_section_single_product_tabs',
			'botiga_section_wishlist',
		);

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register() {
			wp_register_ability(
				'botiga/list-sections',
				array(
					'label'               => __(
						'List Botiga Settings Sections',
						'botiga'
					),
					'description'         => __(
						'Returns the configurable Botiga Customizer sections.',
						'botiga'
					),
					'category'            => 'botiga',
					'output_schema'       => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'id'          => array(
									'type'        => 'string',
									'description' => __(
										'Customizer section ID.',
										'botiga'
									),
								),
								'label'       => array(
									'type'        => 'string',
									'description' => __(
										'Human-readable section label.',
										'botiga'
									),
								),
								'category'     => array(
									'type'        => 'string',
									'enum'        => array(
										'site',
										'general',
										'blog',
										'woocommerce',
									),
									'description' => __(
										'High-level settings category.',
										'botiga'
									),
								),
								'requires_pro' => array(
									'type'        => 'boolean',
									'description' => __(
										'Whether the section is provided by Botiga Pro.',
										'botiga'
									),
								),
								'available'    => array(
									'type'        => 'boolean',
									'description' => __(
										'Whether the section is available to the current user and site configuration.',
										'botiga'
									),
								),
								'field_count' => array(
									'type'        => 'integer',
									'minimum'     => 0,
									'description' => __(
										'Number of configurable fields in the section.',
										'botiga'
									),
								),
							),
							'required'   => array(
								'id',
								'label',
								'category',
								'requires_pro',
								'available',
								'field_count',
							),
						),
					),
					'execute_callback'    => array(
						$this,
						'execute',
					),
					'permission_callback' => array(
						$this,
						'can_manage_theme',
					),
					'meta'                => array(
						'public'       => true,
						'annotations'  => array(
							'readonly'    => true,
							'destructive' => false,
							'idempotent'  => true,
						),
						'show_in_rest' => true,
					),
				)
			);
		}

		/**
		 * Returns configurable Botiga sections.
		 *
		 * @since 2.4.8
		 *
		 * @return array|WP_Error
		 */
		public function execute() {
			$manager = $this->registry->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			$field_counts =
				$this->registry->get_section_field_counts();

			if ( is_wp_error( $field_counts ) ) {
				return $field_counts;
			}

			$sections = array();

			foreach ( $manager->sections() as $section ) {
				if (
					! $this->is_supported_section(
						$section->id
					)
				) {
					continue;
				}

				$field_count =
					$field_counts[ $section->id ] ?? 0;

				if ( 0 === $field_count ) {
					continue;
				}

				$requires_pro = $this->requires_pro(
					$section->id
				);

				$available = $section->check_capabilities();

				if (
					$requires_pro &&
					! $this->is_pro_active()
				) {
					$available = false;
				}

				$sections[] = array(
					'id'           => $section->id,
					'label'        => wp_specialchars_decode(
						wp_strip_all_tags( $section->title ),
						ENT_QUOTES
					),
					'category'     => $this->get_section_category(
						$section->id
					),
					'requires_pro' => $requires_pro,
					'available'    => $available,
					'field_count'  => $field_count,
				);
			}

			usort(
				$sections,
				static function( $first, $second ) {
					return strcmp(
						$first['id'],
						$second['id']
					);
				}
			);

			return $sections;
		}

		/**
		 * Returns the high-level category for a section.
		 *
		 * @param string $section_id Customizer section ID.
		 * @return string
		 */
		private function get_section_category( $section_id ) {
			if ( 'title_tagline' === $section_id ) {
				return 'site';
			}

			if (
				in_array(
					$section_id,
					self::BLOG_SECTION_IDS,
					true
				)
			) {
				return 'blog';
			}

			if (
				in_array(
					$section_id,
					self::WOOCOMMERCE_SECTION_IDS,
					true
				)
			) {
				return 'woocommerce';
			}

			return 'general';
		}
	}
}
