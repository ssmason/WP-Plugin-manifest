<?php
/**
 * Block render template — satori-manifest/price-list.
 *
 * Called by WordPress when rendering the block on the frontend. WordPress
 * makes three variables available in this file's scope:
 *
 *   $attributes (array)    — Block attribute values from the editor.
 *   $content    (string)   — Inner block content (unused — dynamic block).
 *   $block      (WP_Block) — The block instance object.
 *
 * All output is escaped at the point of echo. No direct user input reaches
 * this file — attributes are validated and sanitized on save by the editor.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Read attributes ───────────────────────────────────────────────────────────

$manifest_ids = isset( $attributes['manifestIds'] ) && is_array( $attributes['manifestIds'] )
	? array_map( 'absint', $attributes['manifestIds'] )
	: array();

$layout                = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'single-column';
$color_scheme          = isset( $attributes['colorScheme'] ) ? sanitize_key( $attributes['colorScheme'] ) : 'default';
$show_prices           = isset( $attributes['showPrices'] ) ? (bool) $attributes['showPrices'] : true;
$show_descs            = isset( $attributes['showDescriptions'] ) ? (bool) $attributes['showDescriptions'] : true;
$price_prefix_override = isset( $attributes['pricePrefix'] ) ? sanitize_text_field( (string) $attributes['pricePrefix'] ) : '';

$show_background  = isset( $attributes['showBackground'] )  ? (bool) $attributes['showBackground'] : true;
$title_bg_color   = isset( $attributes['titleBgColor'] )   ? sanitize_hex_color( (string) $attributes['titleBgColor'] ) : '';
$title_font_size   = isset( $attributes['titleFontSize'] )   ? absint( $attributes['titleFontSize'] ) : 0;
$title_font_weight = isset( $attributes['titleFontWeight'] ) ? sanitize_text_field( (string) $attributes['titleFontWeight'] ) : '';
$title_font_family = isset( $attributes['titleFontFamily'] ) ? sanitize_text_field( (string) $attributes['titleFontFamily'] ) : '';
$item_font_size    = isset( $attributes['itemFontSize'] )    ? absint( $attributes['itemFontSize'] ) : 0;
$item_font_weight  = isset( $attributes['itemFontWeight'] )  ? sanitize_text_field( (string) $attributes['itemFontWeight'] ) : '';
$item_font_family  = isset( $attributes['itemFontFamily'] )  ? sanitize_text_field( (string) $attributes['itemFontFamily'] ) : '';

$wrapper_classes = implode(
	' ',
	array_filter(
		array(
			'satori-manifest-price-list',
			'is-layout-' . $layout,
			'is-scheme-' . $color_scheme,
			! $show_background ? 'has-no-background' : '',
			$title_bg_color    ? 'has-title-bg'      : '',
		)
	)
);

// Build optional inline style for custom colour scheme and title background.
$wrapper_style = '';
$custom_props  = '';
if ( 'custom' === $color_scheme ) {
	$custom_bg     = isset( $attributes['customBgColor'] )     ? sanitize_hex_color( (string) $attributes['customBgColor'] )     : '';
	$custom_accent = isset( $attributes['customAccentColor'] ) ? sanitize_hex_color( (string) $attributes['customAccentColor'] ) : '';
	$custom_title  = isset( $attributes['customTitleColor'] )  ? sanitize_hex_color( (string) $attributes['customTitleColor'] )  : '';
	if ( $custom_bg ) {
		$custom_props .= '--sm-custom-bg:' . $custom_bg . ';';
	}
	if ( $custom_accent ) {
		$custom_props .= '--sm-custom-accent:' . $custom_accent . ';';
	}
	if ( $custom_title ) {
		$custom_props .= '--sm-custom-title:' . $custom_title . ';';
	}
}
if ( $title_bg_color ) {
	$custom_props .= '--sm-title-bg:' . $title_bg_color . ';';
}
if ( '' !== $title_font_family ) {
	$custom_props .= '--sm-title-font:' . $title_font_family . ';';
}
if ( $title_font_size > 0 ) {
	$custom_props .= '--sm-title-size:' . $title_font_size . 'px;';
}
if ( '' !== $title_font_weight ) {
	$custom_props .= '--sm-title-weight:' . $title_font_weight . ';';
}
if ( '' !== $item_font_family ) {
	$custom_props .= '--sm-item-font:' . $item_font_family . ';';
}
if ( $item_font_size > 0 ) {
	$custom_props .= '--sm-item-size:' . $item_font_size . 'px;';
}
if ( '' !== $item_font_weight ) {
	$custom_props .= '--sm-item-weight:' . $item_font_weight . ';';
}
if ( $custom_props ) {
	$wrapper_style = $custom_props;
}

if ( empty( $manifest_ids ) ) {
	// In admin or REST contexts (e.g. the Patterns editor preview) render a
	// styled demo skeleton so the pattern preview shows the visual style and
	// colour scheme rather than a blank space.
	// On the public frontend an unconfigured block has nothing to show.
	if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$demo_sections = array(
		array(
			'title' => __( 'Section Title', 'satori-manifest' ),
			'items' => array(
				array( 'label' => __( 'Service Name', 'satori-manifest' ), 'description' => __( 'Optional description', 'satori-manifest' ), 'price' => '45.00', 'price_prefix' => 'from' ),
				array( 'label' => __( 'Service Name', 'satori-manifest' ), 'description' => '',                                                 'price' => '60.00', 'price_prefix' => '' ),
				array( 'label' => __( 'Service Name', 'satori-manifest' ), 'description' => __( 'Optional description', 'satori-manifest' ), 'price' => '25.00', 'price_prefix' => 'from' ),
			),
		),
		array(
			'title' => __( 'Another Section', 'satori-manifest' ),
			'items' => array(
				array( 'label' => __( 'Service Name', 'satori-manifest' ), 'description' => '',                                                 'price' => '35.00', 'price_prefix' => '' ),
				array( 'label' => __( 'Service Name', 'satori-manifest' ), 'description' => __( 'Optional description', 'satori-manifest' ), 'price' => '50.00', 'price_prefix' => 'from' ),
			),
		),
	);

	ob_start();
	?>
	<div class="<?php echo esc_attr( $wrapper_classes ); ?>"<?php if ( $wrapper_style ) : ?> style="<?php echo esc_attr( $wrapper_style ); ?>"<?php endif; ?>>
		<div class="satori-manifest-price-list__manifest">
			<?php foreach ( $demo_sections as $demo_section ) : ?>
				<div class="satori-manifest-price-list__section">
					<h3 class="satori-manifest-price-list__section-title">
						<?php echo esc_html( $demo_section['title'] ); ?>
					</h3>
					<ul class="satori-manifest-price-list__items">
						<?php foreach ( $demo_section['items'] as $demo_item ) : ?>
							<li class="satori-manifest-price-list__item">
								<div class="satori-manifest-price-list__item-label">
									<span class="satori-manifest-price-list__item-name">
										<?php echo esc_html( $demo_item['label'] ); ?>
									</span>
									<?php if ( $show_descs && ! empty( $demo_item['description'] ) ) : ?>
										<span class="satori-manifest-price-list__item-desc">
											<?php echo esc_html( $demo_item['description'] ); ?>
										</span>
									<?php endif; ?>
								</div>
								<?php if ( $show_prices ) : ?>
									<span class="satori-manifest-price-list__item-price">
										<?php if ( ! empty( $demo_item['price_prefix'] ) ) : ?>
											<span class="satori-manifest-price-list__item-prefix">
												<?php echo esc_html( $demo_item['price_prefix'] ); ?>
											</span>
										<?php endif; ?>
										<?php echo esc_html( number_format_i18n( (float) $demo_item['price'], 2 ) ); ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

?>
<div class="<?php echo esc_attr( $wrapper_classes ); ?>"<?php if ( $wrapper_style ) : ?> style="<?php echo esc_attr( $wrapper_style ); ?>"<?php endif; ?>>
	<?php foreach ( $manifest_ids as $post_id ) : ?>
		<?php
		$manifest = get_post( $post_id );

		if (
			! $manifest instanceof \WP_Post
			|| \SatoriManifest\Post_Types::CPT_MANIFEST !== $manifest->post_type
			|| 'publish' !== $manifest->post_status
		) {
			continue;
		}

		$sections = \SatoriManifest\Manifest_Repository::get_sections( $post_id );
		?>
		<div class="satori-manifest-price-list__manifest">
			<?php foreach ( $sections as $section ) : ?>
				<?php
				$section_title = isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '';
				$items         = isset( $section['items'] ) && is_array( $section['items'] ) ? $section['items'] : array();

				if ( empty( $section_title ) && empty( $items ) ) {
					continue;
				}
				?>
				<div class="satori-manifest-price-list__section">
					<?php if ( ! empty( $section_title ) ) : ?>
						<h3 class="satori-manifest-price-list__section-title">
							<?php echo esc_html( $section_title ); ?>
						</h3>
					<?php endif; ?>

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
	<?php endforeach; ?>
</div>
