<?php
/**
 * AJAX request handlers.
 *
 * Handles all wp_ajax_ actions for the Satori Manifest admin UI.
 * Every handler verifies a nonce and checks capabilities before
 * reading or writing any data.
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
 * Class Admin_Ajax
 *
 * AJAX callbacks for all plugin admin interactions.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */
class Admin_Ajax {

	/**
	 * Saves or creates a section post and its items meta.
	 *
	 * Expected $_POST keys: nonce, post_id (0 = new), title, items (JSON).
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function save_section(): void {
		Security::verify_ajax_nonce( 'save_section' );
		Security::require_capability( 'edit_posts' );

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified above via Security::verify_ajax_nonce().
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['title'] ) ) : '';
		$items   = isset( $_POST['items'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['items'] ) ) : '[]';
		$order   = isset( $_POST['sort_order'] ) ? absint( $_POST['sort_order'] ) : 0;
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( empty( $title ) ) {
			wp_send_json_error( array( 'message' => __( 'Section title is required.', 'satori-manifest' ) ) );
			return;
		}

		// Validate items JSON.
		$decoded_items = json_decode( $items, true );
		if ( ! is_array( $decoded_items ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid items data.', 'satori-manifest' ) ) );
			return;
		}

		// Sanitize each item.
		$sanitized_items = self::sanitize_items( $decoded_items );

		$post_data = array(
			'post_type'   => Post_Types::CPT_SECTION,
			'post_title'  => $title,
			'post_status' => 'publish',
		);

		if ( $post_id > 0 ) {
			$post_data['ID'] = $post_id;
			$result          = wp_update_post( $post_data, true );
		} else {
			$result = wp_insert_post( $post_data, true );
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			return;
		}

		$saved_id = (int) $result;
		update_post_meta( $saved_id, Post_Types::META_ITEMS, wp_json_encode( $sanitized_items ) );
		update_post_meta( $saved_id, Post_Types::META_ORDER, $order );

		wp_send_json_success(
			array(
				'post_id' => $saved_id,
				'message' => __( 'Section saved.', 'satori-manifest' ),
			)
		);
	}

	/**
	 * Deletes a section post and all its meta.
	 *
	 * Expected $_POST keys: nonce, post_id.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function delete_section(): void {
		Security::verify_ajax_nonce( 'delete_section' );
		Security::require_capability( 'edit_posts' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above via Security::verify_ajax_nonce().
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( $post_id < 1 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid section ID.', 'satori-manifest' ) ) );
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post || Post_Types::CPT_SECTION !== $post->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Section not found.', 'satori-manifest' ) ) );
			return;
		}

		$deleted = wp_delete_post( $post_id, true );

		if ( ! $deleted ) {
			wp_send_json_error( array( 'message' => __( 'Could not delete section.', 'satori-manifest' ) ) );
			return;
		}

		wp_send_json_success( array( 'message' => __( 'Section deleted.', 'satori-manifest' ) ) );
	}

	/**
	 * Saves a new sort order for multiple sections in one request.
	 *
	 * Expected $_POST keys: nonce, order (JSON array of post IDs).
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function reorder_sections(): void {
		Security::verify_ajax_nonce( 'reorder_sections' );
		Security::require_capability( 'edit_posts' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above via Security::verify_ajax_nonce().
		$order_raw = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['order'] ) ) : '[]';
		$order     = json_decode( $order_raw, true );

		if ( ! is_array( $order ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid order data.', 'satori-manifest' ) ) );
			return;
		}

		foreach ( $order as $index => $post_id ) {
			$post_id = absint( $post_id );
			if ( $post_id > 0 ) {
				update_post_meta( $post_id, Post_Types::META_ORDER, $index );
			}
		}

		wp_send_json_success( array( 'message' => __( 'Order saved.', 'satori-manifest' ) ) );
	}

	/**
	 * Saves a customised pattern override to options.
	 *
	 * Expected $_POST keys: nonce, handle, title, content.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @return void
	 */
	public static function save_pattern(): void {
		Security::verify_ajax_nonce( 'save_pattern' );
		Security::require_capability( 'manage_options' );

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified above via Security::verify_ajax_nonce().
		$handle  = isset( $_POST['handle'] ) ? sanitize_key( wp_unslash( (string) $_POST['handle'] ) ) : '';
		$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['title'] ) ) : '';
		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( (string) $_POST['content'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( empty( $handle ) || empty( $title ) ) {
			wp_send_json_error( array( 'message' => __( 'Handle and title are required.', 'satori-manifest' ) ) );
			return;
		}

		$saved = Options::save_pattern(
			$handle,
			array(
				'title'   => $title,
				'content' => $content,
			)
		);

		if ( ! $saved ) {
			wp_send_json_error( array( 'message' => __( 'Could not save pattern.', 'satori-manifest' ) ) );
			return;
		}

		wp_send_json_success( array( 'message' => __( 'Pattern saved.', 'satori-manifest' ) ) );
	}

	/**
	 * Sanitizes an array of section items.
	 *
	 * @author Stephen Mason <steve@satori-digital.com>
	 * @since  1.0.0
	 * @param  array<int,array<string,mixed>> $items  Raw items from JSON decode.
	 * @return array<int,array<string,mixed>>          Sanitized items array.
	 */
	private static function sanitize_items( array $items ): array {
		$sanitized = array();

		foreach ( $items as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$sanitized[] = array(
				'label'        => isset( $item['label'] ) ? sanitize_text_field( (string) $item['label'] ) : '',
				'description'  => isset( $item['description'] ) ? sanitize_text_field( (string) $item['description'] ) : '',
				'price'        => isset( $item['price'] ) ? number_format( (float) $item['price'], 2, '.', '' ) : '0.00',
				'price_prefix' => isset( $item['price_prefix'] ) ? sanitize_text_field( (string) $item['price_prefix'] ) : '',
				'sort_order'   => isset( $item['sort_order'] ) ? absint( $item['sort_order'] ) : $index,
			);
		}

		return $sanitized;
	}
}
