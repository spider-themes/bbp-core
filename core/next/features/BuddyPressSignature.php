<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use BP_XProfile_Group;
use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BuddyPressSignature extends Feature {
	public $feature_name = 'buddypress-signature';
	public $settings = array(
		'xfield_id'  => 0,
		'xfield_add' => false,
		'xfield_del' => false
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bp_init', array( $this, 'init' ) );

		add_filter( 'bp_xprofile_get_field_types', array( $this, 'get_field_types' ) );

		add_action( 'xprofile_get_field_data', array( $this, 'xprofile_get_field_data' ), 10, 3 );
		add_action( 'xprofile_data_before_delete', array( $this, 'xprofile_data_before_delete' ) );
		add_action( 'xprofile_data_after_save', array( $this, 'xprofile_data_after_save' ) );
	}

	public static function instance() : BuddyPressSignature {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new BuddyPressSignature();
		}

		return $instance;
	}

	public function init() {
		if ( $this->xprofile_enabled() && $this->get( 'xfield_add', 'buddypress' ) ) {
			add_action( 'current_screen', array( $this, 'create_signature_field' ) );
		}

		if ( $this->xprofile_enabled() && $this->get( 'xfield_del', 'buddypress' ) ) {
			add_action( 'current_screen', array( $this, 'remove_signature_field' ) );
		}
	}

	public function xprofile_enabled() : bool {
		return bp_is_active( 'xprofile' );
	}

	public function get_field_types( $types ) {
		require_once( GDBBX_PATH . 'core/buddypress/signature.php' );

		$types['signature_textarea'] = 'GDBBX_XProfile_Field_Type_Signature_Text_Area';

		return $types;
	}

	public function has_signature_field() : bool {
		if ( ! bp_is_active( 'xprofile' ) ) {
			return false;
		}

		$field_id = $this->get( 'xfield_id' );
		$field    = xprofile_get_field( $field_id );

		$missing = is_null( $field ) || $field->id !== $field_id || $field->type !== 'signature_textarea';

		return ! $missing;
	}

	public function profile_groups() : array {
		$list = array();

		$raw = BP_XProfile_Group::get( array( 'fetch_fields' => false ) );

		foreach ( $raw as $group ) {
			$list[ $group->id ] = $group->name;
		}

		return $list;
	}

	public function first_group_id() {
		$list   = $this->profile_groups();
		$groups = array_keys( $list );

		return $groups[0];
	}

	public function remove_signature_field() {
		if ( ! bp_is_active( 'xprofile' ) ) {
			return false;
		}

		$field_id = $this->get( 'xfield_id' );

		if ( $field_id > 0 ) {
			xprofile_delete_field( $field_id );
		}

		gdbbx()->set( 'buddypress-signature__xfield_id', 0, 'features' );
		gdbbx()->set( 'buddypress-signature__xfield_del', false, 'features', true );

		wp_redirect_self();
		exit;
	}

	public function create_signature_field() {
		if ( ! bp_is_active( 'xprofile' ) ) {
			return false;
		}

		if ( ! $this->has_signature_field() ) {
			$field_id = xprofile_insert_field( array(
				'field_group_id' => $this->first_group_id(),
				'name'           => __( "Forum Signature", "bbp-core" ),
				'is_required'    => false,
				'type'           => 'signature_textarea',
				'can_delete'     => true
			) );

			gdbbx()->set( 'buddypress-signature__xfield_id', $field_id, 'features' );
		}

		gdbbx()->set( 'buddypress-signature__xfield_add', false, 'features', true );

		wp_redirect_self();
		exit;
	}

	public function xprofile_data_before_delete( $field ) {
		if ( $this->xprofile_enabled() && $field->field_id == $this->get( 'xfield_id' ) ) {
			$user_id = $field->user_id;

			gdbbx_update_raw_user_signature( $user_id, '' );
		}
	}

	public function xprofile_get_field_data( $value, $field_id, $user_id ) {
		if ( $this->xprofile_enabled() && $field_id == $this->get( 'xfield_id' ) ) {
			$value = gdbbx_get_raw_user_signature( $user_id );
		}

		return $value;
	}

	public function xprofile_data_after_save( $field ) {
		if ( $this->xprofile_enabled() && $field->field_id == $this->get( 'xfield_id' ) ) {
			if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
				$user_id   = $field->user_id;
				$signature = $field->value;

				gdbbx_update_raw_user_signature( $user_id, $signature );
			}
		}
	}
}