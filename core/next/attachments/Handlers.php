<?php

namespace Dev4Press\Plugin\GDBBX\Attachments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Handlers {
	public function __construct() {
	}

	public static function instance() : Handlers {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Handlers();
			$instance->run();
		}

		return $instance;
	}

	private function run() {
		add_action( 'before_delete_post', array( $this, 'delete_post' ) );
		add_action( 'gdbbx_init', array( $this, 'delete_attachments' ) );

		if ( gdbbx_attachments()->get( 'hide_attachments_from_media_library' ) ) {
			add_filter( 'ajax_query_attachments_args', array( $this, 'query_attachments' ) );

			if ( is_admin() ) {
				add_action( 'pre_get_posts', array( $this, 'query_attachments_list' ) );
			}
		}
	}

	public function delete_post( $id ) {
		if ( bbp_is_reply( $id ) || bbp_is_topic( $id ) ) {
			if ( gdbbx_attachments()->get( 'delete_attachments' ) == 'delete' ) {
				$files = gdbbx_get_post_attachments( $id );

				if ( is_array( $files ) && ! empty( $files ) ) {
					foreach ( $files as $file ) {
						gdbbx_db()->delete_attachment( $id, $file->ID );
					}
				}
			} else if ( gdbbx_attachments()->get( 'delete_attachments' ) == 'detach' ) {
				gdbbx_db()->remove_attachment_assignment( $id );

				gdbbx_db()->update( gdbbx_db()->wpdb()->posts, array( 'post_parent' => 0 ), array(
					'post_parent' => $id,
					'post_type'   => 'attachment'
				) );
			}

			if ( bbp_is_reply( $id ) ) {
				$topic_id = bbp_get_reply_topic_id( $id );

				gdbbx_db()->update_topic_attachments_count( $topic_id );
			}
		}
	}

	public function query_attachments( $query ) {
		add_filter( 'posts_clauses', array( $this, 'intercept_query_clauses' ) );

		return $query;
	}

	public function intercept_query_clauses( $pieces ) {
		remove_filter( 'posts_clauses', array( $this, 'intercept_query_clauses' ) );

		$pieces['join']  .= ' LEFT JOIN ' . gdbbx_db()->attachments . ' gdbbxa ON gdbbxa.`attachment_id` = ' . gdbbx_db()->wpdb()->posts . '.`ID` ';
		$pieces['where'] .= ' AND gdbbxa.`post_id` IS NULL ';

		return $pieces;
	}

	public function query_attachments_list( $wp_query ) {
		global $pagenow;

		if ( ! in_array( $pagenow, array( 'upload.php' ) ) ) {
			return;
		}

		add_filter( 'posts_clauses', array( $this, 'intercept_query_clauses' ) );
	}

	public function delete_attachments() {
		if ( isset( $_GET['gdbbx-action'] ) ) {
			$action = d4p_sanitize_basic( $_GET['gdbbx-action'] );
			$att_id = absint( $_GET['att_id'] );
			$bbp_id = absint( $_GET['bbp_id'] );

			if ( $att_id > 0 && $bbp_id > 0 && ( $action == 'delete' || $action == 'detach' ) ) {
				$nonce = wp_verify_nonce( $_GET['_wpnonce'], 'gdbbx-attachment-' . $action . '-' . $bbp_id . '-' . $att_id );

				if ( $nonce ) {
					$this->delete_attachment( $att_id, $bbp_id, $action );
				}
			}

			$url = remove_query_arg( array( '_wpnonce', 'gdbbx-action', 'att_id', 'bbp_id' ) );
			wp_redirect( $url );
			exit;
		}
	}

	public function delete_attachment( $att_id, $bbp_id, $action ) {
		$post      = get_post( $bbp_id );
		$author_id = $post->post_author;

		$allow = gdbbx_attachments()->get_deletion_status( $author_id );

		if ( $action == 'delete' && ( $allow == 'delete' || $allow == 'both' ) ) {
			gdbbx_db()->delete_attachment( $bbp_id, $att_id );
		}

		if ( $action == 'detach' && ( $allow == 'detach' || $allow == 'both' ) ) {
			gdbbx_db()->detach_attachment( $bbp_id, $att_id );
		}

		$_topic_id = bbp_is_topic( $bbp_id ) ? $bbp_id : bbp_get_reply_topic_id( $bbp_id );

		gdbbx_db()->update_topic_attachments_count( $_topic_id );
	}
}