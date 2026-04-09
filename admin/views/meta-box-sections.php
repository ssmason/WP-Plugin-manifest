<?php
/**
 * Meta box view — Sections & Items for a single manifest.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="satori-manifest-metabox">

	<input
		type="hidden"
		id="satori-manifest-sections-data"
		name="<?php echo esc_attr( $satori_manifest_post_field ); ?>"
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
					><svg width="16" height="16" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M154 260h568v700H154z" fill="#FF3B30"/><path d="M624.428 261.076v485.956c0 57.379-46.737 103.894-104.391 103.894h-362.56v107.246h566.815V261.076h-99.864z" fill="#030504"/><path d="M320.5 870.07c-8.218 0-14.5-6.664-14.5-14.883V438.474c0-8.218 6.282-14.883 14.5-14.883s14.5 6.664 14.5 14.883v416.713c0 8.219-6.282 14.883-14.5 14.883zM543.5 870.07c-8.218 0-14.5-6.664-14.5-14.883V438.474c0-8.218 6.282-14.883 14.5-14.883s14.5 6.664 14.5 14.883v416.713c0 8.219-6.282 14.883-14.5 14.883z" fill="#152B3C"/><path d="M721.185 345.717v-84.641H164.437z" fill="#030504"/><path d="M633.596 235.166l-228.054-71.773 31.55-99.3 228.055 71.773z" fill="#FF3B30"/><path d="M847.401 324.783c-2.223 0-4.475-.333-6.706-1.034L185.038 117.401c-11.765-3.703-18.298-16.239-14.592-27.996 3.706-11.766 16.241-18.288 27.993-14.595l655.656 206.346c11.766 3.703 18.298 16.239 14.592 27.996-2.995 9.531-11.795 15.631-21.286 15.631z" fill="#FF3B30"/></svg></button>
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
									<?php $satori_manifest_price_val = ( isset( $satori_manifest_item['price'] ) && '0.00' !== $satori_manifest_item['price'] ) ? $satori_manifest_item['price'] : ''; ?>
									<td><input type="number" step="0.01" min="0" class="satori-manifest-item__price" value="<?php echo esc_attr( $satori_manifest_price_val ); ?>" placeholder="0.00" /></td>
									<td>
										<button type="button" class="button-link satori-manifest-item__move-up" aria-label="<?php esc_attr_e( 'Move item up', 'satori-manifest' ); ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
										<button type="button" class="button-link satori-manifest-item__move-down" aria-label="<?php esc_attr_e( 'Move item down', 'satori-manifest' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
										<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">&times;</button>
									</td>
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
			<button type="button" class="button-link satori-manifest-section__remove" aria-label="<?php esc_attr_e( 'Remove section', 'satori-manifest' ); ?>"><svg width="16" height="16" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M154 260h568v700H154z" fill="#FF3B30"/><path d="M624.428 261.076v485.956c0 57.379-46.737 103.894-104.391 103.894h-362.56v107.246h566.815V261.076h-99.864z" fill="#030504"/><path d="M633.596 235.166l-228.054-71.773 31.55-99.3 228.055 71.773z" fill="#FF3B30"/><path d="M847.401 324.783c-2.223 0-4.475-.333-6.706-1.034L185.038 117.401c-11.765-3.703-18.298-16.239-14.592-27.996 3.706-11.766 16.241-18.288 27.993-14.595l655.656 206.346c11.766 3.703 18.298 16.239 14.592 27.996-2.995 9.531-11.795 15.631-21.286 15.631z" fill="#FF3B30"/></svg></button>
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
		<td><input type="number" step="0.01" min="0" class="satori-manifest-item__price" value="" placeholder="0.00" /></td>
		<td>
			<button type="button" class="button-link satori-manifest-item__move-up" aria-label="<?php esc_attr_e( 'Move item up', 'satori-manifest' ); ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
			<button type="button" class="button-link satori-manifest-item__move-down" aria-label="<?php esc_attr_e( 'Move item down', 'satori-manifest' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
			<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">&times;</button>
		</td>
	</tr>
</template>
