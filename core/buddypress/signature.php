<?php

use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;
use SpiderDevs\Plugin\BBPC\BBCodes\Toolbar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBPC_XProfile_Field_Type_Signature_Text_Area extends BP_XProfile_Field_Type {
	public function __construct() {
		parent::__construct();

		$this->name              = __( 'Signature', 'bbp-core' ) . ' (' . __( 'do not use directly!', 'bbp-core' ) . ')';
		$this->supports_richtext = true;

		$this->set_format( '/^.*$/m', 'replace' );

		do_action( 'bp_xprofile_field_type_signature', $this );
	}

	public function edit_field_html( array $raw_properties = [] ) {
		?>

		<legend for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php

		if ( ! Plugin::instance()->is_enabled( 'signatures' ) ) {
			_e( 'Signatures module is disabled.', 'bbp-core' );

			return;
		} elseif ( ! Plugin::instance()->is_enabled( 'buddypress-signature' ) ) {
			_e( 'This field is disabled.', 'bbp-core' );

			return;
		}

		$user_id = isset( $raw_properties['user_id'] ) ? absint( $raw_properties['user_id'] ) : bp_displayed_user_id();

		if ( $user_id > 0 ) {
			$signature = bbpc_get_raw_user_signature( $user_id );
		} else {
			$signature = bp_get_the_profile_field_edit_value();
		}

		$_editor = bbpc_signature()->editor;

		if ( $_editor == 'bbcodes' && ! bbpc_is_bbcodes_toolbar_available() ) {
			$_editor = 'textarea';
		}

		?>

	<div class="<?php echo bbpc_signature_editor_class( 'bbpc-buddypress-xprofile wp-editor-wrap' ); ?>">

		<?php

		do_action( bp_get_the_profile_field_errors_action() );

		if ( $_editor == 'textarea' || $_editor == 'bbcodes' ) {
			if ( $_editor == 'bbcodes' ) {
				Toolbar::instance()->display();
			}

			$r = wp_parse_args(
				$raw_properties,
				[
					'cols' => 40,
					'rows' => 5,
				]
			);

			Enqueue::instance()->toolbar();

			?>

			<textarea<?php echo bbpc_signature()->textarea_data(); ?> class="<?php echo bbpc_signature()->textarea_class(); ?>" <?php echo $this->get_edit_field_html_elements( $r ); ?>><?php echo esc_textarea( $signature ); ?></textarea>

			<?php

		} elseif ( $_editor == 'tinymce' || $_editor == 'tinymce_compact' ) {
			$settings = [
				'textarea_rows' => 5,
				'teeny'         => $_editor == 'tinymce_compact',
			];

			wp_editor( $signature, bp_get_the_profile_field_input_name(), $settings );
		}

		?>
		</div>
		<?php

	}

	public function admin_field_html( array $raw_properties = [] ) {
		if ( ! Plugin::instance()->is_enabled( 'signatures' ) ) {
			_e( "Signatures module is disabled. If you don't use this field, remove it from the Extended Profiles.", 'bbp-core' );

			return;
		} elseif ( ! Plugin::instance()->is_enabled( 'buddypress-signature' ) ) {
			_e( 'Extended Profiles support in BBP Core is disabled. You should remove this field.', 'bbp-core' );

			return;
		}

		$_editor = bbpc_signature()->editor;

		if ( $_editor == 'bbcodes' && ! bbpc_is_bbcodes_toolbar_available() ) {
			$_editor = 'textarea';
		}

		?>
		<div class="<?php echo bbpc_signature_editor_class( 'bbpc-buddypress-xprofile' ); ?>">
							   <?php

								if ( $_editor == 'textarea' || $_editor == 'bbcodes' ) {
									if ( $_editor == 'bbcodes' ) {
										Toolbar::instance()->display();
									}

									$r = wp_parse_args(
										$raw_properties,
										[
											'cols' => 40,
											'rows' => 5,
										]
									);

									?>

			<textarea <?php echo $this->get_edit_field_html_elements( $r ); ?>></textarea>

									<?php

								} elseif ( $_editor == 'tinymce' || $_editor == 'tinymce_compact' ) {
									$settings = [
										'textarea_rows' => 5,
										'teeny'         => $_editor == 'tinymce_compact',
									];

									wp_editor( '', bp_get_the_profile_field_input_name(), $settings );
								}

								?>
		</div>
		<?php

	}

	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
	}

	public static function pre_validate_filter( $field_value, $field_id = '' ) {
		if ( ! Plugin::instance()->is_enabled( 'signatures' ) ) {
			return $field_value;
		} else {
			return bbpc_signature()->format_signature( $field_value );
		}
	}
}
