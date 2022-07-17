<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

use SpiderDevs\Plugin\BBPC\Attachments\Topic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {
		add_action( 'wp_ajax_bbpc_report_post', array( $this, 'report_post' ) );
		add_action( 'wp_ajax_bbpc_say_thanks', array( $this, 'say_thanks' ) );
		add_action( 'wp_ajax_bbpc_attachments_thread', array( $this, 'attachments_thread' ) );

		add_action( 'wp_ajax_bbpc_attachment_detach', array( $this, 'attachment_detach' ) );
		add_action( 'wp_ajax_bbpc_attachment_delete', array( $this, 'attachment_delete' ) );
		add_action( 'wp_ajax_bbpc_attachment_attach', array( $this, 'attachment_attach' ) );
	}

	public static function instance() : AJAX {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	public function report_post() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );

		$report = isset( $_REQUEST['report'] ) ? d4p_sanitize_basic( $_REQUEST['report'] ) : '';

		$post_id = absint( $_REQUEST['post'] );
		$user_id = bbp_get_current_user_id();

		if ( wp_verify_nonce( $nonce, 'bbpc-report-' . $post_id ) !== false ) {
			bbpc_report()->report( $post_id, $user_id, $report );
		}
	}

	public function say_thanks() {
		$nonce   = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$action  = d4p_sanitize_basic( $_REQUEST['say'] );
		$post_id = absint( $_REQUEST['id'] );
		$user_id = bbp_get_current_user_id();

		if ( wp_verify_nonce( $nonce, 'bbpc-thanks-' . $post_id ) !== false ) {
			bbpc_say_thanks()->save_thanks( $action, $post_id, $user_id );

			$type = bbp_is_reply( $post_id ) ? 'reply' : 'topic';

			$render = bbpc_say_thanks()->display_ajax( $post_id, $type );

			die( $render );
		}
	}

	public function attachments_thread() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$topic = absint( $_REQUEST['topic'] );
		$page  = absint( $_REQUEST['page'] );

		$render = '';

		if ( wp_verify_nonce( $nonce, 'bbpc-attachments-thread-' . $topic ) !== false ) {
			$render = Topic::instance()->files( $topic, $page );
		}

		die( $render );
	}

	public function attachment_detach() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$post  = absint( $_REQUEST['post'] );
		$id    = absint( $_REQUEST['id'] );

		$result = array( 'status' => 'ok' );

		if ( wp_verify_nonce( $nonce, 'bbpc-det-' . $post . '-' . $id ) !== false ) {
			bbpc_db()->detach_attachment( $post, $id );

			$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

			bbpc_db()->update_topic_attachments_count( $topic_id );
		} else {
			$result['status'] = 'error';
		}

		d4p_json_die( $result );
	}

	public function attachment_delete() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$post  = absint( $_REQUEST['post'] );
		$id    = absint( $_REQUEST['id'] );

		$result = array( 'status' => 'ok' );

		if ( wp_verify_nonce( $nonce, 'bbpc-del-' . $post . '-' . $id ) !== false ) {
			bbpc_db()->delete_attachment( $post, $id );

			$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

			bbpc_db()->update_topic_attachments_count( $topic_id );
		} else {
			$result['status'] = 'error';
		}

		d4p_json_die( $result );
	}

	public function attachment_attach() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$post  = absint( $_REQUEST['post'] );
		$ids   = (array) $_REQUEST['id'];

		if ( $post > 0 && wp_verify_nonce( $nonce, 'bbpc-att-' . $post ) !== false ) {
			require_once( BBPC_PATH . 'core/functions/admin.php' );

			$result = '';
			$added  = 0;

			foreach ( $ids as $id ) {
				$id = absint( $id );

				if ( $id > 0 && bbpc_db()->is_assign_attachment_possible( $post, $id ) ) {
					bbpc_db()->assign_attachment( $post, $id );

					$result .= bbpc_admin_render_attachment_for_metabox( $post, $id );

					$added ++;
				}
			}

			if ( $added > 0 ) {
				$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

				bbpc_db()->update_topic_attachments_count( $topic_id );

				die( $result );
			}
		}

		die( "<li>" . __( "The request was not valid", "bbp-core" ) . "</li>" );
	}
}
