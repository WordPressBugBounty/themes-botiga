<?php
/**
 * Botiga Patcher end-of-life notice.
 *
 * @package Botiga
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Display the Patcher end-of-life notice and handle plugin removal.
 *
 * @since 2.4.9
 */
class Botiga_Patcher_EOL_Notice {

	/**
	 * Patcher plugin basename.
	 *
	 * @var string
	 */
	const PLUGIN_FILE = 'athemes-patcher/athemes-patcher.php';

	/**
	 * User meta key for the dismissed login session.
	 *
	 * @since 2.4.9
	 *
	 * @var string
	 */
	const DISMISSED_SESSION_META_KEY = 'botiga_patcher_eol_dismissed_session';

	/**
	 * AJAX action used to dismiss the notice for the current login session.
	 *
	 * @since 2.4.9
	 *
	 * @var string
	 */
	const AJAX_ACTION = 'botiga_patcher_eol_dismiss';

	/**
	 * Constructor.
	 *
	 * @since 2.4.9
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'in_admin_header', array( $this, 'contextual_notice_markup' ), 20 );
		add_action( 'admin_notices', array( $this, 'notice_markup' ) );
		add_action( 'admin_post_botiga_remove_patcher', array( $this, 'remove_patcher' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'ajax_dismiss' ) );
	}

	/**
	 * Display the standard notice on the Plugins screen.
	 *
	 * @since 2.4.9
	 */
	public function notice_markup() {
		if ( ! $this->is_plugins_screen() || ! $this->should_show_notice() ) {
			return;
		}
		?>
		<div class="notice notice-info botiga-patcher-eol-notice" data-botiga-patcher-eol-notice style="position:relative">
			<?php $this->notice_content(); ?>
		</div>
		<?php
	}

	/**
	 * Display the contextual top bar on Botiga admin screens.
	 *
	 * @since 2.4.9
	 */
	public function contextual_notice_markup() {
		if ( ! $this->is_botiga_admin_screen() || ! $this->should_show_notice() ) {
			return;
		}
		?>
		<div class="notice botiga-patcher-eol-notice botiga-patcher-eol-bar" data-botiga-patcher-eol-notice>
			<?php $this->notice_content(); ?>
		</div>
		<?php
	}

