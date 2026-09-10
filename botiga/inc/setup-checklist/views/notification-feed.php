<?php
/**
 * Botiga Setup Checklist notification feed.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notification_items = isset( $notification_items ) && is_array( $notification_items )
	? $notification_items
	: array();
?>
<?php if ( ! empty( $notification_items ) ) : ?>
	<?php foreach ( $notification_items as $notification ) : ?>
		<article class="botiga-setup-checklist-notifications__item">
			<?php if ( ! empty( $notification['display_date'] ) ) : ?>
				<time class="botiga-setup-checklist-notifications__date">
					<?php echo esc_html( $notification['display_date'] ); ?>
				</time>
			<?php endif; ?>

			<div class="botiga-setup-checklist-notifications__content">
				<?php echo wp_kses_post( $notification['content'] ); ?>
			</div>
		</article>
	<?php endforeach; ?>
<?php else : ?>
	<p class="botiga-setup-checklist-notifications__empty">
		<?php esc_html_e( 'No notifications found', 'botiga' ); ?>
	</p>
<?php endif; ?>
