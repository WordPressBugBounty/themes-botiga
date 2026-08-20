<?php
/**
 * Botiga Abilities settings updater.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Settings_Updater' ) ) {

	/**
	 * Validates and saves Customizer field values.
	 */
	class Botiga_Abilities_Settings_Updater {

		/**
		 * Customizer manager service.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Customizer_Manager
		 */
		private $customizer_manager;

		/**
		 * Field schema service.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Field_Schema
		 */
		private $field_schema;

		/**
		 * Settings validator service.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Settings_Validator
		 */
		private $settings_validator;

		/**
		 * Constructor.
		 *
		 * @since 2.4.8
		 *
		 * @param Botiga_Abilities_Customizer_Manager $customizer_manager Customizer manager.
		 * @param Botiga_Abilities_Field_Schema       $field_schema       Field schema service.
		 * @param Botiga_Abilities_Settings_Validator $settings_validator Settings validator.
		 */
		public function __construct( $customizer_manager, $field_schema, $settings_validator ) {
			$this->customizer_manager = $customizer_manager;
			$this->field_schema       = $field_schema;
			$this->settings_validator = $settings_validator;
		}

		/**
		 * Validates and saves a direct field value.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 * @param mixed                $value   Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		public function update_direct_field_value( $control, $value ) {
			$sanitized = $this->settings_validator->validate_direct_field_value(
				$control,
				$value
			);

			if ( is_wp_error( $sanitized ) ) {
				return $sanitized;
			}

			$settings = $this->get_control_settings( $control );
			$setting  = reset( $settings );

			if ( ! current_user_can( $setting->capability ) ) {
				return new WP_Error(
					'botiga_setting_forbidden',
					__(
						'You are not allowed to update this setting.',
						'botiga'
					),
					array(
						'status' => 403,
					)
				);
			}

			$manager = $this->customizer_manager->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			$manager->set_post_value(
				$setting->id,
				$value
			);

			if ( false === $setting->save() ) {
				return new WP_Error(
					'botiga_setting_save_failed',
					__(
						'The setting could not be saved.',
						'botiga'
					),
					array(
						'status' => 500,
					)
				);
			}

			return $this->field_schema->normalize_value(
				$sanitized,
				$this->field_schema->get_value_type(
					$control,
					$setting
				)
			);
		}

		/**
		 * Validates and saves composite field values.
		 *
		 * Partial updates are allowed.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 * @param mixed                $values  Submitted setting values.
		 *
		 * @return array|WP_Error
		 */
		public function update_composite_field_value( $control, $values ) {
			$sanitized_values =
				$this->settings_validator->validate_composite_field_value(
					$control,
					$values
				);

			if ( is_wp_error( $sanitized_values ) ) {
				return $sanitized_values;
			}

			$settings = $this->get_control_settings( $control );

			foreach ( $sanitized_values as $setting_key => $sanitized_value ) {
				$setting = $settings[ $setting_key ];

				if ( current_user_can( $setting->capability ) ) {
					continue;
				}

				return new WP_Error(
					'botiga_setting_forbidden',
					__(
						'You are not allowed to update one or more settings.',
						'botiga'
					),
					array(
						'status' => 403,
					)
				);
			}

			$manager = $this->customizer_manager->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			foreach ( $sanitized_values as $setting_key => $sanitized_value ) {
				$setting = $settings[ $setting_key ];

				$manager->set_post_value(
					$setting->id,
					$values[ $setting_key ]
				);
			}

			$normalized_values = array();

			foreach ( $sanitized_values as $setting_key => $sanitized_value ) {
				$setting = $settings[ $setting_key ];

				if ( false === $setting->save() ) {
					return new WP_Error(
						'botiga_setting_save_failed',
						__(
							'One or more settings could not be saved.',
							'botiga'
						),
						array(
							'status' => 500,
						)
					);
				}

				$normalized_values[ $setting_key ] =
					$this->field_schema->normalize_value(
						$sanitized_value,
						$this->field_schema->get_value_type(
							$control,
							$setting
						)
					);
			}

			return $normalized_values;
		}

		/**
		 * Returns valid settings belonging to a control.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 *
		 * @return WP_Customize_Setting[]
		 */
		private function get_control_settings( $control ) {
			$settings = array();

			foreach ( $control->settings as $setting_key => $setting ) {
				if ( ! $setting instanceof WP_Customize_Setting ) {
					continue;
				}

				$settings[ $setting_key ] = $setting;
			}

			return $settings;
		}
	}
}