	/**
	 * Enqueue assets used by the Patcher end-of-life notice.
	 *
	 * @since 2.4.9
	 */
	public function enqueue_assets() {
		if ( ! $this->is_notice_screen() || ! $this->should_show_notice() ) {
			return;
		}

		if ( $this->is_botiga_admin_screen() ) {
			wp_enqueue_style(
				'botiga-notices',
				get_template_directory_uri() . '/assets/css/admin/botiga-notices.min.css',
				array(),
				BOTIGA_VERSION,
				'all'
			);
		}

		wp_localize_script(
			'botiga-admin-functions',
			'botigaPatcherEol',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => self::AJAX_ACTION,
				'nonce'   => wp_create_nonce( 'botiga_patcher_eol_dismiss' ),
			)
		);
	}

	/**
	 * Render the shared notice content.
	 *
	 * @since 2.4.9
	 */
	private function notice_content() {
		?>
		<p>
			<strong><?php esc_html_e( 'Patcher update', 'botiga' ); ?></strong>
			&mdash;
			<?php esc_html_e( 'The Patcher plugin is no longer needed. You can safely remove it from your site.', 'botiga' ); ?>
			<a href="<?php echo esc_url( $this->get_action_url() ); ?>"><?php esc_html_e( 'Deactivate & Delete', 'botiga' ); ?></a>
		</p>
		<button type="button" class="notice-dismiss" data-botiga-patcher-eol-dismiss>
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'botiga' ); ?></span>
		</button>
		<?php
	}

	/**
	 * Dismiss the notice for the current login session.
	 *
	 * @since 2.4.9
	 */
	public function ajax_dismiss() {
		check_ajax_referer( 'botiga_patcher_eol_dismiss', 'nonce' );

		if ( ! current_user_can( 'delete_plugins' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You do not have permission to dismiss this notice.', 'botiga' ),
				),
				403
			);
		}

		$session_id = $this->get_current_session_id();

		if ( '' === $session_id ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Unable to identify the current login session.', 'botiga' ),
				),
				400
			);
		}

		update_user_meta(
			get_current_user_id(),
			self::DISMISSED_SESSION_META_KEY,
			$session_id
		);

		wp_send_json_success();
	}

	/**
	 * Deactivate and delete Patcher after explicit user action.
	 *
	 * @since 2.4.9
	 */
	public function remove_patcher() {
		if ( ! current_user_can( 'delete_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to delete plugins.', 'botiga' ) );
		}

		check_admin_referer( 'botiga_remove_patcher' );

		$redirect_url = wp_get_referer();

		if ( ! $redirect_url ) {
			$redirect_url = admin_url();
		}

		if ( ! $this->is_patcher_installed() ) {
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$this->load_plugin_api();

		$network_wide = is_multisite() && is_plugin_active_for_network( self::PLUGIN_FILE );

		if ( $network_wide && ! current_user_can( 'manage_network_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage network plugins.', 'botiga' ) );
		}

		if ( is_plugin_active( self::PLUGIN_FILE ) || $network_wide ) {
			deactivate_plugins( self::PLUGIN_FILE, false, $network_wide );
		}

		$result = delete_plugins( array( self::PLUGIN_FILE ) );

		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		delete_user_meta( get_current_user_id(), self::DISMISSED_SESSION_META_KEY );

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Check whether the notice should be displayed.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function should_show_notice() {
		return $this->can_manage_patcher() && ! $this->is_dismissed_for_current_session();
	}

	/**
	 * Check whether the current user can remove an installed Patcher plugin.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function can_manage_patcher() {
		if ( ! current_user_can( 'delete_plugins' ) || ! $this->is_patcher_installed() ) {
			return false;
		}

		if ( is_multisite() && is_plugin_active_for_network( self::PLUGIN_FILE ) && ! current_user_can( 'manage_network_plugins' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check whether the notice was dismissed for the current login session.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function is_dismissed_for_current_session() {
		$session_id = $this->get_current_session_id();

		if ( '' === $session_id ) {
			return false;
		}

		$dismissed_session = (string) get_user_meta(
			get_current_user_id(),
			self::DISMISSED_SESSION_META_KEY,
			true
		);

		return '' !== $dismissed_session && hash_equals( $dismissed_session, $session_id );
	}

	/**
	 * Get a hashed identifier for the current WordPress login session.
	 *
	 * @since 2.4.9
	 *
	 * @return string
	 */
	private function get_current_session_id() {
		$session_token = wp_get_session_token();

		if ( '' === $session_token ) {
			return '';
		}

		return wp_hash( $session_token );
	}

	/**
	 * Check whether the current screen can display the notice.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function is_notice_screen() {
		return $this->is_plugins_screen() || $this->is_botiga_admin_screen();
	}

	/**
	 * Check whether the current screen is the Plugins screen.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function is_plugins_screen() {
		$screen = get_current_screen();

		return $screen && 'plugins' === $screen->base;
	}

	/**
	 * Check whether the current screen belongs to the Botiga admin experience.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function is_botiga_admin_screen() {
		$screen = get_current_screen();

		if ( $screen && 'botiga-dashboard' === $screen->parent_base ) {
			return true;
		}

		if ( $screen && 'athemes_hf' === $screen->post_type ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $page, array( 'botiga-dashboard', 'botiga-setup-checklist' ), true ) ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';

		return 'athemes_hf' === $post_type;
	}

	/**
	 * Get the Patcher removal action URL.
	 *
	 * @since 2.4.9
	 *
	 * @return string
	 */
	private function get_action_url() {
		return wp_nonce_url(
			admin_url( 'admin-post.php?action=botiga_remove_patcher' ),
			'botiga_remove_patcher'
		);
	}

	/**
	 * Check whether Patcher is installed.
	 *
	 * @since 2.4.9
	 *
	 * @return bool
	 */
	private function is_patcher_installed() {
		$this->load_plugin_api();

		$plugins = get_plugins();

		return isset( $plugins[ self::PLUGIN_FILE ] );
	}

	/**
	 * Load the WordPress plugin API when needed.
	 *
	 * @since 2.4.9
	 */
	private function load_plugin_api() {
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
	}
}

new Botiga_Patcher_EOL_Notice();
