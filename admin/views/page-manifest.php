<?php
/**
 * Options page wrapper — renders the tabbed settings page.
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

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$satori_manifest_active_tab = isset( $_GET['tab'] ) ? sanitize_key( (string) $_GET['tab'] ) : 'lists';
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$satori_manifest_valid_tabs = array( 'lists', 'patterns' );

if ( ! in_array( $satori_manifest_active_tab, $satori_manifest_valid_tabs, true ) ) {
	$satori_manifest_active_tab = 'lists';
}
?>
<div class="wrap satori-manifest-settings">
	<h1><?php esc_html_e( 'Manifest Settings', 'satori-manifest' ); ?></h1>

	<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings tabs', 'satori-manifest' ); ?>">
		<a
			href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin_Menu::SETTINGS_SLUG . '&tab=lists' ) ); ?>"
			class="nav-tab<?php echo 'lists' === $satori_manifest_active_tab ? ' nav-tab-active' : ''; ?>"
			aria-selected="<?php echo 'lists' === $satori_manifest_active_tab ? 'true' : 'false'; ?>"
		>
			<?php esc_html_e( 'Lists', 'satori-manifest' ); ?>
		</a>
		<a
			href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin_Menu::SETTINGS_SLUG . '&tab=patterns' ) ); ?>"
			class="nav-tab<?php echo 'patterns' === $satori_manifest_active_tab ? ' nav-tab-active' : ''; ?>"
			aria-selected="<?php echo 'patterns' === $satori_manifest_active_tab ? 'true' : 'false'; ?>"
		>
			<?php esc_html_e( 'Patterns', 'satori-manifest' ); ?>
		</a>
	</nav>

	<div class="satori-manifest-tab-content">
		<?php
		if ( 'lists' === $satori_manifest_active_tab ) {
			require_once SATORI_MANIFEST_PATH . 'admin/views/tab-lists.php';
		} else {
			require_once SATORI_MANIFEST_PATH . 'admin/views/tab-patterns.php';
		}
		?>
	</div>
</div>
