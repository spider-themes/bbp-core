<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gdbbx_has_bbpress() : bool {
	if ( function_exists( 'bbp_get_version' ) ) {
		$version = bbp_get_version();
		$version = intval( substr( str_replace( '.', '', $version ), 0, 2 ) );

		return $version > 25;
	} else {
		return false;
	}
}

function gdbbx_has_buddypress() : bool {
	if ( d4p_is_plugin_active( 'buddypress/bp-loader.php' ) && function_exists( 'bp_get_version' ) ) {
		return version_compare( bp_get_version(), '7.0', '>=' );
	} else {
		return false;
	}
}

function gdbbx_is_bbpress() : bool {
	$is = gdbbx_has_bbpress() && is_bbpress();

	return (bool) apply_filters( 'gdbbx_is_bbpress', $is );
}

function bbp_form_reply_title() {
	echo bbp_get_form_reply_title();
}

function bbp_get_form_reply_title() {
	$reply_title = '';

	if ( bbp_is_post_request() && isset( $_POST['bbp_reply_title'] ) ) {
		$reply_title = $_POST['bbp_reply_title'];
	} else if ( bbp_is_reply_edit() ) {
		$reply_title = bbp_get_global_post_field( 'post_title', 'raw' );
	}

	return apply_filters( 'bbp_get_form_reply_title', esc_attr( $reply_title ) );
}

function gdbbx_is_feed() : bool {
	return is_feed() || gdbbx_feed()->is_feed;
}

function gdbbx_no_robots() {
	if ( GDBBX_WPV > 56 ) {
		add_filter( 'wp_robots', 'wp_robots_no_robots' );
	} else {
		wp_no_robots();
	}
}

function gdbbx_get_reply_title( $reply_id ) : string {
	return bbp_get_reply_title_fallback( bbp_get_reply_title( $reply_id ), $reply_id );
}
