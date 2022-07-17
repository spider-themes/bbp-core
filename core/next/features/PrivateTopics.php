<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\BB;
use SpiderDevs\Plugin\BBPC\Manager\PrivateTopics as PrivateTopicsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PrivateTopics extends Feature {
	public $feature_name = 'private-topics';
	public $settings = array(
		'form_position'       => 'bbp_theme_before_topic_form_submit_wrapper',
		'super_admin'         => true,
		'roles'               => null,
		'visitor'             => false,
		'default'             => 'unchecked',
		'moderators_can_read' => true
	);

	public $topics = false;
	public $topic_id = 0;

	public function __construct() {
		parent::__construct();

		add_action( 'bbpc_bbpress_request_first', array( $this, 'request' ) );
		add_action( 'bbpc_bbpress_template_first', array( $this, 'loader' ) );

		add_action( 'bbpc_core', array( $this, 'ready' ) );
		add_action( 'bbpc_feed', array( $this, 'loader_for_feed' ) );
	}

	public static function instance() : PrivateTopics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new PrivateTopics();
		}

		return $instance;
	}

	public function ready() {
		PrivateTopicsManager::instance();
	}

	public function request() {
		if ( $this->allowed() ) {
			add_action( 'bbp_new_topic', array( $this, 'topic_save' ), 1 );
			add_action( 'bbp_edit_topic', array( $this, 'topic_save' ), 1 );
		}
	}

	public function loader() {
		$this->topics = $this->is_enabled_topic_private();

		if ( $this->topics ) {
			if ( $this->allowed() ) {
				add_action( $this->settings['form_position'], array( $this, 'topic_checkbox' ), 8 );
			}

			add_filter( 'bbp_current_user_can_access_create_reply_form', array( $this, 'topic_reply_form' ) );

			add_filter( 'bbp_get_forum_subscribers', array( $this, 'forum_subscribers' ) );

			add_filter( 'bbp_get_topic_subscribe_link', array( $this, 'topic_link_override' ), 10, 2 );
			add_filter( 'bbp_get_user_favorites_link', array( $this, 'topic_link_override' ), 10, 2 );

			add_filter( 'bbp_get_single_topic_description', array( $this, 'topic_description' ), 10, 2 );
			add_filter( 'bbp_after_has_replies_parse_args', array( $this, 'topic_has_replies' ) );
		}

		add_filter( 'bbp_get_reply_class', array( $this, 'reply_post_class' ), 10, 2 );
		add_filter( 'bbp_get_topic_class', array( $this, 'topic_post_class' ), 10, 2 );

		add_filter( 'bbp_get_topic_excerpt', array( $this, 'topic_hiding' ), 10000, 2 );
		add_filter( 'bbp_get_topic_content', array( $this, 'topic_hiding' ), 10000, 2 );
		add_filter( 'bbp_get_reply_excerpt', array( $this, 'topic_hiding' ), 10000, 2 );
		add_filter( 'bbp_get_reply_content', array( $this, 'topic_hiding' ), 10000, 2 );

		add_filter( 'bbp_activity_topic_create_excerpt', array( $this, 'topic_activity_stream' ) );
	}

	public function loader_for_feed() {
		add_filter( 'bbpc_privacy_is_enabled_topic_private', array( $this, 'feed_override_no_forum' ), 10, 2 );

		$this->topics = $this->is_enabled_topic_private();

		if ( $this->topics ) {
			add_filter( 'bbp_get_topic_content', array( $this, 'topic_hiding' ), 10000, 2 );
			add_filter( 'bbp_get_reply_content', array( $this, 'topic_hiding' ), 10000, 2 );
			add_filter( 'bbp_after_has_replies_parse_args', array( $this, 'hide_replies_in_feed' ), 10000 );
		}
	}

	public function feed_override_no_forum( $active, $forum_id ) : bool {
		if ( $forum_id == 0 ) {
			$active = true;
		}

		return (bool) $active;
	}

	public function is_enabled_topic_private() : bool {
		$forum_id = BB::i()->get_forum_id();
		$forum    = bbpc_forum( $forum_id )->privacy()->get( 'enable_topic_private' );

		$active = false;
		if ( $forum == 'default' || $forum == 'yes' ) {
			$active = true;
		} else if ( $forum == 'no' ) {
			$active = false;
		}

		return (bool) apply_filters( 'bbpc_privacy_is_enabled_topic_private', $active, $forum_id );
	}

	public function topic_activity_stream( $content ) {
		if ( $this->is_private( $this->topic_id ) ) {
			$content = apply_filters( 'bbpc_private_topics_is_private_text_for_activity_stream', __( "Topic is marked as private.", "bbp-core" ) );
		}

		return $content;
	}

	public function topic_has_replies( $r ) {
		if ( bbp_is_single_topic() ) {
			$topic_id = $r['post_parent'];

			if ( ! $this->is_user_allowed( $topic_id ) ) {
				$r['post_type'] = '_fake_reply_';
			}
		}

		return $r;
	}

	public function topic_description( $retstr, $r ) {
		if ( ! $this->is_user_allowed( $r['topic_id'] ) ) {
			$retstr = $r['before'] . apply_filters( 'bbpc_private_topics_is_private_text_for_description', __( "This topic is marked as private", "bbp-core" ) ) . $r['after'];
		}

		return $retstr;
	}

	public function topic_link_override( $content, $r ) {
		$topic_id = isset( $r['object_id'] ) ? absint( $r['object_id'] ) : absint( $r['topic_id'] );
		$user_id  = absint( $r['user_id'] );

		if ( ! $this->is_user_allowed( $topic_id, $user_id ) ) {
			$content = '';
		}

		return $content;
	}

	public function topic_post_class( $classes, $topic_id ) {
		if ( $this->is_private( $topic_id ) ) {
			$classes[] = 'bbpc-private-topic';

			if ( ! $this->is_user_allowed( $topic_id ) ) {
				$classes[] = 'bbpc-private-topic-locked';
			}
		}

		return $classes;
	}

	public function reply_post_class( $classes, $reply_id ) {
		if ( bbp_is_topic( $reply_id ) ) {
			$classes = $this->topic_post_class( $classes, $reply_id );
		}

		return $classes;
	}

	public function forum_subscribers( $users ) : array {
		$final = array();

		foreach ( $users as $user_id ) {
			if ( $this->is_user_allowed( $this->topic_id, $user_id ) ) {
				$final[] = $user_id;
			}
		}

		return $final;
	}

	public function is_user_allowed( $topic_id = 0, $user_id = 0 ) : bool {
		if ( $topic_id == 0 ) {
			$topic_id = bbp_get_topic_id();
		}

		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		$allowed = true;
		$private = false;

		if ( bbp_is_topic( $topic_id ) && $this->is_private( $topic_id ) ) {
			$private   = true;
			$author_id = bbp_get_topic_author_id( $topic_id );

			$allowed = false;

			if ( $user_id > 0 ) {
				$allowed = $author_id == $user_id;
			}

			if ( ! $allowed && $this->settings['moderators_can_read'] ) {
				$allowed = bbpc_can_user_moderate();
			}
		}

		return (bool) apply_filters( 'bbpc_private_is_user_allowed_to_topic', $allowed, $private, $topic_id, $user_id );
	}

	public function topic_reply_form( $retval ) : bool {
		if ( ! $this->is_user_allowed() ) {
			$retval = false;
		}

		return (bool) $retval;
	}

	public function topic_hiding( $content, $topic_id ) {
		if ( ! $this->is_user_allowed( $topic_id ) ) {
			$content = apply_filters( 'bbpc_private_topics_is_private_text', __( "This topic has been marked as private.", "bbp-core" ) );
		}

		return $content;
	}

	public function hide_replies_in_feed( $r ) {
		if ( isset( $r['feed'] ) && $r['feed'] ) {
			if ( $r['post_parent'] != 'any' ) {
				$topic_id = $r['post_parent'];

				if ( ! $this->is_user_allowed( $topic_id ) ) {
					$r['post_parent'] = - 1;
				}
			}
		}

		return $r;
	}

	public function topic_save( $topic_id = 0 ) {
		$this->topic_id = $topic_id;

		if ( isset( $_POST['bbpc_private_topic'] ) ) {
			update_post_meta( $topic_id, '_bbp_topic_is_private', '1' );
		} else {
			delete_post_meta( $topic_id, '_bbp_topic_is_private' );
		}
	}

	public function privacy_status( $topic_id = 0, $private = true ) {
		if ( $private ) {
			update_post_meta( $topic_id, '_bbp_topic_is_private', '1' );
		} else {
			delete_post_meta( $topic_id, '_bbp_topic_is_private' );
		}
	}

	public function topic_checkbox() {
		$edit   = bbp_is_topic_edit();
		$status = $edit ? $this->is_private() : ( $this->settings['default'] == "checked" );

		$label = apply_filters( 'bbpc_private_topic_checkbox_label', __( "Set this as private topic", "bbp-core" ) );

		?>

        <p>
            <input name="bbpc_private_topic" id="bbpc_private_topic" type="checkbox"<?php checked( '1', $status ); ?> value="1"/>
            <label for="bbpc_private_topic"><?php echo $label; ?></label>
        </p>

		<?php
	}

	public function is_private( $topic_id = 0 ) : bool {
		if ( $topic_id == 0 ) {
			$topic_id = bbp_get_topic_id();
		}

		$status = bbpc_cache()->private_post( $topic_id );

		return (bool) apply_filters( 'bbpc_is_topic_private', $status, $topic_id );
	}
}
