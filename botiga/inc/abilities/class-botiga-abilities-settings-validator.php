<?php
/**
 * Botiga Abilities settings validator.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Settings_Validator' ) ) {

	/**
	 * Validates and sanitizes Customizer field values.
	 */
	class Botiga_Abilities_Settings_Validator {

		/**
		 * Field schema service.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Field_Schema
		 */
		private $field_schema;

		/**
		 * Constructor.
		 *
		 * @since 2.4.8
		 *
		 * @param Botiga_Abilities_Field_Schema $field_schema Field schema service.
		 */
		public function __construct( $field_schema ) {
			$this->field_schema = $field_schema;
		}

		/**
		 * Validates and sanitizes a direct field value.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 * @param mixed                $value   Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		public function validate_direct_field_value( $control, $value ) {
			$settings = array();

			foreach ( $control->settings as $setting ) {
				if ( $setting instanceof WP_Customize_Setting ) {
					$settings[] = $setting;
				}
			}

			if ( 1 !== count( $settings ) ) {
				return new WP_Error(
					'botiga_field_not_direct',
					__(
						'The requested field contains multiple settings.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			return $this->validate_setting_value(
				$control,
				reset( $settings ),
				$value
			);
		}

		/**
		 * Validates and sanitizes a composite field value.
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
		public function validate_composite_field_value( $control, $values ) {
			if ( ! is_array( $values ) ) {
				return new WP_Error(
					'invalid_type',
					__(
						'The submitted field value must be an object.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			if ( empty( $values ) ) {
				return new WP_Error(
					'botiga_empty_composite_value',
					__(
						'At least one composite setting value must be provided.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			$settings = array();

			foreach ( $control->settings as $setting_key => $setting ) {
				if ( $setting instanceof WP_Customize_Setting ) {
					$settings[ $setting_key ] = $setting;
				}
			}

			if ( count( $settings ) < 2 ) {
				return new WP_Error(
					'botiga_field_not_composite',
					__(
						'The requested field does not contain multiple settings.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			$sanitized_values  = array();
			$validation_errors = array();

			foreach ( $values as $setting_key => $value ) {
				if ( ! isset( $settings[ $setting_key ] ) ) {
					$validation_errors[ $setting_key ] = array(
						'unknown_setting_key',
					);

					continue;
				}

				$result = $this->validate_setting_value(
					$control,
					$settings[ $setting_key ],
					$value
				);

				if ( is_wp_error( $result ) ) {
					$validation_errors[ $setting_key ] =
						$result->get_error_codes();

					continue;
				}

				$sanitized_values[ $setting_key ] = $result;
			}

			if ( ! empty( $validation_errors ) ) {
				return new WP_Error(
					'botiga_invalid_field_value',
					__(
						'One or more submitted field values are invalid.',
						'botiga'
					),
					array(
						'status' => 400,
						'errors' => $validation_errors,
					)
				);
			}

			return $sanitized_values;
		}

		/**
		 * Validates and sanitizes a Customizer setting value.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 * @param WP_Customize_Setting $setting Customizer setting.
		 * @param mixed                $value   Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		private function validate_setting_value( $control, $setting, $value ) {
			$value_type = $this->field_schema->get_value_type(
				$control,
				$setting
			);

			$valid_type = true;

			switch ( $value_type ) {
				case 'boolean':
					$valid_type = is_bool( $value );
					break;

				case 'integer':
					$valid_type = is_int( $value );
					break;

				case 'number':
					$valid_type = is_int( $value ) ||
						is_float( $value );
					break;

				case 'array':
					$valid_type = is_array( $value );
					break;

				default:
					$valid_type = is_string( $value );
					break;
			}

			if ( ! $valid_type ) {
				return new WP_Error(
					'invalid_type',
					__(
						'The submitted value has an invalid type.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			$choices = is_array( $control->choices )
				? $control->choices
				: array();

			$allowed_values = array_keys( $choices );

			if ( ! empty( $allowed_values ) ) {
				$submitted_values = is_array( $value )
					? $value
					: array( $value );

				foreach ( $submitted_values as $submitted_value ) {
					if (
						in_array(
							$submitted_value,
							$allowed_values,
							true
						)
					) {
						continue;
					}

					return new WP_Error(
						'value_not_allowed',
						__(
							'The submitted value is not allowed.',
							'botiga'
						),
						array(
							'status' => 400,
						)
					);
				}
			}

			$input_attrs = is_array( $control->input_attrs )
				? $control->input_attrs
				: array();

			if (
				in_array(
					$value_type,
					array( 'integer', 'number' ),
					true
				)
			) {
				if (
					isset( $input_attrs['min'] ) &&
					$value < (float) $input_attrs['min']
				) {
					return new WP_Error(
						'below_minimum',
						__(
							'The submitted value is below the minimum.',
							'botiga'
						),
						array(
							'status' => 400,
						)
					);
				}

				if (
					isset( $input_attrs['max'] ) &&
					$value > (float) $input_attrs['max']
				) {
					return new WP_Error(
						'above_maximum',
						__(
							'The submitted value exceeds the maximum.',
							'botiga'
						),
						array(
							'status' => 400,
						)
					);
				}
			}

			$validated = $setting->validate( $value );

			if ( is_wp_error( $validated ) ) {
				return $validated;
			}

			if ( false === $validated ) {
				return new WP_Error(
					'invalid_value',
					__(
						'The submitted value failed validation.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			$sanitized = $setting->sanitize( $value );

			if ( null === $sanitized ) {
				return new WP_Error(
					'invalid_value',
					__(
						'The submitted value could not be sanitized.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			return $sanitized;
		}
	}
}
