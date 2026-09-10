<?php
/**
 * Botiga Setup Checklist notification drawer.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_tabs  = ! empty( $notification_data['show_tabs'] );
$body_class = 'botiga-setup-checklist-notifications__body' . ( $show_tabs ? ' has-tabs' : '' );
?>
<aside
	id="botiga-setup-checklist-notifications"
	class="botiga-setup-checklist-notifications"
	aria-hidden="true"
	data-latest-notification-date="<?php echo esc_attr( $notification_data['latest_date'] ); ?>"
	data-read-heading="<?php esc_attr_e( 'Latest News', 'botiga' ); ?>"
>
	<button
		type="button"
		class="botiga-setup-checklist-notifications__close"
		aria-label="<?php esc_attr_e( 'Close notifications', 'botiga' ); ?>"
		data-botiga-setup-checklist-notifications-close
	>
		<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
	</button>

	<header class="botiga-setup-checklist-notifications__header">
		<div class="botiga-setup-checklist-notifications__header-icon" aria-hidden="true">
			<span class="dashicons dashicons-megaphone"></span>
		</div>
		<div>
			<h2>
				<?php echo esc_html( $notification_data['is_read'] ? __( 'Latest News', 'botiga' ) : __( 'New Update', 'botiga' ) ); ?>
			</h2>
			<p><?php esc_html_e( 'Check the latest news from Botiga', 'botiga' ); ?></p>
		</div>
	</header>

	<?php if ( $show_tabs ) : ?>
		<div class="botiga-setup-checklist-notifications__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Notification feeds', 'botiga' ); ?>">
			<button
				id="botiga-setup-checklist-notifications-tab-botiga"
				type="button"
				class="botiga-setup-checklist-notifications__tab is-active"
				role="tab"
				aria-selected="true"
				aria-controls="botiga-setup-checklist-notifications-botiga"
				data-botiga-setup-checklist-notification-tab="botiga"
			>
				<?php esc_html_e( 'Botiga', 'botiga' ); ?>
			</button>
			<button
				id="botiga-setup-checklist-notifications-tab-botiga-pro"
				type="button"
				class="botiga-setup-checklist-notifications__tab"
				role="tab"
				aria-selected="false"
				aria-controls="botiga-setup-checklist-notifications-botiga-pro"
				data-botiga-setup-checklist-notification-tab="botiga-pro"
			>
				<?php esc_html_e( 'Botiga Pro', 'botiga' ); ?>
			</button>
		</div>
	<?php endif; ?>

	<div class="<?php echo esc_attr( $body_class ); ?>">
		<div
			id="botiga-setup-checklist-notifications-botiga"
			class="botiga-setup-checklist-notifications__panel is-active"
			<?php if ( $show_tabs ) : ?>
				role="tabpanel"
				aria-labelledby="botiga-setup-checklist-notifications-tab-botiga"
			<?php endif; ?>
			data-botiga-setup-checklist-notification-panel="botiga"
		>
			<?php
			$notification_items = $notification_data['items'];
			require get_template_directory() . '/inc/setup-checklist/views/notification-feed.php';
			?>
		</div>

		<?php if ( $show_tabs ) : ?>
			<div
				id="botiga-setup-checklist-notifications-botiga-pro"
				class="botiga-setup-checklist-notifications__panel"
				role="tabpanel"
				aria-labelledby="botiga-setup-checklist-notifications-tab-botiga-pro"
				hidden
				data-botiga-setup-checklist-notification-panel="botiga-pro"
			>
				<?php
				$notification_items = $notification_data['pro_items'];
				require get_template_directory() . '/inc/setup-checklist/views/notification-feed.php';
				?>
			</div>
		<?php endif; ?>
	</div>
</aside>
