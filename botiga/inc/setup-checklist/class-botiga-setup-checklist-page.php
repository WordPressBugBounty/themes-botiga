<?php
/**
 * Botiga Setup Checklist page chrome.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Page' ) ) {

	/**
	 * Provides Setup Checklist header and footer data.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Page {

		/**
		 * Dashboard settings.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $settings = array();

		/**
		 * Notification drawer data.
		 *
		 * @since 2.4.9
		 *
		 * @var array|null
		 */
		private $notifications = null;

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		public function __construct() {
			$settings = apply_filters( 'botiga_dashboard_settings', array() );

			$this->settings = is_array( $settings ) ? $settings : array();
		}

		/**
		 * Gets header data.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_header_data() {
			$has_pro       = defined( 'BOTIGA_PRO_VERSION' );
			$notifications = $this->get_notifications_data();

			return array(
				'version'            => $has_pro ? BOTIGA_PRO_VERSION : BOTIGA_VERSION,
				'edition'            => $has_pro ? __( 'PRO', 'botiga' ) : __( 'FREE', 'botiga' ),
				'documentation_url'  => $this->settings['documentation_link'] ?? 'https://docs.athemes.com/collection/318-botiga',
				'support_url'        => $this->settings['support_link'] ?? 'https://athemes.com/support/',
				'notification_count' => $notifications['unread_count'],
				'notification_read'  => $notifications['is_read'],
			);
		}

		/**
		 * Gets notification drawer data.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_notifications_data() {
			if ( null === $this->notifications ) {
				$this->notifications = Botiga_Setup_Checklist_Notifications::instance()->get_data( $this->settings );
			}

			return $this->notifications;
		}

		/**
		 * Gets footer data.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		public function get_footer_data() {
			$has_pro = defined( 'BOTIGA_PRO_VERSION' );

			return array(
				'support_url'      => $this->settings['support_link'] ?? 'https://athemes.com/support/',
				'docs_url'         => $this->settings['documentation_link'] ?? 'https://docs.athemes.com/collection/318-botiga',
				'community_url'    => $this->settings['community_link'] ?? 'https://www.facebook.com/groups/athemes/',
				'free_plugins_url' => 'https://athemes.com/wordpress-plugins/',
				'review_url'       => $this->settings['review_link'] ?? 'https://wordpress.org/support/theme/botiga/reviews/',
				'facebook_url'     => $this->settings['facebook_link'] ?? 'https://www.facebook.com/groups/athemes/',
				'instagram_url'    => 'https://www.instagram.com/athemesdotcom/',
				'linkedin_url'     => 'https://www.linkedin.com/company/athemes/',
				'twitter_url'      => $this->settings['twitter_link'] ?? 'https://twitter.com/athemesdotcom',
				'youtube_url'      => $this->settings['youtube_link'] ?? 'https://www.youtube.com/@Athemes',
				'version'          => $has_pro ? BOTIGA_PRO_VERSION : BOTIGA_VERSION,
			);
		}
	}
}
