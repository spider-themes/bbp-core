<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\bbPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserSettings extends Feature {
	public $feature_name = 'user-settings';

	private $registered = array();
	private $groups = array();

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_user_edit_after', array( $this, 'fieldset' ) );
		add_action( 'personal_options_update', array( $this, 'update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'update' ) );

		$this->groups = apply_filters( 'gdbbx_user_settings_groups', array(
			'settings'      => array(
				'label' => __( "Settings", "bbp-core" )
			),
			'notifications' => array(
				'label' => __( "Email Notifications", "bbp-core" )
			)
		) );
	}

	public static function instance() : UserSettings {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new UserSettings();
		}

		return $instance;
	}

	public function find( $name ) {
		if ( isset( $this->registered[ $name ] ) ) {
			return $this->registered[ $name ];
		}

		return false;
	}

	public function register( $name, $label, $description, $group, $type, $default, $args = array() ) {
		$this->registered[ $name ] = (object) array(
			'name'        => $name,
			'label'       => $label,
			'description' => $description,
			'group'       => $this->validate_group( $group ),
			'type'        => $this->validate_type( $type ),
			'default'     => $default,
			'args'        => $args
		);
	}

	public function validate_group( $group, $fallback = 'settings' ) {
		return isset( $this->groups[ $group ] ) ? $group : $fallback;
	}

	public function validate_type( $type, $fallback = 'text' ) {
		$valid = array(
			'checkbox',
			'text',
			'textarea',
			'number'
		);

		return in_array( $type, $valid ) ? $type : $fallback;
	}

	public function get_group_fields( $group ) {
		$list = array();

		foreach ( $this->registered as $key => $obj ) {
			if ( $obj->group == $group ) {
				$list[ $key ] = $obj;
			}
		}

		return $list;
	}

	public function fieldset() {
		if ( empty( $this->registered ) ) {
			return;
		}

		$user_id = bbp_get_displayed_user_id();

		foreach ( array_keys( $this->groups ) as $group ) {
			$this->render_form( $user_id, $group );
		}
	}

	public function render_form( $user_id, $group ) {
		$list = $this->get_group_fields( $group );

		if ( empty( $list ) ) {
			return;
		}

		$group_title = apply_filters( 'gdbbx_user_settings_fieldset_legend', $this->groups[ $group ]['label'] );

		if ( bbPress::instance()->theme_package == 'default' ) {
			echo '<h2 class="entry-title">' . $group_title . '</h2>';
		}

		echo '<fieldset class="bbp-form gdbbx-user-settings">';
		echo '<legend>' . $group_title . '</legend>';

		foreach ( $list as $key => $obj ) {
			$value = gdbbx_user( $user_id )->get( $obj->name );

			echo '<div class="gdbbx-user-settings-' . $obj->type . '">';

			if ( $obj->type == 'checkbox' ) {
				echo '<label>';
				echo '<input value="on" class="checkbox" type="checkbox" name="' . $key . '" id="' . $key . '"' . checked( $value, true, false ) . ' /> ';
				echo $obj->label;

				if ( ! empty( $obj->description ) ) {
					echo '<span class="description">' . $obj->description . '</span>';
				}

				echo '</label>';
			} else {
				echo '<label for="' . $key . '">' . $obj->label . '</label>';

				switch ( $obj->type ) {
					case 'text':
						echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
						break;
					case 'number':
						$defaults = array(
							'min'  => '',
							'max'  => '',
							'step' => '1'
						);

						$atts = wp_parse_args( $obj->args, $defaults );

						echo '<input type="number" min="' . $atts['min'] . '" max="' . $atts['max'] . '" step="' . $atts['step'] . '" name="' . $key . '" id="' . $key . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
						break;
					case 'textarea':
						echo '<textarea name="' . $key . '" id="' . $key . '">' . esc_textarea( $value ) . '</textarea>';
						break;
				}
			}

			echo '</div>';
		}

		echo '</fieldset>';
	}

	public function update( $user_id ) {
		if ( empty( $this->registered ) ) {
			return;
		}

		$global = apply_filters( 'gdbbx_user_settings_are_global', false );

		foreach ( $this->registered as $key => $obj ) {
			$value = null;

			if ( $obj->type == 'checkbox' ) {
				$value = isset( $_POST[ $key ] ) && $_POST[ $key ] == 'on';
			} else {
				if ( isset( $_POST[ $key ] ) ) {
					$raw = $_POST[ $key ];

					switch ( $obj->type ) {
						case 'text':
						case 'textarea':
						case 'select':
							$value = d4p_sanitize_basic( $raw );
							break;
						case 'number':
							$value = is_numeric( $raw ) ? $raw : (float) $value;
							break;
					}

					$value = apply_filters( 'gdbbx_user_settings_save_' . $key, $value, $raw );
				}
			}

			if ( ! is_null( $value ) ) {
				update_user_option( $user_id, $key, $value, $global );
			} else {
				delete_user_option( $user_id, $key );
			}
		}
	}
}
