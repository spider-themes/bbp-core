<?php

if (!defined('ABSPATH')) {
	exit;
}

class gdbbx_grid_bbcodes extends d4p_grid {
	public $_table_valid_roles = array();
	public $_table_class_name = 'gdbbx-grid-bbcodes';

	public function __construct($args = array()) {
		parent::__construct(array(
			'singular' => 'bbcode',
			'plural' => 'bbcodes',
			'ajax' => false
		));

		foreach (gdbbx_get_user_roles() as $role => $label) {
			if ($role != 'bbp_blocked') {
				$this->_table_valid_roles[ $role ] = $label;
			}
		}
	}

	protected function display_tablenav( $which ) {

	}

	public function get_columns() {
		$columns = array(
			'title' => __( "Title", "bbp-core" ),
			'bbcode' => __( "BBCode", "bbp-core" ),
			'status' => '<input data-column="status" type="checkbox" />'.__( "Status", "bbp-core" ),
			'toolbar' => '<input data-column="toolbar" type="checkbox" />'.__( "Toolbar", "bbp-core" ),
			'visitor' => '<input data-column="visitor" type="checkbox" />'.__( "Anonymous", "bbp-core" )
		);

		foreach ($this->_table_valid_roles as $role => $label) {
			$columns[ $role ] = '<input data-column="'.$role.'" type="checkbox" />'.$label;
		}

		return $columns;
	}

	public function column_title($item) {
		return $item['title'];
	}

	public function column_bbcode($item) {
		return '['.$item['bbcode'].']';
	}

	public function column_status($item) {
		return '<input type="checkbox" name="gdbbx[bbcodes]['.$item['bbcode'].'][status]" '.checked($item['settings']['status'], true, false).' />';
	}

	public function column_toolbar($item) {
		return '<input type="checkbox" name="gdbbx[bbcodes]['.$item['bbcode'].'][toolbar]" '.checked($item['settings']['toolbar'], true, false).' />';
	}

	public function column_visitor($item) {
		return '<input type="checkbox" name="gdbbx[bbcodes]['.$item['bbcode'].'][visitor]" '.checked($item['settings']['visitor'], true, false).' />';
	}

	public function column_default($item, $column_name) {
		foreach ( $this->_table_valid_roles as $role => $label ) {
			if ($role == $column_name) {
				$settings = $item['settings']['roles'];
				$is = $settings === true || (is_array($settings) && in_array($role, $settings));

				return '<input type="checkbox" name="gdbbx[bbcodes]['.$item['bbcode'].'][role]['.$role.']" '.checked($is, true, false).' />';
			}
		}
	}

	public function prepare_items() {
		$this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

		require_once(GDBBX_PATH.'core/functions/bbcodes.php');

		foreach (gdbbx_get_bbcodes_list() as $code => $data) {
			$this->items[$code] = array(
				'bbcode' => $code,
				'title' => $data['title'],
				'examples' => $data['examples'],
				'settings' => gdbbx()->get($code, 'bbcodes')
			);
		}
	}
}
