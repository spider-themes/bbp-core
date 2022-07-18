<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminWidgets extends Feature {
	public $feature_name = 'admin-widgets';
	public $settings = array(
		'activity' => true,
		'online'   => false
	);

	public function __construct() {
		parent::__construct();

		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widgets' ), 300 );
	}

	public static function instance() : AdminWidgets {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new AdminWidgets();
		}

		return $instance;
	}

	public function dashboard_widgets() {
		if ( $this->settings['activity'] ) {
			wp_add_dashboard_widget( 'gdbbx-dashboard-activity', __( "Latest Forum Topics and Replies", "bbp-core" ), array(
				$this,
				'widget_latest_activity'
			) );
		}

		if ( $this->settings['online'] ) {
			wp_add_dashboard_widget( 'gdbbx-dashboard-users', __( "Online Users in the Forums", "bbp-core" ), array(
				$this,
				'widget_online_users'
			) );
		}
	}

	public function widget_latest_activity() {
		include( GDBBX_PATH . 'forms/meta/dashboard.activity.php' );
	}

	public function widget_online_users() {
		include( GDBBX_PATH . 'forms/meta/dashboard.online.php' );
	}
}
