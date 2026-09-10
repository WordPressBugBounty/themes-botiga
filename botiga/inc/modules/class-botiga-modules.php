<?php
/**
 * Modules.
 *
 * @package Botiga
 */

if ( ! class_exists( 'Botiga_Modules' ) ) {

	class Botiga_Modules {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'modules_default_status' ) );
			add_filter( 'option_botiga-modules', array( $this, 'filter_botiga_modules_option' ) );
		}

		/**
		 * Check if a specific module is activated
		 */
		public static function is_module_active( $module ) {
			$all_modules = get_option( 'botiga-modules' );
			$all_modules = ( is_array( $all_modules ) ) ? $all_modules : (array) $all_modules;

			if ( array_key_exists( $module, $all_modules ) && true === $all_modules[$module] ) {
				return true;
		}
		
			return false;
		}

		/**
		 * Enable/disable default modules
		 * 
		 * Always enable/disable these modules for new users
		 * This will happen only when the module slug is not present in the 'botiga-modules' option
		 * If the condition matches, the theme will force the respective module to be enabled
		 * 
		 */
		public function modules_default_status() {
			/**
			 * Hook 'botiga_modules_default_status'
			 *
			 * @since 1.0.0
			 */
			$modules_default_status = apply_filters( 'botiga_modules_default_status', array(
				'hf-builder'         => true,
				'local-google-fonts' => true,
				'adobe-typekit'      => true,
			) );

			$all_modules = get_option( 'botiga-modules' );
			if( $all_modules ) {
				foreach( $modules_default_status as $module => $status ) {
					if( ! isset( $all_modules[ $module ] ) ) {
						update_option( 'botiga-modules', array_merge( $all_modules, array( $module => $status ) ) );
					}
				}
			}
		}
		
		/**
		 * Filter the botiga-modules option so only modules that exist for the current site remain.
		 * - Lite only: keep Lite filesystem ids.
		 * - Lite + Pro active: keep Lite + Pro filesystem ids.
		 *
		 * @param mixed $value Option value.
		 *
		 * @return mixed
		 */
		public function filter_botiga_modules_option( $value ) {
			$value = is_array( $value ) ? $value : (array) $value;

			if ( ! function_exists( 'botiga_get_available_modules_ids' ) ) {
				return $value;
			}

			$modules_ids = botiga_get_available_modules_ids();

			// Botiga Pro can become active after the available module IDs were cached earlier in the request.
			if ( class_exists( 'Botiga_Pro' ) && ! in_array( 'shop-filters', $modules_ids, true ) ) {
				return $value;
			}

			if ( empty( $modules_ids ) ) {
				return array();
			}

			return array_intersect_key( $value, array_flip( $modules_ids ) );
		}
	}   
    
    new Botiga_Modules();
}