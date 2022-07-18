<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ScheduleTopic extends Feature {
	public $feature_name = 'schedule-topic';
	public $settings = array(
		'allow_super_admin' => true,
		'allow_roles'       => null,
		'form_location'     => 'bbp_theme_after_topic_form_content'
	);

	public function __construct() {
		parent::__construct();

		add_action( 'gdbbx_template', array( $this, 'loader' ) );
	}

	public static function instance() : ScheduleTopic {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new ScheduleTopic();
		}

		return $instance;
	}

	public function loader() {
		if ( $this->allowed( 'allow' ) ) {
			add_action( 'bbp_new_topic_pre_insert', array( $this, 'topic_pre_insert' ) );

			add_filter( 'gdbbx_script_values', array( $this, 'script_values' ) );
			add_action( $this->settings['form_location'], array( $this, 'load_fieldset' ) );
		}
	}

	public function script_values( $values ) : array {
		$values['load'][] = 'scheduler';

		return $values;
	}

	public function load_fieldset() {
		$load = true;

		if ( bbp_is_topic_edit() && ! $this->is_topic_scheduled() ) {
			$load = false;
		}

		if ( $load ) {
			include( gdbbx_get_template_part( 'gdbbx-form-scheduler.php' ) );

			Enqueue::instance()->schedule();
		}
	}

	public function is_topic_scheduled( $topic_id = 0 ) {
		$topic_id     = bbp_get_topic_id( $topic_id );
		$topic_status = ( bbp_get_topic_status( $topic_id ) === 'future' );

		return (bool) apply_filters( 'gdbbx_is_topic_scheduled', $topic_status, $topic_id );
	}

	public function topic_pre_insert( $post ) {
		if ( isset( $_REQUEST['gdbbx_schedule_when'] ) && d4p_sanitize_slug( $_REQUEST['gdbbx_schedule_when'] ) === 'future' ) {
			$datetime = isset( $_REQUEST['gdbbx_schedule_datetime'] ) ? d4p_sanitize_basic( $_REQUEST['gdbbx_schedule_datetime'] ) : '';

			if ( ! empty( $datetime ) ) {
				$timestamp = strtotime( $datetime );

				if ( $timestamp > time() ) {
					$post['post_status'] = 'future';
					$post['post_date']   = date( 'Y-m-d H:i:s', $timestamp );
				}
			}
		}

		return $post;
	}
}
