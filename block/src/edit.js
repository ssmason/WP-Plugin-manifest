/**
 * Satori Manifest — Block edit component.
 *
 * Renders a live preview of selected manifests directly in the editor,
 * matching the frontend output structure so the user sees real data.
 * Falls back to a placeholder when no manifests are selected, and shows
 * a spinner while the REST API resolves.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

import Inspector from './inspector';
import { parseSections, formatPrice } from './utils';

/**
 * Demo sections shown when no manifests are selected.
 *
 * Rendered in the editor when the block is first dropped onto a page so the
 * user sees the visual style immediately rather than a blank placeholder.
 * The block Patterns preview is server-side (render.php) — this data is only
 * used in the React edit component. Items use generic service names and prices.
 *
 * @type {Array<Object>}
 */
const DEMO_SECTIONS = [
	{
		title: 'Section Title',
		items: [
			{ label: 'Service Name', description: 'Optional description', price: '45.00', price_prefix: 'from' },
			{ label: 'Service Name', description: '', price: '60.00', price_prefix: '' },
			{ label: 'Service Name', description: 'Optional description', price: '25.00', price_prefix: 'from' },
		],
	},
	{
		title: 'Another Section',
		items: [
			{ label: 'Service Name', description: '', price: '35.00', price_prefix: '' },
			{ label: 'Service Name', description: 'Optional description', price: '50.00', price_prefix: 'from' },
			{ label: 'Service Name', description: '', price: '80.00', price_prefix: '' },
		],
	},
];

/**
 * Fields requested from the REST API for each manifest record.
 *
 * Defined as a module-level constant so the object reference is stable
 * across renders — an inline object would be recreated every render,
 * causing hasFinishedResolution() to never match on reference equality.
 *
 * @type {Object}
 */
const ENTITY_FIELDS = { _fields: 'id,title,meta' };

