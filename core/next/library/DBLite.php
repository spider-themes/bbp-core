<?php

namespace SpiderDevs\Plugin\BBPC\Library;

use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class DBLite {
	protected $_queries_log = array();

	public function __construct() {
	}

	public function init() {
	}

	abstract public static function instance();

	public function __get( $name ) {
		if ( isset( $this->wpdb()->$name ) ) {
			return $this->wpdb()->$name;
		}

		return false;
	}

	public function clean_ids_list( $ids ) : array {
		$ids = (array) $ids;

		$ids = array_map( 'absint', $ids );
		$ids = array_unique( $ids );

		return array_filter( $ids );
	}

	public function build_query( $sql, $calc_found_rows = true ) : string {
		$defaults = array(
			'select' => array(),
			'from'   => array(),
			'where'  => array(),
			'group'  => '',
			'order'  => '',
			'limit'  => ''
		);

		$sql = wp_parse_args( $sql, $defaults );

		$_build = 'SELECT' . ( $calc_found_rows ? ' SQL_CALC_FOUND_ROWS' : '' ) . ' ' . join( ', ', $sql['select'] ) . ' FROM ' . join( ' ', $sql['from'] );

		if ( ! empty( $sql['where'] ) ) {
			$_build .= ' WHERE ' . join( ' AND ', $sql['where'] );
		}

		if ( ! empty( $sql['group'] ) ) {
			$_build .= ' GROUP BY ' . $sql['group'];
		}

		if ( ! empty( $sql['order'] ) ) {
			$_build .= ' ORDER BY ' . $sql['order'];
		}

		if ( ! empty( $sql['limit'] ) ) {
			$_build .= ' LIMIT ' . $sql['limit'];
		}

		return $_build;
	}

	public function query( $query ) {
		$_value = $this->wpdb()->query( $query );

		$this->_copy_logged_query();

		return $_value;
	}

	public function run( $query = null, $output = OBJECT ) {
		$_value = $this->wpdb()->get_results( $query, $output );

		$this->_copy_logged_query();

		return $_value;
	}

	public function run_and_index( $query, $field, $output = OBJECT ) {
		$raw = $this->wpdb()->get_results( $query, $output );

		$_value = $this->index( $raw, $field );

		$this->_copy_logged_query();

		return $_value;
	}

	public function get_var( $query, $x = 0, $y = 0 ) {
		$_value = $this->wpdb()->get_var( $query, $x, $y );

		$this->_copy_logged_query();

		return $_value;
	}

	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$_value = $this->wpdb()->get_row( $query, $output, $y );

		$this->_copy_logged_query();

		return $_value;
	}

	public function get_col( $query = null, $x = 0 ) {
		$_value = $this->wpdb()->get_col( $query, $x );

		$this->_copy_logged_query();

		return $_value;
	}

	public function get_results( $query = null, $output = OBJECT ) {
		$_value = $this->wpdb()->get_results( $query, $output );

		$this->_copy_logged_query();

		return $_value;
	}

	public function insert( $table, $data, $format = null ) {
		$_value = $this->wpdb()->insert( $table, $data, $format );

		$this->_copy_logged_query();

		return $_value;
	}

	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		$_value = $this->wpdb()->update( $table, $data, $where, $format, $where_format );

		$this->_copy_logged_query();

		return $_value;
	}

	public function delete( $table, $where, $where_format = null ) {
		$_value = $this->wpdb()->delete( $table, $where, $where_format );

		$this->_copy_logged_query();

		return $_value;
	}

	public function prepare( $query, $args ) {
		$args = func_get_args();
		array_shift( $args );

		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args = $args[0];
		}

		return $this->wpdb()->prepare( $query, $args );
	}

	public function insert_meta_data( $table, $column, $id, $meta ) {
		foreach ( $meta as $key => $value ) {
			$this->insert( $table, array(
				$column      => $id,
				'meta_key'   => $key,
				'meta_value' => maybe_serialize( $value )
			) );
		}
	}

	public function update_meta( $meta_type, $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( $this->_prefix . '_' . $meta_type, $object_id, $meta_key, $meta_value, $prev_value );
	}

	public function add_meta( $meta_type, $object_id, $meta_key, $meta_value, $unique = false ) {
		return add_metadata( $this->_prefix . '_' . $meta_type, $object_id, $meta_key, $meta_value, $unique );
	}

	public function get_meta( $meta_type, $object_id, $meta_key, $single = false ) {
		return get_metadata( $this->_prefix . '_' . $meta_type, $object_id, $meta_key, $single );
	}

	public function delete_meta( $meta_type, $object_id, $meta_key, $delete_all = false ) {
		return delete_metadata( $this->_prefix . '_' . $meta_type, $object_id, $meta_key, $delete_all );
	}

	public function pluck( $list, $field, $index_key = null ) {
		return wp_list_pluck( $list, $field, $index_key );
	}

	public function index( $list, $field ) {
		$new = array();

		foreach ( $list as $item ) {
			$id = is_array( $item ) ? $item[ $field ] : $item->$field;

			$new[ $id ] = $item;
		}

		return $new;
	}

	public function mysqli() : bool {
		$use_mysqli = false;

		if ( function_exists( 'mysqli_connect' ) ) {
			$use_mysqli = true;

			if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
				$use_mysqli = ! WP_USE_EXT_MYSQL;
			}
		}

		return $use_mysqli;
	}

	public function prefix() : string {
		return $this->wpdb()->prefix;
	}

	public function base_prefix() : string {
		return $this->wpdb()->base_prefix;
	}

	public function rows_affected() : int {
		return $this->wpdb()->rows_affected;
	}

	public function blog_id() : int {
		return $this->wpdb()->blogid;
	}

	public function get_insert_id() : int {
		return $this->wpdb()->insert_id;
	}

	public function get_found_rows() {
		return $this->get_var( 'SELECT FOUND_ROWS()' );
	}

	public function save_queries() : bool {
		return defined( 'SAVEQUERIES' ) && SAVEQUERIES;
	}

	public function gmt_offset() {
		$offset = get_option( 'gmt_offset' );

		if ( empty( $offset ) ) {
			$offset = wp_timezone_override_offset();
		}

		return $offset === false ? 0 : $offset;
	}

	public function get_offset_string() : string {
		$offset = $this->gmt_offset();

		$hours   = intval( $offset );
		$minutes = absint( ( $offset - floor( $offset ) ) * 60 );

		return sprintf( '%+03d:%02d', $hours, $minutes );
	}

	public function enable_save_queries() : bool {
		if ( ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );

			return true;
		}

		return SAVEQUERIES === true;
	}

	public function log_get_queries() {
		return $this->_queries_log;
	}

	public function log_get_elapsed_time() {
		$time = 0;

		foreach ( $this->_queries_log as $q ) {
			$time += $q[1];
		}

		return $time;
	}

	public function log_get_last_query( $what = 'sql' ) {
		if ( ! empty( $this->_queries_log ) ) {
			$id  = count( $this->_queries_log ) - 1;
			$log = $this->_queries_log[ $id ];

			if ( $what == 'sql' ) {
				return $log[0];
			}

			return $log;
		}

		return false;
	}

	public function timestamp( $gmt = true ) {
		return current_time( 'timestamp', $gmt );
	}

	public function datetime( $gmt = true ) {
		return current_time( 'mysql', $gmt );
	}

	/**
	 * @return wpdb
	 *
	 * @global wpdb $wpdb
	 */
	public function wpdb() : wpdb {
		global $wpdb;

		return $wpdb;
	}

	protected function _copy_logged_query() {
		if ( $this->save_queries() ) {
			$id                   = count( $this->wpdb()->queries ) - 1;
			$this->_queries_log[] = $this->wpdb()->queries[ $id ];
		}
	}
}
