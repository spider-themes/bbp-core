<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostAnonymously extends Feature {
	public $feature_name = 'post-anonymously';
	public $settings = array(
		'allowed_roles'                => array( 'bbp_keymaster', 'bbp_moderator', 'bbp_participant' ),
		'allowed_in_forums'            => array(),
		'topic_form_position'          => 'bbp_theme_before_topic_form_submit_wrapper',
		'reply_form_position'          => 'bbp_theme_before_reply_form_submit_wrapper',
		'anonymous_name'               => 'Anonymous {{HASH}}',
		'anonymous_email'              => '{{HASH}}@anon-account.email',
		'anonymous_hash'               => array( 'topic_id', 'user_id', 'user_email' ),
		'original_author_store_method' => 'limited', // limited, unlimited, no
		'original_author_store_days'   => 365,
		'original_author_store_roles'  => array( 'bbp_keymaster' ),
		'forced_in_forums'             => array(),
		'forced_exception_roles'       => array( 'bbp_keymaster', 'bbp_moderator' )
	);

	public $forum_id = 0;
	public $topic_id = 0;
	public $is_checked = false;
	public $is_request = false;
	public $request_type = '';

	public function __construct() {
		parent::__construct();

		if ( is_user_logged_in() ) {
			add_action( 'gdbbx_template', array( $this, 'frontend' ) );
		}
	}

	public static function instance() : PostAnonymously {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new PostAnonymously();
		}

		return $instance;
	}

	public function frontend() {
		add_filter( 'bbp_current_user_can_access_create_topic_form', array( $this, 'prepare_topic_form' ) );
		add_filter( 'bbp_current_user_can_access_create_reply_form', array( $this, 'prepare_reply_form' ) );

		add_action( 'bbp_post_request', array( $this, 'prepare_topic_request' ), 9 );
		add_action( 'bbp_post_request', array( $this, 'prepare_reply_request' ), 9 );
	}

	public function prepare_topic_request( $action = '' ) {
		if ( 'bbp-new-topic' === $action ) {
			$this->request_type = 'topic';
			$this->is_request   = true;

			add_filter( 'bbp_new_topic_pre_insert', array( $this, 'modify_pre_insert_topic' ) );
		}
	}

	public function prepare_reply_request( $action = '' ) {
		if ( 'bbp-new-reply' === $action ) {
			$this->request_type = 'reply';
			$this->is_request   = true;

			add_filter( 'bbp_new_reply_pre_insert', array( $this, 'modify_pre_insert_reply' ) );
		}
	}

	public function modify_pre_insert_topic( $data ) {
		$this->forum_id = $data['post_parent'];

		$data = $this->process_pre_insert( $data );

		if ( $data['post_author'] === 0 ) {
			add_action( 'bbp_new_topic_post_extras', array( $this, 'generate_anonymous_topic' ) );
		}

		return $data;
	}

	public function modify_pre_insert_reply( $data ) {
		$topic_id       = $data['post_parent'];
		$this->forum_id = bbp_get_topic_forum_id( $topic_id );

		$data = $this->process_pre_insert( $data );

		if ( $data['post_author'] === 0 ) {
			add_action( 'bbp_new_reply_post_extras', array( $this, 'generate_anonymous_reply' ) );
		}

		return $data;
	}

	public function generate_anonymous_topic( $topic_id ) {
		$this->generate_anonymous_final( $topic_id, $topic_id );
	}

	public function generate_anonymous_reply( $reply_id ) {
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		$this->generate_anonymous_final( $reply_id, $topic_id );
	}

	public function prepare_topic_form( $retval ) {
		if ( bbp_is_topic_edit() ) {
			return $retval;
		}

		$this->forum_id = apply_filters( 'gdbbx_post_anonymously_prepare_topic_form_forum_id', bbp_get_forum_id() );

		return $this->prepare_the_form( $retval, 'topic' );
	}

	public function prepare_reply_form( $retval ) {
		if ( bbp_is_reply_edit() ) {
			return $retval;
		}

		$this->forum_id = apply_filters( 'gdbbx_post_anonymously_prepare_reply_form_forum_id', bbp_get_topic_forum_id() );

		return $this->prepare_the_form( $retval, 'reply' );
	}

	public function is_user_allowed() : bool {
		return $this->allowed( 'allowed', 'post-anonymously-allowed' );
	}

	public function is_user_forced_exception() : bool {
		return $this->allowed( 'forced_exception', 'post-anonymously-forced-exception' );
	}

	public function is_forum_allowed( $forum_id = 0 ) : bool {
		if ( empty( $this->get( 'allowed_in_forums', array() ) ) ) {
			$allowed = true;
		} else {
			$allowed = $forum_id > 0 && in_array( $forum_id, $this->get( 'allowed_in_forums', array() ) );
		}

		return apply_filters( 'gdbbx_post_anonymously_is_forum_allowed', $allowed, $forum_id );
	}

	public function is_forum_forced( $forum_id = 0 ) : bool {
		if ( $forum_id > 0 && $this->is_forum_allowed( $forum_id ) ) {
			$forced = in_array( $forum_id, $this->get( 'forced_in_forums', array() ) );
		} else {
			$forced = false;
		}

		return apply_filters( 'gdbbx_post_anonymously_is_forum_forced', $forced, $forum_id );
	}

	public function topic_allowed_checkbox() {
		include( gdbbx_get_template_part( 'gdbbx-form-topic-post-anonymously.php' ) );
	}

	public function reply_allowed_checkbox() {
		include( gdbbx_get_template_part( 'gdbbx-form-reply-post-anonymously.php' ) );
	}

	public function topic_forced_message() {
		?>

        <div class="bbp-template-notice info">
            <p><?php echo apply_filters( 'gdbbx_post_anonymously_topic_form_forced_message', __( "The topic you create in this forum will be posted anonymously.", "bbp-core" ) ); ?></p>
        </div>

		<?php
	}

	public function reply_forced_message() {
		?>

        <div class="bbp-template-notice info">
            <p><?php echo apply_filters( 'gdbbx_post_anonymously_reply_form_forced_message', __( "The reply you create in this forum topic will be posted anonymously.", "bbp-core" ) ); ?></p>
        </div>

		<?php
	}

	private function generate_anonymous_final( $post_id, $topic_id ) {
		$elements = array(
			'topic_id'   => $topic_id,
			'forum_id'   => bbp_get_topic_forum_id( $topic_id ),
			'user_id'    => bbp_get_current_user_id(),
			'user_email' => $this->get_user_email( bbp_get_current_user_id() )
		);

		$source = $this->get( 'anonymous_hash', array() );
		$source = empty( $source ) ? array( 'user_id' ) : $source;

		$hash_key = '';
		foreach ( $source as $key ) {
			$hash_key .= $elements[ $key ];
		}

		$full_hash = md5( $hash_key );
		$hash      = apply_filters( 'gdbbx_post_anonymously_anon_hash', substr( $full_hash, 0, 6 ) . substr( $full_hash, 26 ), $full_hash );

		$anon = array(
			'_bbp_anonymous_name'  => str_replace( '{{HASH}}', $hash, $this->get( 'anonymous_name' ) ),
			'_bbp_anonymous_email' => str_replace( '{{HASH}}', $hash, $this->get( 'anonymous_email' ) ),
			'_bbp_anonymous_hash'  => $full_hash
		);

		if ( $this->get( 'original_author_store_method' ) !== 'no' ) {
			$anon['_bbp_anonymous_link'] = $elements['user_id'];
		}

		$anon = apply_filters( 'gdbbx_post_anonymously_anon_data', $anon, $elements, $hash );

		foreach ( $anon as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	private function process_pre_insert( $data ) {
		$_user_allowed   = $this->is_user_allowed();
		$_user_exception = $this->is_user_forced_exception();
		$_allowed        = $this->is_forum_allowed( $this->forum_id );
		$_forced         = $this->is_forum_forced( $this->forum_id );

		$_check_post = false;
		$_post_anon  = false;

		if ( $_allowed ) {
			if ( $_user_allowed ) {
				if ( $_forced ) {
					if ( $_user_exception ) {
						$_check_post = true;
					} else {
						$_post_anon = true;
					}
				} else {
					$_check_post = true;
				}
			}
		}

		if ( $_check_post ) {
			if ( isset( $_POST['gdbbx_post_anonymously'] ) && $_POST['gdbbx_post_anonymously'] === '1' ) {
				$_post_anon = true;
			}
		}

		if ( $_post_anon ) {
			$data['post_author'] = 0;
		}

		return $data;
	}

	private function prepare_the_form( $retval, $type ) {
		$_user_allowed   = $this->is_user_allowed();
		$_user_exception = $this->is_user_forced_exception();
		$_allowed        = $this->is_forum_allowed( $this->forum_id );
		$_forced         = $this->is_forum_forced( $this->forum_id );

		if ( $_allowed ) {
			if ( $_user_allowed ) {
				if ( $_forced ) {
					if ( $_user_exception ) {
						$this->is_checked = true;

						if ( $type == 'topic' ) {
							add_action( $this->settings['topic_form_position'], array(
								$this,
								'topic_allowed_checkbox'
							), 9 );
						} else if ( $type == 'reply' ) {
							add_action( $this->settings['reply_form_position'], array(
								$this,
								'reply_allowed_checkbox'
							), 9 );
						}
					} else {
						if ( $type == 'topic' ) {
							add_action( 'bbp_theme_before_topic_form_notices', array(
								$this,
								'topic_forced_message'
							), 9 );
						} else if ( $type == 'reply' ) {
							add_action( 'bbp_theme_before_reply_form_notices', array(
								$this,
								'reply_forced_message'
							), 9 );
						}
					}
				} else {
					if ( $type == 'topic' ) {
						add_action( $this->settings['topic_form_position'], array(
							$this,
							'topic_allowed_checkbox'
						), 9 );
					} else if ( $type == 'reply' ) {
						add_action( $this->settings['reply_form_position'], array(
							$this,
							'reply_allowed_checkbox'
						), 9 );
					}
				}
			} else {
				if ( $_forced ) {
					$retval = false;
				}
			}
		}

		return $retval;
	}

	private function get_user_email( $user_id ) : string {
		$user = get_userdata( $user_id );

		return ! empty( $user->user_email ) ? $user->user_email : '';
	}
}
