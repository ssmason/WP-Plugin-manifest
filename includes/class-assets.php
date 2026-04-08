<?php
/**
 * Asset enqueue management.
 *
 * Enqueues compiled scripts and styles for the admin, block editor, and
 * public-facing frontend. All handles are prefixed with satori-manifest-.
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
 * Class Assets
 *
 * Manages script and style enqueueing for all plugin contexts.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Assets {

	/**
	 * Enqueues scripts and styles for admin pages.
	 *
	 * Only loads on plugin admin pages to avoid polluting other screens.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $hook_suffix  The current admin page hook suffix.
	 * @return void
	 */
	public static function enqueue_admin( string $hook_suffix ): void {
		if ( ! self::is_manifest_edit_screen( $hook_suffix ) ) {
			return;
		}

		$admin_js_path  = SATORI_MANIFEST_PATH . 'block/build/admin.js';
		$admin_css_path = SATORI_MANIFEST_PATH . 'src/scss/admin/build/admin.css';
		$admin_js_ver   = file_exists( $admin_js_path ) ? (string) filemtime( $admin_js_path ) : SATORI_MANIFEST_VERSION;
		$admin_css_ver  = file_exists( $admin_css_path ) ? (string) filemtime( $admin_css_path ) : SATORI_MANIFEST_VERSION;

		wp_enqueue_style(
			'satori-manifest-admin',
			SATORI_MANIFEST_URL . 'src/scss/admin/build/admin.css',
			array(),
			$admin_css_ver
		);

		wp_enqueue_script(
			'satori-manifest-admin',
			SATORI_MANIFEST_URL . 'block/build/admin.js',
			array(),
			$admin_js_ver,
			true
		);
	}

	/**
	 * Enqueues scripts and styles for the block editor.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_editor(): void {
		wp_enqueue_style(
			'satori-manifest-editor',
			SATORI_MANIFEST_URL . 'src/scss/editor/build/editor.css',
			array( 'wp-edit-blocks' ),
			SATORI_MANIFEST_VERSION
		);
	}

	/**
	 * Enqueues styles for the public frontend.
	 *
	 * Only loads when the price-list block is present on the current page.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_frontend(): void {
		if ( ! self::page_has_price_list_block() ) {
			return;
		}

		wp_enqueue_style(
			'satori-manifest-frontend',
			SATORI_MANIFEST_URL . 'src/scss/frontend/build/frontend.css',
			array(),
			SATORI_MANIFEST_VERSION
		);
	}

	/**
	 * Returns true only on the manifest CPT add/edit screens.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $hook_suffix  The current admin page hook suffix.
	 * @return bool
	 */
	private static function is_manifest_edit_screen( string $hook_suffix ): bool {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		global $post_type;
		return Post_Types::CPT_MANIFEST === $post_type;
	}

	/**
	 * Checks whether the queried post/page contains the price-list block.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return bool
	 */
	private static function page_has_price_list_block(): bool {
		global $post;

		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		return has_block( 'satori-manifest/price-list', $post );
	}
}
