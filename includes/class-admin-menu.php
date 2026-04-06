<?php
/**
 * Admin menu registration.
 *
 * Registers a top-level primary menu item "Manifest" with sub-pages for
 * All Sections (CPT list), Add New Section, and Settings (options page).
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
	 * Settings sub-page slug.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const SETTINGS_SLUG = 'satori-manifest-settings';

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
		// Top-level menu.
		add_menu_page(
			__( 'Manifest', 'satori-manifest' ),
			__( 'Manifest', 'satori-manifest' ),
			'manage_options',
			self::MENU_SLUG,
			array( self::class, 'render_redirect' ),
			'dashicons-list-view',
			26
		);

		// All Sections — links to CPT list table.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'All Sections', 'satori-manifest' ),
			__( 'All Sections', 'satori-manifest' ),
			'edit_posts',
			'edit.php?post_type=' . Post_Types::CPT_SECTION
		);

		// Add New Section — links to CPT new post screen.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Add New Section', 'satori-manifest' ),
			__( 'Add New Section', 'satori-manifest' ),
			'edit_posts',
			'post-new.php?post_type=' . Post_Types::CPT_SECTION
		);

		// Settings — options page with tabs.
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Manifest Settings', 'satori-manifest' ),
			__( 'Settings', 'satori-manifest' ),
			'manage_options',
			self::SETTINGS_SLUG,
			array( self::class, 'render_settings' )
		);

		// Remove the duplicate top-level entry added by add_menu_page.
		remove_submenu_page( self::MENU_SLUG, self::MENU_SLUG );
	}

	/**
	 * Redirects the top-level menu click to All Sections.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function render_redirect(): void {
		wp_safe_redirect(
			admin_url( 'edit.php?post_type=' . Post_Types::CPT_SECTION )
		);
		exit;
	}

	/**
	 * Renders the settings options page.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function render_settings(): void {
		Security::require_capability( 'manage_options' );
		require_once SATORI_MANIFEST_PATH . 'admin/views/page-manifest.php';
	}
}
