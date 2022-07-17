<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Tasks\Cleanup;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Profiles extends Feature {
	public $feature_name = 'profiles';
	public $settings = array(
		'hide_from_visitors' => false,
		'thanks_display'     => false,
		'thanks_private'     => false,
		'extras_display'     => false,
		'extras_actions'     => true,
		'extras_private'     => true,
	);

	private $counts = array();

	public function __construct() {
		parent::__construct();

		if ( ! is_user_logged_in() && $this->settings['hide_from_visitors'] ) {
			add_filter( 'bbp_get_template_part', array( $this, 'replace_profile' ), 10, 3 );
		}

		if ( $this->settings['thanks_display'] ) {
			add_action( 'bbp_template_after_user_profile', array( $this, 'profile_thanks' ), 15 );
		}

		if ( $this->settings['extras_display'] ) {
			add_action( 'bbp_template_after_user_profile', array( $this, 'profile_extras' ), 20 );

			if ( isset( $_GET['bbx-remove'] ) && isset( $_GET['_wpnonce'] ) ) {
				add_action( 'bbp_template_redirect', array( $this, 'remove_action' ) );
			}
		}
	}

	public static function instance() : Profiles {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Profiles();
		}

		return $instance;
	}

	public function remove_action() {
		$remove = d4p_sanitize_key_expanded( $_GET['bbx-remove'] );
		$nonce  = d4p_sanitize_key_expanded( $_GET['_wpnonce'] );

		if ( in_array( $remove, array( 'topic_favorites', 'topic_subscriptions', 'forum_subscriptions' ) ) ) {
			$user_id   = get_current_user_id();
			$nonce_key = 'bbx-remove-' . $remove . '-' . $user_id;

			if ( wp_verify_nonce( $nonce, $nonce_key ) ) {
				switch ( $remove ) {
					case 'forum_subscriptions':
						Cleanup::instance()->clear_user_forum_subscriptions( $user_id );
						break;
					case 'topic_subscriptions':
						Cleanup::instance()->clear_user_topic_subscriptions( $user_id );
						break;
					case 'topic_favorites':
						Cleanup::instance()->clear_user_favorites( $user_id );
						break;
				}
			}
		}

		wp_redirect( bbp_get_user_profile_url( get_current_user_id() ) );
		exit;
	}

	public function profile_thanks() {
		$keep_private = $this->settings['thanks_private'];

		if ( ! $keep_private || $this->is_profile_owner() ) {
			include( bbpc_get_template_part( 'bbpc-user-thanks.php' ) );
		}
	}

	public function profile_extras() {
		$keep_private = $this->settings['extras_private'];

		if ( ! $keep_private || $this->is_profile_owner() ) {
			$this->counts = bbpc_db()->get_user_subscription_favorites_counts( $this->current_profile_user()->ID );

			include( bbpc_get_template_part( 'bbpc-user-extra.php' ) );
		}
	}

	public function replace_profile( $templates, $slug, $name ) {
		if ( $slug == 'content' && $name == 'single-user' ) {
			$templates = array( 'bbpc-user-profile-protected.php' );
		}

		return $templates;
	}

	public function message_profile_protected() {
		return apply_filters( 'bbpc_user_profile_protected_message', sprintf( __( "You must be <a href='%s'>logged in</a> to access user profile pages.", "bbp-core" ), wp_login_url( get_permalink() ) ) );
	}

	/** @return \WP_User */
	public function current_profile_user() {
		return bbpress()->displayed_user;
	}

	public function is_profile_owner() {
		return is_user_logged_in() && $this->current_profile_user()->ID == get_current_user_id();
	}

	public function get_count_value( $name ) {
		if ( isset( $this->counts[ $name ] ) ) {
			return $this->counts[ $name ];
		}

		return 0;
	}

	public function get_action_link( $name ) {
		if ( $this->is_profile_owner() && $this->get_count_value( $name ) > 0 ) {
			$label = $name == 'topic_favorites' ? __( "Remove all favorites", "bbp-core" ) : __( "Remove all subscriptions", "bbp-core" );

			$url = bbp_get_user_profile_url( $this->current_profile_user()->ID );
			$url = add_query_arg( 'bbx-remove', $name, $url );
			$url = add_query_arg( '_wpnonce', wp_create_nonce( 'bbx-remove-' . $name . '-' . $this->current_profile_user()->ID ), $url );

			return apply_filters( 'bbpc_user_profile_link_remove_' . $name, ' (<a href="' . $url . '">' . $label . '</a>)', $url, $label );
		}

		return '';
	}
}