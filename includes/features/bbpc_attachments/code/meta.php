<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GDATTAdminMeta {
	function __construct() {
		add_action( 'after_setup_theme', [ $this, 'load' ], 10 );
	}

	public function load() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_meta' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );

		add_action( 'save_post', [ $this, 'save_edit_forum' ] );

		add_action( 'manage_topic_posts_columns', [ $this, 'admin_post_columns' ], 1000 );
		add_action( 'manage_reply_posts_columns', [ $this, 'admin_post_columns' ], 1000 );

		add_action( 'manage_topic_posts_custom_column', [ $this, 'admin_columns_data' ], 1000, 2 );
		add_action( 'manage_reply_posts_custom_column', [ $this, 'admin_columns_data' ], 1000, 2 );
	}

	public static function instance() {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new GDATTAdminMeta();
		}

		return $instance;
	}

	public function admin_init() {
		if ( isset( $_POST['gdbb-attach-submit'] ) ) {

			check_admin_referer( 'bbp-core' );

			//TODO: Check all these.
			BBPCATTCore::instance()->o['max_file_size']       = absint( $_POST['max_file_size'] );
			BBPCATTCore::instance()->o['max_to_upload']       = absint( $_POST['max_to_upload'] );
			BBPCATTCore::instance()->o['roles_to_upload']     = (array) $_POST['roles_to_upload'];
			BBPCATTCore::instance()->o['is_attachment_icon']  = isset( $_POST['is_attachment_icon'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['is_attachment_icons'] = isset( $_POST['is_attachment_icons'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['hide_from_visitors']  = isset( $_POST['hide_from_visitors'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['include_always']      = isset( $_POST['include_always'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['delete_attachments']  = bbpc_sanitize_basic( $_POST['delete_attachments'] );

			update_option( 'bbp-core', BBPCATTCore::instance()->o );
			wp_redirect( esc_url_raw( add_query_arg( 'settings-updated', 'true' ) ) );
			exit();
		}

		if ( isset( $_POST['gdbb-att-advanced-submit'] ) ) {
			check_admin_referer( 'bbp-core' );

			BBPCATTCore::instance()->o['log_upload_errors']            = isset( $_POST['log_upload_errors'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['errors_visible_to_admins']     = isset( $_POST['errors_visible_to_admins'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['errors_visible_to_moderators'] = isset( $_POST['errors_visible_to_moderators'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['errors_visible_to_author']     = isset( $_POST['errors_visible_to_author'] ) ? 1 : 0;
			BBPCATTCore::instance()->o['delete_visible_to_admins']     = true; //TODO: Customize in pro version
			BBPCATTCore::instance()->o['delete_visible_to_moderators'] = true; //TODO: Customize in pro version
			BBPCATTCore::instance()->o['delete_visible_to_author']     = false; //TODO: Customize in pro version

			update_option( 'bbp-core', BBPCATTCore::instance()->o );
			wp_redirect( esc_url_raw( add_query_arg( 'settings-updated', 'true' ) ) );
			exit();
		}
	}

	public function admin_head() { ?>
		<style type="text/css">
			/*<![CDATA[*/
			th.column-gdbbatt_count,
			td.column-gdbbatt_count {
				width: 3%;
				text-align: center;
			}
			/*]]>*/
		</style>
		<?php
	}

	public function save_edit_forum( $post_id ) {
		if ( isset( $_POST['post_ID'] ) && $_POST['post_ID'] > 0 ) {
			$post_id = $_POST['post_ID'];
		}

		if ( isset( $_POST['gdbbatt_forum_meta'] ) && $_POST['gdbbatt_forum_meta'] == 'edit' ) {
			$data = (array) $_POST['gdbbatt'];
			$meta = [
				'disable'            => isset( $data['disable'] ) ? 1 : 0,
				'to_override'        => isset( $data['to_override'] ) ? 1 : 0,
				'hide_from_visitors' => isset( $data['hide_from_visitors'] ) ? 1 : 0,
				'max_file_size'      => absint( $data['max_file_size'] ),
				'max_to_upload'      => absint( $data['max_to_upload'] ),
			];

			update_post_meta( $post_id, '_gdbbatt_settings', $meta );
		}
	}

	public function admin_post_columns( $columns ) {
		$columns['gdbbatt_count'] = '<img src="' . BBPCATTACHMENT_URL . 'css/gfx/attachment.png" width="16" height="12" alt="' . __( 'Attachments', 'bbp-core' ) . '" title="' . __( 'Attachments', 'bbp-core' ) . '" />';

		return $columns;
	}

	public function admin_columns_data( $column, $id ) {
		if ( $column == 'gdbbatt_count' ) {
			$attachments = bbpc_get_post_attachments( $id );
			echo count( $attachments );
		}
	}

	public function admin_meta() {
		if ( current_user_can( BBPC_ATTACHMENT ) ) {
			add_meta_box( 'gdbbattach-meta-forum', __( 'Attachments Settings', 'bbp-core' ), [ $this, 'metabox_forum' ], 'forum', 'side', 'high' );
			add_meta_box( 'gdbbattach-meta-files', __( 'Attachments List', 'bbp-core' ), [ $this, 'metabox_files' ], 'topic', 'side', 'high' );
			add_meta_box( 'gdbbattach-meta-files', __( 'Attachments List', 'bbp-core' ), [ $this, 'metabox_files' ], 'reply', 'side', 'high' );
		}
	}

	public function metabox_forum() {
		global $post_ID;

		$meta = get_post_meta( $post_ID, '_gdbbatt_settings', true );
		if ( ! is_array( $meta ) ) {

			$meta = [
				'disable'            => 0,
				'to_override'        => 0,
				'hide_from_visitors' => 1,
				'max_file_size'      => BBPCATTCore::instance()->get_file_size( true ),
				'max_to_upload'      => BBPCATTCore::instance()->get_max_files( true ),
			];
		}

		include BBPCATTACHMENTS_PATH . 'forms/attachments/meta_forum.php';
	}

	public function metabox_files() {
		global $post_ID, $user_ID;

		$post      = get_post( $post_ID );
		$author_id = $post->post_author;

		include BBPCATTACHMENTS_PATH . 'forms/attachments/meta_files.php';
	}
}
