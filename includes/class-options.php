<?php
/**
 * Plugin options management.
 *
 * Handles reading, writing and seeding default values for plugin options.
 * All reads/writes route through Multisite::get_option() / update_option()
 * to ensure network-activation compatibility.
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
 * Class Options
 *
 * Plugin-level options CRUD and default seeding.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Options {

	/**
	 * Option key for customised pattern overrides.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const KEY_PATTERNS = 'satori_manifest_patterns';

	/**
	 * Seeds default option values on first activation.
	 *
	 * Only writes if the option does not already exist to avoid overwriting
	 * existing data on re-activation.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function seed_defaults(): void {
		if ( false === Multisite::get_option( self::KEY_PATTERNS ) ) {
			Multisite::update_option( self::KEY_PATTERNS, array() );
		}
	}

	/**
	 * Returns all customised pattern overrides.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return array<string,mixed>  Associative array of pattern handle → data.
	 */
	public static function get_patterns(): array {
		$value = Multisite::get_option( self::KEY_PATTERNS, array() );
		return is_array( $value ) ? $value : array();
	}

	/**
	 * Saves a customised pattern override.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string              $handle  The block pattern handle (slug).
	 * @param  array<string,mixed> $data    Pattern data to persist.
	 * @return bool                         True on success.
	 */
	public static function save_pattern( string $handle, array $data ): bool {
		$patterns            = self::get_patterns();
		$patterns[ $handle ] = $data;
		return Multisite::update_option( self::KEY_PATTERNS, $patterns );
	}

	/**
	 * Removes a customised pattern override, restoring the built-in default.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $handle  The block pattern handle (slug).
	 * @return bool            True on success.
	 */
	public static function delete_pattern( string $handle ): bool {
		$patterns = self::get_patterns();
		unset( $patterns[ $handle ] );
		return Multisite::update_option( self::KEY_PATTERNS, $patterns );
	}
}
