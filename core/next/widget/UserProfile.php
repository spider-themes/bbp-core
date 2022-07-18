<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserProfile extends Widget {
	public $widget_base = 'd4p_bbw_userprofile';
	public $widget_class = 'gdbbx-widget gdbbx-widget-userprofile';

	public $defaults = array(
		'title'              => 'My Profile',
		'template'           => 'gdbbx-widget-userprofile.php',
		'show_login'         => true,
		'show_profile'       => true,
		'show_stats'         => true,
		'show_role'          => true,
		'show_topics'        => true,
		'show_replies'       => true,
		'show_favorites'     => true,
		'show_subscriptions' => true,
		'show_engagements'   => true,
		'show_edit'          => true,
		'show_logout'        => true,
		'avatar_size'        => 96
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "User Profile", "bbp-core" );
		$this->widget_description = __( "Logged in user profile and links.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function is_visible( $instance ) : bool {
		return ( is_user_logged_in() && bbp_user_has_profile() ) || $instance['show_login'];
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'user-profile-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['show_profile']       = isset( $new_instance['show_profile'] );
		$instance['show_stats']         = isset( $new_instance['show_stats'] );
		$instance['show_role']          = isset( $new_instance['show_role'] );
		$instance['show_topics']        = isset( $new_instance['show_topics'] );
		$instance['show_replies']       = isset( $new_instance['show_replies'] );
		$instance['show_favorites']     = isset( $new_instance['show_favorites'] );
		$instance['show_subscriptions'] = isset( $new_instance['show_subscriptions'] );
		$instance['show_engagements']   = isset( $new_instance['show_engagements'] );
		$instance['show_edit']          = isset( $new_instance['show_edit'] );
		$instance['show_logout']        = isset( $new_instance['show_logout'] );
		$instance['show_login']         = isset( $new_instance['show_login'] );
		$instance['avatar_size']        = absint( $new_instance['avatar_size'] );

		return $instance;
	}

	private function profile_data( $instance ) {
		$profile_data = array();

		if ( $instance['show_role'] ) {
			$profile_data['role'] = __( "Role", "bbp-core" ) . ': <strong>' . bbp_get_user_display_role( bbp_get_current_user_id() ) . '</strong>';
		}

		if ( $instance['show_stats'] ) {
			$profile_data['topics']  = __( "Topics Started", "bbp-core" ) . ': <strong>' . bbp_get_user_topic_count_raw( bbp_get_current_user_id() ) . '</strong>';
			$profile_data['replies'] = __( "Replies Created", "bbp-core" ) . ': <strong>' . bbp_get_user_reply_count_raw( bbp_get_current_user_id() ) . '</strong>';
		}

		apply_filters( 'gdbbx-widget-userprofile-profile-data', $profile_data, $instance, $this );

		return $profile_data;
	}

	private function profile_links( $instance ) {
		$profile_data = array();

		if ( $instance['show_topics'] ) {
			$profile_data['topics'] = '<a href="' . esc_url( bbp_get_user_topics_created_url( bbp_get_current_user_id() ) ) . '">' . __( "Topics Started", "bbp-core" ) . '</a>';
		}

		if ( $instance['show_replies'] ) {
			$profile_data['replies'] = '<a href="' . esc_url( bbp_get_user_replies_created_url( bbp_get_current_user_id() ) ) . '">' . __( "Replies Created", "bbp-core" ) . '</a>';
		}

		if ( $instance['show_favorites'] ) {
			$profile_data['favorites'] = '<a href="' . esc_url( bbp_get_favorites_permalink( bbp_get_current_user_id() ) ) . '">' . __( "Favorites", "bbp-core" ) . '</a>';
		}

		if ( $instance['show_subscriptions'] ) {
			$profile_data['subscriptions'] = '<a href="' . esc_url( bbp_get_subscriptions_permalink( bbp_get_current_user_id() ) ) . '">' . __( "Subscriptions", "bbp-core" ) . '</a>';
		}

		if ( $instance['show_engagements'] && function_exists( 'bbp_get_user_engagements_url' ) ) {
			$profile_data['engagements'] = '<a href="' . esc_url( bbp_get_user_engagements_url( bbp_get_current_user_id() ) ) . '">' . __( "Engagements", "bbp-core" ) . '</a>';
		}

		apply_filters( 'gdbbx-widget-userprofile-profile-links', $profile_data, $instance, $this );

		return $profile_data;
	}

	private function profile_login( $instance ) {
		$links = array(
			'login' => '<a href="' . esc_url( wp_login_url() ) . '">' . __( "Log In", "bbp-core" ) . '</a>'
		);

		if ( get_option( 'users_can_register' ) ) {
			$links['register'] = '<a href="' . esc_url( site_url( 'wp-login.php?action=register', 'login' ) ) . '">' . __( "Register", "bbp-core" ) . '</a>';
		}

		apply_filters( 'gdbbx-widget-userprofile-profile-login-links', $links, $instance, $this );

		return $links;
	}

	public function the_render( $instance, $results = false ) {
		$profile = $this->profile_data( $instance );
		$links   = $this->profile_links( $instance );
		$login   = $this->profile_login( $instance );

		$template = apply_filters( 'gdbbx-widget-userprofile-template', $instance['template'], $results, $this );

		include( gdbbx_get_template_part( $template ) );
	}
}
