<?php
/**
 * Botiga Setup Checklist.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist' ) ) {

	/**
	 * Coordinates the Botiga Setup Checklist.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist {

		/**
		 * Setup Checklist admin page slug.
		 *
		 * @since 2.4.9
		 */
		const PAGE_SLUG = 'botiga-setup-checklist';

		/**
		 * User meta key used to dismiss the checklist.
		 *
		 * @since 2.4.9
		 */
		const DISMISSED_META_KEY = 'botiga_setup_checklist_dismissed';

		/**
		 * Singleton instance.
		 *
		 * @since 2.4.9
		 *
		 * @var Botiga_Setup_Checklist|null
		 */
		private static $instance = null;

		/**
		 * Completion detector.
		 *
		 * @since 2.4.9
		 *
		 * @var Botiga_Setup_Checklist_Detector
		 */
		private $detector;

		/**
		 * Promotional card resolver.
		 *
		 * @since 2.4.9
		 *
		 * @var Botiga_Setup_Checklist_Promotions
		 */
		private $promotions;

		/**
		 * Resolved actionable sections for the current request.
		 *
		 * @since 2.4.9
		 *
		 * @var array|null
		 */
		private $actionable_sections = null;

		/**
		 * Gets the singleton instance.
		 *
		 * @since 2.4.9
		 *
		 * @return Botiga_Setup_Checklist
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Gets actionable sections with visibility and completion state applied.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_actionable_sections() {
			if ( null !== $this->actionable_sections ) {
				return $this->actionable_sections;
			}

			$sections = Botiga_Setup_Checklist_Config::get_actionable_sections();

			foreach ( $sections as $section_key => $section ) {
				$items = isset( $section['items'] ) && is_array( $section['items'] )
					? $section['items']
					: array();

				foreach ( $items as $item_key => $item ) {
					if ( ! $this->is_item_visible( $item ) ) {
						unset( $items[ $item_key ] );
						continue;
					}

					$completion = isset( $item['completion'] )
						? sanitize_key( $item['completion'] )
						: '';
					$is_complete = $completion
						? $this->detector->is_complete( $completion )
						: false;

					$items[ $item_key ]['is_complete'] = $is_complete;

					if ( ! $is_complete ) {
						$items[ $item_key ] = $this->resolve_plugin_action( $items[ $item_key ] );
					}
				}

				if ( empty( $items ) ) {
					unset( $sections[ $section_key ] );
					continue;
				}

				$sections[ $section_key ]['items']       = $items;
				$sections[ $section_key ]['is_complete'] = $this->are_items_complete( $items );
			}

			$this->actionable_sections = $sections;

			return $this->actionable_sections;
		}

		/**
		 * Gets promotional sections.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_promotional_sections() {
			return $this->promotions->get_sections();
		}

		/**
		 * Gets section-level Setup Checklist progress.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_progress() {
			$sections  = $this->get_actionable_sections();
			$total     = count( $sections );
			$completed = 0;

			foreach ( $sections as $section ) {
				if ( ! empty( $section['is_complete'] ) ) {
					$completed++;
				}
			}

			return array(
				'completed'  => $completed,
				'total'      => $total,
				'percentage' => $total ? (int) round( ( $completed / $total ) * 100 ) : 0,
			);
		}

		/**
		 * Records an acknowledged completion signal.
		 *
		 * @since 2.4.9
		 *
		 * @param string $completion Completion signal key.
		 *
		 * @return bool
		 */
		public function record_completion( $completion ) {
			$completion = sanitize_key( $completion );

			if ( '' === $completion ) {
				return false;
			}

			$recorded = $this->detector->record_complete( $completion );

			if ( $recorded ) {
				$this->actionable_sections = null;
			}

			return $recorded;
		}

		/**
		 * Checks whether the checklist is dismissed for the current user.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		public function is_dismissed() {
			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				return false;
			}

			return (bool) get_user_meta( $user_id, self::DISMISSED_META_KEY, true );
		}

		/**
		 * Dismisses the checklist for the current user.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		public function dismiss() {
			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				return false;
			}

			return (bool) update_user_meta( $user_id, self::DISMISSED_META_KEY, 1 );
		}

		/**
		 * Resolves the action for an installed plugin.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Checklist item configuration.
		 *
		 * @return array
		 */
		private function resolve_plugin_action( $item ) {
			$plugin_name = isset( $item['plugin_name'] ) ? (string) $item['plugin_name'] : '';

			if ( '' === $plugin_name ) {
				return $item;
			}

			$plugin_status = Botiga_Setup_Checklist_Plugins::get_status( $plugin_name );

			$item['plugin_action'] = 'inactive' === $plugin_status ? 'activate' : 'install';

			if ( 'inactive' === $plugin_status ) {
				$item['action_label'] = __( 'Activate', 'botiga' );
			}

			return $item;
		}

		/**
		 * Checks whether an item applies to the current site.
		 *
		 * @since 2.4.9
		 *
		 * @param array $item Checklist item configuration.
		 *
		 * @return bool
		 */
		private function is_item_visible( $item ) {
			if ( empty( $item['requires_woocommerce'] ) ) {
				return true;
			}

			return class_exists( 'WooCommerce' );
		}

		/**
		 * Checks whether every visible item in a section is complete.
		 *
		 * @since 2.4.9
		 *
		 * @param array $items Visible checklist items.
		 *
		 * @return bool
		 */
		private function are_items_complete( $items ) {
			foreach ( $items as $item ) {
				if ( empty( $item['is_complete'] ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		private function __construct() {
			$this->detector   = new Botiga_Setup_Checklist_Detector();
			$this->promotions = new Botiga_Setup_Checklist_Promotions();
		}
	}
}
