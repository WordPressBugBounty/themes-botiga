<?php
/**
 * Shared Starter Sites banner component.
 *
 * Callers provide presentation-specific copy, classes, and actions through
 * the $starter_banner array so the component can be reused without coupling
 * it to a particular admin screen. Action labels and URLs intentionally live
 * in caller configuration so each surface can customize its CTA independently.
 *
 * @since 2.4.9
 *
 * @package Botiga
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

( static function ( $starter_banner ) {
	if ( empty( $starter_banner ) || ! is_array( $starter_banner ) ) {
		return;
	}

	$allowed_tags = array( 'div', 'section', 'p', 'h2', 'span', 'sup' );
	$root_tag     = isset( $starter_banner['root_tag'] ) && in_array( $starter_banner['root_tag'], $allowed_tags, true )
		? $starter_banner['root_tag']
		: 'div';
	$title_tag    = isset( $starter_banner['title_tag'] ) && in_array( $starter_banner['title_tag'], $allowed_tags, true )
		? $starter_banner['title_tag']
		: '';
	$description_tag = isset( $starter_banner['description_tag'] ) && in_array( $starter_banner['description_tag'], $allowed_tags, true )
		? $starter_banner['description_tag']
		: 'div';
	$badge_tag = isset( $starter_banner['badge']['tag'] ) && in_array( $starter_banner['badge']['tag'], $allowed_tags, true )
		? $starter_banner['badge']['tag']
		: 'span';
	$classes = isset( $starter_banner['classes'] ) && is_array( $starter_banner['classes'] )
		? $starter_banner['classes']
		: array();
	$actions = isset( $starter_banner['actions'] ) && is_array( $starter_banner['actions'] )
		? $starter_banner['actions']
		: array();

	$render_text = static function ( $value, $allow_html = false ) {
		if ( $allow_html ) {
			echo wp_kses_post( $value );
			return;
		}

		echo esc_html( $value );
	};

	$render_attributes = static function ( $attributes ) {
		if ( empty( $attributes ) || ! is_array( $attributes ) ) {
			return;
		}

		foreach ( $attributes as $name => $value ) {
			$name = strtolower( (string) $name );

			if ( ! preg_match( '/^(?:data|aria)-[a-z0-9_-]+$/', $name ) ) {
				continue;
			}

			printf( ' %1$s="%2$s"', esc_attr( $name ), esc_attr( $value ) );
		}
	};
	?>
	<<?php echo esc_attr( $root_tag ); ?> class="<?php echo esc_attr( $classes['root'] ?? '' ); ?>">
		<div class="<?php echo esc_attr( $classes['content'] ?? '' ); ?>">
			<?php if ( ! empty( $starter_banner['greeting'] ) ) : ?>
				<div class="<?php echo esc_attr( $classes['greeting'] ?? '' ); ?>">
					<?php $render_text( $starter_banner['greeting'], ! empty( $starter_banner['greeting_allow_html'] ) ); ?>
				</div>
			<?php endif; ?>

			<div class="<?php echo esc_attr( $classes['heading'] ?? '' ); ?>">
				<?php if ( $title_tag ) : ?>
					<<?php echo esc_attr( $title_tag ); ?>><?php $render_text( $starter_banner['title'] ?? '', ! empty( $starter_banner['title_allow_html'] ) ); ?></<?php echo esc_attr( $title_tag ); ?>>
				<?php else : ?>
					<?php $render_text( $starter_banner['title'] ?? '', ! empty( $starter_banner['title_allow_html'] ) ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $starter_banner['badge']['label'] ) ) : ?>
					<<?php echo esc_attr( $badge_tag ); ?> class="<?php echo esc_attr( $starter_banner['badge']['class'] ?? '' ); ?>">
						<?php echo esc_html( $starter_banner['badge']['label'] ); ?>
					</<?php echo esc_attr( $badge_tag ); ?>>
				<?php endif; ?>
			</div>

			<<?php echo esc_attr( $description_tag ); ?> class="<?php echo esc_attr( $classes['description'] ?? '' ); ?>">
				<?php $render_text( $starter_banner['description'] ?? '', ! empty( $starter_banner['description_allow_html'] ) ); ?>
			</<?php echo esc_attr( $description_tag ); ?>>

			<?php if ( $actions ) : ?>
				<div class="<?php echo esc_attr( $classes['actions'] ?? '' ); ?>">
					<?php foreach ( $actions as $action ) : ?>
						<?php
						$action_type  = isset( $action['type'] ) ? sanitize_key( $action['type'] ) : 'link';
						$action_label = isset( $action['label'] ) ? (string) $action['label'] : '';
						$action_class = isset( $action['class'] ) ? (string) $action['class'] : '';
						?>
						<?php if ( 'button' === $action_type ) : ?>
							<button type="button" class="<?php echo esc_attr( $action_class ); ?>"<?php $render_attributes( $action['attributes'] ?? array() ); ?>>
								<?php echo esc_html( $action_label ); ?>
							</button>
						<?php else : ?>
							<a href="<?php echo esc_url( $action['url'] ?? '' ); ?>" class="<?php echo esc_attr( $action_class ); ?>"<?php if ( ! empty( $action['target'] ) ) : ?> target="<?php echo esc_attr( $action['target'] ); ?>"<?php endif; ?><?php $render_attributes( $action['attributes'] ?? array() ); ?>>
								<?php echo esc_html( $action_label ); ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $starter_banner['notice'] ) ) : ?>
				<div class="<?php echo esc_attr( $classes['notice'] ?? '' ); ?>">
					<?php $render_text( $starter_banner['notice'], ! empty( $starter_banner['notice_allow_html'] ) ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $starter_banner['image_url'] ) ) : ?>
			<div class="<?php echo esc_attr( $classes['image'] ?? '' ); ?>"<?php if ( ! empty( $starter_banner['image_aria_hidden'] ) ) : ?> aria-hidden="true"<?php endif; ?>>
				<img src="<?php echo esc_url( $starter_banner['image_url'] ); ?>"<?php if ( array_key_exists( 'image_alt', $starter_banner ) ) : ?> alt="<?php echo esc_attr( $starter_banner['image_alt'] ); ?>"<?php endif; ?>>
			</div>
		<?php endif; ?>
	</<?php echo esc_attr( $root_tag ); ?>>
	<?php
} )( $starter_banner ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
