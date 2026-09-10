<?php
/**
 * Botiga Setup Checklist promotional sections.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $promotional_sections ) ) {
	return;
}

$can_manage_plugins = current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' );
?>
<div class="botiga-setup-checklist__promotions">
	<?php foreach ( $promotional_sections as $section_key => $section ) : ?>
		<?php
		$layout  = sanitize_html_class( $section['layout'] ?? 'default' );
		$body_id = 'botiga-setup-checklist-promotion-' . sanitize_html_class( $section_key );
		?>
		<section
			class="botiga-setup-checklist__promotion is-<?php echo esc_attr( $layout ); ?>"
			data-promotion="<?php echo esc_attr( $section_key ); ?>"
		>
			<button
				type="button"
				class="botiga-setup-checklist__promotion-toggle"
				aria-expanded="true"
				aria-controls="<?php echo esc_attr( $body_id ); ?>"
			>
				<span class="botiga-setup-checklist__promotion-heading">
					<span class="botiga-setup-checklist__promotion-title"><?php echo esc_html( $section['label'] ); ?></span>
					<?php if ( ! empty( $section['subtitle'] ) ) : ?>
						<span class="botiga-setup-checklist__promotion-separator" aria-hidden="true">—</span>
						<span class="botiga-setup-checklist__promotion-subtitle"><?php echo esc_html( $section['subtitle'] ); ?></span>
					<?php endif; ?>
				</span>
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="8" fill="none" aria-hidden="true">
					<path d="m6.281 7.719-6-6a1.059 1.059 0 0 1 0-1.438 1.059 1.059 0 0 1 1.438 0L7 5.594 12.281.28a1.06 1.06 0 0 1 1.438 0 1.059 1.059 0 0 1 0 1.438l-6 6a1.059 1.059 0 0 1-1.438 0Z" fill="#A7AAAD"/>
				</svg>
			</button>

			<div
				id="<?php echo esc_attr( $body_id ); ?>"
				class="botiga-setup-checklist__promotion-body"
			>
				<div class="botiga-setup-checklist__promotion-grid">
					<?php foreach ( $section['items'] as $item ) : ?>
						<article class="botiga-setup-checklist__promotion-card">
							<?php if ( ! empty( $item['icon'] ) ) : ?>
								<div class="botiga-setup-checklist__promotion-icon" aria-hidden="true">
									<img src="<?php echo esc_url( $item['icon'] ); ?>" alt="">
								</div>
							<?php endif; ?>

							<div class="botiga-setup-checklist__promotion-card-content">
								<?php if ( ! empty( $item['badge_label'] ) ) : ?>
									<span class="botiga-setup-checklist__promotion-badge <?php echo esc_attr( $item['badge_class'] ?? '' ); ?>">
										<?php echo esc_html( $item['badge_label'] ); ?>
									</span>
								<?php endif; ?>

								<h3><?php echo esc_html( $item['title'] ); ?></h3>

								<?php if ( ! empty( $item['description'] ) ) : ?>
									<p class="botiga-setup-checklist__promotion-card-description">
										<?php echo esc_html( $item['description'] ); ?>
									</p>
								<?php endif; ?>

								<?php if ( ! empty( $item['action_label'] ) && ( 'plugin' !== ( $item['action_type'] ?? '' ) || $can_manage_plugins ) ) : ?>
									<div class="botiga-setup-checklist__promotion-card-actions">
										<?php if ( 'plugin' === ( $item['action_type'] ?? '' ) && ! empty( $item['plugin_slug'] ) && ! empty( $item['plugin_name'] ) ) : ?>
											<button
												type="button"
												class="botiga-setup-checklist__promotion-card-link botiga-install-plugin"
												data-plugin-slug="<?php echo esc_attr( $item['plugin_slug'] ); ?>"
												data-plugin-name="<?php echo esc_attr( $item['plugin_name'] ); ?>"
												data-plugin-action="<?php echo esc_attr( $item['plugin_action'] ?? 'install' ); ?>"
											>
												<?php echo esc_html( $item['action_label'] ); ?>
											</button>
										<?php elseif ( 'link' === ( $item['action_type'] ?? '' ) && ! empty( $item['action_url'] ) ) : ?>
											<a
												class="botiga-setup-checklist__promotion-card-link"
												href="<?php echo esc_url( $item['action_url'] ); ?>"
												<?php if ( ! empty( $item['action_external'] ) ) : ?>
													target="_blank"
													rel="noopener noreferrer"
												<?php endif; ?>
											>
												<?php echo esc_html( $item['action_label'] ); ?>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( ! empty( $section['footer_text'] ) ) : ?>
					<footer class="botiga-setup-checklist__promotion-footer">
						<span class="botiga-setup-checklist__promotion-footer-text">
							<?php echo esc_html( $section['footer_text'] ); ?>
						</span>

						<?php if ( ! empty( $section['footer_action_label'] ) && ( 'plugin' !== ( $section['footer_action_type'] ?? '' ) || $can_manage_plugins ) ) : ?>
							<?php if ( 'plugin' === ( $section['footer_action_type'] ?? '' ) && ! empty( $section['footer_plugin_slug'] ) && ! empty( $section['footer_plugin_name'] ) ) : ?>
								<button
									type="button"
									class="button button-primary botiga-setup-checklist__promotion-footer-action botiga-install-plugin"
									data-plugin-slug="<?php echo esc_attr( $section['footer_plugin_slug'] ); ?>"
									data-plugin-name="<?php echo esc_attr( $section['footer_plugin_name'] ); ?>"
									data-plugin-action="<?php echo esc_attr( $section['footer_plugin_action'] ?? 'install' ); ?>"
								>
									<?php echo esc_html( $section['footer_action_label'] ); ?>
									<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
								</button>
							<?php elseif ( ! empty( $section['footer_action_url'] ) ) : ?>
								<a
									class="button button-primary botiga-setup-checklist__promotion-footer-action"
									href="<?php echo esc_url( $section['footer_action_url'] ); ?>"
									<?php if ( ! empty( $section['footer_action_external'] ) ) : ?>
										target="_blank"
										rel="noopener noreferrer"
									<?php endif; ?>
								>
									<?php echo esc_html( $section['footer_action_label'] ); ?>
									<span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</footer>
				<?php endif; ?>
			</div>
		</section>
	<?php endforeach; ?>
</div>
