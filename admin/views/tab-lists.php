<?php
/**
 * Lists tab — manage price list sections and their items.
 *
 * Renders a JS-driven interface for adding, editing, reordering and deleting
 * sections and their line items. All mutations are handled via AJAX.
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

$satori_manifest_sections = Post_Types::get_all_sections();
?>
<div class="satori-manifest-lists" id="satori-manifest-lists">

	<div class="satori-manifest-lists__toolbar">
		<button
			type="button"
			class="button button-primary"
			id="satori-manifest-add-section"
		>
			<?php esc_html_e( '+ Add Section', 'satori-manifest' ); ?>
		</button>
	</div>

	<div class="satori-manifest-lists__notice" id="satori-manifest-notice" aria-live="polite" hidden></div>

	<?php if ( empty( $satori_manifest_sections ) ) : ?>
		<p class="satori-manifest-lists__empty">
			<?php esc_html_e( 'No sections yet. Click "Add Section" to create your first price list section.', 'satori-manifest' ); ?>
		</p>
	<?php endif; ?>

	<ul class="satori-manifest-sections" id="satori-manifest-section-list">
		<?php foreach ( $satori_manifest_sections as $satori_manifest_section ) : ?>
			<?php
			$satori_manifest_post_id = (int) $satori_manifest_section->ID;
			$satori_manifest_items   = Post_Types::get_section_items( $satori_manifest_post_id );
			?>
			<li
				class="satori-manifest-section"
				data-post-id="<?php echo esc_attr( (string) $satori_manifest_post_id ); ?>"
			>
				<div class="satori-manifest-section__header">
					<span class="satori-manifest-section__drag dashicons dashicons-move" title="<?php esc_attr_e( 'Drag to reorder', 'satori-manifest' ); ?>"></span>
					<h3 class="satori-manifest-section__title">
						<input
							type="text"
							class="satori-manifest-section__title-input"
							value="<?php echo esc_attr( $satori_manifest_section->post_title ); ?>"
							aria-label="<?php esc_attr_e( 'Section title', 'satori-manifest' ); ?>"
						/>
					</h3>
					<div class="satori-manifest-section__actions">
						<button type="button" class="button satori-manifest-section__save">
							<?php esc_html_e( 'Save', 'satori-manifest' ); ?>
						</button>
						<button type="button" class="button satori-manifest-section__delete">
							<?php esc_html_e( 'Delete', 'satori-manifest' ); ?>
						</button>
					</div>
				</div>

				<table class="satori-manifest-items wp-list-table widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Item', 'satori-manifest' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Description', 'satori-manifest' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Prefix', 'satori-manifest' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Price', 'satori-manifest' ); ?></th>
							<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'satori-manifest' ); ?></span></th>
						</tr>
					</thead>
					<tbody class="satori-manifest-items__body">
						<?php if ( ! empty( $satori_manifest_items ) ) : ?>
							<?php foreach ( $satori_manifest_items as $satori_manifest_idx => $satori_manifest_item ) : ?>
								<tr class="satori-manifest-item" data-index="<?php echo esc_attr( (string) $satori_manifest_idx ); ?>">
									<td>
										<input
											type="text"
											class="satori-manifest-item__label"
											value="<?php echo esc_attr( $satori_manifest_item['label'] ?? '' ); ?>"
											placeholder="<?php esc_attr_e( 'Item name', 'satori-manifest' ); ?>"
										/>
									</td>
									<td>
										<input
											type="text"
											class="satori-manifest-item__description"
											value="<?php echo esc_attr( $satori_manifest_item['description'] ?? '' ); ?>"
											placeholder="<?php esc_attr_e( 'Optional detail', 'satori-manifest' ); ?>"
										/>
									</td>
									<td>
										<input
											type="text"
											class="satori-manifest-item__prefix"
											value="<?php echo esc_attr( $satori_manifest_item['price_prefix'] ?? '' ); ?>"
											placeholder="<?php esc_attr_e( 'from', 'satori-manifest' ); ?>"
										/>
									</td>
									<td>
										<input
											type="number"
											step="0.01"
											min="0"
											class="satori-manifest-item__price"
											value="<?php echo esc_attr( $satori_manifest_item['price'] ?? '0.00' ); ?>"
										/>
									</td>
									<td>
										<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">
											&times;
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
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
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<template id="satori-manifest-section-template">
	<li class="satori-manifest-section" data-post-id="0">
		<div class="satori-manifest-section__header">
			<span class="satori-manifest-section__drag dashicons dashicons-move" title="<?php esc_attr_e( 'Drag to reorder', 'satori-manifest' ); ?>"></span>
			<h3 class="satori-manifest-section__title">
				<input
					type="text"
					class="satori-manifest-section__title-input"
					value=""
					placeholder="<?php esc_attr_e( 'Section title\u2026', 'satori-manifest' ); ?>"
					aria-label="<?php esc_attr_e( 'Section title', 'satori-manifest' ); ?>"
				/>
			</h3>
			<div class="satori-manifest-section__actions">
				<button type="button" class="button satori-manifest-section__save">
					<?php esc_html_e( 'Save', 'satori-manifest' ); ?>
				</button>
				<button type="button" class="button satori-manifest-section__delete">
					<?php esc_html_e( 'Delete', 'satori-manifest' ); ?>
				</button>
			</div>
		</div>
		<table class="satori-manifest-items wp-list-table widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Item', 'satori-manifest' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Description', 'satori-manifest' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Prefix', 'satori-manifest' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Price', 'satori-manifest' ); ?></th>
					<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'satori-manifest' ); ?></span></th>
				</tr>
			</thead>
			<tbody class="satori-manifest-items__body"></tbody>
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
	</li>
</template>

<template id="satori-manifest-item-template">
	<tr class="satori-manifest-item">
		<td>
			<input type="text" class="satori-manifest-item__label" value="" placeholder="<?php esc_attr_e( 'Item name', 'satori-manifest' ); ?>" />
		</td>
		<td>
			<input type="text" class="satori-manifest-item__description" value="" placeholder="<?php esc_attr_e( 'Optional detail', 'satori-manifest' ); ?>" />
		</td>
		<td>
			<input type="text" class="satori-manifest-item__prefix" value="" placeholder="<?php esc_attr_e( 'from', 'satori-manifest' ); ?>" />
		</td>
		<td>
			<input type="number" step="0.01" min="0" class="satori-manifest-item__price" value="0.00" />
		</td>
		<td>
			<button type="button" class="button-link satori-manifest-item__remove" aria-label="<?php esc_attr_e( 'Remove item', 'satori-manifest' ); ?>">&times;</button>
		</td>
	</tr>
</template>
