<?php

namespace SpiderDevs\Plugin\BBPC\Manager;

use SpiderDevs\Plugin\BBPC\Features\LockTopics as LockTopicsFeature;
use SpiderDevs\Plugin\GDFAR\Manager\Process;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LockTopics {
	private $_defaults = array(
		'topic' => array(
			'edit' => array( 'bbpc-lock' ),
			'bulk' => array( 'bbpc-lock' )
		)
	);

	public function __construct() {
		add_action( 'gdfar_register_actions', array( $this, 'register' ) );

		foreach ( $this->_defaults as $scope => $actions ) {
			foreach ( $actions as $action => $names ) {
				foreach ( $names as $name ) {
					$key    = $scope . '-' . $action . '-' . $name;
					$method = $scope . '_' . $action . '_' . str_replace( '-', '_', $name );

					add_filter( 'bbpc-action-visible-' . $key, '__return_true' );
					add_filter( 'bbpc-action-display-' . $key, array( $this, 'display_' . $method ), 10, 2 );
					add_filter( 'bbpc-action-process-' . $key, array( $this, 'process_' . $method ), 10, 2 );
				}
			}
		}
	}

	public static function instance() : LockTopics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new LockTopics();
		}

		return $instance;
	}

	public function modded( $type, $id ) {
		if ( defined( 'GDFAR_EDITOR_PROCESSING' ) && GDFAR_EDITOR_PROCESSING === true ) {
			Process::instance()->modded( $type, $id );
		}
	}

	public function feature() : LockTopicsFeature {
		return LockTopicsFeature::instance();
	}

	public function register() {
		gdfar_register_action( 'bbpc-lock', array(
			'scope'       => 'topic',
			'action'      => 'edit',
			'prefix'      => 'bbpc',
			'label'       => __( "Lock", "bbp-core" ),
			'description' => __( "Change topic lock status.", "bbp-core" ),
			'source'      => 'BBP Core'
		) );

		gdfar_register_action( 'bbpc-lock', array(
			'scope'       => 'topic',
			'action'      => 'bulk',
			'prefix'      => 'bbpc',
			'label'       => __( "Lock", "bbp-core" ),
			'description' => __( "Change topic lock status.", "bbp-core" ),
			'source'      => 'BBP Core'
		) );
	}

	public function display_topic_edit_bbpc_lock( $render, $args = array() ) : string {
		$list = array(
			'lock'   => __( "Locked", "bbp-core" ),
			'unlock' => __( "Unlocked", "bbp-core" )
		);

		$locked = $this->feature()->is_topic_temp_locked( $args['id'] ) ? 'lock' : 'unlock';

		return gdfar_render()->select( $list, array(
			'selected' => $locked,
			'name'     => $args['base'] . '[lock]',
			'id'       => $args['element']
		) );
	}

	public function process_topic_edit_bbpc_lock( $result, $args = array() ) {
		$topic_id = $args['id'];

		$new_status = $args['value']['lock'] ?? '';
		$old_status = $this->feature()->is_topic_temp_locked( $topic_id ) ? 'lock' : 'unlock';

		if ( empty( $new_status ) || ! in_array( $new_status, array( 'lock', 'unlock' ) ) ) {
			return new WP_Error( "invalid_lock", __( "Invalid lock value.", "bbp-core" ) );
		}

		if ( $new_status != $old_status ) {
			$this->feature()->lock_topic( $topic_id, $new_status );

			$this->modded( 'topic', $topic_id );
		}

		return $result;
	}

	public function display_topic_bulk_bbpc_lock( $render, $args = array() ) : string {
		$list = array(
			''       => __( "Don't Change", "bbp-core" ),
			'lock'   => __( "Locked", "bbp-core" ),
			'unlock' => __( "Unlocked", "bbp-core" )
		);

		return gdfar_render()->select( $list, array(
			'selected' => '',
			'name'     => $args['base'] . '[lock]',
			'id'       => $args['element']
		) );
	}

	public function process_topic_bulk_bbpc_lock( $result, $args = array() ) {
		$new_status = $args['value']['lock'] ?? '';

		if ( ! empty( $new_status ) ) {
			if ( ! in_array( $new_status, array( 'lock', 'unlock' ) ) ) {
				return new WP_Error( "invalid_lock", __( "Invalid lock value.", "bbp-core" ) );
			}

			foreach ( $args['id'] as $topic_id ) {
				$old_status = $this->feature()->is_topic_temp_locked( $topic_id ) ? 'lock' : 'unlock';

				if ( $new_status != $old_status ) {
					$this->feature()->lock_topic( $topic_id, $new_status );

					$this->modded( 'topic', $topic_id );
				}
			}
		}

		return $result;
	}
}
