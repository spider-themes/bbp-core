<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminColumns extends Feature {
	public $feature_name = 'admin-columns';
	public $settings = array(
		'forum_subscriptions' => true,
		'topic_attachments'   => true,
		'topic_private'       => true,
		'topic_favorites'     => true,
		'topic_subscriptions' => true,
		'reply_attachments'   => true,
		'reply_private'       => true,
		'user_content'        => true,
		'user_last_activity'  => true
	);

	private $is_calculated = false;
	private $data = array();

	public function __construct() {
		parent::__construct();

		add_action( 'pre_user_query', array( $this, 'pre_user_query' ) );

		add_filter( 'manage_users_columns', array( $this, 'users_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'users_custom_column' ), 10, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this, 'users_sortable_columns' ) );

		add_action( 'manage_forum_posts_columns', array( $this, 'admin_forum_columns' ), 990 );
		add_action( 'manage_forum_posts_custom_column', array( $this, 'admin_forum_columns_data' ), 990, 2 );

		add_action( 'manage_topic_posts_columns', array( $this, 'admin_topic_columns' ), 990 );
		add_action( 'manage_topic_posts_custom_column', array( $this, 'admin_topic_columns_data' ), 990, 2 );

		add_action( 'manage_reply_posts_columns', array( $this, 'admin_reply_columns' ), 990 );
		add_action( 'manage_reply_posts_custom_column', array( $this, 'admin_reply_columns_data' ), 990, 2 );
	}

	public static function instance() : AdminColumns {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new AdminColumns();
		}

		return $instance;
	}

	public function admin_forum_columns( $columns ) {
		if ( $this->settings['forum_subscriptions'] ) {
			$columns['gdbbx-forum-subscriptions'] = '<span class="vers dashicons dashicons-star-filled" title="' . esc_attr_x( "Subscribers", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Subscribers", "bbp-core" ) . '</span></span>';
		}

		return $columns;
	}

	public function admin_forum_columns_data( $column, $id ) {
		$this->forum_calculate_counts();

		if ( $column == 'gdbbx-forum-subscriptions' ) {
			if ( isset( $this->data[ $id ] ) ) {
				$value = $this->data[ $id ];

				echo '<a href="admin.php?page=gd-bbpress-toolbox-users&view=forum_subscriptions&filter-forum=' . $id . '" title="' . esc_attr( sprintf( _n( "%s User", "%s Users", $value, "bbp-core" ), $value ) ) . '">' . esc_html( $value ) . '</a>';
			} else {
				echo '0';
			}
		}
	}

	public function forum_calculate_counts() {
		global $wp_query;

		if ( $this->is_calculated || ! $wp_query ) {
			return;
		}

		$forums = wp_list_pluck( $wp_query->posts, 'ID' );

		$sql = "SELECT ID, COUNT(*) AS counter FROM " . gdbbx_db()->wpdb()->postmeta . " m INNER JOIN " . gdbbx_db()->wpdb()->posts . " p ON p.ID = m.post_id WHERE m.meta_key = '_bbp_subscription' AND p.post_type = '" . bbp_get_forum_post_type() . "' AND p.ID IN (" . join( ", ", $forums ) . ") GROUP BY p.ID";
		$raw = gdbbx_db()->get_results( $sql );

		foreach ( $raw as $row ) {
			$this->data[ $row->ID ] = absint( $row->counter );
		}

		$this->is_calculated = true;
	}

	public function admin_topic_columns( $columns ) {
		if ( $this->settings['topic_private'] && Plugin::instance()->is_enabled( 'private-topics' ) ) {
			$columns['gdbbx-is-private'] = '<span class="vers dashicons dashicons-lock" title="' . esc_attr_x( "Private", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Private", "bbp-core" ) . '</span></span>';
		}

		if ( $this->settings['topic_attachments'] ) {
			$columns['gdbbx-attachments-count'] = '<span class="vers dashicons dashicons-admin-media" title="' . esc_attr_x( "Attachments", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Attachments", "bbp-core" ) . '</span></span>';
		}

		if ( $this->settings['topic_subscriptions'] ) {
			$columns['gdbbx-topic-subscriptions'] = '<span class="vers dashicons dashicons-star-filled" title="' . esc_attr_x( "Subscribers", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Subscribers", "bbp-core" ) . '</span></span>';
		}

		if ( $this->settings['topic_favorites'] ) {
			$columns['gdbbx-topic-favorites'] = '<span class="vers dashicons dashicons-heart" title="' . esc_attr_x( "Favorited", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Favorited", "bbp-core" ) . '</span></span>';
		}

		return $columns;
	}

	public function admin_topic_columns_data( $column, $id ) {
		$this->topic_calculate_counts();

		if ( $column == 'gdbbx-is-private' ) {
			if ( gdbbx_is_topic_private( $id ) ) {
				echo '&#x2713;';
			}
		} else if ( $column == 'gdbbx-topic-subscriptions' ) {
			if ( isset( $this->data[ $id ]['subscription'] ) ) {
				$value = $this->data[ $id ]['subscription'];

				echo '<a href="admin.php?page=gd-bbpress-toolbox-users&view=topic_subscriptions&filter-topic=' . $id . '" title="' . esc_attr( sprintf( _n( "%s User", "%s Users", $value, "bbp-core" ), $value ) ) . '">' . esc_html( $value ) . '</a>';
			} else {
				echo '0';
			}
		} else if ( $column == 'gdbbx-topic-favorites' ) {
			if ( isset( $this->data[ $id ]['favorite'] ) ) {
				$value = $this->data[ $id ]['favorite'];

				echo '<a href="admin.php?page=gd-bbpress-toolbox-users&view=topic_favorites&filter-topic=' . $id . '" title="' . esc_attr( sprintf( _n( "%s User", "%s Users", $value, "bbp-core" ), $value ) ) . '">' . esc_html( $value ) . '</a>';
			} else {
				echo '0';
			}
		} else if ( $column == 'gdbbx-attachments-count' ) {
			$attachments = gdbbx_get_post_attachments( $id );
			$count       = count( $attachments );

			if ( $count == 0 ) {
				echo '0';
			} else {
				echo '<a href="' . admin_url( 'admin.php?page=gd-bbpress-toolbox-attachments&bbp_topic_id=' . $id ) . '">' . esc_html( $count ) . '</a>';
			}
		}
	}

	public function topic_calculate_counts() {
		global $wp_query;

		if ( $this->is_calculated || ! $wp_query ) {
			return;
		}

		$topics = wp_list_pluck( $wp_query->posts, 'ID' );

		gdbbx_cache()->private_run_bulk_posts( $topics );

		$sql = "SELECT ID, SUBSTR(m.meta_key, 6) AS type, COUNT(*) AS counter FROM " . gdbbx_db()->wpdb()->postmeta . " m INNER JOIN " . gdbbx_db()->wpdb()->posts . " p ON p.ID = m.post_id WHERE m.meta_key IN ('_bbp_subscription', '_bbp_favorite') AND p.post_type = '" . bbp_get_topic_post_type() . "' AND p.ID IN (" . join( ", ", $topics ) . ") GROUP BY p.ID, m.meta_key";
		$raw = gdbbx_db()->get_results( $sql );

		foreach ( $raw as $row ) {
			if ( ! isset( $this->data[ $row->ID ] ) ) {
				$this->data[ $row->ID ] = array();
			}

			$this->data[ $row->ID ][ $row->type ] = absint( $row->counter );
		}

		$this->is_calculated = true;
	}

	public function admin_reply_columns( $columns ) {
		if ( $this->settings['reply_private'] && Plugin::instance()->is_enabled( 'private-replies' ) ) {
			$columns['gdbbx-is-private'] = '<span class="vers dashicons dashicons-lock" title="' . esc_attr_x( "Private", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Private", "bbp-core" ) . '</span></span>';
		}

		if ( $this->settings['reply_attachments'] ) {
			$columns['gdbbx-attachments-count'] = '<span class="vers dashicons dashicons-admin-media" title="' . esc_attr_x( "Attachments", "Admin Column", "bbp-core" ) . '"><span class="screen-reader-text">' . esc_html__( "Attachments", "bbp-core" ) . '</span></span>';
		}

		return $columns;
	}

	public function admin_reply_columns_data( $column, $id ) {
		$this->reply_calculate_counts();

		if ( $column == 'gdbbx-is-private' ) {
			if ( gdbbx_is_reply_private( $id ) ) {
				echo '&#x2713;';
			}
		} else if ( $column == 'gdbbx-attachments-count' ) {
			$attachments = gdbbx_get_post_attachments( $id );
			$count       = count( $attachments );

			if ( $count == 0 ) {
				echo '0';
			} else {
				echo '<a href="' . admin_url( 'admin.php?page=gd-bbpress-toolbox-attachments&bbp_reply_id=' . $id ) . '">' . esc_html( $count ) . '</a>';
			}
		}
	}

	public function reply_calculate_counts() {
		global $wp_query;

		if ( $this->is_calculated || ! $wp_query ) {
			return;
		}

		$replies = wp_list_pluck( $wp_query->posts, 'ID' );

		gdbbx_cache()->private_run_bulk_posts( $replies );

		$this->is_calculated = true;
	}

	public function pre_user_query( $query ) {
		if ( ! isset( $query->query_vars['toolbox'] ) ) {
			if ( $query->query_vars['orderby'] == 'usr.replies' ) {
				$query->query_from .= " LEFT JOIN (SELECT post_author, count(*) as replies FROM " . gdbbx_db()->wpdb()->posts . " WHERE post_type = 'reply' AND post_status IN ('publish', 'pending', 'closed') GROUP BY post_author) usr ON usr.post_author = " . gdbbx_db()->wpdb()->users . ".ID";
			} else if ( $query->query_vars['orderby'] == 'usr.topics' ) {
				$query->query_from .= " LEFT JOIN (SELECT post_author, count(*) as topics FROM " . gdbbx_db()->wpdb()->posts . " WHERE post_type = 'topic' AND post_status IN ('publish', 'pending', 'closed') GROUP BY post_author) usr ON usr.post_author = " . gdbbx_db()->wpdb()->users . ".ID";
			}

			if ( $query->query_vars['orderby'] == 'usr.replies' || $query->query_vars['orderby'] == 'usr.topics' ) {
				$query->query_orderby = 'ORDER BY ' . $query->query_vars['orderby'] . ' ' . $query->query_vars['order'];
			}
		}
	}

	public function users_columns( $columns ) {
		if ( $this->settings['user_content'] ) {
			$columns['bbp-topics']  = __( "Topics", "bbp-core" );
			$columns['bbp-replies'] = __( "Replies", "bbp-core" );
		}

		if ( $this->settings['user_last_activity'] ) {
			$columns['bbp-activity'] = __( "Last Forum Activity", "bbp-core" );
		}

		return $columns;
	}

	public function users_custom_column( $value, $column, $user_id ) {
		global $wp_list_table;

		$this->users_calculate_counts();

		if ( $column == 'bbp-topics' ) {
			$value = isset( $wp_list_table->items[ $user_id ]->data->forums['topic'] ) ? $wp_list_table->items[ $user_id ]->data->forums['topic'] : 0;

			if ( $value > 0 ) {
				$value = '<a href="' . admin_url( "edit.php?post_type=topic&amp;author=$user_id" ) . '">' . esc_html( $value ) . '</a>';
			}
		} else if ( $column == 'bbp-replies' ) {
			$value = isset( $wp_list_table->items[ $user_id ]->data->forums['reply'] ) ? $wp_list_table->items[ $user_id ]->data->forums['reply'] : 0;

			if ( $value > 0 ) {
				$value = '<a href="' . admin_url( "edit.php?post_type=reply&amp;author=$user_id" ) . '">' . esc_html( $value ) . '</a>';
			}
		} else if ( $column == 'bbp-activity' ) {
			$value = $this->get_last_activity( $user_id );
		}

		return $value;
	}

	public function users_sortable_columns( $columns ) {
		$columns['bbp-topics']  = 'usr.topics';
		$columns['bbp-replies'] = 'usr.replies';

		return $columns;
	}

	private function users_calculate_counts() {
		global $wp_list_table;

		if ( $this->is_calculated || ! $wp_list_table ) {
			return;
		}

		$users = array_keys( $wp_list_table->items );
		$sql   = "SELECT post_type, post_author, count(*) AS counter FROM " . gdbbx_db()->wpdb()->posts . " WHERE post_type IN ('reply', 'topic') AND post_status IN ('pending', 'publish', 'closed') AND post_author IN (" . join( ', ', $users ) . ") GROUP BY post_type, post_author";
		$raw   = gdbbx_db()->get_results( $sql );

		foreach ( $raw as $row ) {
			$wp_list_table->items[ $row->post_author ]->data->forums[ $row->post_type ] = $row->counter;
		}

		$this->is_calculated = true;
	}

	private function get_last_activity( $user_id ) {
		$timestamp = gdbbx_plugin()->get_user_last_activity( $user_id ) + d4p_gmt_offset() * 3600;

		if ( $timestamp == 0 ) {
			return '—';
		} else {
			return date( 'Y-m-d', $timestamp ) . '<br/>@ ' . date( 'H:i:s', $timestamp );
		}
	}
}