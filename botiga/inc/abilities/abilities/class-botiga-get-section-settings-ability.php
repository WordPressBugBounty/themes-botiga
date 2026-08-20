<?php
/**
 * Botiga get-section-settings ability.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Get_Section_Settings_Ability' ) ) {

	/**
	 * Registers and executes the get-section-settings ability.
	 */
	class Botiga_Get_Section_Settings_Ability extends Botiga_Ability {

		/**
		 * Registers the ability.
		 *
		 * @since 2.4.8
		 *
		 * @return void
		 */
		public function register() {
			wp_register_ability(
				'botiga/get-section-settings',
				array(
					'label'               => __(
						'Get Botiga Section Settings',
						'botiga'
					),
					'description'         => __(
						'Returns the configurable fields and current values for a Botiga Customizer section.',
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
							'include_schema' => array(
								'type'        => 'boolean',
								'default'     => false,
								'description' => __(
									'Whether to include complete field definitions and validation metadata.',
									'botiga'
								),
							),
						),
						'required'             => array(
							'section_id',
						),
						'additionalProperties' => false,
					),
					'output_schema'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'type'        => 'string',
								'description' => __(
									'Customizer section ID.',
									'botiga'
								),
							),
							'label'  => array(
								'type'        => 'string',
								'description' => __(
									'Human-readable section label.',
									'botiga'
								),
							),
							'settings' => array(
								'type'                 => 'object',
								'description'          => __(
									'Current values keyed by configurable field ID.',
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
										'null',
									),
								),
							),
							'fields' => array(
								'type'  => 'array',
								'items' => array(
									'type'       => 'object',
									'properties' => array(
										'id'             => array(
											'type' => 'string',
										),
										'label'          => array(
											'type' => 'string',
										),
										'description' => array(
											'type' => 'string',
										),
										'control_type'   => array(
											'type' => 'string',
										),
										'kind'           => array(
											'type' => 'string',
											'enum' => array(
												'direct',
												'composite',
											),
										),
										'settings'       => array(
											'type' => 'object',
										),
										'allowed_values' => array(
											'type'  => 'array',
											'items' => array(
												'type' => array(
													'string',
													'integer',
												),
											),
										),
										'value_shape' => array(
											'type' => 'string',
											'enum' => array(
												'scalar',
												'list',
												'composite',
												'opaque',
											),
										),
										'choices'        => array(
											'type' => array(
												'array',
												'object',
											),
										),
										'input_attrs'    => array(
											'type' => array(
												'array',
												'object',
											),
										),
										'support' => array(
											'type'       => 'object',
											'properties' => array(
												'readable' => array(
													'type' => 'boolean',
												),
												'writable' => array(
													'type' => 'boolean',
												),
												'write_reason' => array(
													'type' => array(
														'string',
														'null',
													),
												),
											),
											'required'   => array(
												'readable',
												'writable',
												'write_reason',
											),
											'additionalProperties' => false,
										),
									),
									'required'   => array(
										'id',
										'label',
										'description',
										'control_type',
										'kind',
										'value_shape',
										'support',
										'settings',
										'allowed_values',
										'choices',
										'input_attrs',
									),
									'additionalProperties' => false,
								),
							),
						),
						'required'   => array(
							'id',
							'label',
							'settings',
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
		 * Returns the settings belonging to a Botiga section.
		 *
		 * @since 2.4.8
		 *
		 * @param array $input Ability input.
		 *
		 * @return array|WP_Error
		 */
		public function execute( $input ) {
			$section_id = $input['section_id'];

			$section_validation = $this->validate_section_id(
				$section_id
			);

			if ( is_wp_error( $section_validation ) ) {
				return $section_validation;
			}

			return $this->registry->get_section_data(
				$section_id,
				! empty( $input['include_schema'] )
			);
		}
	}
}
