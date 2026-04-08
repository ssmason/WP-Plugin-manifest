<?php
/**
 * Admin menu registration.
 *
 * Registers a top-level primary menu item "Manifest" with sub-pages for
 * All Manifests (CPT list), Add New Manifest, and Settings (options page).
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
 * Class Admin_Menu
 *
 * Registers the primary admin menu for Satori Manifest.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Admin_Menu {

	/**
	 * Menu slug constant.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const MENU_SLUG = 'satori-manifest';

	/**
	 * Registers the admin menu and sub-menu pages.
	 *
	 * Hooked to 'admin_menu'.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		// Top-level menu — redirects to CPT list table.
		add_menu_page(
			__( 'Manifest', 'satori-manifest' ),
			__( 'Manifest', 'satori-manifest' ),
			'edit_posts',
			self::MENU_SLUG,
			array( self::class, 'render_redirect' ),
			'dashicons-list-view',
			26
		);

		// All Manifests — CPT list table.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'All Manifests', 'satori-manifest' ),
			__( 'All Manifests', 'satori-manifest' ),
			'edit_posts',
			'edit.php?post_type=' . Post_Types::CPT_MANIFEST
		);

		// Add New Manifest.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Add New Manifest', 'satori-manifest' ),
			__( 'Add New Manifest', 'satori-manifest' ),
			'edit_posts',
			'post-new.php?post_type=' . Post_Types::CPT_MANIFEST
		);

		// Remove the duplicate top-level entry added by add_menu_page.
		remove_submenu_page( self::MENU_SLUG, self::MENU_SLUG );
	}

	/**
	 * Redirects the top-level menu click to All Manifests.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function render_redirect(): void {
		wp_safe_redirect(
			admin_url( 'edit.php?post_type=' . Post_Types::CPT_MANIFEST )
		);
		exit;
	}
}
