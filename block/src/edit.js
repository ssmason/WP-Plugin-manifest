/**
 * Satori Manifest — Block edit component.
 *
 * Renders the editor-side view of the price-list block. Shows a live
 * preview of selected sections or a placeholder if none are chosen.
 *
 * @package
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { listView } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';

import Inspector from './inspector';

/**
 * Edit component for the satori-manifest/price-list block.
 *
 * @param {Object}   props               Component props passed by the block editor.
 * @param {Object}   props.attributes    Block attribute values.
 * @param {Function} props.setAttributes Setter for block attributes.
 * @return {JSX.Element} The editor UI for this block.
 */
export default function Edit({ attributes, setAttributes }) {
	const { sectionIds, layout } = attributes;

	const blockProps = useBlockProps({
		className: `satori-manifest-price-list is-layout-${layout}`,
	});

	// Resolve section data for the editor preview.
	const sections = useSelect(
		(select) => {
			if (!sectionIds || sectionIds.length === 0) {
				return [];
			}
			return sectionIds
				.map((id) =>
					select('core').getEntityRecord(
						'postType',
						'sm_price_section',
						id
					)
				)
				.filter(Boolean);
		},
		[sectionIds]
	);

	const hasSelections = sectionIds && sectionIds.length > 0;

	return (
		<>
			<Inspector attributes={attributes} setAttributes={setAttributes} />

			<div {...blockProps}>
				{!hasSelections ? (
					<Placeholder
						icon={listView}
						label={__('Price List', 'satori-manifest')}
						instructions={__(
							'Select one or more price list sections in the sidebar to display them here.',
							'satori-manifest'
						)}
					/>
				) : (
					<div className="satori-manifest-price-list__preview">
						{sections.map((section) => (
							<div
								key={section.id}
								className="satori-manifest-section-preview"
							>
								<h3
									className="satori-manifest-section-preview__title"
									dangerouslySetInnerHTML={{
										__html: section.title.rendered,
									}}
								/>
								<p className="satori-manifest-section-preview__hint">
									{__(
										'Items will render on the frontend.',
										'satori-manifest'
									)}
								</p>
							</div>
						))}
					</div>
				)}
			</div>
		</>
	);
}
