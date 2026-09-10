<?php
/**
 * Botiga Setup Checklist admin integration.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Botiga_Setup_Checklist_Admin' ) ) {

	/**
	 * Registers and renders the Setup Checklist admin page.
	 *
	 * @since 2.4.9
	 */
	class Botiga_Setup_Checklist_Admin {

		/**
		 * Setup Checklist page hook suffix.
		 *
		 * @since 2.4.9
		 *
		 * @var string|false
		 */
		private $page_hook = false;

		/**
		 * Constructor.
		 *
		 * @since 2.4.9
		 */
		public function __construct() {
			if ( ! is_admin() ) {
				return;
			}

			add_action( 'admin_init', array( $this, 'maybe_record_completion' ) );
			add_action( 'admin_menu', array( $this, 'register_page' ), 20 );
			add_action( 'admin_menu', array( $this, 'preserve_dashboard_parent_link' ), PHP_INT_MAX );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'in_admin_header', array( $this, 'suppress_admin_notices' ), PHP_INT_MAX );
			add_action( 'admin_post_botiga_dismiss_setup_checklist', array( $this, 'handle_dismissal' ) );
			add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		}

		/**
		 * Records acknowledgement-only checklist items from their CTA.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function maybe_record_completion() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( empty( $_GET['botiga_setup_checklist_complete'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$completion = sanitize_key( wp_unslash( $_GET['botiga_setup_checklist_complete'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( 'review_pages' !== $completion ) {
				return;
			}

			$nonce = isset( $_GET['_wpnonce'] )
				? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) )
				: '';

			if ( ! wp_verify_nonce( $nonce, 'botiga_setup_checklist_complete_' . $completion ) ) {
				return;
			}

			Botiga_Setup_Checklist::instance()->record_completion( $completion );
		}

		/**
		 * Registers the Setup Checklist submenu page.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function register_page() {
			if ( ! $this->is_available() ) {
				return;
			}

			$this->page_hook = add_submenu_page( // phpcs:ignore WPThemeReview.PluginTerritory.NoAddAdminPages.add_submenu_page
				'botiga-dashboard',
				esc_html__( 'Botiga Setup Checklist', 'botiga' ),
				$this->get_menu_title(),
				'manage_options',
				Botiga_Setup_Checklist::PAGE_SLUG,
				array( $this, 'render_page' ),
				0
			);
		}

		/**
		 * Keeps the Botiga parent menu linked to the Theme Dashboard.
		 *
		 * WordPress uses the first submenu entry as the top-level menu URL. Keep
		 * Botiga's hidden dashboard entry first internally while the Setup Checklist
		 * remains the first visible submenu item.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function preserve_dashboard_parent_link() {
			global $menu, $submenu;

			$parent_slug = 'botiga-dashboard';

			if ( empty( $submenu[ $parent_slug ] ) ) {
				return;
			}

			foreach ( $submenu[ $parent_slug ] as $index => $item ) {
				if ( empty( $item[2] ) || $parent_slug !== $item[2] ) {
					continue;
				}

				if ( 0 === $index ) {
					return;
				}

				unset( $submenu[ $parent_slug ][ $index ] );
				array_unshift( $submenu[ $parent_slug ], $item );

				return;
			}

			foreach ( (array) $menu as $parent_menu ) {
				if ( empty( $parent_menu[2] ) || $parent_slug !== $parent_menu[2] ) {
					continue;
				}

				array_unshift( $submenu[ $parent_slug ], array_slice( $parent_menu, 0, 4 ) );
				return;
			}
		}

		/**
		 * Enqueues Setup Checklist assets.
		 *
		 * The menu stylesheet is loaded throughout wp-admin so the sidebar progress
		 * indicator is available wherever the Botiga menu is rendered. Page assets
		 * remain scoped to the Setup Checklist screen.
		 *
		 * @since 2.4.9
		 *
		 * @param string $hook Current admin page hook suffix.
		 *
		 * @return void
		 */
		public function enqueue_assets( $hook ) {
			if ( ! $this->is_available() ) {
				return;
			}

			$this->enqueue_menu_style();

			if ( ! $this->is_checklist_page( $hook ) ) {
				return;
			}

			$this->enqueue_page_assets();
		}

		/**
		 * Suppresses admin notices on the Setup Checklist page.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function suppress_admin_notices() {
			if ( ! $this->is_checklist_screen() ) {
				return;
			}

			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'user_admin_notices' );
		}

		/**
		 * Adds the Setup Checklist page body class.
		 *
		 * @since 2.4.9
		 *
		 * @param string $classes Admin body classes.
		 *
		 * @return string
		 */
		public function add_body_class( $classes ) {
			if ( ! $this->is_checklist_screen() ) {
				return $classes;
			}

			return $classes . ' botiga-setup-checklist-page';
		}

		/**
		 * Handles Setup Checklist dismissal.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function handle_dismissal() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to dismiss this checklist.', 'botiga' ) );
			}

			check_admin_referer( 'botiga_dismiss_setup_checklist' );

			Botiga_Setup_Checklist::instance()->dismiss();

			wp_safe_redirect(
				add_query_arg(
					'page',
					'botiga-dashboard',
					admin_url( 'admin.php' )
				)
			);
			exit;
		}

		/**
		 * Renders the Setup Checklist admin page.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		public function render_page() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			require get_template_directory() . '/inc/setup-checklist/views/setup-checklist.php';
		}

		/**
		 * Checks whether the Setup Checklist is available.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function is_available() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			if ( defined( 'BOTIGA_AWL_ACTIVE' ) ) {
				return false;
			}

			return ! Botiga_Setup_Checklist::instance()->is_dismissed();
		}

		/**
		 * Checks whether the current hook is the Setup Checklist page.
		 *
		 * @since 2.4.9
		 *
		 * @param string $hook Current admin page hook suffix.
		 *
		 * @return bool
		 */
		private function is_checklist_page( $hook ) {
			return $this->page_hook && $hook === $this->page_hook;
		}

		/**
		 * Checks whether the current screen is the Setup Checklist page.
		 *
		 * @since 2.4.9
		 *
		 * @return bool
		 */
		private function is_checklist_screen() {
			$screen = get_current_screen();

			if ( ! $screen || ! $this->page_hook ) {
				return false;
			}

			return $screen->id === $this->page_hook;
		}

		/**
		 * Enqueues the sidebar Setup Checklist progress style.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		private function enqueue_menu_style() {
			wp_enqueue_style(
				'botiga-setup-checklist',
				get_template_directory_uri() . '/assets/css/admin/botiga-setup-checklist.min.css',
				array(),
				BOTIGA_VERSION
			);
		}

		/**
		 * Enqueues assets used only by the Setup Checklist page.
		 *
		 * @since 2.4.9
		 *
		 * @return void
		 */
		private function enqueue_page_assets() {
			wp_enqueue_style(
				'botiga-setup-checklist-page',
				get_template_directory_uri() . '/assets/css/admin/botiga-setup-checklist-page.min.css',
				array( 'botiga-setup-checklist' ),
				BOTIGA_VERSION
			);

			wp_enqueue_style(
				'botiga-setup-checklist-notifications',
				get_template_directory_uri() . '/assets/css/admin/botiga-setup-checklist-notifications.min.css',
				array( 'botiga-setup-checklist-page' ),
				BOTIGA_VERSION
			);

			wp_enqueue_script(
				'botiga-setup-checklist',
				get_template_directory_uri() . '/assets/js/admin/botiga-setup-checklist.min.js',
				array(),
				BOTIGA_VERSION,
				true
			);

			wp_localize_script(
				'botiga-setup-checklist',
				'botigaSetupChecklist',
				array(
					'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
					'notifications' => array(
						'action' => Botiga_Setup_Checklist_Notifications::AJAX_ACTION,
						'nonce'  => wp_create_nonce( 'botiga_setup_checklist_notifications' ),
					),
				)
			);
		}

		/**
		 * Builds the Setup Checklist submenu label and progress bar.
		 *
		 * @since 2.4.9
		 *
		 * @return string
		 */
		private function get_menu_title() {
			$progress = Botiga_Setup_Checklist::instance()->get_progress();

			return sprintf(
				'<span class="botiga-setup-checklist-menu-label">%1$s<span class="botiga-setup-checklist-menu-progress" aria-hidden="true"><span style="width:%2$d%%"></span></span></span>',
				esc_html__( 'Setup Checklist', 'botiga' ),
				absint( $progress['percentage'] )
			);
		}
	}
}

new Botiga_Setup_Checklist_Admin();
