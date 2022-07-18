<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use Dev4Press\Plugin\GDBBX\Attachments\Topic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {
		add_action( 'wp_ajax_gdbbx_report_post', array( $this, 'report_post' ) );
		add_action( 'wp_ajax_gdbbx_say_thanks', array( $this, 'say_thanks' ) );
		add_action( 'wp_ajax_gdbbx_attachments_thread', array( $this, 'attachments_thread' ) );

		add_action( 'wp_ajax_gdbbx_attachment_detach', array( $this, 'attachment_detach' ) );
		add_action( 'wp_ajax_gdbbx_attachment_delete', array( $this, 'attachment_delete' ) );
		add_action( 'wp_ajax_gdbbx_attachment_attach', array( $this, 'attachment_attach' ) );
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

		if ( wp_verify_nonce( $nonce, 'gdbbx-report-' . $post_id ) !== false ) {
			gdbbx_report()->report( $post_id, $user_id, $report );
		}
	}

	public function say_thanks() {
		$nonce   = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$action  = d4p_sanitize_basic( $_REQUEST['say'] );
		$post_id = absint( $_REQUEST['id'] );
		$user_id = bbp_get_current_user_id();

		if ( wp_verify_nonce( $nonce, 'gdbbx-thanks-' . $post_id ) !== false ) {
			gdbbx_say_thanks()->save_thanks( $action, $post_id, $user_id );

			$type = bbp_is_reply( $post_id ) ? 'reply' : 'topic';

			$render = gdbbx_say_thanks()->display_ajax( $post_id, $type );

			die( $render );
		}
	}

	public function attachments_thread() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$topic = absint( $_REQUEST['topic'] );
		$page  = absint( $_REQUEST['page'] );

		$render = '';

		if ( wp_verify_nonce( $nonce, 'gdbbx-attachments-thread-' . $topic ) !== false ) {
			$render = Topic::instance()->files( $topic, $page );
		}

		die( $render );
	}

	public function attachment_detach() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$post  = absint( $_REQUEST['post'] );
		$id    = absint( $_REQUEST['id'] );

		$result = array( 'status' => 'ok' );

		if ( wp_verify_nonce( $nonce, 'gdbbx-det-' . $post . '-' . $id ) !== false ) {
			gdbbx_db()->detach_attachment( $post, $id );

			$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

			gdbbx_db()->update_topic_attachments_count( $topic_id );
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

		if ( wp_verify_nonce( $nonce, 'gdbbx-del-' . $post . '-' . $id ) !== false ) {
			gdbbx_db()->delete_attachment( $post, $id );

			$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

			gdbbx_db()->update_topic_attachments_count( $topic_id );
		} else {
			$result['status'] = 'error';
		}

		d4p_json_die( $result );
	}

	public function attachment_attach() {
		$nonce = d4p_sanitize_basic( $_REQUEST['nonce'] );
		$post  = absint( $_REQUEST['post'] );
		$ids   = (array) $_REQUEST['id'];

		if ( $post > 0 && wp_verify_nonce( $nonce, 'gdbbx-att-' . $post ) !== false ) {
			require_once( GDBBX_PATH . 'core/functions/admin.php' );

			$result = '';
			$added  = 0;

			foreach ( $ids as $id ) {
				$id = absint( $id );

				if ( $id > 0 && gdbbx_db()->is_assign_attachment_possible( $post, $id ) ) {
					gdbbx_db()->assign_attachment( $post, $id );

					$result .= gdbbx_admin_render_attachment_for_metabox( $post, $id );

					$added ++;
				}
			}

			if ( $added > 0 ) {
				$topic_id = bbp_is_topic( $post ) ? $post : bbp_get_reply_topic_id( $post );

				gdbbx_db()->update_topic_attachments_count( $topic_id );

				die( $result );
			}
		}

		die( "<li>" . __( "The request was not valid", "bbp-core" ) . "</li>" );
	}
}
