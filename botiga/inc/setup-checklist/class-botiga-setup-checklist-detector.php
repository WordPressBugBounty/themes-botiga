<?php
/**
 * Botiga Setup Checklist completion detection.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Detector' ) ) {

	/**
	 * Detects Setup Checklist item completion.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Detector {

		/**
		 * Stored Setup Checklist state option.
		 *
		 * @since 2.4.9
		 */
		const STATE_OPTION = 'botiga_setup_checklist_state';

		/**
		 * Completion detectors.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $detectors = array(
			'homepage'               => 'detect_homepage',
			'navigation_menu'        => 'detect_navigation_menu',
			'contact_form'           => 'detect_contact_form',
			'woocommerce_onboarding' => 'detect_woocommerce_onboarding',
			'email_delivery'         => 'detect_email_delivery',
			'site_backup'            => 'detect_site_backup',
			'seo'                    => 'detect_seo',
			'privacy_compliance'     => 'detect_privacy_compliance',
		);

		/**
		 * Cached completion results for the current request.
		 *
		 * @since 2.4.9
		 *
		 * @var array
		 */
		private $results = array();

		/**
		 * Cached recorded completion state.
		 *
		 * @since 2.4.9
		 *
		 * @var array|null
		 */
		private $state = null;

		/**
		 * Checks whether a completion signal is satisfied.
		 *
		 * @since 2.4.9
		 *
		 * @param string $completion Completion signal key.
		 *
		 * @return bool
		 */
		public function is_complete( $completion ) {
			$completion = sanitize_key( $completion );

			if ( '' === $completion ) {
				return false;
			}

			if ( array_key_exists( $completion, $this->results ) ) {
				return $this->results[ $completion ];
			}

			if ( isset( $this->detectors[ $completion ] ) ) {
				$method = $this->detectors[ $completion ];

				$this->results[ $completion ] = (bool) $this->{$method}();

				return $this->results[ $completion ];
			}

			$this->results[ $completion ] = $this->is_recorded_complete( $completion );

			return $this->results[ $completion ];
		}

		/**
		 * Records a completion signal.
		 *
		 * @since 2.4.9
		 *
		 * @param string $completion Completion signal key.
		 *
		 * @return bool
		 */
		public function record_complete( $completion ) {
			$completion = sanitize_key( $completion );

			if ( '' === $completion ) {
				return false;
			}

			$state = $this->get_state();

			if ( ! empty( $state[ $completion ] ) ) {
				$this->results[ $completion ] = true;

				return true;
			}

			$state[ $completion ] = true;

			if ( ! update_option( self::STATE_OPTION, $state ) ) {
				return false;
			}

			$this->state                  = $state;
			$this->results[ $completion ] = true;

			return true;
		}

		/**
		 * Detects whether a static homepage is configured.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_homepage() {
			if ( 'page' !== get_option( 'show_on_front' ) ) {
				return false;
			}

			$page_id = absint( get_option( 'page_on_front' ) );

			if ( ! $page_id ) {
				return false;
			}

			return 'publish' === get_post_status( $page_id );
		}

		/**
		 * Detects whether the primary navigation has an assigned menu.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_navigation_menu() {
			return has_nav_menu( 'primary' );
		}

		/**
		 * Detects whether WPForms Lite or Pro is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_contact_form() {
			if ( function_exists( 'wpforms' ) || defined( 'WPFORMS_VERSION' ) ) {
				return true;
			}

			return $this->is_any_plugin_active(
				array(
					'wpforms-lite/wpforms.php',
					'wpforms/wpforms.php',
				)
			);
		}

		/**
		 * Detects whether WooCommerce onboarding is complete.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_woocommerce_onboarding() {
			$profile = get_option( 'woocommerce_onboarding_profile', array() );

			if ( is_array( $profile ) && ! empty( $profile['completed'] ) ) {
				return true;
			}

			return 'yes' === get_option( 'woocommerce_task_list_complete' );
		}

		/**
		 * Detects whether an email delivery plugin is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_email_delivery() {
			return $this->is_any_plugin_active(
				array(
					'wp-mail-smtp/wp_mail_smtp.php',
					'wp-mail-smtp-pro/wp_mail_smtp.php',
					'fluent-smtp/fluent-smtp.php',
					'post-smtp/postman-smtp.php',
				)
			);
		}

		/**
		 * Detects whether a recognized backup plugin is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_site_backup() {
			return $this->is_any_plugin_active(
				array(
					'duplicator/duplicator.php',
					'duplicator-pro/duplicator-pro.php',
					'updraftplus/updraftplus.php',
					'backwpup/backwpup.php',
					'wpvivid-backuprestore/wpvivid-backuprestore.php',
					'all-in-one-wp-migration/all-in-one-wp-migration.php',
				)
			);
		}

		/**
		 * Detects whether a recognized SEO plugin is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_seo() {
			return $this->is_any_plugin_active(
				array(
					'all-in-one-seo-pack/all_in_one_seo_pack.php',
					'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
					'wordpress-seo/wp-seo.php',
					'seo-by-rank-math/rank-math.php',
					'wp-seopress/seopress.php',
					'autodescription/autodescription.php',
				)
			);
		}

		/**
		 * Detects whether a recognized privacy plugin is active.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function detect_privacy_compliance() {
			return $this->is_any_plugin_active(
				array(
					'wpconsent-cookies-banner-privacy-suite/wpconsent.php',
					'complianz-gdpr/complianz-gpdr.php',
					'cookie-law-info/cookie-law-info.php',
					'cookie-notice/cookie-notice.php',
					'gdpr-cookie-compliance/moove-gdpr.php',
				)
			);
		}

		/**
		 * Checks whether any plugin basename is active.
		 *
		 * @since 2.4.9
		 *
		 * @param string[] $plugins Plugin basenames.
		 *
		 * @return bool
		 */
		private function is_any_plugin_active( $plugins ) {
			foreach ( $plugins as $plugin ) {
				if ( Botiga_Setup_Checklist_Plugins::is_active( $plugin ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks a previously recorded completion signal.
		 *
		 * @since 2.4.9
		 *
		 * @param string $completion Completion signal key.
		 *
		 * @return bool
		 */
		private function is_recorded_complete( $completion ) {
			$state = $this->get_state();

			return ! empty( $state[ $completion ] );
		}

		/**
		 * Gets the cached recorded completion state.
		 *
		 * @since 2.4.9
		 *
		 * @return array
		 */
		private function get_state() {
			if ( null !== $this->state ) {
				return $this->state;
			}

			$state       = get_option( self::STATE_OPTION, array() );
			$this->state = is_array( $state ) ? $state : array();

			return $this->state;
		}
	}
}
