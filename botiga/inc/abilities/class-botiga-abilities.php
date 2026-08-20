<?php
/**
 * Botiga Abilities API integration.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities' ) ) {

	/**
	 * Registers Botiga abilities.
	 */
	class Botiga_Abilities {

		/**
		 * Class instance.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities
		 */
		private static $instance;

		/**
		 * Returns the class instance.
		 *
		 * @since 2.4.8
		 *
		 * @return Botiga_Abilities
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
			add_filter(
				'atss_abilities_enabled',
				array(
					'Botiga_Abilities_Access',
					'is_enabled',
				)
			);

			add_filter(
				'atss_abilities_allow_writes',
				array(
					'Botiga_Abilities_Access',
					'can_edit',
				)
			);

			add_action(
				'wp_abilities_api_categories_init',
				array( $this, 'register_categories' )
			);

			add_action(
				'wp_abilities_api_init',
				array( $this, 'register_abilities' )
			);
		}

		/**
		 * Registers Botiga ability categories.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register_categories() {
			if ( ! Botiga_Abilities_Access::is_enabled() ) {
				return;
			}

			wp_register_ability_category(
				'botiga',
				array(
					'label'       => __( 'Botiga', 'botiga' ),
					'description' => __(
						'Abilities for discovering and managing Botiga theme settings.',
						'botiga'
					),
				)
			);
		}

		/**
		 * Registers Botiga abilities.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register_abilities() {
			if ( ! Botiga_Abilities_Access::is_enabled() ) {
				return;
			}

			$registry =
				Botiga_Abilities_Settings_Registry::get_instance();

			$abilities = array(
				new Botiga_Get_Site_Capabilities_Ability(
					$registry
				),
				new Botiga_List_Sections_Ability(
					$registry
				),
				new Botiga_Get_Section_Settings_Ability(
					$registry
				),
			);

			if ( Botiga_Abilities_Access::can_edit() ) {
				$abilities[] = new Botiga_Update_Setting_Ability(
					$registry
				);

				$abilities[] =
					new Botiga_Update_Section_Settings_Ability(
						$registry
					);
			}

			foreach ( $abilities as $ability ) {
				$ability->register();
			}
		}
	}

	Botiga_Abilities::get_instance();
}
