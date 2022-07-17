<?php

namespace SpiderDevs\Plugin\BBPC\Widget;

use SpiderDevs\Plugin\BBPC\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OnlineUsers extends Widget {
	public $widget_base = 'd4p_bbw_onlineusers';
	public $widget_class = 'bbpc-widget bbpc-widget-onlineusers';

	public $defaults = array(
		'title'             => 'Users Online',
		'template'          => 'bbpc-widget-onlineusers.php',
		'show_users'        => 'profile_link',
		'show_users_avatar' => true,
		'show_users_list'   => true,
		'show_max_users'    => true,
		'show_user_roles'   => true,
		'show_users_limit'  => 5
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'BBP Core: ' . __( "Online Users", "bbp-core" );
		$this->widget_description = __( "List of current online users.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'online-users-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['show_users']        = absint( $new_instance['show_users'] );
		$instance['show_users_limit']  = d4p_sanitize_basic( $new_instance['show_users_limit'] );
		$instance['show_users_list']   = isset( $new_instance['show_users_list'] );
		$instance['show_users_avatar'] = isset( $new_instance['show_users_avatar'] );
		$instance['show_user_roles']   = isset( $new_instance['show_user_roles'] );
		$instance['show_max_users']    = isset( $new_instance['show_max_users'] );

		return $instance;
	}

	public function the_render( $instance, $results = false ) {
		$template = apply_filters( 'bbpc-widget-onlineusers-template', $instance['template'], $results, $this );

		include( bbpc_get_template_part( $template ) );
	}
}
