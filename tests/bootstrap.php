<?php
/**
 * PHPUnit bootstrap — loads the WordPress test suite then the plugin.
 *
 * wp-env sets WP_TESTS_DIR to /tmp/wordpress-tests-lib inside the
 * tests-cli container. The polyfills path must be defined before the
 * WP bootstrap runs so it can locate the Yoast PHPUnit Polyfills.
 *
 * @package SatoriManifest
 */

declare( strict_types=1 );

$_tests_dir  = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';
$_plugin_dir = dirname( __DIR__ );

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	fprintf( STDERR, "Could not find WP test suite at %s\n", $_tests_dir );
	exit( 1 );
}

// Required by the WP test suite when running against PHPUnit 10+.
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_plugin_dir . '/vendor/yoast/phpunit-polyfills' );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Loads the plugin before the WP test environment boots.
 *
 * @return void
 */
function satori_manifest_load_plugin(): void {
	require dirname( __DIR__ ) . '/satori-manifest.php';
}
tests_add_filter( 'muplugins_loaded', 'satori_manifest_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
