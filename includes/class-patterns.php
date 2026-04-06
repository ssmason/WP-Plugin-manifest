<?php
/**
 * Block pattern registration.
 *
 * Registers the four built-in block patterns for the price-list block.
 * If a customised override exists in options it is registered in place of
 * the built-in definition.
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
 * Handles registration and definition of built-in block patterns.
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
	 * Registers the pattern category and all patterns.
	 *
	 * Hooked to 'init'.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		self::register_category();

		$custom_overrides = Options::get_patterns();

		foreach ( self::get_built_in_definitions() as $handle => $definition ) {
			if ( isset( $custom_overrides[ $handle ] ) ) {
				$definition['title']   = $custom_overrides[ $handle ]['title'];
				$definition['content'] = $custom_overrides[ $handle ]['content'];
			}

			register_block_pattern(
				'satori-manifest/' . $handle,
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
	 * Registers the Satori Manifest block pattern category.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	private static function register_category(): void {
		register_block_pattern_category(
			self::CATEGORY,
			array( 'label' => __( 'Manifest Price Lists', 'satori-manifest' ) )
		);
	}

	/**
	 * Returns the built-in pattern definitions.
	 *
	 * Each entry is keyed by a short handle (without the namespace prefix).
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return array<string,array<string,string>>  Pattern definitions.
	 */
	public static function get_built_in_definitions(): array {
		return array(
			'single-column' => array(
				'title'       => __( 'Classic List', 'satori-manifest' ),
				'description' => __( 'A simple single-column price list — clean and readable for any service menu.', 'satori-manifest' ),
				'content'     => self::pattern_single_column(),
			),
			'two-column'    => array(
				'title'       => __( 'Split Grid', 'satori-manifest' ),
				'description' => __( 'Two-column layout — ideal for displaying multiple sections side by side.', 'satori-manifest' ),
				'content'     => self::pattern_two_column(),
			),
			'card-style'    => array(
				'title'       => __( 'Card Style', 'satori-manifest' ),
				'description' => __( 'Each section in a card container — great for visually distinct service categories.', 'satori-manifest' ),
				'content'     => self::pattern_card_style(),
			),
			'minimal'       => array(
				'title'       => __( 'Minimal', 'satori-manifest' ),
				'description' => __( 'Stripped-back layout with no borders — maximum whitespace and elegance.', 'satori-manifest' ),
				'content'     => self::pattern_minimal(),
			),
		);
	}

	/**
	 * Returns the block markup for the Classic List pattern.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return string  Block pattern HTML/block markup.
	 */
	private static function pattern_single_column(): string {
		return '<!-- wp:satori-manifest/price-list {"layout":"single-column","showPrices":true,"showDescriptions":true} /-->';
	}

	/**
	 * Returns the block markup for the Split Grid pattern.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return string  Block pattern HTML/block markup.
	 */
	private static function pattern_two_column(): string {
		return '<!-- wp:satori-manifest/price-list {"layout":"two-column","showPrices":true,"showDescriptions":true} /-->';
	}

	/**
	 * Returns the block markup for the Card Style pattern.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return string  Block pattern HTML/block markup.
	 */
	private static function pattern_card_style(): string {
		return '<!-- wp:satori-manifest/price-list {"layout":"card-style","showPrices":true,"showDescriptions":true} /-->';
	}

	/**
	 * Returns the block markup for the Minimal pattern.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return string  Block pattern HTML/block markup.
	 */
	private static function pattern_minimal(): string {
		return '<!-- wp:satori-manifest/price-list {"layout":"minimal","showPrices":true,"showDescriptions":false} /-->';
	}
}
