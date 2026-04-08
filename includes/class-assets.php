<?php
/**
 * Asset enqueue management.
 *
 * Enqueues compiled scripts and styles for the admin meta box only.
 * Block editor styles and frontend styles are registered automatically by
 * WordPress via the editorStyle and style keys in block/block.json —
 * no manual enqueue is needed for those contexts.
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
 * Manages script and style enqueueing for the manifest CPT admin screens.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Assets {

	/**
	 * Enqueues scripts and styles for the manifest CPT add/edit screen.
	 *
	 * Only loads on the manifest CPT edit screens to avoid polluting other
	 * admin pages. Hooked to 'admin_enqueue_scripts'.
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
	 * Returns true only on the manifest CPT add/edit screens.
	 *
	 * Uses get_current_screen() rather than the $post_type global, which is
	 * unreliable early in the admin_enqueue_scripts lifecycle.
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

		$screen = get_current_screen();
		return $screen instanceof \WP_Screen && Post_Types::CPT_MANIFEST === $screen->post_type;
	}
}
