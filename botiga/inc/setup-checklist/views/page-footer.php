<?php
/**
 * Botiga Setup Checklist page footer.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$social_links = array(
	'facebook'  => array(
		'url'      => $footer_data['facebook_url'],
		'label'    => __( 'Facebook', 'botiga' ),
		'svg' => 'icon-facebook',
	),
	'instagram' => array(
		'url'      => $footer_data['instagram_url'],
		'label'    => __( 'Instagram', 'botiga' ),
		'svg' => 'icon-instagram',
	),
	'linkedin'  => array(
		'url'      => $footer_data['linkedin_url'],
		'label'    => __( 'LinkedIn', 'botiga' ),
		'svg' => 'icon-linkedin',
	),
	'x'         => array(
		'url'   => $footer_data['twitter_url'],
		'label' => __( 'X', 'botiga' ),
		'svg'   => 'icon-x.com',
	),
	'youtube'   => array(
		'url'      => $footer_data['youtube_url'],
		'label'    => __( 'YouTube', 'botiga' ),
		'svg' => 'icon-youtube',
	),
);

$social_svg_allowed_html = array(
	'svg'  => array(
		'aria-hidden' => true,
		'class'       => true,
		'fill'        => true,
		'height'      => true,
		'viewbox'     => true,
		'width'       => true,
		'xmlns'       => true,
	),
	'path' => array(
		'd' => true,
	),
);
?>
<footer class="botiga-setup-checklist__page-footer">
	<div class="botiga-setup-checklist__footer-main">
		<a class="botiga-setup-checklist__dismiss" href="<?php echo esc_url( $dismiss_url ); ?>">
			<?php esc_html_e( 'Dismiss This Checklist', 'botiga' ); ?>
		</a>

		<p class="botiga-setup-checklist__footer-credit">
			<?php esc_html_e( 'Made with ♥ by the aThemes Team', 'botiga' ); ?>
		</p>

		<nav class="botiga-setup-checklist__footer-links" aria-label="<?php esc_attr_e( 'aThemes resources', 'botiga' ); ?>">
			<a href="<?php echo esc_url( $footer_data['support_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Support', 'botiga' ); ?>
			</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( $footer_data['docs_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Docs', 'botiga' ); ?>
			</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( $footer_data['community_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Community', 'botiga' ); ?>
			</a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( $footer_data['free_plugins_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Free Plugins', 'botiga' ); ?>
			</a>
		</nav>

		<div class="botiga-setup-checklist__social-links">
			<?php foreach ( $social_links as $social_link ) : ?>
				<a
					href="<?php echo esc_url( $social_link['url'] ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="<?php echo esc_attr( $social_link['svg'] ); ?>"
					aria-label="<?php echo esc_attr( $social_link['label'] ); ?>"
				>
					<?php if ( ! empty( $social_link['svg'] ) ) : ?>
						<?php echo wp_kses( Botiga_SVG_Icons::get_svg_icon( $social_link['svg'] ), $social_svg_allowed_html ); ?>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="botiga-setup-checklist__footer-meta">
		<p>
			<?php esc_html_e( 'Please rate Botiga', 'botiga' ); ?>
			<a href="<?php echo esc_url( $footer_data['review_url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Rate Botiga five stars on WordPress.org', 'botiga' ); ?>">★★★★★</a>
			<?php esc_html_e( 'on', 'botiga' ); ?>
			<a href="<?php echo esc_url( $footer_data['review_url'] ); ?>" target="_blank" rel="noopener noreferrer">WordPress.org</a>
			<?php esc_html_e( 'to help us spread the word.', 'botiga' ); ?>
		</p>

		<span>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: Botiga version number. */
					__( 'Botiga %s', 'botiga' ),
					$footer_data['version']
				)
			);
			?>
		</span>
	</div>
</footer>
