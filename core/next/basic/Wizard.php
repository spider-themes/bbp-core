<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wizard {
	public $panel = false;
	public $panels = array();

	public $_default = array(
		'toolbar'        => array(
			'toolbar__super_admin' => true,
			'toolbar__visitor'     => true,
			'toolbar__roles'       => null,
			'toolbar__title'       => 'Forums',
			'toolbar__information' => true
		),
		'signatures'     => array(
			'signatures__scope'                => 'global',
			'signatures__limiter'              => true,
			'signatures__length'               => 512,
			'signatures__super_admin'          => true,
			'signatures__roles'                => null,
			'signatures__edit_super_admin'     => true,
			'signatures__edit_roles'           => null,
			'signatures__editor'               => 'textarea',
			'signatures__enhanced_active'      => true,
			'signatures__enhanced_method'      => 'html',
			'signatures__enhanced_super_admin' => true,
			'signatures__enhanced_roles'       => null,
			'signatures__process_smilies'      => true,
			'signatures__process_chars'        => true,
			'signatures__process_autop'        => true
		),
		'quotes'         => array(
			'quote__method'       => 'bbcode',
			'quote__full_content' => 'postquote',
			'quote__super_admin'  => true,
			'quote__visitor'      => false,
			'quote__roles'        => null
		),
		'canned'         => array(
			'canned-replies__canned_roles'         => array( 'bbp_keymaster', 'bbp_moderator' ),
			'canned-replies__post_type_singular'   => 'Canned Reply',
			'canned-replies__post_type_plural'     => 'Canned Replies',
			'canned-replies__use_taxonomy'         => false,
			'canned-replies__taxonomy_singular'    => 'Category',
			'canned-replies__taxonomy_plural'      => 'Categories',
			'canned-replies__auto_close_on_insert' => true
		),
		'thanks'         => array(
			'thanks__removal'           => false,
			'thanks__topic'             => true,
			'thanks__reply'             => true,
			'thanks__allow_super_admin' => true,
			'thanks__allow_roles'       => null,
			'thanks__limit_display'     => 20,
			'thanks__display_date'      => 'no',
			'thanks__notify_active'     => false,
			'thanks__notify_override'   => false,
			'thanks__notify_shortcodes' => true,
			'thanks__notify_content'    => '',
			'thanks__notify_subject'    => '[%BLOG_NAME%] Thanks received: %POST_TITLE%'
		),
		'report'         => array(
			'report__allow_roles'                           => null,
			'report__report_mode'                           => 'form',
			'report__scroll_form'                           => true,
			'report__show_report_status'                    => false,
			'report__show_report_status_to_moderators_only' => true,
			'report__notify_active'                         => true,
			'report__notify_keymasters'                     => true,
			'report__notify_moderators'                     => true,
			'report__notify_shortcodes'                     => true,
			'report__notify_content'                        => '',
			'report__notify_subject'                        => '[%BLOG_NAME%] Post reported: %REPORT_TITLE%'
		),
		'private'        => array(
			'private-topics__form_position'        => 'bbp_theme_before_topic_form_submit_wrapper',
			'private-topics__super_admin'          => true,
			'private-topics__roles'                => null,
			'private-topics__visitor'              => false,
			'private-topics__default'              => 'unchecked',
			'private-topics__moderators_can_read'  => true,
			'private-replies__form_position'       => 'bbp_theme_before_reply_form_submit_wrapper',
			'private-replies__super_admin'         => true,
			'private-replies__roles'               => null,
			'private-replies__visitor'             => false,
			'private-replies__default'             => 'unchecked',
			'private-replies__moderators_can_read' => true,
			'private-replies__threaded'            => true,
			'private-replies__css_hide'            => false
		),
		'stats'          => array(
			'users-stats__super_admin'            => true,
			'users-stats__visitor'                => false,
			'users-stats__roles'                  => null,
			'users-stats__show_online_status'     => true,
			'users-stats__show_registration_date' => false,
			'users-stats__show_topics'            => true,
			'users-stats__show_replies'           => true,
			'users-stats__show_thanks_given'      => false,
			'users-stats__show_thanks_received'   => false
		),
		'attachments'    => array(
			'attachments__hide_from_visitors'      => true,
			'attachments__preview_for_visitors'    => false,
			'attachments__max_file_size'           => 512,
			'attachments__max_to_upload'           => 4,
			'attachments__file_target_blank'       => false,
			'attachments__roles_to_upload'         => null,
			'attachments__roles_no_limit'          => array( 'bbp_keymaster' ),
			'attachments__attachment_icons'        => true,
			'attachments__download_link_attribute' => true
		),
		'content_editor' => array(
			'content-editor__topic'                       => 'textarea',
			'content-editor__reply'                       => 'textarea',
			'content-editor__bbcodes_topic_size'          => 'medium',
			'content-editor__bbcodes_topic_editor_fix'    => true,
			'content-editor__bbcodes_reply_size'          => 'medium',
			'content-editor__bbcodes_reply_editor_fix'    => true,
			'content-editor__tinymce_topic_teeny'         => false,
			'content-editor__tinymce_topic_media_buttons' => false,
			'content-editor__tinymce_topic_wpautop'       => true,
			'content-editor__tinymce_topic_quicktags'     => true,
			'content-editor__tinymce_topic_textarea_rows' => 12,
			'content-editor__tinymce_reply_teeny'         => false,
			'content-editor__tinymce_reply_media_buttons' => false,
			'content-editor__tinymce_reply_wpautop'       => true,
			'content-editor__tinymce_reply_quicktags'     => true,
			'content-editor__tinymce_reply_textarea_rows' => 12
		),
		'activity_off'   => array(
			'latest_track_users_topic'               => false,
			'latest_topic_new_replies_badge'         => false,
			'latest_topic_new_replies_mark'          => false,
			'latest_topic_new_replies_strong_title'  => false,
			'latest_topic_new_replies_in_thread'     => false,
			'latest_topic_new_topic_badge'           => false,
			'latest_topic_new_topic_strong_title'    => false,
			'latest_topic_unread_topic_badge'        => false,
			'latest_topic_unread_topic_strong_title' => false,
			'latest_forum_new_posts_badge'           => false,
			'latest_forum_new_posts_strong_title'    => false,
			'latest_forum_unread_forum_badge'        => false,
			'latest_forum_unread_forum_strong_title' => false,
			'track_last_activity_active'             => false
		),
		'activity'       => array(
			'latest_track_users_topic'                => true,
			'latest_use_cutoff_timestamp'             => true,
			'latest_topic_new_replies_badge'          => true,
			'latest_topic_new_replies_mark'           => true,
			'latest_topic_new_replies_strong_title'   => true,
			'latest_topic_new_replies_in_thread'      => true,
			'latest_topic_new_topic_badge'            => true,
			'latest_topic_new_topic_strong_title'     => true,
			'latest_topic_unread_topic_badge'         => true,
			'latest_topic_unread_topic_strong_title'  => true,
			'latest_forum_new_posts_badge'            => true,
			'latest_forum_new_posts_strong_title'     => false,
			'latest_forum_unread_forum_badge'         => true,
			'latest_forum_unread_forum_strong_title'  => false,
			'track_last_activity_active'              => true,
			'track_current_session_cookie_expiration' => 60,
			'track_basic_cookie_expiration'           => 365
		)
	);

	public function __construct() {
		$this->_init_panels();
	}

	public static function instance() : Wizard {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Wizard();
		}

		return $instance;
	}

	private function _init_panels() {
		$this->panels = array(
			'intro'       => array( 'label' => __( "Intro", "bbp-core" ) ),
			'features'    => array( 'label' => __( "Features", "bbp-core" ) ),
			'editors'     => array( 'label' => __( "Editors", "bbp-core" ) ),
			'attachments' => array( 'label' => __( "Attachments", "bbp-core" ) ),
			'tracking'    => array( 'label' => __( "Tracking", "bbp-core" ) ),
			'finish'      => array( 'label' => __( "Finish", "bbp-core" ) )
		);

		$this->setup_panel( bbpc_admin()->panel );
	}

	public function setup_panel( $panel ) {
		$this->panel = $panel;

		if ( ! isset( $this->panels[ $panel ] ) || $panel === false || is_null( $panel ) ) {
			$this->panel = 'intro';
		}
	}

	public function current_panel() {
		return $this->panel;
	}

	public function panels_index() {
		return array_keys( $this->panels );
	}

	public function next_panel() {
		$panel = $this->current_panel();
		$all   = $this->panels_index();

		$index = array_search( $panel, $all );
		$next  = $index + 1;

		if ( $next == count( $all ) ) {
			$next = 0;
		}

		return $all[ $next ];
	}

	public function is_last_panel() {
		$panel = $this->current_panel();
		$all   = $this->panels_index();

		$index = array_search( $panel, $all );

		return $index + 1 == count( $all );
	}

	public function get_form_action() {
		return 'admin.php?page=bbp-core-wizard&panel=' . $this->next_panel();
	}

	public function get_form_nonce() {
		return wp_create_nonce( 'bbpc-wizard-nonce-' . $this->current_panel() );
	}

	public function panel_postback() {
		$post = $_POST['bbpc']['wizard'];

		$this->setup_panel( $post['_page'] );

		if ( wp_verify_nonce( $post['_nonce'], 'bbpc-wizard-nonce-' . $this->current_panel() ) ) {
			$data = isset( $post[ $this->current_panel() ] ) ? (array) $post[ $this->current_panel() ] : array();

			switch ( $this->current_panel() ) {
				case 'intro':
					$this->_postback_intro( $data );
					break;
				case 'features':
					$this->_postback_features( $data );
					break;
				case 'attachments':
					$this->_postback_attachments( $data );
					break;
				case 'editors':
					$this->_postback_editors( $data );
					break;
				case 'tracking':
					$this->_postback_tracking( $data );
					break;
			}

			wp_redirect( 'admin.php?page=bbp-core-wizard&panel=' . $this->next_panel() );
			exit;
		} else {
			wp_redirect( 'admin.php?page=bbp-core-wizard&panel=' . $this->current_panel() );
			exit;
		}
	}

	private function _copy_settings( $what, $group = 'features' ) {
		foreach ( $this->_default[ $what ] as $key => $value ) {
			bbpc()->set( $key, $value, $group );
		}
	}

	private function _postback_intro( $data ) {
		if ( isset( $data['toolbar'] ) && $data['toolbar'] == 'yes' ) {
			bbpc()->set( 'toolbar', true, 'load' );

			$this->_copy_settings( 'toolbar' );
		} else {
			bbpc()->set( 'toolbar', false, 'load' );
		}

		if ( isset( $data['signatures'] ) && $data['signatures'] == 'yes' ) {
			bbpc()->set( 'signatures', true, 'load' );

			$this->_copy_settings( 'signatures' );
		} else {
			bbpc()->set( 'signatures', false, 'load' );
		}

		if ( isset( $data['bbcodes'] ) && $data['bbcodes'] == 'yes' ) {
			bbpc()->set( 'bbcodes', true, 'load' );
		} else {
			bbpc()->set( 'bbcodes', false, 'load' );
		}

		if ( isset( $data['quotes'] ) && $data['quotes'] == 'yes' ) {
			bbpc()->set( 'quote', true, 'load' );

			$this->_copy_settings( 'quotes' );

			bbpc()->set( 'tweaks__kses_allowed_override', 'expanded', 'features' );

			if ( isset( $data['bbcodes'] ) && $data['bbcodes'] == 'yes' ) {
				bbpc()->set( 'quote__method', 'bbcode', 'features' );
			} else {
				bbpc()->set( 'quote__method', 'html', 'features' );
			}
		} else {
			bbpc()->set( 'quote', false, 'load' );
		}

		bbpc()->save( 'load' );
		bbpc()->save( 'features' );
		bbpc()->save( 'tools' );
		bbpc()->save( 'bbpress' );
	}

	private function _postback_features( $data ) {
		if ( isset( $data['canned'] ) && $data['canned'] == 'yes' ) {
			bbpc()->set( 'canned-replies', true, 'load' );

			$this->_copy_settings( 'canned' );
		} else {
			bbpc()->set( 'canned-replies', false, 'load' );
		}

		if ( isset( $data['thanks'] ) && $data['thanks'] == 'yes' ) {
			bbpc()->set( 'thanks', true, 'load' );

			$this->_copy_settings( 'thanks' );
		} else {
			bbpc()->set( 'thanks', false, 'load' );
		}

		if ( isset( $data['report'] ) && $data['report'] == 'yes' ) {
			bbpc()->set( 'report', true, 'load' );

			$this->_copy_settings( 'report' );
		} else {
			bbpc()->set( 'report', false, 'load' );
		}

		if ( isset( $data['private'] ) && $data['private'] == 'yes' ) {
			bbpc()->set( 'private-topics', true, 'load' );
			bbpc()->set( 'private-replies', true, 'load' );

			$this->_copy_settings( 'private' );
		} else {
			bbpc()->set( 'private-topics', false, 'load' );
			bbpc()->set( 'private-replies', false, 'load' );
		}

		if ( isset( $data['stats'] ) && $data['stats'] == 'yes' ) {
			bbpc()->set( 'users-stats', true, 'load' );

			$this->_copy_settings( 'stats' );

			if ( isset( $data['thanks'] ) && $data['thanks'] == 'yes' ) {
				bbpc()->set( 'users-stats__show_thanks_given', true, 'features' );
				bbpc()->set( 'users-stats__show_thanks_received', true, 'features' );
			}
		} else {
			bbpc()->set( 'users-stats', false, 'load' );
		}

		bbpc()->save( 'load' );
		bbpc()->save( 'features' );
	}

	private function _postback_editors( $data ) {
		bbpc()->set( 'content-editor', true, 'load' );

		$this->_copy_settings( 'content_editor' );

		$update_tinymce_settings = false;

		if ( isset( $data['replace'] ) && $data['replace'] == 'yes' ) {
			switch ( $data['editor'] ) {
				case 'quicktags':
					bbpc()->set( 'content-editor__topic', 'richarea', 'features' );
					bbpc()->set( 'content-editor__reply', 'richarea', 'features' );
					break;
				case 'bbcodes':
					bbpc()->set( 'content-editor__topic', 'bbcodes', 'features' );
					bbpc()->set( 'content-editor__reply', 'bbcodes', 'features' );
					break;
				case 'teeny':
				case 'tinymce':
					$update_tinymce_settings = true;

					bbpc()->set( 'content-editor__topic', 'tinymce', 'features' );
					bbpc()->set( 'content-editor__reply', 'tinymce', 'features' );
					bbpc()->set( 'content-editor__tinymce_topic_teeny', $data['editor'] == 'teeny', 'features' );
					bbpc()->set( 'content-editor__tinymce_reply_teeny', $data['editor'] == 'teeny', 'features' );
					break;
			}
		}

		if ( isset( $data['library'] ) && $data['library'] == 'yes' ) {
			bbpc()->set( 'tweaks__participant_media_library_upload', true, 'features' );

			if ( $update_tinymce_settings ) {
				bbpc()->set( 'content-editor__tinymce_topic_media_buttons', true, 'features' );
				bbpc()->set( 'content-editor__tinymce_reply_media_buttons', true, 'features' );
			}
		} else {
			bbpc()->set( 'tweaks__participant_media_library_upload', false, 'features' );
		}

		bbpc()->save( 'features' );
	}

	private function _postback_attachments( $data ) {
		if ( isset( $data['attach'] ) && $data['attach'] == 'yes' ) {
			bbpc()->set( 'attachments', true, 'load' );

			foreach ( $this->_default['attachments'] as $key => $value ) {
				bbpc()->set( $key, $value, 'features' );
			}

			if ( isset( $data['enhance'] ) && $data['enhance'] == 'yes' ) {
				bbpc()->set( 'attachments__method', 'enhanced', 'features' );
			} else {
				bbpc()->set( 'attachments__method', 'classic', 'features' );
			}

			if ( isset( $data['images'] ) && $data['images'] == 'yes' ) {
				bbpc()->set( 'attachments__files_list_mode', 'mixed', 'features' );
			} else {
				bbpc()->set( 'attachments__files_list_mode', 'list', 'features' );
			}

			$mime = isset( $data['mime'] ) ? sanitize_text_field( $data['mime'] ) : 'all';

			switch ( $mime ) {
				default:
				case 'all':
					$value = array();
					break;
				case 'images':
					$value = array( 'jpg|jpeg|jpe', 'png', 'gif' );
					break;
				case 'media':
					$value = array( 'jpg|jpeg|jpe', 'png', 'gif', 'mp3|m4a|m4b', 'mov|qt', 'avi', 'wmv', 'mid|midi' );
					break;
			}

			bbpc()->set( 'attachments__mime_types_list', $value, 'features' );
		} else {
			bbpc()->set( 'attachments', true, 'load' );
		}

		bbpc()->save( 'load' );
		bbpc()->save( 'features' );
	}

	private function _postback_tracking( $data ) {
		if ( isset( $data['online'] ) && $data['online'] == 'yes' ) {
			bbpc()->set( 'active', true, 'online' );
			bbpc()->set( 'track_users', true, 'online' );
			bbpc()->set( 'track_guests', true, 'online' );
		} else {
			bbpc()->set( 'active', false, 'online' );
		}

		if ( isset( $data['activity'] ) && $data['activity'] == 'yes' ) {
			foreach ( $this->_default['activity'] as $key => $value ) {
				bbpc()->set( $key, $value, 'tools' );
			}
		} else {
			foreach ( $this->_default['activity_off'] as $key => $value ) {
				bbpc()->set( $key, $value, 'tools' );
			}
		}

		bbpc()->save( 'online' );
		bbpc()->save( 'tools' );
	}
}
