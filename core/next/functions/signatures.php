<?php

use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gdbbx_render_signature_editor( $content ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		gdbbx_signature()->generate_editor( $content );
	}
}

function gdbbx_signature_editor_class( $extra_class = '' ) : string {
	if ( ! Plugin::instance()->is_enabled( 'signatures' ) ) {
		return '';
	}

	$class = 'gdbbx-signature-form gdbbx-editor-';

	if ( gdbbx_signature()->tinymce ) {
		$class .= 'tinymce';
	} else {
		$class .= gdbbx_signature()->settings['editor'];
	}

	if ( ! empty( $extra_class ) ) {
		$class .= ' ' . $extra_class;
	}

	return apply_filters( 'gdbbx_signature_editor_class', $class );
}

function gdbbx_display_signature_editor_form( $user_id, $form_template = '' ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		gdbbx_signature()->editor_form_generic( $user_id, $form_template );
	}
}

function gdbbx_save_signature_from_post( $user_id ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		gdbbx_signature()->editor_save( $user_id );
	}
}

function gdbbx_get_raw_user_signature( $user_id ) {
	if ( gdbbx_signature()->settings['scope'] == 'global' ) {
		return get_user_meta( $user_id, 'signature', true );
	} else {
		return get_user_option( 'signature', $user_id );
	}
}

function gdbbx_update_raw_user_signature( $user_id, $signature ) {
	$global = gdbbx_signature()->settings['scope'] == 'global';

	update_user_option( $user_id, 'signature', $signature, $global );
}

function gdbbx_signature_display_disable() {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		gdbbx_signature()->remove_content_filters();
	}
}

function gdbbx_signature_display_enable() {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		gdbbx_signature()->add_content_filters();
	}
}

if ( ! function_exists( 'gdbbx_user_signature' ) ) {
	function gdbbx_user_signature( $user_id, $args = array() ) {
		$defaults = array(
			'echo'    => true,
			'before'  => '<div class="gdbbx-signature">',
			'after'   => '</div>',
			'smilies' => gdbbx_signature()->settings['process_smilies'],
			'chars'   => gdbbx_signature()->settings['process_chars'],
			'autop'   => gdbbx_signature()->settings['process_autop']
		);

		$args = wp_parse_args( $args, $defaults );

		$signature = gdbbx_get_raw_user_signature( $user_id );

		$sig = gdbbx_update_shorthand_bbcodes( $signature );

		if ( $sig != $signature ) {
			gdbbx_update_raw_user_signature( $user_id, $sig );
		}

		if ( $sig != '' ) {
			if ( $args['smilies'] ) {
				$sig = convert_smilies( $sig );
			}

			if ( $args['chars'] ) {
				$sig = convert_chars( $sig );
			}

			if ( $args['autop'] ) {
				$sig = wpautop( $sig );
				$sig = shortcode_unautop( $sig );
			}

			$sig = do_shortcode( $sig );
		}

		$sig = apply_filters( 'gdbbx_signature_for_display', $sig, $signature, $user_id );

		if ( $sig != '' ) {
			$sig = $args['before'] . $sig . $args['after'];
		}

		if ( $args['echo'] ) {
			echo $sig;
		} else {
			return $sig;
		}
	}
}
