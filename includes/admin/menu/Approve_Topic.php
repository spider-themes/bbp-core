<?php
namespace admin\menu;

defined( 'ABSPATH' ) || exit();

class Approve_Topic {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'approve_spam_topic' ] );
		add_action( 'admin_init', [ $this, 'approve_pending_reply' ] );
	}

	public function approve_spam_topic() {
		$approval_request = $_GET['bbpc_approve_topic_id'] ?? false;

		if ( $approval_request ) {
			// Verify user has permission to moderate topics
			if ( ! current_user_can( 'moderate' ) ) {
				wp_die( esc_html__( 'You do not have permission to approve topics.', 'bbp-core' ) );
			}

			// Verify nonce for CSRF protection
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bbpc_approve_topic_' . (int) $approval_request ) ) {
				wp_die( esc_html__( 'Security check failed. Please try again.', 'bbp-core' ) );
			}

			bbp_approve_topic( (int) $approval_request );
			wp_safe_redirect( admin_url( 'admin.php?page=bbp-core' ) );
			exit;
		}
	}

	public function approve_pending_reply() {
		$approval_request = $_GET['bbpc_approve_reply_id'] ?? false;

		if ( $approval_request ) {
			// Verify user has permission to moderate replies
			if ( ! current_user_can( 'moderate' ) ) {
				wp_die( esc_html__( 'You do not have permission to approve replies.', 'bbp-core' ) );
			}

			// Verify nonce for CSRF protection
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bbpc_approve_reply_' . (int) $approval_request ) ) {
				wp_die( esc_html__( 'Security check failed. Please try again.', 'bbp-core' ) );
			}

			bbp_approve_reply( (int) $approval_request );
			wp_safe_redirect( admin_url( 'admin.php?page=bbp-core' ) );
			exit;
		}
	}
}

new Approve_Topic();


