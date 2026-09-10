<?php
/**
 * Botiga Setup Checklist page header.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edition_class      = 'botiga-setup-checklist__edition' . ( 'PRO' === $header_data['edition'] ? ' is-pro' : '' );
$notification_class = 'botiga-setup-checklist__topbar-action is-notifications' . ( ! empty( $header_data['notification_read'] ) ? ' is-read' : '' );
?>
<header class="botiga-setup-checklist__page-header">
	<div class="botiga-setup-checklist__topbar">
		<a class="botiga-setup-checklist__brand" href="<?php echo esc_url( add_query_arg( 'page', 'botiga-dashboard', admin_url( 'admin.php' ) ) ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="none" aria-hidden="true">
			    <path d="M24.615.028 8.598 14.388.056 4.208 24.615.028Z" fill="#335EEA"/>
			    <path d="M24.614.028 8.395 14.147l8.542 10.18L24.614.028Z" fill="#BECCF9"/>
			</svg>
			<span><?php esc_html_e( 'Botiga', 'botiga' ); ?></span>
		</a>

		<div class="botiga-setup-checklist__topbar-actions">
			<div class="botiga-setup-checklist__version">
				<span><?php echo esc_html( $header_data['version'] ); ?></span>
				<span class="<?php echo esc_attr( $edition_class ); ?>">
					<?php echo esc_html( $header_data['edition'] ); ?>
				</span>
			</div>

			<button
				type="button"
				class="<?php echo esc_attr( $notification_class ); ?>"
				aria-label="<?php esc_attr_e( 'Theme News', 'botiga' ); ?>"
				aria-expanded="false"
				aria-controls="botiga-setup-checklist-notifications"
				data-botiga-setup-checklist-notifications-toggle
			>
    			<svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" fill="none" aria-hidden="true">
    			    <path xmlns="http://www.w3.org/2000/svg" d="M8.862.131a.752.752 0 0 0-.728-.065l-3.837 1.59a.726.726 0 0 1-.286.06H1.555a.758.758 0 0 0-.55.24.84.84 0 0 0-.228.578v.065H0v1.964h.777v.085a.84.84 0 0 0 .235.566c.145.15.34.233.543.233l.933 2.082c.063.14.162.258.286.342A.755.755 0 0 0 3.19 8h.392a.759.759 0 0 0 .546-.242.84.84 0 0 0 .225-.576V5.525l3.781 1.591a.755.755 0 0 0 .293.06.774.774 0 0 0 .435-.145.805.805 0 0 0 .246-.284.852.852 0 0 0 .096-.37V.806a.853.853 0 0 0-.092-.382.805.805 0 0 0-.25-.293ZM3.576 2.534v2.114H1.555V2.534h2.02Zm0 4.648h-.392L2.42 5.447h1.157v1.735Zm1.007-2.436a1.384 1.384 0 0 0-.23-.098v-2.16c.079-.017.156-.041.23-.072L8.427.806v5.55l-3.844-1.61Zm4.64-1.983v1.636a.758.758 0 0 0 .55-.24.84.84 0 0 0 .227-.578.84.84 0 0 0-.228-.578.758.758 0 0 0-.55-.24Z" fill="#1E1E1E"/>
    			</svg>
				<?php if ( ! empty( $header_data['notification_count'] ) ) : ?>
					<span class="botiga-setup-checklist__notification-count">
						<?php echo esc_html( $header_data['notification_count'] ); ?>
					</span>
				<?php endif; ?>
			</button>

			<a
				class="botiga-setup-checklist__topbar-action"
				href="<?php echo esc_url( $header_data['documentation_url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="9" height="12" fill="none" aria-hidden="true">
				    <path d="M1.5 0h3.75v3c0 .422.328.75.75.75h3v6.75c0 .844-.68 1.5-1.5 1.5h-6A1.48 1.48 0 0 1 0 10.5v-9C0 .68.656 0 1.5 0ZM6 0l3 3H6V0ZM2.625 6a.385.385 0 0 0-.375.375c0 .21.164.375.375.375h3.75a.385.385 0 0 0 .375-.375A.403.403 0 0 0 6.375 6h-3.75Zm0 1.5a.385.385 0 0 0-.375.375c0 .21.164.375.375.375h3.75a.385.385 0 0 0 .375-.375.403.403 0 0 0-.375-.375h-3.75Zm0 1.5a.385.385 0 0 0-.375.375c0 .21.164.375.375.375h3.75a.385.385 0 0 0 .375-.375A.403.403 0 0 0 6.375 9h-3.75Z" fill="#A7AAAD"/>
				</svg>
				<span><?php esc_html_e( 'Documentation', 'botiga' ); ?></span>
			</a>

			<a
				class="botiga-setup-checklist__topbar-action"
				href="<?php echo esc_url( $header_data['support_url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" fill="none" aria-hidden="true">
				    <path d="M6.281 12a5.965 5.965 0 0 1-5.203-3 5.97 5.97 0 0 1 0-6 6.014 6.014 0 0 1 5.203-3 6.004 6.004 0 0 1 5.18 3 5.97 5.97 0 0 1 0 6 5.955 5.955 0 0 1-5.18 3ZM4.242 3.89v.024a.585.585 0 0 0 .352.727.55.55 0 0 0 .703-.352l.023-.023a.176.176 0 0 1 .164-.118h1.36c.21 0 .375.141.375.352 0 .117-.07.234-.188.305L6 5.39c-.188.093-.281.28-.281.492v.304c0 .329.234.563.562.563.305 0 .54-.234.563-.54l.75-.444c.445-.258.75-.75.75-1.266 0-.82-.68-1.5-1.5-1.5h-1.36a1.31 1.31 0 0 0-1.242.89Zm1.29 4.36c0 .422.327.75.75.75.398 0 .75-.328.75-.75a.771.771 0 0 0-.75-.75.755.755 0 0 0-.75.75Z" fill="#A7AAAD"/>
				</svg>
				<span><?php esc_html_e( 'Support', 'botiga' ); ?></span>
			</a>
		</div>
	</div>

	<div class="botiga-setup-checklist__subheader">
		<div class="botiga-setup-checklist__title">
			<?php esc_html_e( 'Botiga Setup Checklist', 'botiga' ); ?>
		</div>

		<div class="botiga-setup-checklist__progress">
			<div class="botiga-setup-checklist__progress-meta">
				<span><?php esc_html_e( 'Setup Progress', 'botiga' ); ?></span>
				<span>
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: completed sections, 2: total sections. */
							__( '%1$d / %2$d', 'botiga' ),
							$progress['completed'],
							$progress['total']
						)
					);
					?>
				</span>
			</div>
			<div
				class="botiga-setup-checklist__progress-bar"
				role="progressbar"
				aria-valuemin="0"
				aria-valuemax="100"
				aria-valuenow="<?php echo esc_attr( $progress['percentage'] ); ?>"
			>
				<span style="width: <?php echo esc_attr( $progress['percentage'] ); ?>%;"></span>
			</div>
		</div>
	</div>
</header>

<?php
$notification_data = $checklist_page->get_notifications_data();
require get_template_directory() . '/inc/setup-checklist/views/notifications-drawer.php';
?>
