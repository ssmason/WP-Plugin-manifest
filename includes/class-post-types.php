<?php
/**
 * Custom post type registration.
 *
 * Registers the sm_price_section CPT. Section items are stored as
 * post meta (JSON array) rather than a second CPT, keeping the data model flat
 * and dependency-free.
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
 * Class Post_Types
 *
 * Handles registration of the sm_price_section custom post type.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Post_Types {

	/**
	 * CPT slug constant.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const CPT_SECTION = 'sm_price_section';

	/**
	 * Meta key for storing section items as JSON.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const META_ITEMS = '_satori_manifest_items';

	/**
	 * Meta key for section sort order.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const META_ORDER = '_satori_manifest_order';

	/**
	 * Registers the section CPT and its associated post meta.
	 *
	 * Hooked to 'init'.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		self::register_section_cpt();
		self::register_meta();
	}

	/**
	 * Registers the sm_price_section CPT.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	private static function register_section_cpt(): void {
		$labels = array(
			'name'               => __( 'Price List Sections', 'satori-manifest' ),
			'singular_name'      => __( 'Section', 'satori-manifest' ),
			'add_new'            => __( 'Add New Section', 'satori-manifest' ),
			'add_new_item'       => __( 'Add New Section', 'satori-manifest' ),
			'edit_item'          => __( 'Edit Section', 'satori-manifest' ),
			'new_item'           => __( 'New Section', 'satori-manifest' ),
			'view_item'          => __( 'View Section', 'satori-manifest' ),
			'search_items'       => __( 'Search Sections', 'satori-manifest' ),
			'not_found'          => __( 'No sections found.', 'satori-manifest' ),
			'not_found_in_trash' => __( 'No sections found in Trash.', 'satori-manifest' ),
			'menu_name'          => __( 'Sections', 'satori-manifest' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'satori-manifest',
			'show_in_rest'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'custom-fields' ),
		);

		register_post_type( self::CPT_SECTION, $args );
	}

	/**
	 * Registers post meta fields for the section CPT.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	private static function register_meta(): void {
		register_post_meta(
			self::CPT_SECTION,
			self::META_ITEMS,
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => true,
				'auth_callback'     => function (): bool {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			self::CPT_SECTION,
			self::META_ORDER,
			array(
				'single'            => true,
				'type'              => 'integer',
				'show_in_rest'      => true,
				'auth_callback'     => function (): bool {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => 'absint',
			)
		);
	}

	/**
	 * Returns all section posts ordered by sort order then title.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return \WP_Post[]  Array of WP_Post objects.
	 */
	public static function get_all_sections(): array {
		return get_posts(
			array(
				'post_type'      => self::CPT_SECTION,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => array(
					'meta_value_num' => 'ASC',
					'title'          => 'ASC',
				),
				'meta_key'       => self::META_ORDER, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
	}

	/**
	 * Returns the decoded items array for a given section post.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  int $post_id  The section post ID.
	 * @return array<int,array<string,mixed>>  Array of item data arrays.
	 */
	public static function get_section_items( int $post_id ): array {
		$raw = get_post_meta( $post_id, self::META_ITEMS, true );

		if ( empty( $raw ) ) {
			return array();
		}

		$decoded = json_decode( $raw, true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return $decoded;
	}
}
