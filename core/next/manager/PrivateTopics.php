<?php

namespace SpiderDevs\Plugin\BBPC\Manager;

use SpiderDevs\Plugin\BBPC\Features\PrivateTopics as PrivateTopicsFeature;
use SpiderDevs\Plugin\GDFAR\Manager\Process;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PrivateTopics {
	private $_defaults = array(
		'topic' => array(
			'edit' => array( 'bbpc-private' ),
			'bulk' => array( 'bbpc-private' )
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

	public static function instance() : PrivateTopics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new PrivateTopics();
		}

		return $instance;
	}

	public function modded( $type, $id ) {
		if ( defined( 'GDFAR_EDITOR_PROCESSING' ) && GDFAR_EDITOR_PROCESSING === true ) {
			Process::instance()->modded( $type, $id );
		}
	}

	public function feature() : PrivateTopicsFeature {
		return PrivateTopicsFeature::instance();
	}

	public function register() {
		gdfar_register_action( 'bbpc-private', array(
			'scope'       => 'topic',
			'action'      => 'edit',
			'prefix'      => 'bbpc',
			'label'       => __( "Private", "bbp-core" ),
			'description' => __( "Change topic privacy status.", "bbp-core" ),
			'source'      => 'BBP Core'
		) );

		gdfar_register_action( 'bbpc-private', array(
			'scope'       => 'topic',
			'action'      => 'bulk',
			'prefix'      => 'bbpc',
			'label'       => __( "Private", "bbp-core" ),
			'description' => __( "Change topic privacy status.", "bbp-core" ),
			'source'      => 'BBP Core'
		) );
	}

	public function display_topic_edit_bbpc_private( $render, $args = array() ) : string {
		$list = array(
			'private' => __( "Private", "bbp-core" ),
			'public'  => __( "Public", "bbp-core" )
		);

		$locked = $this->feature()->is_private( $args['id'] ) ? 'private' : 'public';

		return gdfar_render()->select( $list, array(
			'selected' => $locked,
			'name'     => $args['base'] . '[private]',
			'id'       => $args['element']
		) );
	}

	public function process_topic_edit_bbpc_private( $result, $args = array() ) {
		$topic_id = $args['id'];

		$new_status = $args['value']['private'] ?? '';
		$old_status = $this->feature()->is_private( $topic_id ) ? 'private' : 'public';

		if ( empty( $new_status ) || ! in_array( $new_status, array( 'private', 'public' ) ) ) {
			return new WP_Error( "invalid_private", __( "Invalid Privacy value.", "bbp-core" ) );
		}

		if ( $new_status != $old_status ) {
			$this->feature()->privacy_status( $topic_id, $new_status === 'private' );

			$this->modded( 'topic', $topic_id );
		}

		return $result;
	}

	public function display_topic_bulk_bbpc_private( $render, $args = array() ) : string {
		$list = array(
			''        => __( "Don't Change", "bbp-core" ),
			'private' => __( "Private", "bbp-core" ),
			'public'  => __( "Public", "bbp-core" )
		);

		return gdfar_render()->select( $list, array(
			'selected' => '',
			'name'     => $args['base'] . '[private]',
			'id'       => $args['element']
		) );
	}

	public function process_topic_bulk_bbpc_private( $result, $args = array() ) {
		$new_status = $args['value']['private'] ?? '';

		if ( ! empty( $new_status ) ) {
			if ( ! in_array( $new_status, array( 'private', 'public' ) ) ) {
				return new WP_Error( "invalid_private", __( "Invalid Privacy value.", "bbp-core" ) );
			}

			foreach ( $args['id'] as $topic_id ) {
				$old_status = $this->feature()->is_private( $topic_id ) ? 'private' : 'public';

				if ( $new_status != $old_status ) {
					$this->feature()->privacy_status( $topic_id, $new_status === 'private' );

					$this->modded( 'topic', $topic_id );
				}
			}
		}

		return $result;
	}
}