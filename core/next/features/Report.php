<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Report extends Feature {
	public $feature_name = 'report';
	public $feature_allowed = false;
	public $settings = array(
		'allow_roles'                           => null,
		'report_mode'                           => 'form',
		'scroll_form'                           => true,
		'show_report_status'                    => false,
		'show_report_status_to_moderators_only' => true,
		'notify_active'                         => true,
		'notify_keymasters'                     => true,
		'notify_moderators'                     => true,
		'notify_shortcodes'                     => true,
		'notify_content'                        => '',
		'notify_subject'                        => '[%BLOG_NAME%] Post reported: %REPORT_TITLE%'
	);

	private $form_added = false;

	public function __construct() {
		parent::__construct();

		$this->feature_allowed = $this->allowed( 'allow' );

		if ( $this->feature_allowed ) {
			add_action( 'gdbbx_template_before_replies_loop', array( $this, 'before_replies_loop' ), 10, 2 );

			add_filter( 'gdbbx_script_values', array( $this, 'script_values' ) );

			if ( $this->settings['show_report_status'] ) {
				$mods_only = $this->settings['show_report_status_to_moderators_only'];

				if ( ! $mods_only || ( $mods_only && gdbbx_can_user_moderate() ) ) {
					add_action( 'bbp_theme_after_topic_content', array( $this, 'report_status' ), 200 );
					add_action( 'bbp_theme_after_reply_content', array( $this, 'report_status' ), 200 );
				}
			}
		}
	}

	public static function instance() : Report {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Report();
		}

		return $instance;
	}

	public function before_replies_loop( $posts, $users ) {
		gdbbx_cache()->report_run_bulk_list( $posts );
	}

	public function script_values( $values ) {
		$values['load'][] = 'report';
		$values['report'] = apply_filters( 'gdbbx_report_script_values', array(
			'alert'   => _x( "Report message is required.", "Report Form", "bbp-core" ),
			'after'   => _x( "Reported", "Report Form", "bbp-core" ),
			'confirm' => _x( "Are you sure you want to report this post?", "Report Form", "bbp-core" ),
			'scroll'  => $this->settings['scroll_form'],
			'mode'    => $this->settings['report_mode'],
			'min'     => 4
		) );

		return $values;
	}

	public function report_status() {
		$post_id = bbp_get_reply_id();

		if ( $post_id == 0 ) {
			$post_id = bbp_get_topic_id();
		}

		if ( gdbbx_cache()->report_is_reported( $post_id ) ) {
			$message = bbp_is_topic( $post_id )
				?
				__( "This topic has been reported.", "bbp-core" )
				:
				__( "This reply has been reported.", "bbp-core" );

			$notice = '<div class="gdbbx-report-notice bbp-template-notice error"><p>' . $message . '</p></div>';

			echo apply_filters( 'gdbbx_notice_report_status', $notice, $message, $post_id );
		}
	}

	public function get_report_link( $id ) {
		$show = apply_filters( 'gdbbx_report_show_link', true, $id );
		$link = false;

		if ( $this->feature_allowed && $show ) {
			if ( ! gdbbx_cache()->report_user_reported( $id, bbp_get_current_user_id() ) ) {
				$nonce     = wp_create_nonce( 'gdbbx-report-' . $id );
				$type      = bbp_is_reply( $id ) ? 'reply' : 'topic';
				$post_type = bbp_is_reply( $id ) ? bbp_get_reply_post_type() : bbp_get_topic_post_type();

				$link = '<a role="button" href="#" data-nonce="' . $nonce . '" data-type="' . $type . '" data-post-type="' . $post_type . '" data-id="' . $id . '" class="gdbbx-link-report gdbbx-link-report-' . $id . '">' . $this->_string( 'report' ) . '</a>';
			} else {
				$link = '<span>' . $this->_string( 'reported' ) . '</span>';
			}

			if ( ! $this->form_added ) {
				add_action( 'wp_footer', array( $this, 'embed_form' ) );

				$this->form_added = true;
			}
		}

		return $link;
	}

	public function embed_form() {
		$path = gdbbx_get_template_part( 'gdbbx-form-report-post.php' );
		$form = apply_filters( 'gdbbx_report_form_file', $path );

		include_once( $form );
	}

	public function report( $post_id, $user_id, $report ) {
		if ( ! gdbbx_db()->report_given( $post_id, $user_id ) ) {
			gdbbx_db()->report_add( $post_id, $user_id, $report );

			$this->notify( $user_id, $post_id, $report );

			do_action( 'gdbbx_post_reported', $post_id, $user_id, $report );
		}
	}

	public function notify( $user_id, $post_id, $report = '' ) {
		if ( $this->settings['notify_active'] ) {
			$start_content = $this->settings['notify_content'];
			$start_subject = __( $this->settings['notify_subject'], "bbp-core" );

			$_title = bbp_is_reply( $post_id ) ? bbp_get_reply_title( $post_id ) : bbp_get_topic_title( $post_id );
			$_url   = bbp_is_reply( $post_id ) ? bbp_get_reply_url( $post_id ) : get_permalink( $post_id );
			$_forum = bbp_is_reply( $post_id ) ? bbp_get_reply_forum_id( $post_id ) : bbp_get_topic_forum_id( $post_id );

			$tags_content = array(
				'BLOG_NAME'      => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				'REPORT_AUTHOR'  => bbp_get_user_nicename( $user_id ),
				'REPORT_TITLE'   => wp_kses( $_title, array() ),
				'REPORT_LINK'    => $_url,
				'REPORT_CONTENT' => $report,
				'REPORTS_LIST'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-reported-posts' ),
				'FORUM_TITLE'    => strip_tags( bbp_get_forum_title( $_forum ) )
			);

			$tags_subject = array(
				'BLOG_NAME'    => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				'REPORT_TITLE' => wp_kses( $_title, array() ),
			);

			if ( $this->settings['notify_shortcodes'] ) {
				$start_content = do_shortcode( $start_content );
			}

			$content = d4p_replace_tags_in_content( $start_content, $tags_content );
			$subject = d4p_replace_tags_in_content( $start_subject, $tags_subject );

			$users = array();

			if ( $this->settings['notify_keymasters'] ) {
				$users = array_merge( $users, get_users( array( 'role' => bbp_get_keymaster_role() ) ) );
			}

			if ( $this->settings['notify_moderators'] ) {
				$users = array_merge( $users, get_users( array( 'role' => bbp_get_moderator_role() ) ) );
			}

			$users = apply_filters( 'gdbbx_report_notification_emails', $users, $user_id, $post_id, $report );

			foreach ( $users as $user ) {
				wp_mail( $user->user_email, $subject, $content );
			}
		}
	}

	private function _string( $name ) {
		switch ( $name ) {
			default:
			case 'report':
				return apply_filters( 'gdbbx_report_string_report', __( "Report", "bbp-core" ) );
			case 'reported':
				return apply_filters( 'gdbbx_report_string_reported', __( "Reported", "bbp-core" ) );
		}
	}
}
