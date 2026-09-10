<?php
/**
 * Botiga Setup Checklist onboarding integration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Onboarding' ) ) {

	/**
	 * Routes Starter Sites onboarding back to the Setup Checklist.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Onboarding {

		/**
		 * Starter Sites onboarding script handle.
		 *
		 * @since 2.4.9
		 */
		const SCRIPT_HANDLE = 'atss-onboarding-wizard';

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		public function __construct() {
			if ( ! is_admin() ) {
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'set_dashboard_url' ), 100 );
		}

		/**
		 * Points the Starter Sites close and completion dashboard actions here.
		 *
		 * Starter Sites reads its dashboardUrl value for both the wizard close
		 * action and the Botiga completion screen's Visit Dashboard action.
		 *
		 * @since 2.4.9
		 *
		 * @param string $hook Current admin page hook suffix.
		 *
		 * @return void
		 */
		public function set_dashboard_url( $hook ) {
			if ( false === strpos( $hook, 'atss-onboarding-wizard' ) ) {
				return;
			}

			if ( defined( 'BOTIGA_AWL_ACTIVE' ) ) {
				return;
			}

			if ( Botiga_Setup_Checklist::instance()->is_dismissed() ) {
				return;
			}

			if ( ! wp_script_is( self::SCRIPT_HANDLE, 'enqueued' ) ) {
				return;
			}

			$checklist_url = add_query_arg(
				'page',
				Botiga_Setup_Checklist::PAGE_SLUG,
				admin_url( 'admin.php' )
			);

			wp_add_inline_script(
				self::SCRIPT_HANDLE,
				'if ( window.atssOnboarding ) { window.atssOnboarding.dashboardUrl = ' . wp_json_encode( esc_url_raw( $checklist_url ) ) . '; }',
				'before'
			);
		}
	}
}

new Botiga_Setup_Checklist_Onboarding();
