<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;
use SpiderDevs\Plugin\BBPC\Basic\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UsersStats extends Feature {
	public $feature_name = 'users-stats';
	public $settings = array(
		'super_admin'            => true,
		'visitor'                => false,
		'roles'                  => null,
		'show_online_status'     => true,
		'show_registration_date' => false,
		'show_topics'            => true,
		'show_replies'           => true,
		'show_thanks_given'      => false,
		'show_thanks_received'   => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->allowed() ) {
			add_action( 'bbp_theme_after_topic_author_details', array( $this, 'user_stats' ) );
			add_action( 'bbp_theme_after_reply_author_details', array( $this, 'user_stats' ) );

			add_action( 'bbpc_template_before_replies_loop', array( $this, 'before_replies_loop' ), 10, 2 );
		}
	}

	public static function instance() : UsersStats {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new UsersStats();
		}

		return $instance;
	}

	public function before_replies_loop( $posts, $users ) {
		bbpc_cache()->userstats_run_bulk_counts( $users );
		bbpc_cache()->userstats_run_bulk_online( $users );
	}

	public function user_stats() {
		if ( bbp_is_reply_anonymous() ) {
			return;
		}

		Enqueue::instance()->core();

		$list   = array();
		$author = bbp_get_reply_author_id();

		if ( $author > 0 ) {
			$user = User::instance( $author );

			if ( $this->settings['show_online_status'] && function_exists( 'bbpc_module_online' ) ) {
				$list['online_status'] = $user->render_item_online_status();
			}

			if ( $this->settings['show_registration_date'] ) {
				$item = $user->render_item_registration_date();

				if ( ! empty( $item ) ) {
					$list['registered'] = $item;
				}
			}

			if ( $this->settings['show_topics'] ) {
				$list['topics'] = $user->render_item_topics_count();
			}

			if ( $this->settings['show_replies'] ) {
				$list['replies'] = $user->render_item_replies_count();
			}

			if ( Plugin::instance()->is_enabled( 'thanks' ) ) {
				if ( $this->settings['show_thanks_given'] ) {
					$list['thanks_given'] = $user->render_item_thanks_given();
				}

				if ( $this->settings['show_thanks_received'] ) {
					$list['thanks_received'] = $user->render_item_thanks_received();
				}
			}
		}

		$list = apply_filters( 'bbpc_user_stats_items', $list, $author );

		echo '<div class="bbpc-user-stats">' . join( '', $list ) . '</div>';
	}
}
