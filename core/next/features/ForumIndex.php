<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use WP_User;
use WP_User_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ForumIndex extends Feature {
	public $feature_name = 'forum-index';
	public $settings = array(
		'welcome_front'                          => false,
		'welcome_filter'                         => 'before',
		'welcome_front_roles'                    => null,
		'welcome_show_links'                     => true,
		'statistics_front'                       => false,
		'statistics_filter'                      => 'after',
		'statistics_front_roles'                 => null,
		'statistics_front_visitor'               => false,
		'statistics_show_online'                 => true,
		'statistics_show_online_overview'        => true,
		'statistics_show_online_top'             => true,
		'statistics_show_users'                  => 0,
		'statistics_show_users_colors'           => false,
		'statistics_show_users_avatars'          => false,
		'statistics_show_users_links'            => false,
		'statistics_show_users_limit'            => 32,
		'statistics_show_legend'                 => false,
		'statistics_show_statistics'             => true,
		'statistics_show_statistics_totals'      => true,
		'statistics_show_statistics_newest_user' => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->settings['welcome_front'] && $this->allowed( 'welcome_front', 'forum-index-welcome' ) ) {
			if ( $this->settings['welcome_filter'] == 'before' ) {
				add_action( 'bbp_template_before_forums_index', array( $this, 'welcome_index' ) );
			} else {
				add_action( 'bbp_template_after_forums_index', array( $this, 'welcome_index' ) );
			}
		}

		if ( $this->settings['statistics_front'] && $this->allowed( 'statistics_front', 'forum-index-statistics' ) ) {
			if ( $this->settings['statistics_filter'] == 'before' ) {
				add_action( 'bbp_template_before_forums_index', array( $this, 'forum_index' ) );
			} else {
				add_action( 'bbp_template_after_forums_index', array( $this, 'forum_index' ) );
			}
		}
	}

	public static function instance() : ForumIndex {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new ForumIndex();
		}

		return $instance;
	}

	public function get_welcome( $name ) {
		return $this->settings[ 'welcome_show_' . $name ];
	}

	public function get_statistics( $name ) {
		return $this->settings[ 'statistics_show_' . $name ];
	}

	public function welcome_index() {
		Enqueue::instance()->core();

		include( bbpc_get_template_part( 'bbpc-forums-welcome.php' ) );
	}

	public function forum_index() {
		Enqueue::instance()->core();

		include( bbpc_get_template_part( 'bbpc-forums-statistics.php' ) );
	}

	private function last_activity() {
		$value = defined( 'BBPC_LAST_ACTIVTY' ) ? BBPC_LAST_ACTIVTY : bbpc_plugin()->get_user_last_activity( bbp_get_current_user_id() );

		return absint( $value );
	}

	public function user_visit() : array {
		$activity  = $this->last_activity();
		$timestamp = $activity + 3600 * d4p_gmt_offset();

		$out = array(
			'timestamp' => $activity,
			'topics'    => bbpc_db()->get_topics_count_since( $activity ),
			'replies'   => bbpc_db()->get_replies_count_since( $activity ),
			'time'      => date_i18n( get_option( 'time_format' ), $timestamp ),
			'date'      => date_i18n( get_option( 'date_format' ), $timestamp ),
		);

		$out['posts'] = $out['topics'] + $out['replies'];

		if ( $out['posts'] == 0 ) {
			add_filter( 'bbpc_welcome_back_user_links_last_visit', '__return_false' );
		}

		return $out;
	}

	public function user_links() : array {
		$links = array();

		$_view_new_posts = CustomViews::instance()->settings['newposts_slug'];
		if ( bbp_get_view_id( $_view_new_posts ) !== false && apply_filters( 'bbpc_welcome_back_user_links_last_visit', true ) ) {
			$links[] = '<a href="' . bbp_get_view_url( $_view_new_posts ) . '">' . __( "New posts since last visit", "bbp-core" ) . '</a>';
		}

		$_view_latest_topics = CustomViews::instance()->settings['latesttopics_slug'];
		if ( bbp_get_view_id( $_view_latest_topics ) !== false ) {
			$links[] = '<a href="' . bbp_get_view_url( $_view_latest_topics ) . '">' . __( "All latest topics", "bbp-core" ) . '</a>';
		}

		$links[] = '<a href="' . bbp_get_user_profile_url( bbp_get_current_user_id() ) . '">' . __( "My user profile page", "bbp-core" ) . '</a>';

		return $links;
	}

	public function user_roles_legend() : string {
		$_roles = bbpc_get_user_roles();

		$items = array();

		foreach ( $_roles as $role => $name ) {
			$items[] = '<span class="bbpc-front-user bbpc-user-color-' . $role . '">' . $name . '</span>';
		}

		return join( ', ', $items );
	}

	public function users_list( $_show = null, $_limit = null, $user_args = array() ) {
		$_show  = is_null( $_show ) ? $this->get_statistics( 'users' ) : absint( $_show );
		$_limit = is_null( $_limit ) ? $this->get_statistics( 'users_limit' ) : absint( $_limit );

		$items = array();

		if ( $_show == 0 ) {
			$online = bbpc_module_online()->online();

			$_users = array();
			foreach ( $online['roles'] as $ids ) {
				$_users = array_merge( $_users, $ids );
			}

			foreach ( $_users as $id ) {
				if ( count( $items ) == $_limit ) {
					break;
				}

				$_user = get_user_by( 'id', absint( $id ) );

				if ( $_user !== false ) {
					$items[] = $_user;
				}
			}

			$label = __( "Users currently online", "bbp-core" );
		} else {
			$_users = array_keys( bbpc_db()->get_users_active_in_past( $_show * MINUTE_IN_SECONDS, $_limit ) );

			foreach ( $_users as $id ) {
				$_user = get_user_by( 'id', absint( $id ) );

				if ( $_user !== false ) {
					$items[] = $_user;
				}
			}

			$standard = array(
				30    => __( "30 minutes", "bbp-core" ),
				60    => __( "60 minutes", "bbp-core" ),
				120   => __( "2 hours", "bbp-core" ),
				720   => __( "12 hours", "bbp-core" ),
				1440  => __( "24 hours", "bbp-core" ),
				4320  => __( "3 days", "bbp-core" ),
				10080 => __( "7 days", "bbp-core" )
			);

			if ( isset( $standard[ $_show ] ) ) {
				$period = $standard[ $_show ];
			} else {
				$period = sprintf( _n( "%s minute", "%s minutes", $_show, "bbp-core" ), $_show );
			}

			$label = sprintf( __( "Users active in the past %s", "bbp-core" ), $period );
		}

		$render = array();

		foreach ( $items as $user ) {
			if ( $user instanceof WP_User ) {
				$render[] = $this->_user_format_for_display( $user, $user_args );
			}
		}

		if ( empty( $render ) ) {
			$render[] = '&minus;';
		}

		return '<label>' . $label . ':</label> ' . join( ', ', $render );
	}

	public function newest_user() {
		$users = new WP_User_Query( array(
			'orderby' => 'registered',
			'order'   => 'DESC',
			'number'  => 1
		) );

		$user = $users->get_results();

		return $this->_user_format_for_display( $user[0] );
	}

	private function _user_format_for_display( WP_User $user, $args = array() ) {
		$defaults = array(
			'color'   => $this->get_statistics( 'users_colors' ),
			'avatar'  => $this->get_statistics( 'users_avatars' ),
			'link'    => $this->get_statistics( 'users_links' ),
			'wrapped' => true
		);

		$args = wp_parse_args( $args, $defaults );

		$_roles = $args['color'] ? $this->_user_roles( $user ) : array();

		$_class = 'bbpc-front-user';

		if ( ! empty( $_roles ) ) {
			$_class .= ' bbpc-user-color-' . $_roles[0];
		}

		$item = '<span class="' . $_class . '">';

		if ( $args['wrapped'] && $args['avatar'] ) {
			if ( $args['link'] ) {
				$item .= '<a class="bbp-author-avatar" href="' . esc_url( bbp_get_user_profile_url( $user->ID ) ) . '">';
			}

			$item .= get_avatar( $user, '14' );
			$item .= $user->display_name;

			if ( $args['link'] ) {
				$item .= '</a>';
			}
		} else {
			if ( $args['avatar'] ) {
				if ( $args['link'] ) {
					$item .= '<a class="bbp-author-avatar" href="' . esc_url( bbp_get_user_profile_url( $user->ID ) ) . '">';
				}

				$item .= get_avatar( $user, '14' );

				if ( $args['link'] ) {
					$item .= '</a>';
				}
			}

			if ( $args['link'] ) {
				$item .= bbp_get_user_profile_link( $user->ID );
			} else {
				$item .= $user->display_name;
			}
		}

		$item .= '</span>';

		return $item;
	}

	private function _user_roles( WP_User $user ) {
		$_roles = array_keys( bbpc_get_user_roles() );
		$_inter = array_intersect( $user->roles, $_roles );

		return array_values( $_inter );
	}
}
