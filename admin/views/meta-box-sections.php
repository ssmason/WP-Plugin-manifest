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

	<details class="satori-manifest-legend">
		<summary><?php esc_html_e( 'Controls', 'satori-manifest' ); ?></summary>
		<ul class="satori-manifest-legend__list">
			<li>
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
				<?php esc_html_e( 'Reorder sections or items', 'satori-manifest' ); ?>
			</li>
			<li>
				<span class="dashicons dashicons-arrow-down-alt2 satori-manifest-legend__chevron"></span>
				<?php esc_html_e( 'Collapse / expand a section', 'satori-manifest' ); ?>
			</li>
			<li>
				<svg width="13" height="13" viewBox="0 0 1024 1024" fill="#f70202" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z"/><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z"/><path d="M328 340.8l32-31.2 348 348-32 32z"/></svg>
				<?php esc_html_e( 'Remove item — no confirmation', 'satori-manifest' ); ?>
			</li>
			<li>
				<svg width="15" height="15" viewBox="0 0 1024 1024" fill="#f70202" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z"/><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z"/><path d="M328 340.8l32-31.2 348 348-32 32z"/></svg>
				<?php esc_html_e( 'Remove section — click twice to confirm', 'satori-manifest' ); ?>
			</li>
			<li>
				<span class="dashicons dashicons-editor-help"></span>
				<?php esc_html_e( 'Leave price blank to use item as a subsection header', 'satori-manifest' ); ?>
			</li>
		</ul>
	</details>

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
					><svg width="25" height="25" viewBox="0 0 1024 1024" fill="#f70202" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z"/><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z"/><path d="M328 340.8l32-31.2 348 348-32 32z"/></svg></button>
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
										<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>"><svg width="15" height="15" viewBox="0 0 1024 1024" fill="#f70202" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z"/><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z"/><path d="M328 340.8l32-31.2 348 348-32 32z"/></svg></button>
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
			<button type="button" class="button-link satori-manifest-section__remove" aria-label="<?php esc_attr_e( 'Remove section', 'satori-manifest' ); ?>"><svg width="25" height="25" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M154 260h568v700H154z" fill="#FF3B30"/><path d="M624.428 261.076v485.956c0 57.379-46.737 103.894-104.391 103.894h-362.56v107.246h566.815V261.076h-99.864z" fill="#030504"/><path d="M633.596 235.166l-228.054-71.773 31.55-99.3 228.055 71.773z" fill="#FF3B30"/><path d="M847.401 324.783c-2.223 0-4.475-.333-6.706-1.034L185.038 117.401c-11.765-3.703-18.298-16.239-14.592-27.996 3.706-11.766 16.241-18.288 27.993-14.595l655.656 206.346c11.766 3.703 18.298 16.239 14.592 27.996-2.995 9.531-11.795 15.631-21.286 15.631z" fill="#FF3B30"/></svg></button>
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
			<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>"><svg width="15" height="15" viewBox="0 0 1024 1024" fill="#f70202" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M512 897.6c-108 0-209.6-42.4-285.6-118.4-76-76-118.4-177.6-118.4-285.6 0-108 42.4-209.6 118.4-285.6 76-76 177.6-118.4 285.6-118.4 108 0 209.6 42.4 285.6 118.4 157.6 157.6 157.6 413.6 0 571.2-76 76-177.6 118.4-285.6 118.4z m0-760c-95.2 0-184.8 36.8-252 104-67.2 67.2-104 156.8-104 252s36.8 184.8 104 252c67.2 67.2 156.8 104 252 104 95.2 0 184.8-36.8 252-104 139.2-139.2 139.2-364.8 0-504-67.2-67.2-156.8-104-252-104z"/><path d="M707.872 329.392L348.096 689.16l-31.68-31.68 359.776-359.768z"/><path d="M328 340.8l32-31.2 348 348-32 32z"/></svg></button>
		</td>
	</tr>
</template>
