<?php

use SpiderDevs\Plugin\BBPC\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bbpc_render_signature_editor( $content ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		bbpc_signature()->generate_editor( $content );
	}
}

function bbpc_signature_editor_class( $extra_class = '' ) : string {
	if ( ! Plugin::instance()->is_enabled( 'signatures' ) ) {
		return '';
	}

	$class = 'bbpc-signature-form bbpc-editor-';

	if ( bbpc_signature()->tinymce ) {
		$class .= 'tinymce';
	} else {
		$class .= bbpc_signature()->settings['editor'];
	}

	if ( ! empty( $extra_class ) ) {
		$class .= ' ' . $extra_class;
	}

	return apply_filters( 'bbpc_signature_editor_class', $class );
}

function bbpc_display_signature_editor_form( $user_id, $form_template = '' ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		bbpc_signature()->editor_form_generic( $user_id, $form_template );
	}
}

function bbpc_save_signature_from_post( $user_id ) {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		bbpc_signature()->editor_save( $user_id );
	}
}

function bbpc_get_raw_user_signature( $user_id ) {
	if ( bbpc_signature()->settings['scope'] == 'global' ) {
		return get_user_meta( $user_id, 'signature', true );
	} else {
		return get_user_option( 'signature', $user_id );
	}
}

function bbpc_update_raw_user_signature( $user_id, $signature ) {
	$global = bbpc_signature()->settings['scope'] == 'global';

	update_user_option( $user_id, 'signature', $signature, $global );
}

function bbpc_signature_display_disable() {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		bbpc_signature()->remove_content_filters();
	}
}

function bbpc_signature_display_enable() {
	if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
		bbpc_signature()->add_content_filters();
	}
}

if ( ! function_exists( 'bbpc_user_signature' ) ) {
	function bbpc_user_signature( $user_id, $args = array() ) {
		$defaults = array(
			'echo'    => true,
			'before'  => '<div class="bbpc-signature">',
			'after'   => '</div>',
			'smilies' => bbpc_signature()->settings['process_smilies'],
			'chars'   => bbpc_signature()->settings['process_chars'],
			'autop'   => bbpc_signature()->settings['process_autop']
		);

		$args = wp_parse_args( $args, $defaults );

		$signature = bbpc_get_raw_user_signature( $user_id );

		$sig = bbpc_update_shorthand_bbcodes( $signature );

		if ( $sig != $signature ) {
			bbpc_update_raw_user_signature( $user_id, $sig );
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

		$sig = apply_filters( 'bbpc_signature_for_display', $sig, $signature, $user_id );

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