/**
 * Edit component for the satori-manifest/price-list block.
 *
 * @param {Object}   props               Component props passed by the block editor.
 * @param {Object}   props.attributes    Block attribute values.
 * @param {Function} props.setAttributes Setter for block attributes.
 * @return {JSX.Element} The editor UI for this block.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		manifestIds,
		layout,
		colorScheme,
		showPrices,
		showDescriptions,
		pricePrefix,
		customBgColor,
		customAccentColor,
		customTitleColor,
		customCardColor,
		showBackground,
		cardPadding,
		titlePadding,
		showItemBorder,
		titleBgColor,
		titleFontSize,
		titleFontWeight,
		titleFontFamily,
		itemFontSize,
		itemFontWeight,
		itemFontFamily,
	} = attributes;

	const blockClasses = [
		'satori-manifest-price-list',
		`is-layout-${ layout }`,
		`is-scheme-${ colorScheme }`,
		! showBackground  ? 'has-no-background'   : '',
		titleBgColor      ? 'has-title-bg'        : '',
		! cardPadding     ? 'has-no-card-padding'   : '',
		! titlePadding    ? 'has-no-title-padding'  : '',
		! showItemBorder  ? 'has-no-item-border'   : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const blockStyle = {
		...( colorScheme === 'custom' && customBgColor     ? { '--sm-custom-bg':     customBgColor     } : {} ),
		...( colorScheme === 'custom' && customAccentColor ? { '--sm-custom-accent': customAccentColor } : {} ),
		...( colorScheme === 'custom' && customTitleColor  ? { '--sm-custom-title':  customTitleColor  } : {} ),
		...( colorScheme === 'custom' && customCardColor   ? { '--sm-custom-card':   customCardColor   } : {} ),
		...( titleBgColor    ? { '--sm-title-bg':      titleBgColor              } : {} ),
		...( titleFontFamily ? { '--sm-title-font':    titleFontFamily           } : {} ),
		...( titleFontSize   ? { '--sm-title-size':   `${ titleFontSize }px`    } : {} ),
		...( titleFontWeight ? { '--sm-title-weight':  titleFontWeight          } : {} ),
		...( itemFontFamily  ? { '--sm-item-font':     itemFontFamily           } : {} ),
		...( itemFontSize    ? { '--sm-item-size':    `${ itemFontSize }px`     } : {} ),
		...( itemFontWeight  ? { '--sm-item-weight':   itemFontWeight           } : {} ),
	};

	const blockProps = useBlockProps( {
		className: blockClasses,
		style: blockStyle,
	} );

	// Fetch full manifest records including meta from the REST API.
	const { manifests, isLoading } = useSelect(
		( select ) => {
			if ( ! manifestIds || manifestIds.length === 0 ) {
				return { manifests: [], isLoading: false };
			}

			const store = select( 'core' );
			const records = manifestIds
				.map( ( id ) =>
					store.getEntityRecord(
						'postType',
						'sm_manifest',
						id,
						ENTITY_FIELDS
					)
				)
				.filter( Boolean );

			const isDone = manifestIds.every( ( id ) =>
				store.hasFinishedResolution( 'getEntityRecord', [
					'postType',
					'sm_manifest',
					id,
					ENTITY_FIELDS,
				] )
			);

			return { manifests: records, isLoading: ! isDone };
		},
		[ manifestIds ]
	);

	// ── No selection — render styled demo so patterns preview looks correct ───

	if ( ! manifestIds || manifestIds.length === 0 ) {
		return (
			<>
				<Inspector
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
				<div { ...blockProps }>
					<div className="satori-manifest-price-list__demo-notice">
						{ __(
							'Select manifests in the sidebar to display live data.',
							'satori-manifest'
						) }
					</div>
					<div className="satori-manifest-price-list__manifest">
						{ DEMO_SECTIONS.map( ( section, si ) => (
							<div
								key={ si }
								className="satori-manifest-price-list__section"
							>
								<h3 className="satori-manifest-price-list__section-title">
									{ section.title }
								</h3>
								<ul className="satori-manifest-price-list__items">
									{ section.items.map( ( item, ii ) => (
										<li
											key={ ii }
											className="satori-manifest-price-list__item"
										>
											<div className="satori-manifest-price-list__item-label">
												<span className="satori-manifest-price-list__item-name">
													{ item.label }
												</span>
												{ showDescriptions && item.description && (
													<span className="satori-manifest-price-list__item-desc">
														{ item.description }
													</span>
												) }
											</div>
											{ showPrices && (
												<span className="satori-manifest-price-list__item-price">
													{ item.price_prefix && (
														<span className="satori-manifest-price-list__item-prefix">
															{ item.price_prefix }{ ' ' }
														</span>
													) }
													{ formatPrice( item.price ) }
												</span>
											) }
										</li>
									) ) }
								</ul>
							</div>
						) ) }
					</div>
				</div>
			</>
		);
	}

	// ── Loading state ──────────────────────────────────────────────────────────

	if ( isLoading ) {
		return (
			<>
				<Inspector
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
				<div { ...blockProps }>
					<Spinner />
				</div>
			</>
		);
	}

	// ── Live preview ───────────────────────────────────────────────────────────

	return (
		<>
			<Inspector attributes={ attributes } setAttributes={ setAttributes } />

			<div { ...blockProps }>
				{ manifests.map( ( manifest ) => {
					const sections = parseSections(
						manifest.meta?._satori_manifest_sections
					);

					return (
						<div
							key={ manifest.id }
							className="satori-manifest-price-list__manifest"
						>
							{ sections.map( ( section, si ) => {
								const items = Array.isArray( section.items )
									? section.items
									: [];

								if ( ! section.title && items.length === 0 ) {
									return null;
								}

								return (
									<div
										// Composite key: title + index guards against duplicate titles.
										key={ `${ section.title }-${ si }` }
										className="satori-manifest-price-list__section"
									>
										{ section.title && (
											<h3 className="satori-manifest-price-list__section-title">
												{ section.title }
											</h3>
										) }

										{ items.length > 0 && (
											<ul className="satori-manifest-price-list__items">
												{ items.map( ( item, ii ) => {
													if ( ! item.label ) {
														return null;
													}

													const prefix =
														pricePrefix ||
														item.price_prefix ||
														'';

													return (
														<li
															key={ `${ item.label }-${ ii }` }
															className="satori-manifest-price-list__item"
														>
															<div className="satori-manifest-price-list__item-label">
																<span className="satori-manifest-price-list__item-name">
																	{ item.label }
																</span>
																{ showDescriptions &&
																	item.description && (
																		<span className="satori-manifest-price-list__item-desc">
																			{ item.description }
																		</span>
																	) }
															</div>

															{ showPrices && (
																<span className="satori-manifest-price-list__item-price">
																	{ prefix && (
																		<span className="satori-manifest-price-list__item-prefix">
																			{ prefix }{ ' ' }
																		</span>
																	) }
																	{ formatPrice( item.price ) }
																</span>
															) }
														</li>
													);
												} ) }
											</ul>
										) }
									</div>
								);
							} ) }

							{ sections.length === 0 && (
								<p className="satori-manifest-price-list__empty">
									{ __(
										'This manifest has no sections yet. Edit it under Manifest → All Manifests.',
										'satori-manifest'
									) }
								</p>
							) }
						</div>
					);
				} ) }
			</div>
		</>
	);
}
