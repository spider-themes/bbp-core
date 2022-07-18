<?php

use Dev4Press\Plugin\GDBBX\Attachments\Display;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

function gdbbx_attachments_display_disable() {
    if (gdbbx_is_module_loaded('attachments')) {
    	Display::instance()->remove_content_filters();
    }
}

function gdbbx_attachments_display_enable() {
    if (gdbbx_is_module_loaded('attachments')) {
        Display::instance()->run();
    }
}

function gdbbx_front_display_welcome() {
    if ( Plugin::instance()->is_enabled('forum-index')) {
        gdbbx_forum_index()->welcome_index();
    }
}

function gdbbx_front_display_statistics() {
    if ( Plugin::instance()->is_enabled('forum-index')) {
        gdbbx_forum_index()->forum_index();
    }
}

function gdbbx_can_user_moderate() {
    $roles = apply_filters('gdbbx_moderation_roles', array('bbp_keymaster', 'bbp_moderator'));

    if (is_user_logged_in()) {
        if (is_super_admin()) {
            return true;
        } else {
            global $current_user;

            if (is_array($current_user->roles)) {
                $matched = array_intersect($current_user->roles, $roles);
                return !empty($matched);
            }
        }
    }

    return false;
}

function gdbbx_current_user_can_moderate() {
    return current_user_can('moderate');
}

function gdbbx_is_current_user_bbp_moderator() {
    return d4p_is_current_user_roles(bbp_get_moderator_role());
}

function gdbbx_is_current_user_bbp_keymaster() {
    return d4p_is_current_user_roles(bbp_get_keymaster_role());
}

function gdbbx_check_if_user_replied_to_topic($topic_id = 0, $user_id = 0) {
    return gdbbx_db()->user_replied_to_topic($topic_id, $user_id);
}

function gdbbx_check_if_user_said_thanks_to_topic($topic_id = 0, $user_id = 0) {
    return gdbbx_db_cache()->thanks_given($topic_id, $user_id);
}

function gdbbx_get_topic_id_from_slug($slug) {
    $slug = esc_sql($slug);
    $slug = sanitize_title_for_query($slug);
    $post_type = bbp_get_topic_post_type();

    return gdbbx_db()->get_id_from_slug($slug, $post_type);
}

function gdbbx_get_topic_last_reply_time($topic_id = 0, $format = 'G') {
    $topic_id = bbp_get_topic_id($topic_id);
    $reply_id = bbp_get_topic_last_reply_id($topic_id);

    $active = !empty($reply_id) ? get_post_field('post_date_gmt', $reply_id) : get_post_field('post_date_gmt', $topic_id);

    return mysql2date($format, $active);
}

function gdbbx_get_topic_post_time($topic_id = 0, $format = 'G') {
    $topic_id = bbp_get_topic_id($topic_id);

    $active = get_post_field('post_date_gmt', $topic_id);

    return mysql2date($format, $active);
}

function gdbbx_get_user_display_name( $user_id = 0 ) {
	if ( $user_id == 0 ) {
		$user_id = bbp_get_current_user_id();

		if ( $user_id > 0 ) {
			$author_name = get_the_author_meta( 'display_name', $user_id );

			if ( empty( $author_name ) ) {
				$author_name = get_the_author_meta( 'user_login', $user_id );
			}

			return $author_name;
		}
	}

	return '';
}

function gdbbx_get_topic_thumbnail( $topic_id = 0 ) {
	$topic = bbp_get_topic_id( $topic_id );

	$thumb_size = apply_filters( 'gdbbx_topic_thumbnail_size', 'thumbnail', $topic );

	$img = d4p_get_thumbnail_url( $topic, $thumb_size );

	if ( $img == '' ) {
		$post = get_post();

		$matches = array();
		$match   = array();

		if ( preg_match_all( "/<img(.+?)>/", $post->post_content, $matches ) ) {
			foreach ( $matches[1] as $image ) {
				if ( preg_match( '/src=(["\'])(.*?)\1/', $image, $match ) ) {
					return $match[2];
				}
			}
		}

		return '';
	}

	return $img;
}

function gdbbx_is_bbcodes_toolbar_available() : bool {
	return Plugin::instance()->is_enabled('bbcodes');
}