<?php
/**
 * Block pattern registration.
 *
 * Registers the four built-in price-list patterns and a plugin pattern
 * category. All pattern content is defined once in get_pattern_definitions()
 * and consumed by both register() (inserter templates) and
 * seed_user_patterns() (editable wp_block posts).
 *
 * Patterns appear in the block inserter and in Appearance → Editor → Patterns
 * (the "Design" area). The wp_block posts created by seed_user_patterns() are
 * unsynced user patterns — fully editable and independent of each other.
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
 * Class Patterns
 *
 * Registers the plugin's block pattern category, inserter patterns, and
 * editable user patterns.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Patterns {

	/**
	 * Pattern category slug.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const CATEGORY = 'satori-manifest';

	/**
	 * Returns the canonical pattern definitions.
	 *
	 * Single source of truth for titles and block content — consumed by both
	 * register() (inserter patterns) and seed_user_patterns() (wp_block posts).
	 * Each entry may also carry a description used only in the inserter.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return array<int,array{slug:string,title:string,description:string,content:string}>
	 */
	private static function get_pattern_definitions(): array {
		return array(
			array(
				'slug'        => 'satori-manifest/classic-list',
				'title'       => __( 'Classic List', 'satori-manifest' ),
				'description' => __( 'A clean single-column price list with default styling.', 'satori-manifest' ),
				'content'     => '<!-- wp:satori-manifest/price-list {"layout":"single-column","colorScheme":"default","showPrices":true,"showDescriptions":true} /-->',
			),
			array(
				'slug'        => 'satori-manifest/split-grid',
				'title'       => __( 'Split Grid', 'satori-manifest' ),
				'description' => __( 'Two-column layout with sections displayed side by side.', 'satori-manifest' ),
				'content'     => '<!-- wp:satori-manifest/price-list {"layout":"two-column","colorScheme":"warm","showPrices":true,"showDescriptions":true} /-->',
			),
			array(
				'slug'        => 'satori-manifest/card-style',
				'title'       => __( 'Card Style', 'satori-manifest' ),
				'description' => __( 'Each section displayed as a card with terracotta accents.', 'satori-manifest' ),
				'content'     => '<!-- wp:satori-manifest/price-list {"layout":"card-style","colorScheme":"terracotta","showPrices":true,"showDescriptions":true} /-->',
			),
			array(
				'slug'        => 'satori-manifest/minimal',
				'title'       => __( 'Minimal', 'satori-manifest' ),
				'description' => __( 'Typography-led layout with no borders or backgrounds.', 'satori-manifest' ),
				'content'     => '<!-- wp:satori-manifest/price-list {"layout":"minimal","colorScheme":"minimal","showPrices":true,"showDescriptions":false} /-->',
			),
		);
	}

	/**
	 * Registers the pattern category and all inserter patterns.
	 *
	 * Hooked to 'init'. Patterns registered here are read-only inserter
	 * templates. The editable versions in Appearance → Editor → Patterns are
	 * seeded as wp_block posts by seed_user_patterns() on activation.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		register_block_pattern_category(
			self::CATEGORY,
			array( 'label' => __( 'Manifest', 'satori-manifest' ) )
		);

		foreach ( self::get_pattern_definitions() as $definition ) {
			register_block_pattern(
				$definition['slug'],
				array(
					'title'       => $definition['title'],
					'description' => $definition['description'],
					'categories'  => array( self::CATEGORY ),
					'content'     => $definition['content'],
				)
			);
		}
	}

	/**
	 * Ensures user patterns exist, running once per plugin version.
	 *
	 * Called on 'init' so it fires even when the activation hook is missed
	 * (e.g. during development or after manual file drops). Stores the last
	 * seeded version in an option and skips if already up to date.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function maybe_seed_user_patterns(): void {
		if ( get_option( 'satori_manifest_patterns_seeded' ) === SATORI_MANIFEST_VERSION ) {
			return;
		}

		self::seed_user_patterns();
		update_option( 'satori_manifest_patterns_seeded', SATORI_MANIFEST_VERSION );
	}

	/**
	 * Seeds the four patterns as editable wp_block posts (user patterns).
	 *
	 * Creates one wp_block post per definition if a post with the same title
	 * does not already exist, making this safe to call on every activation.
	 * Each post is marked as unsynced so edits affect only that copy.
	 *
	 * Called from Plugin::activate() and maybe_seed_user_patterns().
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function seed_user_patterns(): void {
		foreach ( self::get_pattern_definitions() as $definition ) {
			$existing = get_posts(
				array(
					'post_type'              => 'wp_block',
					'title'                  => $definition['title'],
					'post_status'            => 'publish',
					'numberposts'            => 1,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);

			if ( ! empty( $existing ) ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'wp_block',
					'post_title'   => $definition['title'],
					'post_content' => $definition['content'],
					'post_status'  => 'publish',
				)
			);

			// Unsynced — edits to this copy do not affect other usages.
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, 'wp_pattern_sync_status', 'unsynced' );
			}
		}
	}
}
