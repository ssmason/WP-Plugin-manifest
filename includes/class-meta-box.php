<?php
/**
 * Meta box registration and save handler.
 *
 * Registers the "Sections & Items" meta box on the sm_manifest edit screen
 * and handles the form save. Input sanitization is delegated to Sanitizer
 * and nonce handling to Security, keeping this class focused on WordPress
 * meta box wiring only.
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
 * Class Meta_Box
 *
 * Registers and saves the sections meta box on the manifest CPT edit screen.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Meta_Box {

	/**
	 * Nonce action key (without prefix) for the sections save.
	 *
	 * Passed to Security::output_form_nonce() and Security::verify_form_nonce().
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const NONCE_ACTION = 'save_sections';

	/**
	 * POST field name for the serialised sections JSON.
	 *
	 * The hidden input is populated by admin.js before the WP form submits.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public const POST_FIELD = 'satori_manifest_sections';

	/**
	 * Registers the meta box with WordPress.
	 *
	 * Hooked to 'add_meta_boxes'.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function register(): void {
		add_meta_box(
			'satori_manifest_sections',
			__( 'Sections & Items', 'satori-manifest' ),
			array( self::class, 'render' ),
			Post_Types::CPT_MANIFEST,
			'normal',
			'high'
		);
	}

	/**
	 * Renders the meta box HTML.
	 *
	 * Outputs the nonce field via Security, then loads the view template.
	 * The template receives $satori_manifest_sections (array) and outputs:
	 *   - a hidden field that admin.js serialises into before submit
	 *   - the sections list rendered from saved data
	 *   - <template> elements admin.js clones when adding sections/items
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  \WP_Post $post  The current post object.
	 * @return void
	 */
	public static function render( \WP_Post $post ): void {
		$satori_manifest_sections   = Manifest_Repository::get_sections( $post->ID );
		$satori_manifest_post_field = self::POST_FIELD;
		Security::output_form_nonce( self::NONCE_ACTION );
		require SATORI_MANIFEST_PATH . 'admin/views/meta-box-sections.php';
	}

	/**
	 * Saves the sections JSON when the manifest post is saved.
	 *
	 * Hooked to 'save_post_{CPT_MANIFEST}'. Skips autosaves, verifies the
	 * nonce via Security, checks capability, then delegates sanitization to
	 * Sanitizer before persisting to post meta.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  int $post_id  The post being saved.
	 * @return void
	 */
	public static function save( int $post_id ): void {
		// Skip autosave — the form has not been submitted in this case.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify nonce via Security; bail silently on failure.
		$nonce = isset( $_POST[ Security::NONCE_FIELD ] )
			? sanitize_text_field( wp_unslash( (string) $_POST[ Security::NONCE_FIELD ] ) )
			: '';

		if ( ! Security::verify_form_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		// Capability check.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Read the raw JSON. Only wp_unslash() here — sanitize_text_field() would
		// strip angle-bracket characters and corrupt the JSON before decoding.
		// Field-level sanitization is handled by Sanitizer after decode.
		$raw     = isset( $_POST[ self::POST_FIELD ] )
			? wp_unslash( (string) $_POST[ self::POST_FIELD ] )
			: '[]';
		$decoded = json_decode( $raw, true );

		$sections = is_array( $decoded ) ? Sanitizer::sanitize_sections( $decoded ) : array();

		update_post_meta( $post_id, Post_Types::META_SECTIONS, wp_json_encode( $sections ) );
	}
}
