/**
 * Satori Manifest — Block utility functions.
 *
 * Shared helpers used by the block editor components. Keeping these in a
 * dedicated module prevents utility logic from accumulating inside component
 * files and makes them independently testable.
 *
 * @package
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

/**
 * Parses the _satori_manifest_sections meta string into a sections array.
 *
 * Returns an empty array on missing or malformed JSON so components never
 * throw on a bad or absent REST API response.
 *
 * @param {string|undefined} raw Raw JSON string from post meta.
 * @return {Array} Decoded sections array, or [] on failure.
 */
export function parseSections(raw) {
	if (!raw) {
		return [];
	}
	try {
		const parsed = JSON.parse(raw);
		return Array.isArray(parsed) ? parsed : [];
	} catch {
		return [];
	}
}

/**
 * Formats a price value to two decimal places using the page locale.
 *
 * Uses Intl.NumberFormat for locale-aware output, matching the
 * number_format_i18n() behaviour on the PHP frontend.
 *
 * @param {string|number} value Raw price value.
 * @return {string} Locale-formatted price string (e.g. "12.50").
 */
export function formatPrice(value) {
	return new Intl.NumberFormat(document.documentElement.lang || 'en', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(parseFloat(value) || 0);
}
