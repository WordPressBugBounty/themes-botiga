<?php
/**
 * Botiga Setup Checklist notifications.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Notifications' ) ) {

	/**
	 * Provides the Setup Checklist notification drawer data and read state.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Notifications {

		/**
		 * User meta key shared with the Botiga dashboard notification feed.
		 *
		 * @since 2.4.9
		 */
		const LATEST_READ_META_KEY = 'botiga_dashboard_notifications_latest_read';

		/**
		 * AJAX action used to mark notifications as read.
		 *
		 * @since 2.4.9
		 */
		const AJAX_ACTION = 'botiga_setup_checklist_notifications_read';

		/**
		 * Singleton instance.
		 *
		 * @since 2.4.9
		 *
		 * @var Botiga_Setup_Checklist_Notifications|null
		 */
		private static $instance = null;

		/**
		 * Gets the singleton instance.
		 *
		 * @since 2.4.9
		 *
		 * @return Botiga_Setup_Checklist_Notifications
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Gets notification drawer data from the dashboard notification feeds.
		 *
		 * @since 2.4.9
		 *
		 * @param array $settings Dashboard settings.
		 *
		 * @return array
		 */
		public function get_data( $settings ) {
			$items               = $this->prepare_items( $settings['notifications'] ?? array() );
			$pro_items           = $this->prepare_items( $settings['notifications_pro'] ?? array() );
			$latest_date         = $this->get_latest_date( $items );
			$last_read_timestamp = $this->get_last_read_timestamp();

			return array(
				'items'        => $items,
				'pro_items'    => $pro_items,
				'show_tabs'    => ! empty( $settings['notifications_tabs'] ),
				'latest_date'  => $latest_date,
				'unread_count' => $this->get_unread_count( $items, $last_read_timestamp ),
				'is_read'      => $this->is_latest_read( $latest_date, $last_read_timestamp ),
			);
		}

		/**
		 * Marks the latest dashboard notification as read.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function ajax_mark_read() {
			check_ajax_referer( 'botiga_setup_checklist_notifications', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'You are not allowed to update notifications.', 'botiga' ),
					),
					403
				);
			}

			$latest_date = isset( $_POST['latest_notification_date'] )
				? sanitize_text_field( wp_unslash( $_POST['latest_notification_date'] ) )
				: '';

			if ( '' === $latest_date || ! strtotime( $latest_date ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid notification date.', 'botiga' ),
					),
					400
				);
			}

			update_user_meta(
				get_current_user_id(),
				self::LATEST_READ_META_KEY,
				$latest_date
			);

			wp_send_json_success();
		}

		/**
		 * Prepares notification items for rendering.
		 *
		 * @since 2.4.9
		 *
		 * @param array $notifications Notification posts.
		 *
		 * @return array
		 */
		private function prepare_items( $notifications ) {
			if ( ! is_array( $notifications ) || empty( $notifications ) ) {
				return array();
			}

			$items = array();

			foreach ( $notifications as $notification ) {
				$item = $this->prepare_item( $notification );

				if ( empty( $item ) ) {
					continue;
				}

				$items[] = $item;
			}

			return $items;
		}

		/**
		 * Prepares one dashboard notification for the checklist drawer.
		 *
		 * @since 2.4.9
		 *
		 * @param object $notification Notification post.
		 *
		 * @return array
		 */
		private function prepare_item( $notification ) {
			if ( ! is_object( $notification ) ) {
				return array();
			}

			$content_raw = isset( $notification->post_content ) ? $notification->post_content : '';
			$content     = apply_filters(
				'botiga_dashboard_notification_changelog_content',
				$content_raw,
				$notification
			);

			if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
				return array();
			}

			$date           = isset( $notification->post_date ) ? (string) $notification->post_date : '';
			$date_timestamp = $date ? strtotime( $date ) : false;

			return array(
				'date'         => $date,
				'display_date' => $date_timestamp ? wp_date( 'F j, Y', $date_timestamp ) : '',
				'content'      => $content,
			);
		}

		/**
		 * Gets the latest visible notification date.
		 *
		 * The dashboard read state is based on the first Botiga notification.
		 *
		 * @since 2.4.9
		 *
		 * @param array $items Prepared Botiga notifications.
		 *
		 * @return string
		 */
		private function get_latest_date( $items ) {
			if ( empty( $items[0]['date'] ) ) {
				return '';
			}

			return $items[0]['date'];
		}

		/**
		 * Gets the stored notification read timestamp.
		 *
		 * @since 2.4.9
		 *
		 * @return int
		 */
		private function get_last_read_timestamp() {
			$last_read = get_user_meta(
				get_current_user_id(),
				self::LATEST_READ_META_KEY,
				true
			);

			if ( empty( $last_read ) ) {
				return 0;
			}

			$timestamp = strtotime( $last_read );

			return $timestamp ? $timestamp : 0;
		}

		/**
		 * Gets the unread notification count.
		 *
		 * @since 2.4.9
		 *
		 * @param array $items               Notification items.
		 * @param int   $last_read_timestamp Last read timestamp.
		 *
		 * @return int
		 */
		private function get_unread_count( $items, $last_read_timestamp ) {
			if ( ! $last_read_timestamp ) {
				return count( $items );
			}

			$unread = 0;

			foreach ( $items as $item ) {
				$date = ! empty( $item['date'] ) ? strtotime( $item['date'] ) : false;

				if ( $date && $date > $last_read_timestamp ) {
					$unread++;
				}
			}

			return $unread;
		}

		/**
		 * Checks whether the latest notification is read.
		 *
		 * @since 2.4.9
		 *
		 * @param string $latest_date         Latest notification date.
		 * @param int    $last_read_timestamp Last read timestamp.
		 *
		 * @return bool
		 */
		private function is_latest_read( $latest_date, $last_read_timestamp ) {
			if ( '' === $latest_date ) {
				return true;
			}

			if ( ! $last_read_timestamp ) {
				return false;
			}

			$latest_timestamp = strtotime( $latest_date );

			if ( ! $latest_timestamp ) {
				return true;
			}

			return $last_read_timestamp >= $latest_timestamp;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		private function __construct() {
			add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'ajax_mark_read' ) );
		}
	}
}

Botiga_Setup_Checklist_Notifications::instance();
