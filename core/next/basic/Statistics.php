<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

class Statistics {
	public function __construct() {

	}

	public static function instance() : Statistics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Statistics();
		}

		return $instance;
	}

	public function forums_stats_elements() : array {
		return array(
			'user_count'                      => __( "Members", "bbp-core" ),
			'forum_count'                     => __( "Forums", "bbp-core" ),
			'forum_count_public'              => __( "Public Forums", "bbp-core" ),
			'forum_count_private'             => __( "Private Forums", "bbp-core" ),
			'forum_count_hidden'              => __( "Hidden Forums", "bbp-core" ),
			'forum_count_total'               => __( "Total Forums", "bbp-core" ),
			'topic_count'                     => __( "Topics", "bbp-core" ),
			'topic_count_open'                => __( "Open Topics", "bbp-core" ),
			'topic_count_hidden'              => __( "Hidden Topics", "bbp-core" ),
			'topic_count_closed'              => __( "Closed Topics", "bbp-core" ),
			'reply_count'                     => __( "Replies", "bbp-core" ),
			'reply_count_hidden'              => __( "Hidden Replies", "bbp-core" ),
			'post_count'                      => __( "Posts", "bbp-core" ),
			'topic_tag_count'                 => __( "Topic Tags", "bbp-core" ),
			'empty_topic_tag_count'           => __( "Empty Topic Tags", "bbp-core" ),
			'topics_with_favorites'           => __( "Topics with Favorites", "bbp-core" ),
			'topics_favorites'                => __( "Topics Favorites Totals", "bbp-core" ),
			'topics_with_subscriptions'       => __( "Topics with Subscriptions", "bbp-core" ),
			'topics_subscriptions'            => __( "Topics Subscriptions Totals", "bbp-core" ),
			'forums_with_subscriptions'       => __( "Forums with Subscriptions", "bbp-core" ),
			'forums_subscriptions'            => __( "Forums Subscriptions Totals", "bbp-core" ),
			'users_with_topic_favorites'      => __( "Users with Topics Favorites", "bbp-core" ),
			'users_with_topics_subscriptions' => __( "Users with Topics Subscriptions", "bbp-core" ),
			'users_with_forums_subscriptions' => __( "Users with Forums Subscriptions", "bbp-core" ),
			'canned_replies_count'            => __( "Canned Replies", "bbp-core" ),
			'attachments_unique'              => __( "Unique Attachments", "bbp-core" ),
			'attachments_count'               => __( "Attachments", "bbp-core" ),
			'attachments_topic_count'         => __( "Attachments in Topics", "bbp-core" ),
			'attachments_reply_count'         => __( "Attachments in Replies", "bbp-core" )
		);
	}

	public function forums_stats() : array {
		$the_key    = gdbbx_plugin()->get_transient_key( 'statistics_forums_stats' );
		$expiration = apply_filters( 'gdbbx_statistics_cache_expiration', 7200 );

		$statistics = get_transient( $the_key );

		if ( $statistics === false || ! is_array( $statistics ) ) {
			$statistics = $this->_calculate_forums_stats();

			$expire = apply_filters( 'gdbbx_forum_statistics_expiration', $expiration );

			set_transient( $the_key, $statistics, $expire );
		}

		$statistics['forum_count'] = $statistics['forum_count_public'];

		if ( is_user_logged_in() ) {
			if ( current_user_can( 'read_private_forums' ) ) {
				$statistics['forum_count'] += $statistics['forum_count_private'];
			}

			if ( current_user_can( 'read_hidden_forums' ) ) {
				$statistics['forum_count'] += $statistics['forum_count_hidden'];
			}
		}

		return (array) apply_filters( 'gdbbx_get_statistics', $statistics );
	}

	public function user_counts() : array {
		$the_key = gdbbx_plugin()->get_transient_key( 'statistics_user_counts' );

		$results = get_transient( $the_key );

		if ( $results === false || ! is_array( $results ) ) {
			$results = $this->_calculate_user_counts();

			$expire = apply_filters( 'gdbbx_user_counts_expiration', 7200 );

			set_transient( $the_key, $results, $expire );
		}

		return $results;
	}

	private function _calculate_forums_stats() : array {
		$users_data = $this->user_counts();

		$user_count       = $users_data['user_count'];
		$user_roles_count = $users_data['user_roles_count'];

		$forum_count_public  = wp_count_posts( bbp_get_forum_post_type() )->publish;
		$forum_count_private = wp_count_posts( bbp_get_forum_post_type() )->private;
		$forum_count_hidden  = wp_count_posts( bbp_get_forum_post_type() )->hidden;
		$forum_count_total   = $forum_count_public + $forum_count_private + $forum_count_hidden;

		$topic_count_hidden    = 0;
		$reply_count_hidden    = 0;
		$empty_topic_tag_count = 0;

		$private = bbp_get_private_status_id();
		$spam    = bbp_get_spam_status_id();
		$trash   = bbp_get_trash_status_id();
		$closed  = bbp_get_closed_status_id();

		$all_topics = wp_count_posts( bbp_get_topic_post_type() );

		$topic_count        = $all_topics->publish + $all_topics->{$closed};
		$topic_count_open   = $all_topics->publish;
		$topic_count_closed = $all_topics->{$closed};

		if ( current_user_can( 'read_private_topics' ) || current_user_can( 'edit_others_topics' ) || current_user_can( 'view_trash' ) ) {
			$topics['private'] = current_user_can( 'read_private_topics' ) ? (int) $all_topics->{$private} : 0;
			$topics['spammed'] = current_user_can( 'edit_others_topics' ) ? (int) $all_topics->{$spam} : 0;
			$topics['trashed'] = current_user_can( 'view_trash' ) ? (int) $all_topics->{$trash} : 0;

			$topic_count_hidden = $topics['private'] + $topics['spammed'] + $topics['trashed'];
		}

		$all_replies = wp_count_posts( bbp_get_reply_post_type() );

		$reply_count = $all_replies->publish;

		if ( current_user_can( 'read_private_replies' ) || current_user_can( 'edit_others_replies' ) || current_user_can( 'view_trash' ) ) {
			$replies['private'] = current_user_can( 'read_private_replies' ) ? (int) $all_replies->{$private} : 0;
			$replies['spammed'] = current_user_can( 'edit_others_replies' ) ? (int) $all_replies->{$spam} : 0;
			$replies['trashed'] = current_user_can( 'view_trash' ) ? (int) $all_replies->{$trash} : 0;

			$reply_count_hidden = $replies['private'] + $replies['spammed'] + $replies['trashed'];
		}

		$topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id(), array( 'hide_empty' => true ) );

		if ( current_user_can( 'edit_topic_tags' ) ) {
			$empty_topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id() ) - $topic_tag_count;
		}

		$post_count = $topic_count + $reply_count;

		$canned_replies_count = 0;
		if ( Plugin::instance()->is_enabled( 'canned-replies' ) ) {
			$all_canned_replies   = wp_count_posts( 'bbx_canned_reply' );
			$canned_replies_count = $all_canned_replies->publish;
		}

		$_attachments            = gdbbx_db()->count_attachments_per_type();
		$attachments_topic_count = $_attachments[ bbp_get_topic_post_type() ];
		$attachments_reply_count = $_attachments[ bbp_get_reply_post_type() ];
		$attachments_count       = $attachments_reply_count + $attachments_topic_count;
		$attachments_unique      = gdbbx_db()->count_unique_attachments();

		$raw = array_map( 'absint', compact(
			'user_count',
			'forum_count_public',
			'forum_count_private',
			'forum_count_hidden',
			'forum_count_total',
			'topic_count',
			'topic_count_open',
			'topic_count_hidden',
			'topic_count_closed',
			'reply_count',
			'reply_count_hidden',
			'post_count',
			'topic_tag_count',
			'empty_topic_tag_count',
			'canned_replies_count',
			'attachments_unique',
			'attachments_count',
			'attachments_topic_count',
			'attachments_reply_count'
		) );

		$raw = $raw + gdbbx_db()->engagements_extra_stats();
		$raw = array_map( 'number_format_i18n', $raw );

		$raw['user_roles_count'] = array_map( 'number_format_i18n', $user_roles_count );

		return $raw;
	}

	private function _calculate_user_counts() : array {
		$count = count_users();
		$roles = bbp_get_dynamic_roles();

		$results = array(
			'user_count'       => 0,
			'user_roles_count' => array()
		);

		foreach ( array_keys( $roles ) as $role ) {
			if ( isset( $count['avail_roles'][ $role ] ) ) {
				$c = absint( $count['avail_roles'][ $role ] );

				$results['user_count']                += $c;
				$results['user_roles_count'][ $role ] = $c;
			}
		}

		return $results;
	}
}
