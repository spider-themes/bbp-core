<?php

use SpiderDevs\Plugin\BBPC\Basic\BB;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Basic\Statistics;
use SpiderDevs\Plugin\BBPC\Tasks\Recalculations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @deprecated since version 6.7, to be removed in 7.0
 */
function bbpc_get_active_bbcodes( $what = 'codes', $return = 'active', $class = 'all' ) {
	_deprecated_function( __FUNCTION__, '6.7' );

	return false;
}

/**
 * @deprecated since version 6.7, to be removed in 7.0
 */
function bbpc_module_bbcodes() {
	_deprecated_function( __FUNCTION__, '6.7' );

	return false;
}

/**
 * @deprecated since version 6.0, to be removed in 7.0
 */
function bbpc_enqueue_files_force() {
	_deprecated_function( __FUNCTION__, '6.0', 'Enqueue::instance()->core()' );

	Enqueue::instance()->core();
}

/**
 * @deprecated since version 6.0, to be removed in 7.0
 */
function bbpc_settings() {
	_deprecated_function( __FUNCTION__, '6.0', 'bbpc()' );

	return bbpc();
}

/**
 * @deprecated since version 6.5, to be removed in 7.0
 */
function bbpc_get_statistics() : array {
	_deprecated_function( __FUNCTION__, '6.5', '\Dev4Press\Plugin\BBPC\Basic\Statistics::instance()->forums_stats()' );

	return Statistics::instance()->forums_stats();
}

/**
 * @deprecated since version 6.5, to be removed in 7.0
 */
function bbpc_get_user_counts() : array {
	_deprecated_function( __FUNCTION__, '6.5', '\Dev4Press\Plugin\BBPC\Basic\Statistics::instance()->user_counts()' );

	return Statistics::instance()->user_counts();
}

/**
 * @deprecated since version 6.5, to be removed in 7.0
 */
function bbpc_list_of_statistics_elements() : array {
	_deprecated_function( __FUNCTION__, '6.5', '\Dev4Press\Plugin\BBPC\Basic\Statistics::instance()->forums_stats_elements()' );

	return Statistics::instance()->forums_stats_elements();
}

/**
 * @deprecated since version 6.5, to be removed in 7.0
 */
function bbpc_loader() {
	_deprecated_function( __FUNCTION__, '6.5' );

	return bbpc_plugin();
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_recalculate_subforums_counts() {
	_deprecated_function( __FUNCTION__, '6.6', '\Dev4Press\Plugin\BBPC\Tasks\Recalculations::instance()->sub_forums_counts()' );

	Recalculations::instance()->sub_forums_counts();
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbp_wp_editor_status( $status = true ) {
	_deprecated_function( __FUNCTION__, '6.6' );

	update_option( '_bbp_use_wp_editor', $status );
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_get_all_topic_statuses() : array {
	_deprecated_function( __FUNCTION__, '6.6' );

	return [
		bbp_get_private_status_id(),
		bbp_get_public_status_id(),
		bbp_get_hidden_status_id(),
		bbp_get_closed_status_id(),
	];
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_get_forum_id() {
	_deprecated_function( __FUNCTION__, '6.6', '\Dev4Press\Plugin\BBPC\Basic\BB::i()->get_forum_id()' );

	return BB::i()->get_forum_id();
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_mime_types_list() : array {
	_deprecated_function( __FUNCTION__, '6.6', '\Dev4Press\Plugin\BBPC\Basic\BB::i()->get_mime_types_list()' );

	return BB::i()->get_mime_types_list();
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_is_bbpress_post_type( $post_type ) {
	_deprecated_function( __FUNCTION__, '6.6', '\Dev4Press\Plugin\BBPC\Basic\BB::i()->is_bbpress_post_type()' );

	return BB::i()->is_bbpress_post_type( $post_type );
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_get_last_active_time( $topic_id ) {
	_deprecated_function( __FUNCTION__, '6.6' );

	$last_active = get_post_meta( $topic_id, '_bbp_last_active_time', true );

	if ( empty( $last_active ) ) {
		$reply_id = bbp_get_topic_last_reply_id( $topic_id );

		if ( ! empty( $reply_id ) ) {
			$last_active = get_post_field( 'post_date', $reply_id );
		} else {
			$last_active = get_post_field( 'post_date', $topic_id );
		}
	}

	return $last_active;
}

/**
 * @deprecated since version 6.6, to be removed in 7.0
 */
function bbpc_bbpress_version( $ret = 'code' ) {
	_deprecated_function( __FUNCTION__, '6.6' );

	if ( ! bbpc_has_bbpress() ) {
		return null;
	}

	$version = bbp_get_version();

	if ( isset( $version ) ) {
		if ( $ret == 'code' ) {
			return substr( str_replace( '.', '', $version ), 0, 2 );
		} else {
			return $version;
		}
	}

	return null;
}
