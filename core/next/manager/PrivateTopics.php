<?php

namespace Dev4Press\Plugin\GDBBX\Manager;

use Dev4Press\Plugin\GDBBX\Features\PrivateTopics as PrivateTopicsFeature;
use Dev4Press\Plugin\GDFAR\Manager\Process;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PrivateTopics {
	private $_defaults = array(
		'topic' => array(
			'edit' => array( 'gdbbx-private' ),
			'bulk' => array( 'gdbbx-private' )
		)
	);

	public function __construct() {
		add_action( 'gdfar_register_actions', array( $this, 'register' ) );

		foreach ( $this->_defaults as $scope => $actions ) {
			foreach ( $actions as $action => $names ) {
				foreach ( $names as $name ) {
					$key    = $scope . '-' . $action . '-' . $name;
					$method = $scope . '_' . $action . '_' . str_replace( '-', '_', $name );

					add_filter( 'gdbbx-action-visible-' . $key, '__return_true' );
					add_filter( 'gdbbx-action-display-' . $key, array( $this, 'display_' . $method ), 10, 2 );
					add_filter( 'gdbbx-action-process-' . $key, array( $this, 'process_' . $method ), 10, 2 );
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
		gdfar_register_action( 'gdbbx-private', array(
			'scope'       => 'topic',
			'action'      => 'edit',
			'prefix'      => 'gdbbx',
			'label'       => __( "Private", "bbp-core" ),
			'description' => __( "Change topic privacy status.", "bbp-core" ),
			'source'      => 'GD bbPress Toolbox Pro'
		) );

		gdfar_register_action( 'gdbbx-private', array(
			'scope'       => 'topic',
			'action'      => 'bulk',
			'prefix'      => 'gdbbx',
			'label'       => __( "Private", "bbp-core" ),
			'description' => __( "Change topic privacy status.", "bbp-core" ),
			'source'      => 'GD bbPress Toolbox Pro'
		) );
	}

	public function display_topic_edit_gdbbx_private( $render, $args = array() ) : string {
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

	public function process_topic_edit_gdbbx_private( $result, $args = array() ) {
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

	public function display_topic_bulk_gdbbx_private( $render, $args = array() ) : string {
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

	public function process_topic_bulk_gdbbx_private( $result, $args = array() ) {
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