<?php
/**
 * Botiga update-section-settings ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Update_Section_Settings_Ability' ) ) {

	/**
	 * Registers and executes the update-section-settings ability.
	 */
	class Botiga_Update_Section_Settings_Ability extends Botiga_Ability {

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register() {
			wp_register_ability(
				'botiga/update-section-settings',
				array(
					'label'               => __(
						'Update Botiga Section Settings',
						'botiga'
					),
					'description'         => __(
						'Validates and progressively updates one or more configurable fields in a Botiga Customizer section.',
						'botiga'
					),
					'category'            => 'botiga',
					'input_schema'        => array(
						'type'                 => 'object',
						'properties'           => array(
							'section_id' => array(
								'type'        => 'string',
								'minLength'   => 1,
								'description' => __(
									'Customizer section ID returned by the Botiga section discovery ability.',
									'botiga'
								),
							),
							'settings'   => array(
								'type'                 => 'object',
								'minProperties'        => 1,
								'description'          => __(
									'Field values keyed by Customizer control or field ID.',
									'botiga'
								),
								'additionalProperties' => array(
									'type' => array(
										'boolean',
										'integer',
										'number',
										'string',
										'array',
										'object',
									),
								),
							),
						),
						'required'             => array(
							'section_id',
							'settings',
						),
						'additionalProperties' => false,
					),
					'output_schema'       => array(
						'type'                 => 'object',
						'properties'           => array(
							'section_id' => array(
								'type' => 'string',
							),
							'updated'    => array(
								'type'  => 'array',
								'items' => array(
									'type'                 => 'object',
									'properties'           => array(
										'field_id' => array(
											'type' => 'string',
										),
										'value'    => array(
											'type' => array(
												'boolean',
												'integer',
												'number',
												'string',
												'array',
												'object',
											),
										),
									),
									'required'             => array(
										'field_id',
										'value',
									),
									'additionalProperties' => false,
								),
							),
							'warnings'   => array(
								'type'  => 'array',
								'items' => array(
									'type'                 => 'object',
									'properties'           => array(
										'field_id' => array(
											'type' => 'string',
										),
										'code'     => array(
											'type' => 'string',
										),
										'message'  => array(
											'type' => 'string',
										),
									),
									'required'             => array(
										'field_id',
										'code',
										'message',
									),
									'additionalProperties' => false,
								),
							),
							'errors'     => array(
								'type'  => 'array',
								'items' => array(
									'type'                 => 'object',
									'properties'           => array(
										'field_id' => array(
											'type' => 'string',
										),
										'code'     => array(
											'type' => 'string',
										),
										'message'  => array(
											'type' => 'string',
										),
										'details'  => array(
											'type' => array(
												'array',
												'object',
											),
										),
									),
									'required'             => array(
										'field_id',
										'code',
										'message',
										'details',
									),
									'additionalProperties' => false,
								),
							),
						),
						'required'             => array(
							'section_id',
							'updated',
							'warnings',
							'errors',
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
							'readonly'    => false,
							'destructive' => false,
							'idempotent'  => true,
						),
						'show_in_rest' => true,
					),
				)
			);
		}

		/**
		 * Updates configurable fields belonging to one section.
		 *
		 * Valid fields are saved even when another submitted field is invalid.
		 *
		 * @since 2.4.8
		 *
		 * @param array $input Ability input.
		 *
		 * @return array|WP_Error
		 */
		public function execute( $input ) {
			$section_id = $input['section_id'];
			$settings   = $input['settings'];

			$section_validation = $this->validate_section_id(
				$section_id
			);

			if ( is_wp_error( $section_validation ) ) {
				return $section_validation;
			}

			if ( empty( $settings ) ) {
				return new WP_Error(
					'botiga_empty_section_update',
					__(
						'At least one setting value must be provided.',
						'botiga'
					),
					array(
						'status' => 400,
					)
				);
			}

			$controls = $this->registry->get_section_controls(
				$section_id
			);

			if ( is_wp_error( $controls ) ) {
				return $controls;
			}

			$updated  = array();
			$warnings = array();
			$errors   = array();

			foreach ( $settings as $field_id => $value ) {
				$result = $this->registry->update_field_value(
					$section_id,
					$field_id,
					$value
				);

				if ( is_wp_error( $result ) ) {
					$error_data = $result->get_error_data();

					$errors[] = array(
						'field_id' => $field_id,
						'code'     => $result->get_error_code(),
						'message'  => $result->get_error_message(),
						'details'  => is_array( $error_data )
							? $error_data
							: array(),
					);

					continue;
				}

				$updated[] = array(
					'field_id' => $field_id,
					'value'    => $result,
				);
			}

			foreach ( $updated as $updated_field ) {
				$is_active = $this->registry->is_field_active(
					$section_id,
					$updated_field['field_id']
				);

				if (
					is_wp_error( $is_active ) ||
					$is_active
				) {
					continue;
				}

				$warnings[] = array(
					'field_id' => $updated_field['field_id'],
					'code'     => 'botiga_field_condition_not_met',
					'message'  => __(
						'The setting was saved, but the field is currently inactive because its Customizer condition is not met.',
						'botiga'
					),
				);
			}

			return array(
				'section_id' => $section_id,
				'updated'    => $updated,
				'warnings'   => $warnings,
				'errors'     => $errors,
			);
		}
	}
}
