<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomViews extends Feature {
	public $feature_name = 'custom-views';
	public $settings = array(
		'enable_feed'          => false,
		'with_pending'         => false,
		'pending_active'       => true,
		'pending_slug'         => 'pending',
		'pending_title'        => 'Pending Topics',
		'spam_active'          => false,
		'spam_slug'            => 'spam',
		'spam_title'           => 'Spammed Topics',
		'trash_active'         => false,
		'trash_slug'           => 'trash',
		'trash_title'          => 'Trashed Topics',
		'newposts_active'      => true,
		'newposts_slug'        => 'new-posts-last-visits',
		'newposts_title'       => 'New posts since last visit',
		'topicsfresh_active'   => true,
		'topicsfresh_slug'     => 'topics-freshness',
		'topicsfresh_title'    => 'Topics Freshness',
		'newposts24h_active'   => true,
		'newposts24h_slug'     => 'new-posts-last-day',
		'newposts24h_title'    => 'New posts: Last day',
		'newposts3dy_active'   => true,
		'newposts3dy_slug'     => 'new-posts-last-three-days',
		'newposts3dy_title'    => 'New posts: Last three days',
		'newposts7dy_active'   => true,
		'newposts7dy_slug'     => 'new-posts-last-week',
		'newposts7dy_title'    => 'New posts: Last week',
		'newposts1mn_active'   => true,
		'newposts1mn_slug'     => 'new-posts-last-month',
		'newposts1mn_title'    => 'New posts: Last month',
		'mostreplies_active'   => true,
		'mostreplies_slug'     => 'most-replies',
		'mostreplies_title'    => 'Topics with most replies',
		'latesttopics_active'  => true,
		'latesttopics_slug'    => 'latest-topics',
		'latesttopics_title'   => 'Latest topics',
		'mostthanked_active'   => false,
		'mostthanked_slug'     => 'most-thanked-topics',
		'mostthanked_title'    => 'Most thanked topics',
		'attachments_active'   => false,
		'attachments_slug'     => 'topics-with-attachments',
		'attachments_title'    => 'Topics with attachments',
		'myfuture_active'      => false,
		'myfuture_slug'        => 'my-scheduled-topics',
		'myfuture_title'       => 'My Scheduled Topics',
		'myattachments_active' => false,
		'myattachments_slug'   => 'my-topics-with-attachments',
		'myattachments_title'  => 'My topics with attachments',
		'myactive_active'      => false,
		'myactive_slug'        => 'my-active-topics',
		'myactive_title'       => 'My active topics',
		'mytopics_active'      => false,
		'mytopics_slug'        => 'my-topics',
		'mytopics_title'       => 'All my topics',
		'myreply_active'       => false,
		'myreply_slug'         => 'with-my-reply',
		'myreply_title'        => 'Topics with my reply',
		'mynoreplies_active'   => false,
		'mynoreplies_slug'     => 'my-topics-no-replies',
		'mynoreplies_title'    => 'My topics with no replies',
		'mymostreplies_active' => true,
		'mymostreplies_slug'   => 'my-topics-most-replies',
		'mymostreplies_title'  => 'My topics with most replies',
		'mymostthanked_active' => false,
		'mymostthanked_slug'   => 'my-most-thanked-topics',
		'mymostthanked_title'  => 'My most thanked topics',
		'myfavorite_active'    => true,
		'myfavorite_slug'      => 'my-favorite-topics',
		'myfavorite_title'     => 'My favorite topics',
		'mysubscribed_active'  => true,
		'mysubscribed_slug'    => 'my-subscribed-topics',
		'mysubscribed_title'   => 'My subscribed topics'
	);

	public $skip = array( 'enable_feed', 'with_pending' );
	public $personal = array(
		'newposts',
		'myfuture',
		'myattachments',
		'myactive',
		'mytopics',
		'myreply',
		'mynoreplies',
		'mymostreplies',
		'mymostthanked',
		'myfavorite',
		'mysubscribed'
	);
	public $moderation = array( 'pending', 'spam', 'trash' );

	public $views = array();
	public $mapped = array();

	public $user_id = 0;
	public $moderator = false;

	public $last_activity = '';
	public $user_filtered = 0;
	public $current_user = 0;
	public $new_posts_ids = array();

	public function __construct() {
		parent::__construct();

		add_action( 'gdbbx_core', array( $this, 'ready' ) );
	}

	public static function instance() : CustomViews {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new CustomViews();
		}

		return $instance;
	}

	public function ready() {
		$_raw = array();
		foreach ( $this->settings as $key => $value ) {
			if ( in_array( $key, $this->skip ) ) {
				continue;
			}

			$parts                          = explode( '_', $key, 2 );
			$_raw[ $parts[0] ][ $parts[1] ] = $value;
		}

		$logged    = is_user_logged_in();
		$moderator = is_user_logged_in() && current_user_can( 'moderate' );

		if ( $logged ) {
			$this->user_id = bbp_get_current_user_id();
		}

		if ( $moderator ) {
			$this->moderator = true;
		}

		foreach ( $_raw as $view => $data ) {
			if ( $data['active'] ) {
				unset( $data['active'] );

				if ( in_array( $view, $this->personal ) ) {
					$allowed = $logged;
				} else if ( in_array( $view, $this->moderation ) ) {
					$allowed = $moderator;
				} else {
					$allowed = true;
				}

				if ( $allowed ) {
					$this->views[ $view ]          = $data;
					$this->mapped[ $data['slug'] ] = $view;
				}
			}
		}

		if ( ! empty( $this->views ) ) {
			$this->register_views();

			add_filter( 'bbp_get_view_query_args', array( $this, 'modify_view_args' ), 10, 2 );
		}
	}

	public function register_views() {
		foreach ( $this->views as $view => $data ) {
			$args  = array();
			$slug  = $data['slug'];
			$title = __( $data['title'], "bbp-core" );
			$feed  = $this->_feed_for_view( $view );

			$statuses = array( bbp_get_public_status_id(), bbp_get_closed_status_id() );

			if ( $this->settings['with_pending'] && $this->moderator ) {
				$statuses[] = bbp_get_pending_status_id();
			}

			switch ( $view ) {
				case 'newposts24h':
				case 'newposts3dy':
				case 'newposts7dy':
				case 'newposts1mn':
					$args = array( 'post_status' => $statuses );
					break;
				case 'mymostreplies':
				case 'mostreplies':
					$args = array(
						'meta_key' => '_bbp_reply_count',
						'orderby'  => 'meta_value_num'
					);
					break;
				case 'latesttopics':
					$args = array( 'orderby' => 'post_date', 'post_status' => $statuses );
					break;
				case 'topicsfresh':
					$args = array(
						'orderby'     => 'meta_value',
						'meta_key'    => '_bbp_last_active_time',
						'post_status' => array( bbp_get_public_status_id(), bbp_get_closed_status_id() )
					);
					break;
				case 'spam':
					$args = array(
						'orderby'     => 'post_date',
						'post_status' => array( bbp_get_spam_status_id() )
					);
					break;
				case 'pending':
					$args = array(
						'orderby'     => 'post_date',
						'post_status' => array( bbp_get_pending_status_id() )
					);
					break;
				case 'trash':
					$args = array(
						'orderby'     => 'post_date',
						'post_status' => array( bbp_get_trash_status_id() )
					);
					break;
				case 'myfuture':
					$args = array(
						'orderby'     => 'post_date',
						'post_status' => array( 'future' )
					);
					break;
				case 'myactive':
					$args = array(
						'orderby'     => 'meta_value',
						'meta_key'    => '_bbp_last_active_time',
						'post_status' => bbp_get_public_status_id()
					);
					break;
				case 'myreply':
				case 'mytopics':
					$args = array(
						'orderby'  => 'meta_value',
						'meta_key' => '_bbp_last_active_time'
					);
					break;
				case 'mynoreplies':
					$args = array(
						'meta_key'     => '_bbp_reply_count',
						'meta_value'   => 1,
						'meta_compare' => '<',
						'orderby'      => 'post_date'
					);
					break;
				case 'myfavorite':
					$ids = gdbbx_db()->get_user_favorites_topic_ids( $this->user_id );

					if ( empty( $ids ) ) {
						$args = $this->_empty_view_args();
					} else {
						$args = array( 'post__in' => $ids );
					}
					break;
				case 'mysubscribed':
					$ids = gdbbx_db()->get_user_subscribed_topic_ids( $this->user_id );

					if ( empty( $ids ) ) {
						$args = $this->_empty_view_args();
					} else {
						$args = array( 'post__in' => $ids );
					}
					break;
			}

			$args["gdbbx-custom-view"] = $slug;

			bbp_register_view( $slug, $title, $args, $feed );
		}
	}

	public function modify_view_args( $query, $view ) {
		if ( ! isset( $this->mapped[ $view ] ) ) {
			return $query;
		}

		$map = $this->mapped[ $view ];

		switch ( $map ) {
			case 'mostthanked':
			case 'mymostthanked':
				add_filter( 'posts_clauses', array( $this, 'posts_thanks' ), 10, 2 );
				break;
			case 'newposts24h':
			case 'newposts3dy':
			case 'newposts7dy':
			case 'newposts1mn':
			case 'mymostreplies':
			case 'myactive':
			case 'mynoreplies':
			case 'mytopics':
			case 'myreply':
				add_filter( 'posts_where', array( $this, 'new_posts_where' ), 10, 2 );
				break;
		}

		switch ( $map ) {
			case 'newposts24h':
				$this->last_activity = mktime( date( 'H' ) - 1, 0, 0, date( 'n' ), date( 'j' ) - 1, date( 'Y' ) );
				$this->_get_new_posts();
				break;
			case 'newposts3dy':
				$this->last_activity = mktime( date( 'H' ) - 1, 0, 0, date( 'n' ), date( 'j' ) - 3, date( 'Y' ) );
				$this->_get_new_posts();
				break;
			case 'newposts7dy':
				$this->last_activity = mktime( date( 'H' ) - 1, 0, 0, date( 'n' ), date( 'j' ) - 7, date( 'Y' ) );
				$this->_get_new_posts();
				break;
			case 'newposts1mn':
				$this->last_activity = mktime( date( 'H' ) - 1, 0, 0, date( 'n' ) - 1, date( 'j' ), date( 'Y' ) );
				$this->_get_new_posts();
				break;
		}

		switch ( $map ) {
			case 'mymostthanked':
			case 'mymostreplies':
			case 'myactive':
			case 'mynoreplies':
			case 'myfuture':
			case 'mytopics':
				$this->current_user = bbp_get_current_user_id();
				break;
			case 'myreply':
				$this->user_filtered = bbp_get_current_user_id();
				$this->_get_new_posts();
				break;
			case 'newposts':
				$this->last_activity = defined( 'GDBBX_LAST_ACTIVTY' ) ? GDBBX_LAST_ACTIVTY : gdbbx_plugin()->get_user_last_activity( $this->user_id );

				if ( $this->last_activity != 0 ) {
					add_filter( 'posts_where', array( $this, 'new_posts_where' ), 10, 2 );

					$this->_get_new_posts();
				}
		}

		return $query;
	}

	public function posts_thanks( $query, $obj ) {
		if ( bbp_is_single_view() ) {
			if ( isset( $obj->query['gdbbx-custom-view'] ) ) {
				$query['join']    .= " INNER JOIN " . gdbbx_db()->actions . " gdbbx_ac ON gdbbx_ac.post_id = " . gdbbx_db()->wpdb()->posts . ".ID AND gdbbx_ac.action = 'thanks'";
				$query['orderby'] = "CAST(count(gdbbx_ac.post_id) AS UNSIGNED) DESC";

				if ( $this->current_user > 0 ) {
					$query['where'] .= " AND " . gdbbx_db()->wpdb()->posts . ".post_author = " . $this->current_user;
				}
			}
		}

		return $query;
	}

	public function new_posts_where( $where, $obj ) {
		if ( bbp_is_single_view() ) {
			if ( isset( $obj->query['gdbbx-custom-view'] ) ) {
				if ( ! empty( $this->new_posts_ids ) ) {
					$where .= " AND " . gdbbx_db()->wpdb()->posts . ".ID in (" . join( ', ', $this->new_posts_ids ) . ") ";
				} else if ( $this->current_user > 0 ) {
					$where .= " AND " . gdbbx_db()->wpdb()->posts . ".post_author = " . $this->current_user;
				} else {
					$where .= " AND 1 = 2 ";
				}
			}
		}

		return $where;
	}

	private function _get_new_posts() {
		if ( $this->last_activity != '' ) {
			$this->new_posts_ids = gdbbx_get_new_topics( $this->last_activity );
		} else if ( $this->user_filtered > 0 ) {
			$this->new_posts_ids = gdbbx_db()->get_topics_with_user_reply( $this->user_filtered );
		}
	}

	private function _empty_view_args() {
		return array( 'meta_key' => '_bbp_fake_key_', 'meta_value' => '_fake_' );
	}

	private function _feed_for_view( $view ) {
		$feed = $this->settings['enable_feed'];

		if ( $feed && ( in_array( $view, $this->personal ) || in_array( $view, $this->moderation ) ) ) {
			$feed = false;
		}

		return $feed;
	}
}
