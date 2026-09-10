<?php
/**
 * Botiga Setup Checklist actionable sections.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$can_manage_plugins = current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
?>
<div class="botiga-setup-checklist__sections">
	<?php foreach ( $sections as $section_key => $section ) : ?>
		<?php
		$is_complete    = ! empty( $section['is_complete'] );
		$is_collapsed   = $is_complete;
		$body_id        = 'botiga-setup-checklist-section-' . sanitize_html_class( $section_key );
		$section_class  = 'botiga-setup-checklist__section';
		$section_class .= $is_complete ? ' is-complete' : '';
		$section_class .= $is_collapsed ? ' is-collapsed' : '';
		?>
		<section
			class="<?php echo esc_attr( $section_class ); ?>"
			data-section="<?php echo esc_attr( $section_key ); ?>"
		>
			<button
				type="button"
				class="botiga-setup-checklist__section-toggle"
				aria-expanded="<?php echo esc_attr( $is_collapsed ? 'false' : 'true' ); ?>"
				aria-controls="<?php echo esc_attr( $body_id ); ?>"
			>
				<span class="botiga-setup-checklist__section-title">
					<?php echo esc_html( $section['label'] ); ?>
				</span>
				<span class="botiga-setup-checklist__section-status">
					<?php echo esc_html( $is_complete ? __( 'Complete', 'botiga' ) : __( 'Incomplete', 'botiga' ) ); ?>
				</span>
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="8" fill="none" aria-hidden="true">
					<path d="m6.281 7.719-6-6a1.059 1.059 0 0 1 0-1.438 1.059 1.059 0 0 1 1.438 0L7 5.594 12.281.28a1.06 1.06 0 0 1 1.438 0 1.059 1.059 0 0 1 0 1.438l-6 6a1.059 1.059 0 0 1-1.438 0Z" fill="#A7AAAD"/>
				</svg>
			</button>

			<div
				id="<?php echo esc_attr( $body_id ); ?>"
				class="botiga-setup-checklist__section-body"
			>
				<ul class="botiga-setup-checklist__items">
					<?php foreach ( $section['items'] as $item_key => $item ) : ?>
						<?php
						$item_complete = ! empty( $item['is_complete'] );
						$item_class    = 'botiga-setup-checklist__item' . ( $item_complete ? ' is-complete' : '' );
						?>
						<li
							class="<?php echo esc_attr( $item_class ); ?>"
							data-item="<?php echo esc_attr( $item_key ); ?>"
						>
							<span class="botiga-setup-checklist__item-status" aria-hidden="true"></span>
							<div class="botiga-setup-checklist__item-content">
								<span class="botiga-setup-checklist__item-title"><?php echo esc_html( $item['title'] ); ?></span>
								<?php if ( ! empty( $item['description'] ) ) : ?>
									<span class="botiga-setup-checklist__item-description"><?php echo esc_html( $item['description'] ); ?></span>
								<?php endif; ?>
							</div>

							<?php if ( ! $item_complete && ! empty( $item['action_label'] ) ) : ?>
								<?php if ( ! empty( $item['plugin_slug'] ) && ! empty( $item['plugin_name'] ) ) : ?>
									<?php if ( $can_manage_plugins ) : ?>
										<button
											type="button"
											class="button botiga-setup-checklist__item-action botiga-install-plugin"
											data-plugin-slug="<?php echo esc_attr( $item['plugin_slug'] ); ?>"
											data-plugin-name="<?php echo esc_attr( $item['plugin_name'] ); ?>"
											data-plugin-action="<?php echo esc_attr( $item['plugin_action'] ?? 'install' ); ?>"
										>
											<?php echo esc_html( $item['action_label'] ); ?>
										</button>
									<?php endif; ?>
								<?php elseif ( ! empty( $item['action_url'] ) ) : ?>
									<a
										class="button botiga-setup-checklist__item-action"
										href="<?php echo esc_url( $item['action_url'] ); ?>"
									>
										<?php echo esc_html( $item['action_label'] ); ?>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endforeach; ?>
</div>
