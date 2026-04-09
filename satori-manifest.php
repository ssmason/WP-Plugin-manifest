<?php
/**
 * Plugin Name: Satori Manifest
 * Plugin URI:  https://github.com/ssmason/WP-Plugin-manifest
 * Description: Create and manage structured price lists organised into sections. Output via a custom Gutenberg block. No external PHP dependencies.
 * Version:     1.0.1
 * Author:      Stephen Mason (Satori Digital)
 * Author URI:  https://satori-digital.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: satori-manifest
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.1
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'SATORI_MANIFEST_VERSION', '1.0.1' );
define( 'SATORI_MANIFEST_FILE', __FILE__ );
define( 'SATORI_MANIFEST_PATH', plugin_dir_path( __FILE__ ) );
define( 'SATORI_MANIFEST_URL', plugin_dir_url( __FILE__ ) );
define( 'SATORI_MANIFEST_SLUG', 'satori-manifest' );
define( 'SATORI_MANIFEST_PREFIX', 'satori_manifest_' );

// Autoloader.
if ( file_exists( SATORI_MANIFEST_PATH . 'vendor/autoload.php' ) ) {
	require_once SATORI_MANIFEST_PATH . 'vendor/autoload.php';
}

// Manual class includes.
require_once SATORI_MANIFEST_PATH . 'includes/class-security.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-sanitizer.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-multisite.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-post-types.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-manifest-repository.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-assets.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-admin-menu.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-meta-box.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-block.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-patterns.php';
require_once SATORI_MANIFEST_PATH . 'includes/class-plugin.php';

/**
 * Returns the main plugin instance.
 *
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 * @package SatoriManifest
 * @return  \SatoriManifest\Plugin
 */
function satori_manifest(): \SatoriManifest\Plugin {
	return \SatoriManifest\Plugin::instance();
}

// Activation hook.
register_activation_hook(
	__FILE__,
	function (): void {
		\SatoriManifest\Plugin::activate();
	}
);

// Deactivation hook.
register_deactivation_hook(
	__FILE__,
	function (): void {
		\SatoriManifest\Plugin::deactivate();
	}
);

// Boot.
satori_manifest();
