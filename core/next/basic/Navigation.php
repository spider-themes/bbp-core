<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Navigation {
	function __construct() {
		add_action( 'admin_head-nav-menus.php', array( $this, 'register_metaboxes' ), 10, 1 );

		add_filter( 'wp_get_nav_menu_items', array( $this, 'extras_items_processing' ), 10, 3 );
	}

	public static function instance() : Navigation {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Navigation();
		}

		return $instance;
	}

	public function register_metaboxes( $object ) {
		add_meta_box( 'bbx-add-extras', __( "bbPress Specific", "bbp-core" ), array(
			$this,
			'admin_extras_metabox'
		), 'nav-menus', 'side', 'default' );
		add_meta_box( 'bbx-add-views', __( "bbPress Topic Views", "bbp-core" ), array(
			$this,
			'admin_views_metabox'
		), 'nav-menus', 'side', 'default' );
	}

	public function admin_extras_metabox() {
		include( GDBBX_PATH . 'forms/meta/navmenu.extras.php' );
	}

	public function admin_views_metabox() {
		include( GDBBX_PATH . 'forms/meta/navmenu.views.php' );
	}

	public function extras_items_processing( $items, $menu, $args ) {
		foreach ( $items as &$item ) {
			switch ( $item->object ) {
				case 'bbx-extra':
					switch ( $item->type ) {
						case 'bbx-home':
							$item->url = bbp_get_forums_url();
							break;
						case 'bbx-profile':
							$item->url = bbp_get_user_profile_url( bbp_get_current_user_id() );
							break;
						case 'bbx-topics':
							$item->url = bbp_get_user_topics_created_url( bbp_get_current_user_id() );
							break;
						case 'bbx-replies':
							$item->url = bbp_get_user_replies_created_url( bbp_get_current_user_id() );
							break;
						case 'bbx-favorites':
							$item->url = bbp_get_favorites_permalink( bbp_get_current_user_id() );
							break;
						case 'bbx-subscriptions':
							$item->url = bbp_get_subscriptions_permalink( bbp_get_current_user_id() );
							break;
						case 'bbx-edit':
							$item->url = bbp_get_user_profile_edit_url( bbp_get_current_user_id() );
							break;
						case 'bbx-login':
							$item->url = wp_login_url( d4p_current_url() );
							break;
						case 'bbx-logout':
							$item->url = wp_logout_url( d4p_current_url() );
							break;
						case 'bbx-register':
							$item->url = wp_registration_url();
							break;
					}

					if ( d4p_current_url() == $item->url ) {
						$item->classes[] = 'current-menu-item active';
						$item->current   = true;
					}
					break;
				case 'bbx-view':
					$item->url = bbp_get_view_url( $item->type );

					if ( get_query_var( 'bbp_view' ) == $item->type ) {
						$item->classes[] = 'current-menu-item  active';
						$item->current   = true;
					}
					break;
			}
		}

		return $items;
	}
}
