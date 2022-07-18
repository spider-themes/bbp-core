<?php

if (!defined('ABSPATH')) {
    exit;
}

function gdbbx_admin_render_attachment_for_metabox($post_id, $attachment_id) {
    $file = get_attached_file($attachment_id);
    $filename = pathinfo($file, PATHINFO_BASENAME);

    $return = '<li class="gdbbx-attachment-id-'.$attachment_id.'">'.$filename.' - <span>';
    $return .= '<a target="_blank" href="'.admin_url('upload.php?item='.$attachment_id).'">'.__("edit", "bbp-core").'</a>';
    $return .= ' | <a class="gdbbx-edit-attachment-detach" href="#" data-nonce="'.wp_create_nonce('gdbbx-det-'.$post_id.'-'.$attachment_id).'" data-id="'.$attachment_id.'" data-post="'.$post_id.'">'.__("detach", "bbp-core").'</a>';
    $return .= ' | <a class="gdbbx-edit-attachment-delete" href="#" data-nonce="'.wp_create_nonce('gdbbx-del-'.$post_id.'-'.$attachment_id).'" data-id="'.$attachment_id.'" data-post="'.$post_id.'">'.__("delete", "bbp-core").'</a>';
    $return .= '</span></li>';

    return $return;
}

function gdbbx_render_check_radios( $values, $args = array(), $attr = array() ) {
	$defaults = array(
		'selected' => '',
		'name'     => '',
		'id'       => '',
		'class'    => '',
		'style'    => '',
		'multi'    => true,
		'echo'     => true,
		'readonly' => false
	);
	$args     = wp_parse_args( $args, $defaults );
	extract( $args );

	$render      = '<div class="d4p-setting-checkboxes">';
	$attributes  = array();
	$selected    = (array) $selected;
	$associative = d4p_is_array_associative( $values );
	$id          = d4p_html_id_from_name( $name, $id );

	if ( $class != '' ) {
		$attributes[] = 'class="' . esc_attr( $class ) . '"';
	}

	if ( $style != '' ) {
		$attributes[] = 'style="' . esc_attr( $style ) . '"';
	}

	if ( $readonly ) {
		$attributes[] = 'readonly';
	}

	foreach ( $attr as $key => $value ) {
		$attributes[] = $key . '="' . esc_attr( $value ) . '"';
	}

	$name = $multi ? $name . '[]' : $name;

	if ( $id != '' ) {
		$attributes[] = 'id="' . esc_attr( $id ) . '"';
	}

	if ( $name != '' ) {
		$attributes[] = 'name="' . esc_attr( $name ) . '"';
	}

	if ( $multi ) {
		$render .= '<div class="d4p-check-uncheck">';

		$render .= '<a href="#checkall" class="d4p-check-all"><i class="d4p-icon d4p-ui-check-box"></i> ' . __( "Check All", "d4plib" ) . '</a>';
		$render .= '<a href="#uncheckall" class="d4p-uncheck-all"><i class="d4p-icon d4p-ui-box"></i> ' . __( "Uncheck All", "d4plib" ) . '</a>';

		$render .= '</div>';
	}

	$render .= '<div class="d4p-content-wrapper">';
	foreach ( $values as $key => $title ) {
		$real_value = $associative ? $key : $title;
		$sel        = in_array( $real_value, $selected ) ? ' checked="checked"' : '';

		$render .= sprintf( '<label><input type="%s" id="%s" value="%s" name="%s"%s class="widefat" />%s</label>',
			$multi ? 'checkbox' : 'radio', esc_attr( $id ), esc_attr( $real_value ), esc_attr( $name ), $sel, $title );
	}
	$render .= '</div>';

	$render .= '</div>';

	if ( $echo ) {
		echo $render;
	} else {
		return $render;
	}
}
