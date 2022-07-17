<?php

namespace SpiderDevs\Plugin\BBPC\Library;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class DB extends DBLite {
	public $db_site = array();
	public $db;

	public $_prefix = '';
	public $_tables = array();
	public $_network_tables = array();
	public $_metas = array();

	protected $_meta_translate = array();

	public function __construct() {
		parent::__construct();

		if ( ! empty( $this->_tables ) ) {
			$this->init();

			add_action( 'switch_blog', array( $this, 'init' ) );
		}
	}

	/** @global \wpdb $wpdb */
	public function init() {
		global $wpdb;

		$plugin        = new stdClass();
		$this->db      = new stdClass();
		$this->db_site = array();

		foreach ( $this->_tables as $name ) {
			$prefix = in_array( $name, $this->_network_tables ) ? $wpdb->base_prefix : $wpdb->prefix;

			$wpdb_name = $this->_prefix . '_' . $name;
			$real_name = $prefix . $wpdb_name;

			$plugin->$name   = $real_name;
			$this->db->$name = $real_name;

			$wpdb->$wpdb_name = $real_name;

			if ( ! in_array( $name, $this->_network_tables ) ) {
				$this->db_site[] = $real_name;
			}
		}

		$wpdb->{$this->_prefix} = $plugin;

		if ( ! empty( $this->_prefix ) && ! empty( $this->_metas ) ) {
			foreach ( $this->_metas as $key => $id ) {
				$this->_meta_translate[ $this->_prefix . '_' . $key . '_id' ] = $id;
			}

			add_filter( 'sanitize_key', array( $this, 'sanitize_meta' ) );
		}
	}

	public function __get( $name ) {
		if ( isset( $this->db->$name ) ) {
			return $this->db->$name;
		} else if ( isset( $this->wpdb()->$name ) ) {
			return $this->wpdb()->$name;
		}

		return false;
	}

	public function sanitize_meta( $key ) {
		if ( isset( $this->_meta_translate[ $key ] ) ) {
			return $this->_meta_translate[ $key ];
		}

		return $key;
	}
}
