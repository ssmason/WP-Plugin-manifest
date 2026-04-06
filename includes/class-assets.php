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
		if ( ! self::is_plugin_admin_page( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			'satori-manifest-admin',
			SATORI_MANIFEST_URL . 'src/scss/admin/build/admin.css',
			array(),
			SATORI_MANIFEST_VERSION
		);

		wp_enqueue_script(
			'satori-manifest-admin',
			SATORI_MANIFEST_URL . 'block/build/admin.js',
			array( 'wp-util' ),
			SATORI_MANIFEST_VERSION,
			true
		);

		wp_localize_script(
			'satori-manifest-admin',
			'satoriManifestAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonces'  => array(
					'saveSection'     => Security::create_nonce( 'save_section' ),
					'deleteSection'   => Security::create_nonce( 'delete_section' ),
					'reorderSections' => Security::create_nonce( 'reorder_sections' ),
					'savePattern'     => Security::create_nonce( 'save_pattern' ),
				),
				'i18n'    => array(
					'confirmDelete' => __( 'Are you sure you want to delete this section? This cannot be undone.', 'satori-manifest' ),
					'saving'        => __( 'Saving…', 'satori-manifest' ),
					'saved'         => __( 'Saved', 'satori-manifest' ),
					'error'         => __( 'An error occurred. Please try again.', 'satori-manifest' ),
					'addSection'    => __( 'Add Section', 'satori-manifest' ),
					'addItem'       => __( 'Add Item', 'satori-manifest' ),
				),
			)
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
	 * Checks whether the current admin page belongs to this plugin.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  string $hook_suffix  The current admin page hook suffix.
	 * @return bool
	 */
	private static function is_plugin_admin_page( string $hook_suffix ): bool {
		$plugin_pages = array(
			'toplevel_page_satori-manifest',
			'manifest_page_satori-manifest-settings',
			'edit.php?post_type=sm_price_section',
		);

		foreach ( $plugin_pages as $page ) {
			if ( false !== strpos( $hook_suffix, 'satori-manifest' ) ) {
				return true;
			}
		}

		// Also load on section CPT screens.
		global $post_type;
		return 'sm_price_section' === $post_type;
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
