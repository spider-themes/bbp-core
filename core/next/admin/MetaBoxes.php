<?php

namespace SpiderDevs\Plugin\BBPC\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MetaBoxes {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_meta' ) );

		add_action( 'bbpc_admin_toolbox_forums_meta_content_attachments', array(
			$this,
			'metabox_content_attachments'
		) );
		add_action( 'bbpc_admin_toolbox_forums_meta_content_privacy', array( $this, 'metabox_content_privacy' ) );
		add_action( 'bbpc_admin_toolbox_forums_meta_content_locking', array( $this, 'metabox_content_locking' ) );
		add_action( 'bbpc_admin_toolbox_forums_meta_content_closing', array( $this, 'metabox_content_closing' ) );

		add_action( 'bbpc_admin_toolbox_topic_attachments_meta_content_files', array(
			$this,
			'metabox_content_files'
		) );
		add_action( 'bbpc_admin_toolbox_topic_attachments_meta_content_errors', array(
			$this,
			'metabox_content_errors'
		) );

		add_action( 'save_post', array( $this, 'save_edit_forum' ) );
	}

	public static function instance() {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new MetaBoxes();
		}

		return $instance;
	}

	public function save_edit_forum( $post_id ) {
		if ( isset( $_POST['bbpc_forum_settings'] ) && $_POST['bbpc_forum_settings'] == 'edit' ) {
			$data = isset( $_POST['bbpc_settings'] ) ? (array) $_POST['bbpc_settings'] : array();

			$meta = array();

			$_string = array(
				'topic_auto_close_after_active',
				'topic_auto_close_after_notice',
				'privacy_lock_topic_form',
				'privacy_lock_reply_form',
				'privacy_enable_topic_private',
				'privacy_enable_reply_private'
			);
			$_html   = array( 'privacy_lock_topic_form_message', 'privacy_lock_reply_form_message' );
			$_int    = array( 'topic_auto_close_after_days' );

			if ( isset( $data['attachments_status'] ) ) {
				$_string = array_merge( $_string, array(
					'attachments_status',
					'attachments_topic_form',
					'attachments_reply_form',
					'attachments_hide_from_visitors',
					'attachments_preview_for_visitors',
					'attachments_max_file_size_override',
					'attachments_max_to_upload_override',
					'attachments_mime_types_list_override'
				) );
				$_int    = array_merge( $_int, array( 'attachments_max_file_size', 'attachments_max_to_upload' ) );
			}

			foreach ( $_string as $key ) {
				if ( isset( $data[ $key ] ) ) {
					$meta[ $key ] = d4p_sanitize_basic( $data[ $key ] );
				}
			}

			foreach ( $_html as $key ) {
				if ( isset( $data[ $key ] ) ) {
					$meta[ $key ] = d4p_sanitize_html( $data[ $key ] );
				}
			}

			foreach ( $_int as $key ) {
				$meta[ $key ] = isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ? absint( $data[ $key ] ) : '';
			}

			if ( isset( $meta['attachments_mime_types_list_override'] ) && $meta['attachments_mime_types_list_override'] == 'yes' ) {
				$meta['attachments_mime_types_list'] = (array) ( $data['attachments_mime_types_list'] );
			}

			$meta = wp_parse_args( $meta, bbpc_default_forum_settings() );

			if ( $meta['topic_auto_close_after_active'] != 'inherit' ) {
				bbpc()->current['rules']['forums_auto_close'][ $post_id ] = $meta['topic_auto_close_after_active'];
			} else {
				if ( isset( bbpc()->current['rules']['forums_auto_close'][ $post_id ] ) ) {
					unset( bbpc()->current['rules']['forums_auto_close'][ $post_id ] );
				}
			}

			bbpc()->save( 'rules' );

			update_post_meta( $post_id, '_bbpc_settings', $meta );
		}
	}

	public function admin_meta() {
		if ( current_user_can( BBPC_CAP ) ) {
			add_meta_box( 'bbpc-meta-forum', __( "BBP Core", "bbp-core" ), array(
				$this,
				'metabox_forum'
			), bbp_get_forum_post_type(), 'advanced', 'high' );
			add_meta_box( 'gdbbattach-meta-files', __( "Attachments List", "bbp-core" ), array(
				$this,
				'metabox_files'
			), array( bbp_get_topic_post_type(), bbp_get_reply_post_type() ), 'side', 'high' );
		}
	}

	public function metabox_forum() {
		include( BBPC_PATH . 'forms/meta/forum.php' );
	}

	public function metabox_files() {
		include( BBPC_PATH . 'forms/meta/attachments.php' );
	}

	public function metabox_content_attachments( $post_id ) {
		include( BBPC_PATH . 'forms/meta/forum.attachments.php' );
	}

	public function metabox_content_privacy( $post_id ) {
		include( BBPC_PATH . 'forms/meta/forum.privacy.php' );
	}

	public function metabox_content_locking( $post_id ) {
		include( BBPC_PATH . 'forms/meta/forum.locking.php' );
	}

	public function metabox_content_closing( $post_id ) {
		include( BBPC_PATH . 'forms/meta/forum.closing.php' );
	}

	public function metabox_content_files( $post_id ) {
		include( BBPC_PATH . 'forms/meta/attachments.files.php' );
	}

	public function metabox_content_errors( $post_id ) {
		include( BBPC_PATH . 'forms/meta/attachments.errors.php' );
	}
}
