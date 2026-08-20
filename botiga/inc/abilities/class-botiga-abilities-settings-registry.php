<?php
/**
 * Botiga Abilities settings registry.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Abilities_Settings_Registry' ) ) {

	/**
	 * Provides lazy access to Botiga's registered Customizer settings.
	 */
	class Botiga_Abilities_Settings_Registry {

		/**
		 * Class instance.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Settings_Registry
		 */
		private static $instance;

		/**
		 * Abilities Customizer manager.
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
		 * Settings updater service.
		 *
		 * @since 2.4.8
		 *
		 * @var Botiga_Abilities_Settings_Updater
		 */
		private $settings_updater;

		/**
		 * Control types that do not represent configurable settings.
		 *
		 * @var string[]
		 */
		private const IGNORED_CONTROL_TYPES = array(
			'botiga-tab-control',
			'botiga-divider-control',
			'botiga-text-control',
			'botiga-accordion',
			'botiga-create-page-control',
			'botiga-typography-preview-control',
			'botiga-upsell-features',
		);

		/**
		 * Returns the class instance.
		 *
		 * @since 2.4.8
		 *
		 * @return Botiga_Abilities_Settings_Registry
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
			$this->customizer_manager =
				Botiga_Abilities_Customizer_Manager::get_instance();

			$this->field_schema =
				new Botiga_Abilities_Field_Schema();

			$this->settings_validator =
				new Botiga_Abilities_Settings_Validator(
					$this->field_schema
				);

			$this->settings_updater =
				new Botiga_Abilities_Settings_Updater(
					$this->customizer_manager,
					$this->field_schema,
					$this->settings_validator
				);
		}

		/**
		 * Returns the populated Customizer manager.
		 *
		 * @since 2.4.8
		 *
		 * @return WP_Customize_Manager|WP_Error
		 */
		public function get_manager() {
			return $this->customizer_manager->get_manager();
		}

		/**
		 * Returns configurable controls belonging to a section.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return WP_Customize_Control[]|WP_Error
		 */
		public function get_section_controls( $section_id ) {
			$manager = $this->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			if ( ! $manager->get_section( $section_id ) ) {
				return new WP_Error(
					'botiga_section_not_found',
					sprintf(
						/* translators: %s: Customizer section ID. */
						__(
							'The Botiga settings section "%s" was not found.',
							'botiga'
						),
						$section_id
					),
					array(
						'status' => 400,
					)
				);
			}

			$controls = array();

			foreach ( $manager->controls() as $control ) {
				if ( $section_id !== $control->section ) {
					continue;
				}

				if ( $this->is_ignored_control( $control ) ) {
					continue;
				}

				$controls[ $control->id ] = $control;
			}

			return $controls;
		}

		/**
		 * Returns one configurable field control.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 *
		 * @return WP_Customize_Control|WP_Error
		 */
		public function get_field_control( $section_id, $field_id ) {
			$controls = $this->get_section_controls( $section_id );

			if ( is_wp_error( $controls ) ) {
				return $controls;
			}

			if ( isset( $controls[ $field_id ] ) ) {
				return $controls[ $field_id ];
			}

			return new WP_Error(
				'botiga_field_not_found',
				sprintf(
					/* translators: %s: Field ID. */
					__(
						'The Botiga settings field "%s" was not found.',
						'botiga'
					),
					$field_id
				),
				array(
					'status' => 400,
				)
			);
		}

		/**
		 * Checks whether a configurable field is currently active.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 *
		 * @return bool|WP_Error
		 */
		public function is_field_active( $section_id, $field_id ) {
			$control = $this->get_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			return (bool) $control->active();
		}

		/**
		 * Returns basic field definitions for a section.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Customizer section ID.
		 *
		 * @return array|WP_Error
		 */
		public function get_section_fields( $section_id ) {
			$controls = $this->get_section_controls( $section_id );

			if ( is_wp_error( $controls ) ) {
				return $controls;
			}

			$fields = array();

			foreach ( $controls as $control ) {
				$field = $this->field_schema->get_field_data(
					$control
				);

				if ( null === $field ) {
					continue;
				}

				$fields[] = $field;
			}

			return $fields;
		}

		/**
		 * Returns a configurable section and its fields.
		 *
		 * @param string $section_id     Customizer section ID.
		 * @param bool   $include_schema Whether to include the field schema.
		 * @return array|WP_Error
		 */
		public function get_section_data( $section_id, $include_schema = false ) {
			$manager = $this->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			$section = $manager->get_section( $section_id );

			if ( ! $section ) {
				return new WP_Error(
					'botiga_section_not_found',
					sprintf(
						/* translators: %s: Customizer section ID. */
						__(
							'The Botiga settings section "%s" was not found.',
							'botiga'
						),
						$section_id
					),
					array(
						'status' => 400,
					)
				);
			}

			$fields = $this->get_section_fields( $section_id );

			if ( is_wp_error( $fields ) ) {
				return $fields;
			}

			$data = array(
				'id'       => $section->id,
				'label'    => wp_specialchars_decode(
					wp_strip_all_tags( $section->title ),
					ENT_QUOTES
				),
				'settings' => $this->get_field_values( $fields ),
			);
			
			if ( $include_schema ) {
				$data['fields'] = $fields;
			}
			
			return $data;
		}

		/**
		 * Returns configurable field counts grouped by section.
		 *
		 * @since 2.4.8
		 *
		 * @return array|WP_Error
		 */
		public function get_section_field_counts() {
			$manager = $this->get_manager();

			if ( is_wp_error( $manager ) ) {
				return $manager;
			}

			$field_counts = array();

			foreach ( $manager->controls() as $control ) {
				if (
					empty( $control->section ) ||
					$this->is_ignored_control( $control )
				) {
					continue;
				}

				$has_setting = false;

				foreach ( $control->settings as $setting ) {
					if ( $setting instanceof WP_Customize_Setting ) {
						$has_setting = true;

						break;
					}
				}

				if ( ! $has_setting ) {
					continue;
				}

				if ( ! isset( $field_counts[ $control->section ] ) ) {
					$field_counts[ $control->section ] = 0;
				}

				++$field_counts[ $control->section ];
			}

			return $field_counts;
		}

		/**
		 * Validates and sanitizes a direct field value.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 * @param mixed  $value      Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		public function validate_direct_field_value( $section_id, $field_id, $value ) {
			$control = $this->get_writable_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			return $this->settings_validator->validate_direct_field_value(
				$control,
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
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 * @param mixed  $values     Submitted setting values.
		 *
		 * @return array|WP_Error
		 */
		public function validate_composite_field_value( $section_id, $field_id, $values ) {
			$control = $this->get_writable_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			return $this->settings_validator->validate_composite_field_value(
				$control,
				$values
			);
		}

		/**
		 * Validates and saves one configurable field value.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 * @param mixed  $value      Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		public function update_field_value( $section_id, $field_id, $value ) {
			$control = $this->get_writable_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			$setting_count = 0;

			foreach ( $control->settings as $setting ) {
				if ( $setting instanceof WP_Customize_Setting ) {
					++$setting_count;
				}
			}

			if ( 0 === $setting_count ) {
				return new WP_Error(
					'botiga_field_not_configurable',
					__(
						'The requested field does not contain a configurable setting.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			if ( 1 === $setting_count ) {
				return $this->settings_updater->update_direct_field_value(
					$control,
					$value
				);
			}

			return $this->settings_updater->update_composite_field_value(
				$control,
				$value
			);
		}

		/**
		 * Validates and saves a direct field value.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 * @param mixed  $value      Submitted value.
		 *
		 * @return mixed|WP_Error
		 */
		public function update_direct_field_value( $section_id, $field_id, $value ) {
			$control = $this->get_writable_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			return $this->settings_updater->update_direct_field_value(
				$control,
				$value
			);
		}

		/**
		 * Validates and saves composite field values.
		 *
		 * Partial updates are allowed.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 * @param mixed  $values     Submitted setting values.
		 *
		 * @return array|WP_Error
		 */
		public function update_composite_field_value( $section_id, $field_id, $values ) {
			$control = $this->get_writable_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			return $this->settings_updater->update_composite_field_value(
				$control,
				$values
			);
		}

		/**
		 * Checks whether a control is UI-only.
		 *
		 * @since 2.4.8
		 *
		 * @param WP_Customize_Control $control Customizer control.
		 *
		 * @return bool
		 */
		private function is_ignored_control( $control ) {
			return in_array(
				$control->type,
				self::IGNORED_CONTROL_TYPES,
				true
			);
		}

		/**
		 * Returns current values keyed by field ID.
		 *
		 * @since 2.4.8
		 *
		 * @param array $fields Section field definitions.
		 * @return array
		 */
		private function get_field_values( $fields ) {
			$values = array();

			foreach ( $fields as $field ) {
				$settings = $field['settings'] ?? array();

				if ( empty( $settings ) ) {
					$values[ $field['id'] ] = null;

					continue;
				}

				if (
					'direct' === $field['kind'] &&
					1 === count( $settings )
				) {
					$setting = reset( $settings );

					$values[ $field['id'] ] =
						$setting['value'] ?? null;

					continue;
				}

				$value = array();

				foreach ( $settings as $setting_key => $setting ) {
					$value[ $setting_key ] =
						$setting['value'] ?? null;
				}

				$values[ $field['id'] ] = $value;
			}

			return $values;
		}

		/**
		 * Returns one field control when it supports ability updates.
		 *
		 * @since 2.4.8
		 *
		 * @param string $section_id Section ID.
		 * @param string $field_id   Field/control ID.
		 *
		 * @return WP_Customize_Control|WP_Error
		 */
		private function get_writable_field_control( $section_id, $field_id ) {
			$control = $this->get_field_control(
				$section_id,
				$field_id
			);

			if ( is_wp_error( $control ) ) {
				return $control;
			}

			$support = $this->field_schema->get_field_support(
				$control
			);

			if ( $support['writable'] ) {
				return $control;
			}

			return new WP_Error(
				'botiga_field_read_only',
				__(
					'The requested field is available for reading but is not supported for updates.',
					'botiga'
				),
				array(
					'status' => 400,
					'reason' => $support['write_reason'],
				)
			);
		}
	}

	Botiga_Abilities_Settings_Registry::get_instance();
}
