<?php

/**
 * Get forum title
 * @return string
 */
function bbpc_forum_title(){
    $forum_id       = bbp_get_forum_id();
    $forum_title    = get_the_title( $forum_id );
    return $forum_title;
}

/**
 * Get the value of a settings field.
 *
 * @param string $option  settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function bbpc_get_opt( $option, $default = '' ) {
	$options = get_option( 'bbp_core_settings' );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}