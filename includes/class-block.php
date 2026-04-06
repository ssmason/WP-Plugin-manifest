<?php
/**
 * Block registration and server-side rendering.
 *
 * Registers the satori-manifest/price-list block and provides the
 * render_callback that outputs HTML from live CPT data.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

declare( strict_types=1 );

namespace SatoriManifest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Block
 *
 * Registers the price-list block and handles server-side rendering.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Block {

	/**
	 * Block name constant.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const BLOCK_NAME = 'satori-manifest/price-list';

	/**
	 * Registers the block type with WordPress.
	 *
	 * Hooked to 'init'.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		register_block_type(
			SATORI_MANIFEST_PATH . 'block/block.json',
			array(
				'render_callback' => array( self::class, 'render' ),
			)
		);
	}

	/**
	 * Renders the price list block on the frontend.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  array<string,mixed> $attributes  Block attribute values.
	 * @param  string              $content     Inner block content (unused — dynamic block).
	 * @return string                           HTML output.
	 */
	public static function render( array $attributes, string $content = '' ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$section_ids           = isset( $attributes['sectionIds'] ) && is_array( $attributes['sectionIds'] )
			? array_map( 'absint', $attributes['sectionIds'] )
			: array();
		$layout                = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'single-column';
		$show_prices           = isset( $attributes['showPrices'] ) ? (bool) $attributes['showPrices'] : true;
		$show_descs            = isset( $attributes['showDescriptions'] ) ? (bool) $attributes['showDescriptions'] : true;
		$price_prefix_override = isset( $attributes['pricePrefix'] ) ? sanitize_text_field( $attributes['pricePrefix'] ) : '';

		if ( empty( $section_ids ) ) {
			return '';
		}

		$wrapper_classes = implode(
			' ',
			array(
				'satori-manifest-price-list',
				'is-layout-' . esc_attr( $layout ),
			)
		);

		ob_start();
		?>
		<div class="<?php echo esc_attr( $wrapper_classes ); ?>">
			<?php foreach ( $section_ids as $post_id ) : ?>
				<?php
				$section = get_post( $post_id );

				if ( ! $section || Post_Types::CPT_SECTION !== $section->post_type || 'publish' !== $section->post_status ) {
					continue;
				}

				$items = Post_Types::get_section_items( $post_id );
				?>
				<div class="satori-manifest-price-list__section">
					<h3 class="satori-manifest-price-list__section-title">
						<?php echo esc_html( $section->post_title ); ?>
					</h3>

					<?php if ( ! empty( $items ) ) : ?>
						<ul class="satori-manifest-price-list__items">
							<?php foreach ( $items as $item ) : ?>
								<?php
								$label       = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
								$description = isset( $item['description'] ) ? sanitize_text_field( $item['description'] ) : '';
								$price       = isset( $item['price'] ) ? (float) $item['price'] : 0.0;
								$prefix      = '' !== $price_prefix_override
									? $price_prefix_override
									: ( isset( $item['price_prefix'] ) ? sanitize_text_field( $item['price_prefix'] ) : '' );

								if ( empty( $label ) ) {
									continue;
								}
								?>
								<li class="satori-manifest-price-list__item">
									<div class="satori-manifest-price-list__item-label">
										<span class="satori-manifest-price-list__item-name">
											<?php echo esc_html( $label ); ?>
										</span>
										<?php if ( $show_descs && ! empty( $description ) ) : ?>
											<span class="satori-manifest-price-list__item-desc">
												<?php echo esc_html( $description ); ?>
											</span>
										<?php endif; ?>
									</div>

									<?php if ( $show_prices ) : ?>
										<span class="satori-manifest-price-list__item-price">
											<?php if ( ! empty( $prefix ) ) : ?>
												<span class="satori-manifest-price-list__item-prefix">
													<?php echo esc_html( $prefix ); ?>
												</span>
											<?php endif; ?>
											<?php
											// translators: %s is a formatted price number.
											echo esc_html( number_format_i18n( $price, 2 ) );
											?>
										</span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}
