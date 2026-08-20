<?php
/**
 * Botiga get-site-capabilities ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Get_Site_Capabilities_Ability' ) ) {

	/**
	 * Registers and executes the get-site-capabilities ability.
	 */
	class Botiga_Get_Site_Capabilities_Ability extends Botiga_Ability {

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register() {
			wp_register_ability(
				'botiga/get-site-capabilities',
				array(
					'label'               => __(
						'Get Botiga Site Capabilities',
						'botiga'
					),
					'description'         => __(
						'Returns the active Botiga edition, license tier, integrations, and available settings features.',
						'botiga'
					),
					'category'            => 'botiga',
					'output_schema'       => array(
						'type'                 => 'object',
						'properties'           => array(
							'theme'        => array(
								'type'                 => 'object',
								'properties'           => array(
									'slug'    => array(
										'type' => 'string',
									),
									'version' => array(
										'type' => 'string',
									),
								),
								'required'             => array(
									'slug',
									'version',
								),
								'additionalProperties' => false,
							),
							'edition'      => array(
								'type' => 'string',
								'enum' => array(
									'free',
									'pro',
								),
							),
							'license'      => array(
								'type'                 => 'object',
								'properties'           => array(
									'active' => array(
										'type' => 'boolean',
									),
									'tier'   => array(
										'type' => 'string',
										'enum' => array(
											'free',
											'pro',
											'agency',
										),
									),
								),
								'required'             => array(
									'active',
									'tier',
								),
								'additionalProperties' => false,
							),
							'access'       => array(
								'type'                 => 'object',
								'properties'           => array(
									'abilities_enabled' => array(
										'type' => 'boolean',
									),
									'edit_abilities_enabled' => array(
										'type' => 'boolean',
									),
								),
								'required'             => array(
									'abilities_enabled',
									'edit_abilities_enabled',
								),
								'additionalProperties' => false,
							),
							'integrations' => array(
								'type'                 => 'object',
								'properties'           => array(
									'woocommerce' => array(
										'type'       => 'object',
										'properties' => array(
											'active'  => array(
												'type' => 'boolean',
											),
											'version' => array(
												'type' => 'string',
											),
										),
										'required'   => array(
											'active',
											'version',
										),
										'additionalProperties' => false,
									),
									'botiga_pro' => array(
										'type'       => 'object',
										'properties' => array(
											'active'  => array(
												'type' => 'boolean',
											),
											'version' => array(
												'type' => 'string',
											),
										),
										'required'   => array(
											'active',
											'version',
										),
										'additionalProperties' => false,
									),
									'starter_sites' => array(
										'type'       => 'object',
										'properties' => array(
											'active' => array(
												'type' => 'boolean',
											),
										),
										'required'   => array(
											'active',
										),
										'additionalProperties' => false,
									),
								),
								'required'             => array(
									'woocommerce',
									'botiga_pro',
									'starter_sites',
								),
								'additionalProperties' => false,
							),
							'features'     => array(
								'type'                 => 'object',
								'properties'           => array(
									'customizer_settings'  => array(
										'type' => 'boolean',
									),
									'woocommerce_settings' => array(
										'type' => 'boolean',
									),
									'pro_settings'         => array(
										'type' => 'boolean',
									),
								),
								'required'             => array(
									'customizer_settings',
									'woocommerce_settings',
									'pro_settings',
								),
								'additionalProperties' => false,
							),
						),
						'required'             => array(
							'theme',
							'edition',
							'license',
							'access',
							'integrations',
							'features',
						),
						'additionalProperties' => false,
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
		 * Returns the current Botiga site capabilities.
		 *
		 * @since 2.4.8
		 *
		 * @return array
		 */
		public function execute() {
			$botiga_pro_active    = defined( 'BOTIGA_PRO_VERSION' );
			$woocommerce_active   = defined( 'WC_VERSION' ) ||
				class_exists( 'WooCommerce' );
			$starter_sites_active = defined( 'ATSS_PATH' );
			$license_active       = $this->is_license_active(
				$botiga_pro_active
			);

			return array(
				'theme'        => array(
					'slug'    => 'botiga',
					'version' => defined( 'BOTIGA_VERSION' )
						? BOTIGA_VERSION
						: '',
				),
				'edition'      => $botiga_pro_active
					? 'pro'
					: 'free',
				'license'      => array(
					'active' => $license_active,
					'tier'   => $this->get_license_tier(
						$botiga_pro_active,
						$license_active
					),
				),
				'access'       => array(
					'abilities_enabled'      =>
						Botiga_Abilities_Access::is_enabled(),
					'edit_abilities_enabled' =>
						Botiga_Abilities_Access::can_edit(),
				),
				'integrations' => array(
					'woocommerce' => array(
						'active'  => $woocommerce_active,
						'version' => defined( 'WC_VERSION' )
							? WC_VERSION
							: '',
					),
					'botiga_pro' => array(
						'active'  => $botiga_pro_active,
						'version' => $botiga_pro_active
							? BOTIGA_PRO_VERSION
							: '',
					),
					'starter_sites' => array(
						'active' => $starter_sites_active,
					),
				),
				'features'     => array(
					'customizer_settings'  => true,
					'woocommerce_settings' => $woocommerce_active,
					'pro_settings'         => $botiga_pro_active,
				),
			);
		}

		/**
		 * Checks whether the Botiga Pro license is active.
		 *
		 * @since 2.4.8
		 *
		 * @param bool $botiga_pro_active Whether Botiga Pro is active.
		 *
		 * @return bool
		 */
		private function is_license_active( $botiga_pro_active ) {
			if ( ! $botiga_pro_active ) {
				return false;
			}

			if ( function_exists( 'botiga_pro_license_is_active' ) ) {
				return botiga_pro_license_is_active();
			}

			return 'valid' === get_option(
				'botiga_pro_license_status',
				''
			);
		}

		/**
		 * Returns the active Botiga license tier.
		 *
		 * @since 2.4.8
		 *
		 * @param bool $botiga_pro_active Whether Botiga Pro is active.
		 * @param bool $license_active     Whether its license is active.
		 *
		 * @return string
		 */
		private function get_license_tier( $botiga_pro_active, $license_active ) {
			if ( ! $botiga_pro_active ) {
				return 'free';
			}

			if (
				$license_active &&
				$this->is_agency_license()
			) {
				return 'agency';
			}

			return 'pro';
		}

		/**
		 * Checks whether the active license is an Agency license.
		 *
		 * @since 2.4.8
		 *
		 * @return bool
		 */
		private function is_agency_license() {
			if ( function_exists( 'botiga_pro_license_is_agency' ) ) {
				return botiga_pro_license_is_agency();
			}

			$price_id = (string) get_option(
				'botiga_pro_license_price_id',
				''
			);

			$license_limit = (int) get_option(
				'botiga_pro_license_limit',
				0
			);

			$agency_price_ids = array_map(
				'strval',
				(array) apply_filters(
					'botiga_pro_license_agency_price_ids',
					array( '18' )
				)
			);

			$minimum_limit = (int) apply_filters(
				'botiga_pro_license_agency_min_limit',
				100
			);

			$matches_price_id =
				'' !== $price_id &&
				in_array(
					$price_id,
					$agency_price_ids,
					true
				);

			$matches_limit =
				$minimum_limit > 0 &&
				$license_limit >= $minimum_limit;

			return $matches_price_id || $matches_limit;
		}
	}
}
