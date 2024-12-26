<?php

namespace App\Services;


class FeedbackResult {
	/**
	 * Boot the service by adding necessary hooks.
	 */
	public function boot(): void {
		$this->add_hooks();
	}

	/**
	 * Add WordPress hooks and shortcodes.
	 */
	public function add_hooks(): void {
		add_shortcode( 'wlc_feedback_results', [ $this, 'render_results_view' ] );
		add_action( 'wp_ajax_wlc_feedback_results', [ $this, 'fetch_results' ] );
		add_action( 'wp_ajax_nopriv_wlc_feedback_results', [ $this, 'fetch_results' ] );
	}

	/**
	 * Render the feedback results view.
	 *
	 * @return string The rendered view as a string.
	 */
	public function render_results_view(): string {
		if ( ! current_user_can( 'administrator' ) ) {
			return '<p class="flex justify-center border border-red-600 w-1/2 p-4 text-sm mx-auto">' .
			       __( 'You are not authorized to view the content of this page.', 'wlc' ) . '</p>';
		}

		return view( 'components.feedback-form.results' )->render();
	}

	/**
	 * Fetch paginated feedback results via AJAX.
	 */
	public function fetch_results(): void {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wlc_feedback_results_nonce' ) ) {
            wp_send_json_error( [
                'message' => __( 'Busted!', 'wlc' )
            ] );
            return;
        }

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [
				'message' => __(
					'You are not authorized to view the content of this page.', 'wlc' )
			] );

			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'feedback_forms';

        if ( isset( $_POST['id'] ) ) {
            $id = intval( sanitize_text_field( $_POST['id'] ) );
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

            if ( $result ) {
                wp_send_json_success( [ 'result' => $result ] );
            } else {
                wp_send_json_error( [ 'message' => __( 'No result found', 'wlc' ) ] );
            }
        } else {
            $page = isset( $_POST['page'] ) ? intval( sanitize_text_field( $_POST['page'] ) ) : 1;
            $per_page = 10;
            $offset = ( $page - 1 ) * $per_page;

            // Get the total number of results
            $total_results = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

            // Get the paginated results sorted by newest first
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $per_page, $offset
            ) );

            wp_send_json_success( [ 'results' => $results, 'total_results' => $total_results ] );
        }
	}


}
