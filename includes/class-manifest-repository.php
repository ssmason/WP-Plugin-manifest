<?php
/**
 * Manifest data access.
 *
 * Provides read-only access to manifest CPT data. Separates data retrieval
 * from CPT registration (class-post-types.php) following single responsibility.
 * All callers needing manifest data should go through this class rather than
 * querying post meta directly.
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
 * Class Manifest_Repository
 *
 * Read-only data access layer for the sm_manifest CPT.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Manifest_Repository {

	/**
	 * Returns all published manifest posts ordered by sort order then title.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return \WP_Post[]  Array of WP_Post objects.
	 */
	public static function get_all(): array {
		return get_posts(
			array(
				'post_type'      => Post_Types::CPT_MANIFEST,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => array(
					'meta_value_num' => 'ASC',
					'title'          => 'ASC',
				),
				'meta_key'       => Post_Types::META_ORDER, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
	}

	/**
	 * Returns the decoded sections array for a given manifest post.
	 *
	 * Each element shape:
	 * { title, sort_order, items: [ { label, description, price, price_prefix, sort_order } ] }
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  int $post_id  The manifest post ID.
	 * @return array<int,array<string,mixed>>  Array of section data arrays, empty on miss.
	 */
	public static function get_sections( int $post_id ): array {
		$raw = get_post_meta( $post_id, Post_Types::META_SECTIONS, true );

		if ( empty( $raw ) ) {
			return array();
		}

		$decoded = json_decode( $raw, true );

		return is_array( $decoded ) ? $decoded : array();
	}
}
