<?php
/**
 * Settings - AI MCP.
 *
 * @since 2.4.8
 *
 * @package Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$botiga_abilities_enabled =
	Botiga_Abilities_Access::is_enabled();

$botiga_edit_abilities_enabled =
	Botiga_Abilities_Access::is_edit_enabled();

?>

<div class="botiga-dashboard-card">
	<div class="botiga-dashboard-card-body">
		<div class="botiga-dashboard-module-card">
			<div class="botiga-dashboard-module-card-header bt-align-items-center">
				<div class="botiga-dashboard-module-card-header-info">
					<h2 class="bt-m-0 bt-mb-10px">
						<?php
						esc_html_e(
							'Enable Abilities',
							'botiga'
						);
						?>
					</h2>

					<p class="bt-text-color-grey">
						<?php
						printf( /* translators: 1: open link to learn more about MCP adapter, 2: close link to learn more about MCP adapter. */
							esc_html__(
								'Register Botiga abilities with the WordPress Abilities API. When enabled, AI clients can list, read, and interact with your theme settings. When disabled, no abilities are registered and AI clients cannot perform any actions on your theme. %1$sView Abilities API Documentation%2$s',
								'botiga'
							),
							'<a href="https://docs.athemes.com/article/abilities-in-botiga/" target="_blank" rel="noopener noreferrer">',
							'</a>'
						);
						?>
					</p>
				</div>

				<div class="botiga-dashboard-module-card-header-actions bt-pt-0">
					<div class="botiga-dashboard-box-link">
						<?php if ( $botiga_abilities_enabled ) : ?>
							<a
								href="#"
								class="botiga-dashboard-link botiga-dashboard-link-danger botiga-dashboard-option-switcher"
								data-option-id="<?php echo esc_attr( Botiga_Abilities_Access::ENABLED_OPTION ); ?>"
								data-option-activate="false"
							>
								<?php
								esc_html_e(
									'Deactivate',
									'botiga'
								);
								?>
							</a>
						<?php else : ?>
							<a
								href="#"
								class="botiga-dashboard-link botiga-dashboard-link-success botiga-dashboard-option-switcher"
								data-option-id="<?php echo esc_attr( Botiga_Abilities_Access::ENABLED_OPTION ); ?>"
								data-option-activate="true"
							>
								<?php
								esc_html_e(
									'Activate',
									'botiga'
								);
								?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="botiga-dashboard-card">
	<div class="botiga-dashboard-card-body">
		<div class="botiga-dashboard-module-card">
			<div class="botiga-dashboard-module-card-header bt-align-items-center">
				<div class="botiga-dashboard-module-card-header-info">
					<h2 class="bt-m-0 bt-mb-10px">
						<?php
						esc_html_e(
							'Enable Edit Abilities',
							'botiga'
						);
						?>
					</h2>

					<p class="bt-text-color-grey">
						<?php
						esc_html_e(
							'When enabled, AI clients can update theme settings such as typography, colors, buttons, and layout options. When disabled, these abilities are unregistered and AI clients can only read your theme data.',
							'botiga'
						);
						?>
					</p>
				</div>

				<div class="botiga-dashboard-module-card-header-actions bt-pt-0">
					<div class="botiga-dashboard-box-link">
						<?php if ( $botiga_edit_abilities_enabled ) : ?>
							<a
								href="#"
								class="botiga-dashboard-link botiga-dashboard-link-danger botiga-dashboard-option-switcher"
								data-option-id="<?php echo esc_attr( Botiga_Abilities_Access::EDIT_ENABLED_OPTION ); ?>"
								data-option-activate="false"
							>
								<?php
								esc_html_e(
									'Deactivate',
									'botiga'
								);
								?>
							</a>
						<?php else : ?>
							<a
								href="#"
								class="botiga-dashboard-link botiga-dashboard-link-success botiga-dashboard-option-switcher"
								data-option-id="<?php echo esc_attr( Botiga_Abilities_Access::EDIT_ENABLED_OPTION ); ?>"
								data-option-activate="true"
							>
								<?php
								esc_html_e(
									'Activate',
									'botiga'
								);
								?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
require get_template_directory() .
	'/inc/dashboard/html-wpvibe-banner.php';
?>
