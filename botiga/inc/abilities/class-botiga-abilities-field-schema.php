<?php
/**
 * Botiga Abilities field schema.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Field_Schema' ) ) {

	/**
	 * Converts Customizer controls and settings into ability field data.
	 */
	class Botiga_Abilities_Field_Schema {

		/**
		 * Individual controls that require a dedicated value adapter.
		 *
		 * @var string[]
		 */
		private const READ_ONLY_CONTROL_IDS = array(
			'display_header_text',
		);

		/**
		 * Controls whose values need a dedicated normalizer before they can be
		 * updated reliably through an ability.
		 *
		 * @var string[]
		 */
		private const READ_ONLY_CONTROL_TYPES = array(
			'botiga-sortable_repeater',
			'botiga-google_fonts',
			'botiga-adobe_fonts',
			'botiga-custom-fonts-control',
			'botiga-typography-custom-control',
			'botiga-dimensions-control',
			'botiga-display-conditions-control',
			'botiga-custom-sidebars-control',
			'media',
			'image',
			'cropped_image',
			'upload',
			'site_icon',
		);

		/**
		 * Builds the ability field data for a Customizer control.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 *
		 * @return array|null
		 */
		public function get_field_data( $control ) {
			$settings = $this->get_settings_data( $control );

			if ( empty( $settings ) ) {
				return null;
			}

			$choices = is_array( $control->choices )
				? $control->choices
				: array();

			$input_attrs = is_array( $control->input_attrs )
				? $control->input_attrs
				: array();

			$support = $this->get_field_support(
				$control,
				$settings
			);

			return array(
				'id'             => $control->id,
				'label'          => wp_specialchars_decode(
					wp_strip_all_tags( $control->label ),
					ENT_QUOTES
				),
				'description'    => wp_specialchars_decode(
					wp_strip_all_tags( $control->description ),
					ENT_QUOTES
				),
				'control_type'   => $control->type,
				'kind'           => count( $settings ) > 1
					? 'composite'
					: 'direct',
				'value_shape'    => $support['value_shape'],
				'support'        => array(
					'readable'     => true,
					'writable'     => $support['writable'],
					'write_reason' => $support['write_reason'],
				),
				'settings'       => $settings,
				'allowed_values' => array_keys( $choices ),
				'choices'        => $choices,
				'input_attrs'    => $input_attrs,
			);
		}

		/**
		 * Returns normalized setting data for a control.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 *
		 * @return array
		 */
		private function get_settings_data( $control ) {
			$settings = array();

			foreach ( $control->settings as $setting_key => $setting ) {
				if ( ! $setting instanceof WP_Customize_Setting ) {
					continue;
				}

				$value_type = $this->get_value_type(
					$control,
					$setting
				);

				$settings[ $setting_key ] = array(
					'id'                => $setting->id,
					'type'              => $value_type,
					'storage_type'      => $setting->type,
					'capability'        => $setting->capability,
					'default'           => $this->normalize_value(
						$setting->default,
						$value_type
					),
					'value'             => $this->normalize_value(
						$setting->value(),
						$value_type
					),
					'sanitize_callback' => $this->normalize_callback(
						$setting->sanitize_callback
					),
					'validate_callback' => $this->normalize_callback(
						$setting->validate_callback
					),
				);
			}

			return $settings;
		}

		/**
		 * Determines the expected value type for a setting.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 * @param WP_Customize_Setting $setting Customizer setting.
		 *
		 * @return string
		 */
		public function get_value_type( $control, $setting ) {
			if ( 'botiga-toggle-control' === $control->type ) {
				return 'boolean';
			}

			if (
				in_array(
					$control->type,
					array( 'number', 'range' ),
					true
				)
			) {
				return 'number';
			}

			$sanitize_callback = $this->normalize_callback(
				$setting->sanitize_callback
			);

			if (
				in_array(
					$sanitize_callback,
					array( 'absint', 'intval' ),
					true
				)
			) {
				return 'integer';
			}

			if ( is_array( $setting->default ) ) {
				return 'array';
			}

			if ( is_float( $setting->default ) ) {
				return 'number';
			}

			if ( is_int( $setting->default ) ) {
				return 'integer';
			}

			return 'string';
		}

		/**
		 * Returns the read and write support state for a field.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control  Customizer control.
		 * @param array|null           $settings Normalized settings data.
		 *
		 * @return array
		 */
		public function get_field_support( $control, $settings = null ) {
			if ( null === $settings ) {
				$settings = $this->get_settings_data( $control );
			}

			if ( empty( $settings ) ) {
				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This field does not contain a configurable setting.',
						'botiga'
					),
				);
			}

			if (
				in_array(
					$control->id,
					self::READ_ONLY_CONTROL_IDS,
					true
				)
			) {
				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This field uses a specialized core value mapping that requires a dedicated update schema.',
						'botiga'
					),
				);
			}

			if (
				in_array(
					$control->type,
					self::READ_ONLY_CONTROL_TYPES,
					true
				)
			) {
				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This field uses a complex value format that is available for reading but is not yet supported for updates.',
						'botiga'
					),
				);
			}

			if ( $this->contains_structured_json( $settings ) ) {
				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This field stores structured data that requires a dedicated update schema.',
						'botiga'
					),
				);
			}

			if ( 1 === count( $settings ) ) {
				$setting = reset( $settings );

				if ( 'array' !== $setting['type'] ) {
					return array(
						'writable'     => true,
						'value_shape'  => 'scalar',
						'write_reason' => null,
					);
				}

				$choices = is_array( $control->choices )
					? $control->choices
					: array();

				if ( ! empty( $choices ) ) {
					return array(
						'writable'     => true,
						'value_shape'  => 'list',
						'write_reason' => null,
					);
				}

				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This array field does not provide a reliable list of supported values.',
						'botiga'
					),
				);
			}

			foreach ( $settings as $setting ) {
				if ( 'array' !== $setting['type'] ) {
					continue;
				}

				return array(
					'writable'     => false,
					'value_shape'  => 'opaque',
					'write_reason' => __(
						'This composite field contains structured values that require a dedicated update schema.',
						'botiga'
					),
				);
			}

			return array(
				'writable'     => true,
				'value_shape'  => 'composite',
				'write_reason' => null,
			);
		}

		/**
		 * Checks whether normalized settings contain JSON-encoded structured data.
		 *
		 * @since 2.4.8
		 *
		 * @param array $settings Normalized settings data.
		 *
		 * @return bool
		 */
		private function contains_structured_json( $settings ) {
			foreach ( $settings as $setting ) {
				if (
					$this->is_structured_json( $setting['default'] ) ||
					$this->is_structured_json( $setting['value'] )
				) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks whether a value is a JSON-encoded array or object.
		 *
		 * @since 2.4.8
		 *
		 * @param mixed $value Value to inspect.
		 *
		 * @return bool
		 */
		private function is_structured_json( $value ) {
			if ( ! is_string( $value ) || '' === trim( $value ) ) {
				return false;
			}

			$decoded = json_decode( $value, true );

			return JSON_ERROR_NONE === json_last_error() &&
				is_array( $decoded );
		}

		/**
		 * Normalizes a setting value for ability responses.
		 *
		 * @since 2.4.8
		 *
		 * @param mixed  $value Setting value.
		 * @param string $type  Expected value type.
		 *
		 * @return mixed
		 */
		public function normalize_value( $value, $type ) {
			if (
				'' === $value &&
				in_array(
					$type,
					array( 'integer', 'number' ),
					true
				)
			) {
				return null;
			}

			if ( 'boolean' === $type ) {
				return (bool) $value;
			}

			if ( 'integer' === $type ) {
				return is_numeric( $value )
					? (int) $value
					: null;
			}

			if ( 'number' === $type ) {
				return is_numeric( $value )
					? (float) $value
					: null;
			}

			if ( 'array' === $type ) {
				return is_array( $value )
					? $value
					: array();
			}

			return $value;
		}

		/**
		 * Normalizes a callback into a readable string.
		 *
		 * @since 2.4.8
		 *
		 * @param mixed $callback Callback value.
		 *
		 * @return string|null
		 */
		private function normalize_callback( $callback ) {
			if ( empty( $callback ) ) {
				return null;
			}

			if ( is_string( $callback ) ) {
				return $callback;
			}

			if (
				is_array( $callback ) &&
				2 === count( $callback )
			) {
				$target = is_object( $callback[0] )
					? get_class( $callback[0] )
					: $callback[0];

				if (
					is_string( $target ) &&
					is_string( $callback[1] )
				) {
					return $target . '::' . $callback[1];
				}
			}

			return null;
		}
	}
}
