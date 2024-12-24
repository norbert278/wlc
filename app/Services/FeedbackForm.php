<?php

namespace App\Services;


class FeedbackForm {
	/**
	 * Boot the service by adding necessary hooks.
	 */
	public function boot(): void {
		$this->create_feedback_forms_table();
		$this->add_hooks();
	}

	/**
	 * Add WordPress hooks and shortcodes.
	 */
	public function add_hooks(): void {
		add_shortcode( 'wlc_feedback_form', [ $this, 'render_custom_view' ] );
		add_action( 'wp_ajax_wlc_feedback_form', [ $this, 'submit_form' ] );
		add_action( 'wp_ajax_nopriv_wlc_feedback_form', [ $this, 'submit_form' ] );
	}

	/**
	 * Render the custom registration form view.
	 *
	 * @return string The rendered view as a string.
	 */
	public function render_custom_view(): string {
		$user_data = [
			'first_name' => '',
			'last_name'  => '',
			'email'      => '',
		];

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$user_data    = [
				'first_name' => $current_user->user_firstname,
				'last_name'  => $current_user->user_lastname,
				'email'      => $current_user->user_email,
			];
		}

		return view( 'components.feedback-form.form', compact( 'user_data' ) )->render();
	}

	/**
	 * Create the feedback forms table if it does not exist.
	 */
	public function create_feedback_forms_table(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'feedback_forms';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                first_name varchar(255) NOT NULL,
                last_name varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                subject varchar(255) NOT NULL,
                message text NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
	            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	            PRIMARY KEY  (id)
            ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

	/**
	 * Handle the form submission via AJAX.
	 */
	public function submit_form(): void {
		// Check nonce for security
		check_ajax_referer( 'feedback_form_nonce', 'nonce' );

		// Check honeypot field
		if ( ! empty( $_POST['honeypot'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Failed to submit form', 'wlc' ) ] );

			return;
		}

		// Sanitize and validate input data
		$first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
		$last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
		$email      = sanitize_email( $_POST['email'] ?? '' );
		$subject    = sanitize_text_field( $_POST['subject'] ?? '' );
		$message    = sanitize_textarea_field( $_POST['message'] ?? '' );

		if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $subject ) ) {
			wp_send_json_error( [ 'message' => __( 'All fields are required', 'wlc' ) ] );

			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'feedback_forms';

		// Prepare and execute the SQL query securely
		$prepared_query = $wpdb->prepare(
			"INSERT INTO $table_name (
                   first_name, last_name, email, subject, message, created_at, updated_at) 
					VALUES (%s, %s, %s, %s, %s, NOW(), NOW())", $first_name, $last_name, $email, $subject, $message
		);

		$inserted = $wpdb->query( $prepared_query );

		if ( $inserted ) {
			wp_send_json_success( [ 'message' => __( 'Thank you for sending us your feedback', 'wlc' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to submit form' . 'wlc' ) ] );
		}
	}


}
