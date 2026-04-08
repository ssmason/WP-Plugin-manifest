<?php
/**
 * Core plugin bootstrap class.
 *
 * Registers all hooks and boots sub-components. Implements a singleton to
 * ensure only one instance is ever loaded.
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
 * Class Plugin
 *
 * Main bootstrap — wires all sub-components together via WordPress hooks.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var   Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Private constructor — use Plugin::instance().
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers all WordPress hooks.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	private function init_hooks(): void {
		add_action( 'init', array( Post_Types::class, 'register' ) );
		add_action( 'init', array( Block::class, 'register' ) );
		add_action( 'init', array( Patterns::class, 'register' ) );
		add_action( 'init', array( Patterns::class, 'maybe_seed_user_patterns' ) );
		add_action( 'admin_menu', array( Admin_Menu::class, 'register' ) );
		add_action( 'add_meta_boxes', array( Meta_Box::class, 'register' ) );
		add_action( 'save_post_' . Post_Types::CPT_MANIFEST, array( Meta_Box::class, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( Assets::class, 'enqueue_admin' ) );
		add_action( 'enqueue_block_editor_assets', array( Assets::class, 'enqueue_editor' ) );
		add_action( 'wp_enqueue_scripts', array( Assets::class, 'enqueue_frontend' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		if ( is_multisite() ) {
			add_action( 'wpmu_new_blog', array( Multisite::class, 'new_site_setup' ) );
		}
	}

	/**
	 * Loads the plugin text domain for translations.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'satori-manifest',
			false,
			dirname( plugin_basename( SATORI_MANIFEST_FILE ) ) . '/languages'
		);
	}

	/**
	 * Runs on plugin activation.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function activate(): void {
		Post_Types::register();
		flush_rewrite_rules();
		Patterns::seed_user_patterns();
		update_option( 'satori_manifest_version', SATORI_MANIFEST_VERSION );
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
