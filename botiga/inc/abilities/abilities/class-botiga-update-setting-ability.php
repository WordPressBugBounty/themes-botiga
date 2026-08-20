<?php
/**
 * Botiga update-setting ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Update_Setting_Ability' ) ) {

	/**
	 * Registers and executes the update-setting ability.
	 */
	class Botiga_Update_Setting_Ability extends Botiga_Ability {

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register() {
			wp_register_ability(
				'botiga/update-setting',
				array(
					'label'               => __(
						'Update a Botiga Setting',
						'botiga'
					),
					'description'         => __(
						'Validates and updates one configurable field in a Botiga Customizer section. Composite fields support partial updates.',
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
							'field_id'   => array(
								'type'        => 'string',
								'minLength'   => 1,
								'description' => __(
									'Customizer control or field ID.',
									'botiga'
								),
							),
							'value'      => array(
								'type'        => array(
									'boolean',
									'integer',
									'number',
									'string',
									'array',
									'object',
								),
								'description' => __(
									'The direct field value or composite setting values.',
									'botiga'
								),
							),
						),
						'required'             => array(
							'section_id',
							'field_id',
							'value',
						),
						'additionalProperties' => false,
					),
					'output_schema'       => array(
						'type'                 => 'object',
						'properties'           => array(
							'section_id' => array(
								'type' => 'string',
							),
							'field_id'   => array(
								'type' => 'string',
							),
							'value'      => array(
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
							'section_id',
							'field_id',
							'value',
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
		 * Updates one configurable Botiga field.
		 *
		 * @since 2.4.8
		 *
		 * @param array $input Ability input.
		 *
		 * @return array|WP_Error
		 */
		public function execute( $input ) {
			$section_id = $input['section_id'];
			$field_id   = $input['field_id'];
			$value      = $input['value'];

			$section_validation = $this->validate_section_id(
				$section_id
			);

			if ( is_wp_error( $section_validation ) ) {
				return $section_validation;
			}

			$result = $this->registry->update_field_value(
				$section_id,
				$field_id,
				$value
			);

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			return array(
				'section_id' => $section_id,
				'field_id'   => $field_id,
				'value'      => $result,
			);
		}
	}
}
