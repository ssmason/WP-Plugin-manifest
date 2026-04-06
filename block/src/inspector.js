/**
 * Satori Manifest — Inspector sidebar controls.
 *
 * Renders the InspectorControls panel for the price-list block,
 * providing section selection, layout preset, and display toggles.
 *
 * @package
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
	TextControl,
	CheckboxControl,
	Spinner,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * Layout preset options.
 *
 * @type {Array<{label: string, value: string}>}
 */
const LAYOUT_OPTIONS = [
	{ label: __('Classic List', 'satori-manifest'), value: 'single-column' },
	{ label: __('Split Grid', 'satori-manifest'), value: 'two-column' },
	{ label: __('Card Style', 'satori-manifest'), value: 'card-style' },
	{ label: __('Minimal', 'satori-manifest'), value: 'minimal' },
];

/**
 * Inspector controls component for the price-list block.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Inspector controls panel.
 */
export default function Inspector({ attributes, setAttributes }) {
	const { sectionIds, layout, showPrices, showDescriptions, pricePrefix } =
		attributes;

	// Load all sections via REST API (registered with show_in_rest: true).
	const { sections, isLoading } = useSelect((select) => {
		const store = select('core');
		const query = {
			per_page: -1,
			status: 'publish',
			orderby: 'meta_value_num',
			order: 'asc',
		};

		return {
			sections: store.getEntityRecords(
				'postType',
				'sm_price_section',
				query
			),
			isLoading: !store.hasFinishedResolution('getEntityRecords', [
				'postType',
				'sm_price_section',
				query,
			]),
		};
	});

	/**
	 * Toggles a section ID in the sectionIds attribute array.
	 *
	 * @param {number}  id      Section post ID.
	 * @param {boolean} checked Whether the checkbox is checked.
	 */
	function toggleSection(id, checked) {
		const next = checked
			? [...sectionIds, id]
			: sectionIds.filter((s) => s !== id);
		setAttributes({ sectionIds: next });
	}

	return (
		<InspectorControls>
			<PanelBody
				title={__('Sections', 'satori-manifest')}
				initialOpen={true}
			>
				{isLoading && <Spinner />}
				{!isLoading && (!sections || sections.length === 0) && (
					<p>
						{__(
							'No sections found. Create sections under Manifest → All Sections.',
							'satori-manifest'
						)}
					</p>
				)}
				{!isLoading &&
					sections &&
					sections.map((section) => (
						<CheckboxControl
							key={section.id}
							label={section.title.rendered}
							checked={sectionIds.includes(section.id)}
							onChange={(checked) =>
								toggleSection(section.id, checked)
							}
						/>
					))}
			</PanelBody>

			<PanelBody
				title={__('Layout', 'satori-manifest')}
				initialOpen={true}
			>
				<SelectControl
					label={__('Display preset', 'satori-manifest')}
					value={layout}
					options={LAYOUT_OPTIONS}
					onChange={(value) => setAttributes({ layout: value })}
				/>
			</PanelBody>

			<PanelBody
				title={__('Display', 'satori-manifest')}
				initialOpen={true}
			>
				<ToggleControl
					label={__('Show prices', 'satori-manifest')}
					checked={showPrices}
					onChange={(value) => setAttributes({ showPrices: value })}
				/>
				<ToggleControl
					label={__('Show item descriptions', 'satori-manifest')}
					checked={showDescriptions}
					onChange={(value) =>
						setAttributes({ showDescriptions: value })
					}
				/>
				<TextControl
					label={__('Price prefix override', 'satori-manifest')}
					help={__(
						"Overrides the per-item prefix. Leave blank to use each item's own prefix.",
						'satori-manifest'
					)}
					value={pricePrefix}
					onChange={(value) => setAttributes({ pricePrefix: value })}
					placeholder={__('e.g. from', 'satori-manifest')}
				/>
			</PanelBody>
		</InspectorControls>
	);
}
