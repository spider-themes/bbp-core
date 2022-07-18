<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use WP_User_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Thanks extends Feature {
	public $feature_name = 'thanks';
	public $allowed = true;
	public $settings = array(
		'removal'           => false,
		'topic'             => true,
		'reply'             => true,
		'allow_super_admin' => true,
		'allow_roles'       => null,
		'limit_display'     => 20,
		'display_date'      => 'no',
		'notify_active'     => false,
		'notify_override'   => false,
		'notify_shortcodes' => true,
		'notify_content'    => '',
		'notify_subject'    => '[%BLOG_NAME%] Thanks received: %POST_TITLE%'
	);

	public function __construct() {
		parent::__construct();

		$this->allowed = $this->allowed( 'allow' );

		add_action( 'gdbbx_template_before_replies_loop', array( $this, 'before_replies_loop' ), 10, 2 );

		add_action( 'bbp_theme_after_topic_content', array( $this, 'thanks_display' ), 40 );
		add_action( 'bbp_theme_after_reply_content', array( $this, 'thanks_display' ), 40 );

		add_action( 'bbp_get_request_thanks', array( $this, 'process_thanks' ) );
		add_action( 'bbp_get_request_unthanks', array( $this, 'process_thanks' ) );

		add_filter( 'gdbbx_script_values', array( $this, 'script_values' ) );

		if ( $this->settings['notify_active'] ) {
			UserSettings::instance()->register(
				'thanks-notification',
				__( "Thanks received", "bbp-core" ),
				__( "Receive instant email notification when someone says thanks to you for topic or reply.", "bbp-core" ),
				'notifications',
				'checkbox',
				false
			);
		}
	}

	public static function instance() : Thanks {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Thanks();
		}

		return $instance;
	}

	private function _thanks( $id ) {
		$is_reply  = bbp_is_reply( $id );
		$user_id   = bbp_get_current_user_id();
		$author_id = $is_reply ? bbp_get_reply_author_id( $id ) : bbp_get_topic_author_id( $id );
		$type      = $is_reply ? 'reply' : 'topic';

		if ( $author_id == $user_id ) {
			return false;
		}

		if ( $this->settings[ $type ] && $this->allowed ) {
			$thanked = gdbbx_cache()->thanks_get_given( $id, $user_id );
			$nonce   = wp_create_nonce( 'gdbbx-thanks-' . $id );

			$data = array(
				'data-thanks-id="' . $id . '"',
				'data-thanks-nonce="' . $nonce . '"'
			);

			if ( $thanked ) {
				if ( $this->settings['removal'] ) {
					$data[] = 'data-thanks-action="unthanks"';

					return '<a role="button" ' . join( ' ', $data ) . ' href="#" class="gdbbx-link-unthanks">' . $this->_string( 'remove' ) . '</a>';
				} else {
					return false;
				}
			} else {
				$data[] = 'data-thanks-action="thanks"';

				return '<a role="button" ' . join( ' ', $data ) . ' href="#" class="gdbbx-link-thanks">' . $this->_string( 'thanks' ) . '</a>';
			}
		} else {
			return false;
		}
	}

	public function get_thanks_link( $id ) {
		return $this->_thanks( $id );
	}

	public function save_thanks( $action, $post_id, $user_id ) {
		if ( $action == 'thanks' ) {
			do_action( 'gdbbx_user_said_thanks', $post_id, $user_id );

			$thanks_id = gdbbx_db()->thanks_add( $post_id, $user_id );

			$this->thanks_notify( $post_id, $user_id, $thanks_id );

			do_action( 'gdbbx_say_thanks_saved', $post_id, $user_id, $thanks_id );
		} else if ( $action == 'unthanks' ) {
			do_action( 'gdbbx_user_removed_thanks', $post_id, $user_id );

			gdbbx_db()->thanks_remove( $post_id, $user_id );

			do_action( 'gdbbx_say_thanks_removed', $post_id, $user_id );
		}
	}

	public function process_thanks() {
		$post_id = intval( $_GET['id'] );
		$user_id = bbp_get_current_user_id();
		$action  = $_GET['action'];

		if ( ! bbp_verify_nonce_request( 'gdbbx-thanks-' . $post_id ) ) {
			bbp_add_error( 'bgdbx_thanks_nonce', __( "<strong>ERROR</strong>: Are you sure you wanted to do that?", "bbp-core" ) );
		}

		if ( bbp_has_errors() ) {
			return;
		}

		$this->save_thanks( $action, $post_id, $user_id );

		$url = remove_query_arg( array( '_wpnonce', 'id', 'action' ) );

		wp_redirect( $url );
		exit;
	}

	public function script_values( $values ) {
		$values['load'][] = 'thanks';
		$values['thanks'] = apply_filters( 'gdbbx_thanks_script_values', array(
			'thanks'   => $this->_string( 'thanks' ),
			'unthanks' => $this->_string( 'remove' ),
			'saved'    => $this->_string( 'saved' ),
			'removal'  => $this->settings['removal']
		) );

		return $values;
	}

	public function thanks_display() {
		$id = bbp_get_reply_id();

		if ( $id == 0 ) {
			$id = bbp_get_topic_id();
		}

		$type = bbp_is_reply( $id ) ? 'reply' : 'topic';

		if ( $this->settings[ $type ] ) {
			$this->display( $id, $type );
		}
	}

	public function thanks_notify( $post_id, $user_id, $thanks_id = 0 ) {
		$active = apply_filters( 'gdbbx_thanks_send_user_notification', $this->settings['notify_active'], $post_id, $user_id );

		if ( $active ) {
			$start_content = _x( "%THANKS_AUTHOR% said thanks for '%POST_TITLE%' in forum: '%FORUM_TITLE%'.

You can see this post here: %POST_LINK%
-----------
Do not reply to this email!", "Email message: notify about post thanks", "bbp-core" );

			$start_subject = _x( "[%BLOG_NAME%] Thanks received: %POST_TITLE%", "Email title: notify about post thanks", "bbp-core" );

			if ( $this->settings['notify_override'] ) {
				$start_content = $this->settings['notify_content'];
				$start_subject = $this->settings['notify_subject'];
			}

			$_author = bbp_is_reply( $post_id ) ? bbp_get_reply_author_id( $post_id ) : bbp_get_topic_author_id( $post_id );
			$user    = get_user_by( 'id', $_author );

			if ( $user ) {
				$send = gdbbx_user( $_author )->get( 'thanks-notification' );

				if ( apply_filters( 'gdbbx_thanks_send_notification_forced_for_author', $send, $_author ) ) {
					$_title = bbp_is_reply( $post_id ) ? bbp_get_reply_title( $post_id ) : bbp_get_topic_title( $post_id );
					$_url   = bbp_is_reply( $post_id ) ? bbp_get_reply_url( $post_id ) : get_permalink( $post_id );
					$_forum = bbp_is_reply( $post_id ) ? bbp_get_reply_forum_id( $post_id ) : bbp_get_topic_forum_id( $post_id );

					$tags_content = array(
						'BLOG_NAME'     => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
						'THANKS_AUTHOR' => bbp_get_user_nicename( $user_id ),
						'POST_TITLE'    => wp_kses( $_title, array() ),
						'POST_LINK'     => $_url,
						'FORUM_TITLE'   => strip_tags( bbp_get_forum_title( $_forum ) )
					);

					$tags_subject = $tags_content;

					if ( $this->settings['notify_shortcodes'] ) {
						$start_content = do_shortcode( $start_content );
					}

					$content = d4p_replace_tags_in_content( $start_content, $tags_content );
					$subject = d4p_replace_tags_in_content( $start_subject, $tags_subject );

					wp_mail( $user->user_email, $subject, $content );
				}
			}
		}
	}

	public function display( $post_id, $post_type = 'topic' ) {
		echo '<div class="gdbbx-thanks-wrapper gdbbx-thanks-type-' . $post_type . ' gdbbx-thanks-post-' . $post_id . '">';

		$thanks_list = gdbbx_cache()->thanks_get_list( $post_id );

		if ( count( $thanks_list ) > 0 ) {
			include( gdbbx_get_template_part( 'gdbbx-thanks-list.php' ) );
		} else {
			include( gdbbx_get_template_part( 'gdbbx-thanks-none.php' ) );
		}

		echo '</div>';
	}

	public function display_ajax( $post_id, $post_type = 'topic' ) {
		ob_start();

		$this->display( $post_id, $post_type );

		return ob_get_clean();
	}

	public function before_replies_loop( $posts, $users ) {
		gdbbx_cache()->thanks_run_bulk_count_given( $users );
		gdbbx_cache()->thanks_run_bulk_count_received( $users );
		gdbbx_cache()->thanks_run_bulk_given( $posts );
		gdbbx_cache()->thanks_run_bulk_list( $posts );
	}

	public function get_list_top_thanked_users( $args = array() ) {
		$default = array( 'limit' => 10, 'return' => 'list' );

		$args = wp_parse_args( $args, $default );

		$raw = gdbbx_db()->top_thanked_users( $args['limit'] );

		if ( ! empty( $raw ) ) {
			if ( $args['return'] == 'ids' ) {
				return $raw;
			}

			$user_query = new WP_User_Query( array(
				'orderby' => 'include',
				'include' => array_keys( $raw )
			) );

			$list = $user_query->get_results();

			$return = array();
			foreach ( $list as $user ) {
				$user->thanks_count = $raw[ $user->ID ];

				$return[ $user->ID ] = $user;
			}

			return $return;
		} else {
			return array();
		}
	}

	public function build_user_for_display( $user ) {
		$user_id   = is_numeric( $user ) ? absint( $user ) : ( isset( $user->user_id ) ? absint( $user->user_id ) : 0 );
		$date_time = is_object( $user ) && isset( $user->logged ) ? $user->logged : false;

		if ( $user_id > 0 && get_userdata( $user_id ) !== false ) {
			$show = array(
				'avatar' => get_avatar( $user_id, '16' ),
				'label'  => bbp_get_user_profile_link( $user_id )
			);

			$show_date = $this->settings['display_date'];

			if ( $show_date != 'no' && $date_time !== false ) {
				$timestamp = gdbbx_plugin()->datetime()->timestamp_gmt_to_local( strtotime( $date_time ) );

				if ( $show_date == 'date' ) {
					$show['date'] = date_i18n( get_option( 'date_format' ), $timestamp );
				} else if ( $show_date == 'age' ) {
					$show['date'] = human_time_diff( $timestamp, time() );
				}
			}

			return apply_filters( 'gdbbx_say_thanks_user_to_display', $show, $user );
		}

		return false;
	}

	private function _string( $name ) {
		switch ( $name ) {
			default:
			case 'thanks':
				return apply_filters( 'gdbbx_thanks_string_thanks', __( "Thanks", "bbp-core" ) );
			case 'remove':
				return apply_filters( 'gdbbx_thanks_string_remove', __( "Remove Thanks", "bbp-core" ) );
			case 'saved':
				return apply_filters( 'gdbbx_thanks_string_saved', __( "Thanks Saved", "bbp-core" ) );
		}
	}
}
