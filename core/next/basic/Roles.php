<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Roles {
	public $media_button = false;
	public $cap = 'activate_plugins';

	public function __construct() {
		add_filter( 'init', array( $this, 'init_capabilities' ) );
		add_filter( 'bbp_get_caps_for_role', array( $this, 'get_caps_for_role' ), 10, 2 );
		add_action( 'gdbbx_plugin_settings_loaded', array( $this, 'settings_loaded' ) );
	}

	public static function instance() : Roles {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Roles();
		}

		return $instance;
	}

	public function get_caps_for_role( $caps, $role ) {
		if ( $role == bbp_get_keymaster_role() ) {
			$caps['gdbbx_standard'] = true;
		}

		switch ( $role ) {
			case bbp_get_keymaster_role():
			case bbp_get_moderator_role():
				$caps['gdbbx_moderation']             = true;
				$caps['gdbbx_moderation_users']       = true;
				$caps['gdbbx_moderation_report']      = true;
				$caps['gdbbx_moderation_attachments'] = true;
				break;
		}

		return $caps;
	}

	public function settings_loaded() {
		$this->media_button = gdbbx()->get( 'tweaks__participant_media_library_upload', 'features' );

		if ( $this->media_button ) {
			add_action( 'bbp_after_setup_theme', array( $this, 'dynamic_roles_media_upload' ) );
			add_filter( 'user_has_cap', array( $this, 'user_has_cap_media_upload' ), 10, 4 );
		}
	}

	public function update_role_before_render() {
		if ( $this->media_button ) {
			add_filter( 'user_has_cap', array( $this, 'user_has_cap_media_upload_early' ), 10, 4 );
		}
	}

	public function user_has_cap_media_upload_early( $allcaps, $caps, $args, $user ) {
		if ( isset( $caps[0] ) && isset( $args[0] ) && $caps[0] == 'upload_files' && $args['0'] == 'upload_files' ) {
			if ( $user->has_cap( bbp_get_participant_role() ) || $user->has_cap( bbp_get_moderator_role() ) ) {
				$allcaps['upload_files'] = true;
			}
		}

		return $allcaps;
	}

	public function update_roles() {
		if ( $this->media_button ) {
			$moderator = get_role( bbp_get_moderator_role() );
			$moderator->add_cap( 'upload_files' );

			$participant = get_role( bbp_get_participant_role() );
			$participant->add_cap( 'upload_files' );
		}
	}

	public function dynamic_roles_media_upload() {
		$_use_db           = wp_roles()->use_db;
		wp_roles()->use_db = false;

		$this->update_roles();

		wp_roles()->use_db = $_use_db;
	}

	public function user_has_cap_media_upload( $allcaps, $caps, $args, $user ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && is_user_logged_in() ) {
			$actions = array( 'upload-attachment', 'query-attachments' );

			if ( isset( $caps[0] ) && isset( $args[0] ) ) {
				if ( in_array( $_REQUEST['action'], $actions, true ) ) {
					if ( $caps[0] == 'upload_files' && $args['0'] == 'upload_files' ) {
						if ( $user->has_cap( bbp_get_participant_role() ) || $user->has_cap( bbp_get_moderator_role() ) ) {
							$allcaps['upload_files'] = true;

							add_filter( 'ajax_query_attachments_args', array( $this, 'user_query_attachments' ) );
						}
					}
				}

				if ( $caps[0] == 'edit_others_forums' && $args[0] == 'edit_post' ) {
					if ( $user->has_cap( bbp_get_participant_role() ) || $user->has_cap( bbp_get_moderator_role() ) ) {
						$allcaps['edit_others_forums'] = true;
					}
				}
			}
		}

		return $allcaps;
	}

	public function user_query_attachments( $args ) {
		$args['author'] = get_current_user_id();

		return $args;
	}

	public function init_capabilities() {
		$role = get_role( 'administrator' );

		if ( ! is_null( $role ) ) {
			$role->add_cap( 'gdbbx_standard' );
			$role->add_cap( 'gdbbx_moderation' );
			$role->add_cap( 'gdbbx_moderation_users' );
			$role->add_cap( 'gdbbx_moderation_report' );
			$role->add_cap( 'gdbbx_moderation_attachments' );
		}

		define( 'GDBBX_CAP', $this->cap );
	}
}
