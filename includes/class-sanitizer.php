<?php
/**
 * Input sanitization for manifest data.
 *
 * Centralises all sanitization logic for sections and items so the meta box
 * save handler (class-meta-box.php) stays focused on WordPress wiring.
 * Any code path that accepts user-submitted sections JSON should pass the
 * decoded array through this class before persisting it.
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
 * Class Sanitizer
 *
 * Sanitizes decoded sections and items arrays before they are persisted.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Sanitizer {

	/**
	 * Sanitizes a decoded sections array.
	 *
	 * Rebuilds the array with only expected keys, re-numbers sort_order from
	 * the array index (honouring client-side reordering), and delegates item
	 * sanitization to sanitize_items().
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  array<int,array<string,mixed>> $sections  Raw decoded sections array.
	 * @return array<int,array<string,mixed>>             Sanitized sections array.
	 */
	public static function sanitize_sections( array $sections ): array {
		$out = array();

		foreach ( $sections as $idx => $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$raw_items = isset( $section['items'] ) && is_array( $section['items'] )
				? $section['items']
				: array();

			$out[] = array(
				'title'      => isset( $section['title'] ) ? sanitize_text_field( (string) $section['title'] ) : '',
				'sort_order' => $idx,
				'items'      => self::sanitize_items( $raw_items ),
			);
		}

		return $out;
	}

	/**
	 * Sanitizes a decoded items array.
	 *
	 * Rebuilds the array with only expected keys, normalises price to a
	 * two-decimal string, and re-numbers sort_order from the array index.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  array<int,array<string,mixed>> $items  Raw decoded items array.
	 * @return array<int,array<string,mixed>>          Sanitized items array.
	 */
	private static function sanitize_items( array $items ): array {
		$out = array();

		foreach ( $items as $idx => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$out[] = array(
				'label'        => isset( $item['label'] ) ? sanitize_text_field( (string) $item['label'] ) : '',
				'description'  => isset( $item['description'] ) ? sanitize_text_field( (string) $item['description'] ) : '',
				'price'        => isset( $item['price'] ) ? number_format( (float) $item['price'], 2, '.', '' ) : '0.00',
				'price_prefix' => isset( $item['price_prefix'] ) ? sanitize_text_field( (string) $item['price_prefix'] ) : '',
				'sort_order'   => $idx,
			);
		}

		return $out;
	}
}
