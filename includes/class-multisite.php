<?php
/**
 * Multisite helpers.
 *
 * Wraps WordPress option functions so the rest of the plugin is agnostic of
 * whether it is running on a standard single-site install or a network-activated
 * multisite environment.
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
 * Class Multisite
 *
 * Network-aware option helpers and per-site setup for multisite installs.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Multisite {

	/**
	 * Returns true when the plugin is network-activated.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return bool
	 */
	public static function is_network_active(): bool {
		if ( ! is_multisite() ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		return is_plugin_active_for_network( plugin_basename( SATORI_MANIFEST_FILE ) );
	}

	/**
	 * Gets an option — uses get_site_option when network-active.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $key           Option key.
	 * @param  mixed  $fallback_val  Default value if option not set.
	 * @return mixed                 Option value.
	 */
	public static function get_option( string $key, mixed $fallback_val = false ): mixed {
		if ( self::is_network_active() ) {
			return get_site_option( $key, $fallback_val );
		}
		return get_option( $key, $fallback_val );
	}

	/**
	 * Updates an option — uses update_site_option when network-active.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $key    Option key.
	 * @param  mixed  $value  Value to store.
	 * @return bool           True on success, false on failure.
	 */
	public static function update_option( string $key, mixed $value ): bool {
		if ( self::is_network_active() ) {
			return update_site_option( $key, $value );
		}
		return update_option( $key, $value );
	}

	/**
	 * Deletes an option — uses delete_site_option when network-active.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $key  Option key.
	 * @return bool         True on success, false on failure.
	 */
	public static function delete_option( string $key ): bool {
		if ( self::is_network_active() ) {
			return delete_site_option( $key );
		}
		return delete_option( $key );
	}

	/**
	 * Runs default setup for a newly created site on a multisite network.
	 *
	 * Hooked to wpmu_new_blog.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  int $blog_id  The new site ID.
	 * @return void
	 */
	public static function new_site_setup( int $blog_id ): void {
		if ( ! self::is_network_active() ) {
			return;
		}

		// Flush rewrite rules for the new site so the CPT slugs register correctly.
		switch_to_blog( $blog_id );
		flush_rewrite_rules();
		restore_current_blog();
	}

	/**
	 * Deletes all plugin data for every site on the network.
	 *
	 * Called from uninstall.php during a network uninstall.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function network_uninstall(): void {
		$sites = get_sites( array( 'number' => 0 ) );

		foreach ( $sites as $site ) {
			switch_to_blog( (int) $site->blog_id );
			self::delete_site_data();
			restore_current_blog();
		}
	}

	/**
	 * Deletes all plugin CPT posts, meta, and options for the current site.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function delete_site_data(): void {
		// Delete all manifest CPT posts.
		$posts = get_posts(
			array(
				'post_type'      => Post_Types::CPT_MANIFEST,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $posts as $post_id ) {
			wp_delete_post( (int) $post_id, true );
		}

		// Delete plugin options.
		delete_option( 'satori_manifest_version' );
	}
}
