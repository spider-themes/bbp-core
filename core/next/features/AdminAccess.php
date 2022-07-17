<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminAccess extends Feature {
	public $feature_name = 'admin-access';
	public $settings = array(
		'disable_roles' => array( 'bbp_keymaster', 'bbp_moderator' )
	);

	public function __construct() {
		parent::__construct();

		if ( ! $this->allowed( 'disable' ) ) {
			$this->disable_access();
		}
	}

	public static function instance() : AdminAccess {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new AdminAccess();
		}

		return $instance;
	}

	/** Based on the code by John James Jacoby from 'bbPress - No Admin' plugin:
	 * https://wordpress.org/extend/plugins/bbpress-no-admin/ */
	private function disable_access() {
		remove_action( 'admin_menu', 'bbp_admin_separator' );
		remove_action( 'custom_menu_order', 'bbp_admin_custom_menu_order' );
		remove_action( 'menu_order', 'bbp_admin_menu_order' );

		add_filter( 'bbp_register_forum_post_type', array( $this, 'disable_post_type' ) );
		add_filter( 'bbp_register_topic_post_type', array( $this, 'disable_post_type' ) );
		add_filter( 'bbp_register_reply_post_type', array( $this, 'disable_post_type' ) );
	}

	public function disable_post_type( $args ) {
		$args['show_in_nav_menus'] = false;
		$args['show_ui']           = false;
		$args['can_export']        = false;
		$args['capability_type']   = null;

		return $args;
	}
}
