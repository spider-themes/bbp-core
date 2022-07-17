<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rewriter extends Feature {
	public $feature_name = 'rewriter';
	public $settings = array(
		'topic_hierarchy'                => false,
		'reply_hierarchy'                => false,
		'forum_remove_attachments_rules' => false,
		'topic_remove_attachments_rules' => false,
		'reply_remove_attachments_rules' => false,
		'forum_remove_comments_rules'    => false,
		'topic_remove_comments_rules'    => false,
		'reply_remove_comments_rules'    => false,
		'forum_remove_feeds_rules'       => false,
		'topic_remove_feeds_rules'       => false,
		'reply_remove_feeds_rules'       => false
	);

	public function __construct() {
		parent::__construct();

		add_action( 'registered_post_type', array( $this, 'add_permastructs' ), 10, 2 );
		add_action( 'generate_rewrite_rules', array( $this, 'rewrite_rules' ), 20 );
		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 2 );
	}

	public static function instance() : Rewriter {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Rewriter();
		}

		return $instance;
	}

	public function rewrite_rules() {
		global $wp_rewrite;

		$_index_edit = 0;
		$_index_main = 0;

		$_attc       = 'index.php?attachment=';
		$_feed       = '&feed=';
		$_reply      = bbp_get_reply_post_type();
		$_topic      = bbp_get_topic_post_type();
		$_forum_slug = bbp_get_forum_slug();
		$_topic_slug = bbp_get_topic_slug();
		$_reply_slug = bbp_get_reply_slug();
		$_edit       = bbp_get_edit_slug();
		$_edit_id    = bbp_get_edit_rewrite_id();

		$_edit_rule  = '/([^/]+)/' . $_edit . '/?$';
		$_forum_edit = $_forum_slug . $_edit_rule;

		$idx        = 0;
		$edit_rules = array();
		foreach ( array_keys( $wp_rewrite->rules ) as $rule ) {
			if ( $rule == $_forum_edit ) {
				$_index_edit = $idx;
				break;
			}

			$idx ++;
		}

		if ( $this->settings['reply_hierarchy'] ) {
			$_old_edit = $_reply_slug . $_edit_rule;
			unset( $wp_rewrite->rules[ $_old_edit ] );

			$_new_edit                = bbp_get_forum_slug() . "/.+?/" . get_option( '_bbp_topic_slug', 'topic' ) . "/.+?/" . get_option( '_bbp_reply_slug', 'reply' ) . $_edit_rule;
			$edit_rules[ $_new_edit ] = 'index.php?' . bbp_get_reply_post_type() . '=$matches[1]&' . $_edit_id . '=1';
		}

		if ( $this->settings['topic_hierarchy'] ) {
			$_old_edit = $_topic_slug . $_edit_rule;
			unset( $wp_rewrite->rules[ $_old_edit ] );

			$_new_edit                = bbp_get_forum_slug() . "/.+?/" . get_option( '_bbp_topic_slug', 'topic' ) . $_edit_rule;
			$edit_rules[ $_new_edit ] = 'index.php?' . bbp_get_topic_post_type() . '=$matches[1]&' . $_edit_id . '=1';
		}

		if ( ! empty( $edit_rules ) ) {
			$wp_rewrite->rules = array_merge( array_slice( $wp_rewrite->rules, 0, $_index_edit ), $edit_rules, array_slice( $wp_rewrite->rules, $_index_edit ) );
		}

		foreach ( $wp_rewrite->rules as $rule => $resolve ) {
			$_remove = false;

			if ( strpos( $rule, $_forum_slug ) === 0 ) {
				if ( $this->settings['forum_remove_attachments_rules'] && strpos( $rule, '/attachment/' ) != false && strpos( $resolve, $_attc ) === 0 ) {
					$_remove = true;
				} else if ( $this->settings['forum_remove_comments_rules'] && ( strpos( $rule, '/comment-page-' ) !== false || strpos( $rule, '/trackback/' ) !== false ) ) {
					$_remove = true;
				} else if ( $this->settings['forum_remove_feeds_rules'] && strpos( $rule, 'feed' ) != false && strpos( $resolve, $_feed ) !== false ) {
					$_remove = true;
				} else if ( $this->settings['topic_hierarchy'] && strpos( $rule, '/' . $_topic . '/' ) !== false && strpos( $rule, '/' . $_reply . '/' ) === false ) {
					if ( $this->settings['topic_remove_attachments_rules'] && strpos( $resolve, $_attc ) === 0 ) {
						$_remove = true;
					} else if ( $this->settings['topic_remove_comments_rules'] && ( strpos( $rule, '/comment-page-' ) !== false || strpos( $rule, '/trackback/' ) !== false ) ) {
						$_remove = true;
					} else if ( $this->settings['topic_remove_feeds_rules'] && strpos( $rule, 'feed' ) != false && strpos( $resolve, $_feed ) !== false ) {
						$_remove = true;
					}
				} else if ( $this->settings['reply_hierarchy'] && strpos( $rule, '/' . $_reply . '/' ) !== false ) {
					if ( $this->settings['reply_remove_attachments_rules'] && strpos( $resolve, $_attc ) === 0 ) {
						$_remove = true;
					} else if ( $this->settings['reply_remove_comments_rules'] && ( strpos( $rule, '/comment-page-' ) !== false || strpos( $rule, '/trackback/' ) !== false ) ) {
						$_remove = true;
					} else if ( $this->settings['reply_remove_feeds_rules'] && strpos( $rule, 'feed' ) != false && strpos( $resolve, $_feed ) !== false ) {
						$_remove = true;
					}
				}
			} else if ( strpos( $rule, $_topic_slug ) === 0 ) {
				if ( $this->settings['topic_remove_attachments_rules'] && strpos( $resolve, $_attc ) === 0 ) {
					$_remove = true;
				} else if ( $this->settings['topic_remove_comments_rules'] && ( strpos( $rule, '/comment-page-' ) !== false || strpos( $rule, '/trackback/' ) !== false ) ) {
					$_remove = true;
				} else if ( $this->settings['topic_remove_feeds_rules'] && strpos( $rule, 'feed' ) != false && strpos( $resolve, $_feed ) !== false ) {
					$_remove = true;
				}
			} else if ( strpos( $rule, $_reply_slug ) === 0 ) {
				if ( $this->settings['reply_remove_attachments_rules'] && strpos( $resolve, $_attc ) === 0 ) {
					$_remove = true;
				} else if ( $this->settings['reply_remove_comments_rules'] && ( strpos( $rule, '/comment-page-' ) !== false || strpos( $rule, '/trackback/' ) !== false ) ) {
					$_remove = true;
				} else if ( $this->settings['reply_remove_feeds_rules'] && strpos( $rule, 'feed' ) != false && strpos( $resolve, $_feed ) !== false ) {
					$_remove = true;
				}
			}

			if ( $_remove ) {
				unset( $wp_rewrite->rules[ $rule ] );
			}
		}

		$main_rules = array();

		foreach ( $wp_rewrite->rules as $rule => $resolve ) {
			if ( $this->settings['reply_hierarchy'] ) {
				if ( strpos( $rule, $_forum_slug ) === 0 && strpos( $rule, '/' . $_reply . '/' ) !== false && strpos( $rule, '/' . $_edit . '/' ) === false ) {
					$main_rules[ $rule ] = $resolve;
					unset( $wp_rewrite->rules[ $rule ] );
				}
			} else {
				if ( strpos( $rule, $_reply_slug ) === 0 && strpos( $rule, '/' . $_edit . '/' ) === false ) {
					$main_rules[ $rule ] = $resolve;
					unset( $wp_rewrite->rules[ $rule ] );
				}
			}
		}

		foreach ( $wp_rewrite->rules as $rule => $resolve ) {
			if ( $this->settings['topic_hierarchy'] ) {
				if ( strpos( $rule, $_forum_slug ) === 0 && strpos( $rule, '/' . $_topic . '/' ) !== false && strpos( $rule, '/' . $_edit . '/' ) === false ) {
					$main_rules[ $rule ] = $resolve;
					unset( $wp_rewrite->rules[ $rule ] );
				}
			} else {
				if ( strpos( $rule, $_topic_slug ) === 0 && strpos( $rule, '/' . $_edit . '/' ) === false ) {
					$main_rules[ $rule ] = $resolve;
					unset( $wp_rewrite->rules[ $rule ] );
				}
			}
		}

		$idx = 0;
		foreach ( array_keys( $wp_rewrite->rules ) as $rule ) {
			if ( $idx > $_index_edit + 4 ) {
				if ( strpos( $rule, $_forum_slug ) === 0 ) {
					$_index_main = $idx;
					break;
				}
			}

			$idx ++;
		}

		if ( ! empty( $main_rules ) ) {
			$wp_rewrite->rules = array_merge( array_slice( $wp_rewrite->rules, 0, $_index_main ), $main_rules, array_slice( $wp_rewrite->rules, $_index_main ) );
		}
	}

	public function add_permastructs( $post_type, $args ) {
		switch ( $post_type ) {
			case bbp_get_topic_post_type():
				$permastruct = $args->rewrite;

				if ( $this->settings['topic_hierarchy'] ) {
					add_permastruct( bbp_get_topic_post_type(), bbp_get_forum_slug() . "/.+?/" . get_option( '_bbp_topic_slug', 'topic' ) . "/%" . bbp_get_topic_post_type() . "%", $permastruct );
				} else {
					add_permastruct( bbp_get_topic_post_type(), bbp_get_topic_slug() . '/%' . bbp_get_topic_post_type() . '%', $permastruct );
				}
				break;
			case bbp_get_reply_post_type():
				$permastruct = $args->rewrite;

				if ( $this->settings['reply_hierarchy'] ) {
					add_permastruct( bbp_get_reply_post_type(), bbp_get_forum_slug() . "/.+?/" . get_option( '_bbp_topic_slug', 'topic' ) . "/.+?/" . get_option( '_bbp_reply_slug', 'reply' ) . "/%" . bbp_get_reply_post_type() . "%", $permastruct );
				} else {
					add_permastruct( bbp_get_reply_post_type(), bbp_get_reply_slug() . '/%' . bbp_get_reply_post_type() . '%', $permastruct );
				}
				break;
		}
	}

	public function post_type_link( $post_link, $post ) {
		if ( empty( $post ) || $post->post_status === 'auto-draft' ) {
			return $post_link;
		}

		switch ( $post->post_type ) {
			case bbp_get_topic_post_type():
				if ( $this->settings['topic_hierarchy'] ) {
					$post_link = bbp_get_forum_slug() . '/' . $this->get_topic_parent_forums_slug( $post ) . '/' . get_option( '_bbp_topic_slug', 'topic' ) . '/' . $this->get_topic_name_slug( $post );
					$post_link = home_url( user_trailingslashit( $post_link ) );
				}
				break;
			case bbp_get_reply_post_type():
				if ( $this->settings['reply_hierarchy'] ) {
					$topic = get_post( bbp_get_reply_topic_id( $post->ID ) );

					$post_link = bbp_get_forum_slug() . '/' . $this->get_topic_parent_forums_slug( $topic ) . '/' . get_option( '_bbp_topic_slug', 'topic' ) . '/' . $this->get_topic_name_slug( $topic ) . '/' . get_option( '_bbp_reply_slug', 'reply' ) . '/' . $post->ID;
					$post_link = home_url( user_trailingslashit( $post_link ) );
				}
				break;
		}

		return $post_link;
	}

	private function get_topic_parent_forums_slug( $topic ) {
		$forum_id = bbp_get_topic_forum_id( $topic->ID );

		$forums = [];

		if ( $forum_id === 0 ) {
			return '/' . apply_filters( 'bbpc_topic_permalink_no_forum_slug', 'no-forum' ) . '/';
		}

		$forum      = get_post( $forum_id );
		$has_parent = $forum instanceof WP_Post;

		while ( $has_parent ) {
			$forums[] = $forum->post_name;

			if ( $forum->post_parent === 0 ) {
				$has_parent = false;
			} else {
				$forum = get_post( $forum->post_parent );

				if ( ! ( $forum instanceof WP_Post ) ) {
					$has_parent = false;
				}
			}
		}

		return join( '/', array_reverse( $forums ) );
	}

	private function get_topic_name_slug( $post ) {
		return empty( $post->post_name ) ? sanitize_title_with_dashes( $post->post_title ) : $post->post_name;
	}
}
