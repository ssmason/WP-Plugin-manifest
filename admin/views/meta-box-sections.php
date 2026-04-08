<?php
/**
 * Meta box view — Sections & Items for a single manifest.
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
?>
<div class="satori-manifest-metabox">

	<input
		type="hidden"
		id="satori-manifest-sections-data"
		name="<?php echo esc_attr( Meta_Box::POST_FIELD ); ?>"
		value="<?php echo esc_attr( (string) wp_json_encode( $satori_manifest_sections ) ); ?>"
	/>

	<div class="satori-manifest-sections-toolbar">
		<button type="button" class="button satori-manifest-add-section">
			<?php esc_html_e( '+ Add Section', 'satori-manifest' ); ?>
		</button>
	</div>

	<div class="satori-manifest-sections" id="satori-manifest-sections-list">
		<?php foreach ( $satori_manifest_sections as $satori_manifest_section ) : ?>
			<?php
			$satori_manifest_items = isset( $satori_manifest_section['items'] ) && is_array( $satori_manifest_section['items'] )
				? $satori_manifest_section['items']
				: array();
			?>
			<div class="satori-manifest-section">
				<div class="satori-manifest-section__header">
					<button
						type="button"
						class="satori-manifest-section__toggle"
						aria-expanded="false"
						aria-label="<?php esc_attr_e( 'Toggle section', 'satori-manifest' ); ?>"
					><span class="dashicons dashicons-arrow-down-alt2"></span></button>

					<input
						type="text"
						class="satori-manifest-section__title-input"
						value="<?php echo esc_attr( $satori_manifest_section['title'] ?? '' ); ?>"
						placeholder="<?php esc_attr_e( 'Section title…', 'satori-manifest' ); ?>"
						aria-label="<?php esc_attr_e( 'Section title', 'satori-manifest' ); ?>"
					/>

					<div class="satori-manifest-section__order">
						<button type="button" class="button-link satori-manifest-section__move-up" aria-label="<?php esc_attr_e( 'Move section up', 'satori-manifest' ); ?>">
							<span class="dashicons dashicons-arrow-up-alt2"></span>
						</button>
						<button type="button" class="button-link satori-manifest-section__move-down" aria-label="<?php esc_attr_e( 'Move section down', 'satori-manifest' ); ?>">
							<span class="dashicons dashicons-arrow-down-alt2"></span>
						</button>
					</div>

					<button
						type="button"
						class="button-link satori-manifest-section__remove"
						aria-label="<?php esc_attr_e( 'Remove section', 'satori-manifest' ); ?>"
					><?php esc_html_e( '&times; Remove', 'satori-manifest' ); ?></button>
				</div>

				<div class="satori-manifest-section__body is-collapsed">
					<table class="satori-manifest-items wp-list-table widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Item', 'satori-manifest' ); ?></th>
								<th><?php esc_html_e( 'Description', 'satori-manifest' ); ?></th>
								<th><?php esc_html_e( 'Prefix', 'satori-manifest' ); ?></th>
								<th><?php esc_html_e( 'Price', 'satori-manifest' ); ?></th>
								<th><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'satori-manifest' ); ?></span></th>
							</tr>
						</thead>
						<tbody class="satori-manifest-items__body">
							<?php foreach ( $satori_manifest_items as $satori_manifest_item ) : ?>
								<tr class="satori-manifest-item">
									<td><input type="text" class="satori-manifest-item__label" value="<?php echo esc_attr( $satori_manifest_item['label'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Item name', 'satori-manifest' ); ?>" /></td>
									<td><input type="text" class="satori-manifest-item__description" value="<?php echo esc_attr( $satori_manifest_item['description'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Optional detail', 'satori-manifest' ); ?>" /></td>
									<td><input type="text" class="satori-manifest-item__prefix" value="<?php echo esc_attr( $satori_manifest_item['price_prefix'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'from', 'satori-manifest' ); ?>" /></td>
									<td><input type="number" step="0.01" min="0" class="satori-manifest-item__price" value="<?php echo esc_attr( $satori_manifest_item['price'] ?? '0.00' ); ?>" /></td>
									<td><button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">&times;</button></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">
									<button type="button" class="button satori-manifest-items__add-row">
										<?php esc_html_e( '+ Add Item', 'satori-manifest' ); ?>
									</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

</div>


<?php
/*
 * ── HTML templates ────────────────────────────────────────────────────────────
 * The two <template> elements below are cloned by admin.js when the user adds
 * a new section or item. PHP translation calls inside them execute at page-
 * render time (server-side), so translated strings are baked into the markup
 * before the browser receives it — no runtime i18n lookup is needed in JS.
 */
?>
<template id="satori-manifest-section-template">
	<div class="satori-manifest-section">
		<div class="satori-manifest-section__header">
			<button type="button" class="satori-manifest-section__toggle" aria-expanded="true" aria-label="<?php esc_attr_e( 'Toggle section', 'satori-manifest' ); ?>">
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</button>
			<input type="text" class="satori-manifest-section__title-input" value="" placeholder="<?php esc_attr_e( 'Section title…', 'satori-manifest' ); ?>" aria-label="<?php esc_attr_e( 'Section title', 'satori-manifest' ); ?>" />
			<div class="satori-manifest-section__order">
				<button type="button" class="button-link satori-manifest-section__move-up" aria-label="<?php esc_attr_e( 'Move section up', 'satori-manifest' ); ?>">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</button>
				<button type="button" class="button-link satori-manifest-section__move-down" aria-label="<?php esc_attr_e( 'Move section down', 'satori-manifest' ); ?>">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
			</div>
			<button type="button" class="button-link satori-manifest-section__remove" aria-label="<?php esc_attr_e( 'Remove section', 'satori-manifest' ); ?>"><?php esc_html_e( '&times; Remove', 'satori-manifest' ); ?></button>
		</div>
		<div class="satori-manifest-section__body">
			<table class="satori-manifest-items wp-list-table widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Item', 'satori-manifest' ); ?></th>
						<th><?php esc_html_e( 'Description', 'satori-manifest' ); ?></th>
						<th><?php esc_html_e( 'Prefix', 'satori-manifest' ); ?></th>
						<th><?php esc_html_e( 'Price', 'satori-manifest' ); ?></th>
						<th><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'satori-manifest' ); ?></span></th>
					</tr>
				</thead>
				<tbody class="satori-manifest-items__body"></tbody>
				<tfoot>
					<tr>
						<td colspan="5">
							<button type="button" class="button satori-manifest-items__add-row"><?php esc_html_e( '+ Add Item', 'satori-manifest' ); ?></button>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</template>

<template id="satori-manifest-item-template">
	<tr class="satori-manifest-item">
		<td><input type="text" class="satori-manifest-item__label" value="" placeholder="<?php esc_attr_e( 'Item name', 'satori-manifest' ); ?>" /></td>
		<td><input type="text" class="satori-manifest-item__description" value="" placeholder="<?php esc_attr_e( 'Optional detail', 'satori-manifest' ); ?>" /></td>
		<td><input type="text" class="satori-manifest-item__prefix" value="" placeholder="<?php esc_attr_e( 'from', 'satori-manifest' ); ?>" /></td>
		<td><input type="number" step="0.01" min="0" class="satori-manifest-item__price" value="0.00" /></td>
		<td><button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">&times;</button></td>
	</tr>
</template>
