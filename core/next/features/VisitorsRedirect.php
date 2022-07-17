<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VisitorsRedirect extends Feature {
	public $feature_name = 'visitors-redirect';
	public $settings = array(
		'for_visitors'       => 'no',
		'for_visitors_url'   => '',
		'hidden_forums'      => 'no',
		'hidden_forums_url'  => '',
		'private_forums'     => 'no',
		'private_forums_url' => '',
		'blocked_users'      => 'no',
		'blocked_users_url'  => '',
		'noaccess_topic'     => 'no',
		'noaccess_topic_url' => ''
	);

	public function __construct() {
		parent::__construct();

		if ( ! is_admin() ) {
			$_skip = false;

			if ( ! is_user_logged_in() && $this->settings['for_visitors'] ) {
				add_action( 'bbp_template_redirect', array( $this, 'redirect_all' ), 0 );

				$_skip = true;
			}

			if ( ! $_skip && $this->settings['blocked_users'] ) {
				remove_action( 'bbp_template_redirect', 'bbp_forum_enforce_blocked', 1 );
				add_action( 'bbp_template_redirect', array( $this, 'blocked_users' ), 1 );

				$_skip = true;
			}

			if ( ! $_skip && $this->settings['hidden_forums'] || $this->settings['noaccess_topic'] ) {
				remove_action( 'bbp_template_redirect', 'bbp_forum_enforce_hidden', 1 );
				add_action( 'bbp_template_redirect', array( $this, 'hidden_forums' ), 1 );

				$_skip = true;
			}

			if ( ! $_skip && $this->settings['private_forums'] || $this->settings['noaccess_topic'] ) {
				remove_action( 'bbp_template_redirect', 'bbp_forum_enforce_private', 1 );
				add_action( 'bbp_template_redirect', array( $this, 'private_forums' ), 1 );
			}
		}
	}

	public static function instance() : VisitorsRedirect {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new VisitorsRedirect();
		}

		return $instance;
	}

	public function redirect_to_key( $key ) {
		$url = $this->settings[ $key ];

		if ( empty( $url ) ) {
			$url = get_site_url();
		}

		$url = apply_filters( 'bbpc_visitors_redirect_' . $key, $url );

		wp_redirect( $url );
		exit;
	}

	public function redirect_to_login() {
		$url = d4p_current_url();

		wp_redirect( wp_login_url( $url ) );
		exit;
	}

	public function redirect_all() {
		if ( apply_filters( 'bbpc_visitors_redirect_bbpress', bbpc_is_bbpress() ) ) {
			if ( $this->get( 'for_visitors' ) === 'custom' ) {
				$this->redirect_to_key( 'for_visitors_url' );
			} else if ( $this->get( 'for_visitors' ) === 'login' ) {
				$this->redirect_to_login();
			}
		}
	}

	public function blocked_users() {
		if ( ! is_user_logged_in() || bbp_is_user_keymaster() ) {
			return;
		}

		$redirect = is_bbpress() && ! current_user_can( 'spectate' );

		if ( apply_filters( 'bbpc_visitors_redirect_blocked_users', $redirect, bbp_get_current_user_id() ) ) {
			$this->redirect_to_key( 'blocked_users_url' );
		}
	}

	public function hidden_forums() {
		if ( bbp_is_user_keymaster() || current_user_can( 'read_hidden_forums' ) ) {
			return;
		}

		$request = $this->get_post_from_wp_query();

		$forum_id = $request['forum'];
		$redirect = ! empty( $forum_id ) && bbp_is_forum_hidden( $forum_id ) && ! current_user_can( 'read_hidden_forums' );

		if ( ( $request['type'] == 'topic' || $request['type'] == 'reply' ) && $this->settings['noaccess_topic'] ) {
			$topic_id = $request['topic'];

			if ( apply_filters( 'bbpc_visitors_redirect_hidden_forums_topic', $redirect, $topic_id, $forum_id ) ) {
				if ( $this->get( 'noaccess_topic' ) === 'custom' ) {
					$this->redirect_to_key( 'noaccess_topic_url' );
				} else if ( $this->get( 'noaccess_topic' ) === 'login' ) {
					$this->redirect_to_login();
				}
			}
		} else {
			if ( apply_filters( 'bbpc_visitors_redirect_hidden_forums', $redirect, $forum_id ) ) {
				if ( $this->get( 'hidden_forums' ) === 'custom' ) {
					$this->redirect_to_key( 'hidden_forums_url' );
				} else if ( $this->get( 'hidden_forums' ) === 'login' ) {
					$this->redirect_to_login();
				}
			}
		}
	}

	public function private_forums() {
		if ( bbp_is_user_keymaster() || current_user_can( 'read_private_forums' ) ) {
			return;
		}

		$request = $this->get_post_from_wp_query();

		$forum_id = $request['forum'];
		$redirect = ! empty( $forum_id ) && bbp_is_forum_private( $forum_id ) && ! current_user_can( 'read_private_forums' );

		if ( ( $request['type'] == 'topic' || $request['type'] == 'reply' ) && $this->settings['noaccess_topic'] ) {
			$topic_id = $request['topic'];

			if ( apply_filters( 'bbpc_visitors_redirect_private_forums_topic', $redirect, $topic_id, $forum_id ) ) {
				if ( $this->get( 'noaccess_topic' ) === 'custom' ) {
					$this->redirect_to_key( 'noaccess_topic_url' );
				} else if ( $this->get( 'noaccess_topic' ) === 'login' ) {
					$this->redirect_to_login();
				}
			}
		} else {
			if ( apply_filters( 'bbpc_visitors_redirect_private_forums', $redirect, $forum_id ) ) {
				if ( $this->get( 'private_forums' ) === 'custom' ) {
					$this->redirect_to_key( 'private_forums_url' );
				} else if ( $this->get( 'private_forums' ) === 'login' ) {
					$this->redirect_to_login();
				}
			}
		}
	}

	private function get_post_from_wp_query() : array {
		global $wp_query;

		$return = array(
			'id'    => 0,
			'type'  => '',
			'forum' => 0,
			'topic' => 0
		);

		if ( $wp_query->is_404() && isset( $wp_query->query['name'] ) ) {
			$post = get_page_by_path( $wp_query->query['name'], OBJECT, bbp_get_forum_post_type() );

			if ( $post ) {
				$return['id']   = $post->ID;
				$return['type'] = 'forum';
			} else {
				$post = $this->get_post_by_slug( $wp_query->query['name'], bbp_get_topic_post_type() );

				if ( $post ) {
					$return['id']   = $post->ID;
					$return['type'] = 'topic';
				} else {
					$post = $this->get_post_by_slug( $wp_query->query['name'], bbp_get_topic_post_type() );

					if ( $post ) {
						$return['id']   = $post->ID;
						$return['type'] = 'reply';
					}
				}
			}
		} else {
			switch ( $wp_query->get( 'post_type' ) ) {
				case bbp_get_forum_post_type() :
					$return['id']   = $wp_query->post->ID;
					$return['type'] = 'forum';
					break;
				case bbp_get_topic_post_type() :
					$return['id']   = $wp_query->post->ID;
					$return['type'] = 'topic';
					break;
				case bbp_get_reply_post_type() :
					$return['id']   = $wp_query->post->ID;
					$return['type'] = 'reply';
					break;
			}
		}

		if ( $return['type'] == 'forum' ) {
			$return['forum'] = $return['id'];
		} else if ( $return['type'] == 'topic' ) {
			$return['topic'] = $return['id'];
			$return['forum'] = bbp_get_topic_forum_id( $return['id'] );
		} else if ( $return['type'] == 'reply' ) {
			$return['forum'] = bbp_get_reply_topic_id( $return['id'] );
			$return['forum'] = bbp_get_reply_forum_id( $return['id'] );
		}

		return $return;
	}

	private function get_post_by_slug( $slug, $type ) {
		$args = array(
			'name'        => $slug,
			'post_type'   => $type,
			'post_status' => bbp_get_public_topic_statuses(),
			'numberposts' => 1
		);

		add_filter( 'bbp_include_all_forums', '__return_true' );

		$results = new WP_Query( $args );

		remove_filter( 'bbp_include_all_forums', '__return_true' );

		return count( $results->posts ) === 1 ? $results->posts[0] : null;
	}
}
