<?php
/**
 * Patterns tab — displays and manages built-in and customised block patterns.
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

$satori_manifest_built_in        = Patterns::get_built_in_definitions();
$satori_manifest_custom_patterns = Options::get_patterns();
?>
<div class="satori-manifest-patterns">

	<p class="description">
		<?php
		esc_html_e(
			'Below are the built-in display patterns for the price list block. To customise a pattern, click "Customise" — a copy will be saved and used in place of the built-in. You can then edit it fully in Appearance > Patterns.',
			'satori-manifest'
		);
		?>
	</p>

	<div class="satori-manifest-notice" id="satori-manifest-pattern-notice" aria-live="polite" hidden></div>

	<div class="satori-manifest-patterns__grid">
		<?php foreach ( $satori_manifest_built_in as $satori_manifest_handle => $satori_manifest_definition ) : ?>
			<?php
			$satori_manifest_is_customised = isset( $satori_manifest_custom_patterns[ $satori_manifest_handle ] );
			?>
			<div
				class="satori-manifest-pattern-card<?php echo $satori_manifest_is_customised ? ' is-customised' : ''; ?>"
				data-handle="<?php echo esc_attr( $satori_manifest_handle ); ?>"
			>
				<div class="satori-manifest-pattern-card__preview">
					<div class="satori-manifest-pattern-card__preview-inner">
						<?php // translators: %s is the pattern title. ?>
						<p><?php printf( esc_html__( 'Preview: %s', 'satori-manifest' ), esc_html( $satori_manifest_definition['title'] ) ); ?></p>
					</div>
				</div>

				<div class="satori-manifest-pattern-card__meta">
					<h3 class="satori-manifest-pattern-card__title">
						<?php echo esc_html( $satori_manifest_definition['title'] ); ?>
						<?php if ( $satori_manifest_is_customised ) : ?>
							<span class="satori-manifest-badge"><?php esc_html_e( 'Customised', 'satori-manifest' ); ?></span>
						<?php endif; ?>
					</h3>
					<p class="description"><?php echo esc_html( $satori_manifest_definition['description'] ); ?></p>
				</div>

				<div class="satori-manifest-pattern-card__actions">
					<?php if ( $satori_manifest_is_customised ) : ?>
						<a
							href="<?php echo esc_url( admin_url( 'site-editor.php?path=/patterns' ) ); ?>"
							class="button"
						>
							<?php esc_html_e( 'Edit in Appearance', 'satori-manifest' ); ?>
						</a>
						<button
							type="button"
							class="button satori-manifest-pattern__restore"
							data-handle="<?php echo esc_attr( $satori_manifest_handle ); ?>"
						>
							<?php esc_html_e( 'Restore Default', 'satori-manifest' ); ?>
						</button>
					<?php else : ?>
						<button
							type="button"
							class="button button-primary satori-manifest-pattern__customise"
							data-handle="<?php echo esc_attr( $satori_manifest_handle ); ?>"
							data-title="<?php echo esc_attr( $satori_manifest_definition['title'] ); ?>"
						>
							<?php esc_html_e( 'Customise', 'satori-manifest' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
