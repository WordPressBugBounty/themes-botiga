<?php
/**
 * Botiga Setup Checklist promotional cards.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Promotions' ) ) {

	/**
	 * Resolves promotional card state for the Setup Checklist.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Promotions {

		/**
		 * Merchant plugin slug.
		 *
		 * @since 2.4.9
		 */
		const MERCHANT_PLUGIN_SLUG = 'merchant';

		/**
		 * Merchant plugin basename.
		 *
		 * @since 2.4.9
		 */
		const MERCHANT_PLUGIN_NAME = 'merchant/merchant.php';

		/**
		 * Merchant Pro main plugin file name.
		 *
		 * @since 2.4.9
		 */
		const MERCHANT_PRO_PLUGIN_FILE = 'merchant-pro.php';

		/**
		 * Theme integration config.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $theme_config = array();

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		public function __construct() {
			$this->theme_config = Botiga_Setup_Checklist_Config::get_theme_config();
		}

		/**
		 * Gets promotional sections with runtime state applied.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = Botiga_Setup_Checklist_Config::get_promotional_sections();

			foreach ( $sections as $section_key => $section ) {
				if ( ! empty( $section['requires_woocommerce'] ) && ! class_exists( 'WooCommerce' ) ) {
					unset( $sections[ $section_key ] );
					continue;
				}

				$items = array();

				foreach ( $section['items'] ?? array() as $item_key => $item ) {
					$resolved_item = $this->resolve_item( $item );

					if ( empty( $resolved_item ) ) {
						continue;
					}

					$items[ $item_key ] = $resolved_item;
				}

				if ( empty( $items ) ) {
					unset( $sections[ $section_key ] );
					continue;
				}

				$sections[ $section_key ]['items'] = $items;

				if ( 'botiga-pro' === $section_key ) {
					$sections[ $section_key ] = $this->resolve_theme_pro_section( $sections[ $section_key ] );
				}

				if ( 'merchant' === $section_key ) {
					$sections[ $section_key ] = $this->resolve_merchant_section( $sections[ $section_key ] );
				}
			}

			return $sections;
		}

		/**
		 * Resolves a promotional card by type.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function resolve_item( $item ) {
			$type = isset( $item['type'] ) ? sanitize_key( $item['type'] ) : '';

			if ( 'plugin' === $type ) {
				return $this->resolve_plugin_item( $item );
			}

			if ( 'theme_pro' === $type ) {
				return $this->resolve_theme_pro_item( $item );
			}

			if ( 'merchant_module' === $type ) {
				return $this->resolve_merchant_item( $item );
			}

			return array();
		}

		/**
		 * Resolves a WordPress.org plugin card.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function resolve_plugin_item( $item ) {
			$plugin_slug = isset( $item['plugin_slug'] ) ? sanitize_key( $item['plugin_slug'] ) : '';
			$plugin_name = isset( $item['plugin_name'] ) ? sanitize_text_field( $item['plugin_name'] ) : '';

			if ( '' === $plugin_slug || '' === $plugin_name ) {
				return array();
			}

			if ( $this->is_plugin_active( $plugin_name ) ) {
				$item['badge_label'] = __( 'Installed', 'botiga' );
				$item['badge_class'] = 'is-installed';

				return $item;
			}

			$item['action_type'] = 'plugin';

			if ( $this->is_plugin_installed( $plugin_name ) ) {
				$item['badge_label']   = __( 'Installed', 'botiga' );
				$item['badge_class']   = 'is-installed';
				$item['action_label']  = __( 'Activate', 'botiga' );
				$item['plugin_action'] = 'activate';

				return $item;
			}

			$item['action_label']  = __( 'Install', 'botiga' );
			$item['plugin_action'] = 'install';

			return $item;
		}

		/**
		 * Resolves a theme Pro feature card.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function resolve_theme_pro_item( $item ) {
			$pro_config       = isset( $this->theme_config['pro'] ) && is_array( $this->theme_config['pro'] )
				? $this->theme_config['pro']
				: array();
			$version_constant = isset( $pro_config['version_constant'] ) ? (string) $pro_config['version_constant'] : '';

			if ( '' !== $version_constant && defined( $version_constant ) ) {
				$item['badge_label'] = __( 'Installed', 'botiga' );
				$item['badge_class'] = 'is-installed';

				return $item;
			}

			$plugin_path_callback = $pro_config['plugin_path_callback'] ?? '';
			$pro_plugin_path      = is_callable( $plugin_path_callback )
				? (string) call_user_func( $plugin_path_callback )
				: '';

			if ( '' !== $pro_plugin_path ) {
				$item['action_type']  = 'link';
				$item['action_label'] = $pro_config['activation_label'] ?? __( 'Activate Pro', 'botiga' );
				$item['action_url']   = $this->get_plugin_activation_url( $pro_plugin_path );

				return $item;
			}

			$upgrade_url = isset( $pro_config['upgrade_url'] ) ? (string) $pro_config['upgrade_url'] : '';

			if ( '' === $upgrade_url ) {
				return $item;
			}

			$item['action_type']     = 'link';
			$item['action_label']    = $pro_config['upgrade_label'] ?? __( 'Upgrade', 'botiga' );
			$item['action_url']      = $upgrade_url;
			$item['action_external'] = true;

			return $item;
		}

		/**
		 * Resolves the Botiga Pro section footer action.
		 *
		 * @since 2.4.9
		 *
		 * @param array $section Botiga Pro section configuration.
		 *
		 * @return array
		 */
		private function resolve_theme_pro_section( $section ) {
			$pro_config = isset( $this->theme_config['pro'] ) && is_array( $this->theme_config['pro'] )
				? $this->theme_config['pro']
				: array();
			$plugin_path_callback = $pro_config['plugin_path_callback'] ?? '';

			if ( ! is_callable( $plugin_path_callback ) ) {
				return $section;
			}

			$pro_plugin_path = (string) call_user_func( $plugin_path_callback );

			if ( '' === $pro_plugin_path ) {
				return $section;
			}

			$section['footer_action_url'] = add_query_arg(
				'page',
				'botiga-dashboard',
				admin_url( 'admin.php' )
			) . '#botiga-pro-dashboard-section';
			$section['footer_action_external'] = false;

			return $section;
		}

		/**
		 * Resolves a Merchant module card.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function resolve_merchant_item( $item ) {
			$module_id = isset( $item['module_id'] ) ? sanitize_key( $item['module_id'] ) : '';

			if ( '' === $module_id ) {
				return array();
			}

			$is_pro = ! empty( $item['pro'] );

			if ( $is_pro ) {
				$item['badge_label'] = __( 'PRO', 'botiga' );
				$item['badge_class'] = 'is-pro';
			}

			if ( ! $this->is_merchant_active() ) {
				return $this->set_merchant_plugin_action( $item );
			}

			if ( $is_pro && ! $this->is_merchant_pro_active() ) {
				return $this->set_merchant_pro_action( $item );
			}

			$item['action_type']  = 'link';
			$item['action_label'] = __( 'View Module', 'botiga' );
			$item['action_url']   = add_query_arg(
				array(
					'page'   => 'merchant',
					'module' => $module_id,
				),
				admin_url( 'admin.php' )
			);

			return $item;
		}

		/**
		 * Resolves the Merchant section footer action.
		 *
		 * @since 2.4.9
		 *
		 * @param array $section Merchant section configuration.
		 *
		 * @return array
		 */
		private function resolve_merchant_section( $section ) {
			if ( $this->is_merchant_active() ) {
				$section['footer_action_type']  = 'link';
				$section['footer_action_label'] = __( 'View All Modules', 'botiga' );
				$section['footer_action_url']   = add_query_arg(
					array(
						'page'    => 'merchant',
						'section' => 'modules',
					),
					admin_url( 'admin.php' )
				);

				return $section;
			}

			$merchant_installed = $this->is_plugin_installed( self::MERCHANT_PLUGIN_NAME );

			$section['footer_action_type']   = 'plugin';
			$section['footer_action_label']  = $merchant_installed
				? __( 'Activate Merchant', 'botiga' )
				: __( 'Install Merchant', 'botiga' );
			$section['footer_plugin_slug']   = self::MERCHANT_PLUGIN_SLUG;
			$section['footer_plugin_name']   = self::MERCHANT_PLUGIN_NAME;
			$section['footer_plugin_action'] = $merchant_installed ? 'activate' : 'install';

			return $section;
		}

		/**
		 * Adds the Merchant plugin install or activation action to a card.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function set_merchant_plugin_action( $item ) {
			$merchant_installed = $this->is_plugin_installed( self::MERCHANT_PLUGIN_NAME );

			$item['action_type']   = 'plugin';
			$item['action_label']  = $merchant_installed
				? __( 'Activate Merchant', 'botiga' )
				: __( 'Install Merchant', 'botiga' );
			$item['plugin_slug']   = self::MERCHANT_PLUGIN_SLUG;
			$item['plugin_name']   = self::MERCHANT_PLUGIN_NAME;
			$item['plugin_action'] = $merchant_installed ? 'activate' : 'install';

			return $item;
		}

		/**
		 * Adds the Merchant Pro activation or upgrade action to a card.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Card configuration.
		 *
		 * @return array
		 */
		private function set_merchant_pro_action( $item ) {
			$merchant_pro_plugin_path = Botiga_Setup_Checklist_Plugins::find_by_file( self::MERCHANT_PRO_PLUGIN_FILE );

			if ( '' !== $merchant_pro_plugin_path ) {
				$item['action_type']   = 'plugin';
				$item['action_label']  = __( 'Activate Merchant Pro', 'botiga' );
				$item['plugin_slug']   = dirname( $merchant_pro_plugin_path );
				$item['plugin_name']   = $merchant_pro_plugin_path;
				$item['plugin_action'] = 'activate';

				return $item;
			}

			$item['action_type']     = 'link';
			$item['action_label']    = __( 'Get Merchant Pro', 'botiga' );
			$item['action_url']      = 'https://athemes.com/merchant-upgrade/';
			$item['action_external'] = true;

			return $item;
		}

		/**
		 * Checks whether Merchant is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function is_merchant_active() {
			return defined( 'MERCHANT_VERSION' ) && $this->is_plugin_active( self::MERCHANT_PLUGIN_NAME );
		}

		/**
		 * Checks whether Merchant Pro is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function is_merchant_pro_active() {
			if ( ! defined( 'MERCHANT_PRO_VERSION' ) ) {
				return false;
			}

			$merchant_pro_plugin_path = Botiga_Setup_Checklist_Plugins::find_by_file( self::MERCHANT_PRO_PLUGIN_FILE );

			return '' !== $merchant_pro_plugin_path && $this->is_plugin_active( $merchant_pro_plugin_path );
		}

		/**
		 * Checks whether a plugin is active.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_name Plugin basename.
		 *
		 * @return bool
		 */
		private function is_plugin_active( $plugin_name ) {
			return Botiga_Setup_Checklist_Plugins::is_active( $plugin_name );
		}

		/**
		 * Checks whether a plugin is installed.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_name Plugin basename.
		 *
		 * @return bool
		 */
		private function is_plugin_installed( $plugin_name ) {
			return Botiga_Setup_Checklist_Plugins::is_installed( $plugin_name );
		}

		/**
		 * Builds a plugin activation URL.
		 *
		 * @since 2.4.9
		 *
		 * @param string $plugin_path Plugin basename.
		 *
		 * @return string
		 */
		private function get_plugin_activation_url( $plugin_path ) {
			return wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin' => $plugin_path,
					),
					admin_url( 'plugins.php' )
				),
				'activate-plugin_' . $plugin_path
			);
		}
	}
}
