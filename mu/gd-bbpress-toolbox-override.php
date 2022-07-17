<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Provide list of roles that can be considered capable for moderation
add_filter( 'bbpc_moderation_roles', 'custom__moderation_roles' );
function custom__moderation_roles( $roles ) {
	return $roles;
}

// Plugin META generator tag
add_filter( 'bbpc_plugin_meta_generator', 'custom__plugin_meta_generator' );
function custom__plugin_meta_generator( $show ) {
	return $show;
}

// Signature Editor Class
add_filter( 'bbpc_signature_editor_class', 'custom__signature_editor_class' );
function custom__signature_editor_class( $class ) {
	return $class;
}

// Filter signature before it is displayed.
add_filter( 'bbpc_signature_for_display', 'custom__signature_for_display', 10, 3 );
function custom__signature_for_display( $signature, $signature_raw, $user_id ) {
	return $signature;
}

// User Stats: List of items to display
add_filter( 'bbpc_user_stats_items', 'custom__user_stats_items' );
function custom__user_stats_items( $items ) {
	return $items;
}

// User Stats: Registered On, Date Format string
add_filter( 'bbpc_user_stats_registered_date_format', 'custom__bbpc_user_stats_registered_date_format', 10 );
function custom__bbpc_user_stats_registered_date_format( $format ) {
	return $format;
}

// User Stats: Registered On
add_filter( 'bbpc_user_stats_registered_on', 'custom__bbpc_user_stats_registered_on', 10, 3 );
function custom__bbpc_user_stats_registered_on( $item, $date, $format ) {
	return $item;
}

// User Stats: Topics Count
add_filter( 'bbpc_user_stats_topics_count', 'custom__user_stats_topics_count', 10, 2 );
function custom__user_stats_topics_count( $item, $topics ) {
	return $item;
}

// User Stats: Replies Count
add_filter( 'bbpc_user_stats_replies_count', 'custom__user_stats_replies_count', 10, 2 );
function custom__user_stats_replies_count( $item, $replies ) {
	return $item;
}

// User Stats: Thanks Given
add_filter( 'bbpc_user_stats_thanks_given_count', 'custom__user_stats_thanks_given_count', 10, 2 );
function custom__user_stats_thanks_given_count( $item, $thanks ) {
	return $item;
}

// User Stats: Thanks Received
add_filter( 'bbpc_user_stats_thanks_received_count', 'custom__user_stats_thanks_received_count', 10, 2 );
function custom__user_stats_thanks_received_count( $item, $thanks ) {
	return $item;
}

// User Stats: Online Status
add_filter( 'bbpc_user_stats_online_status', 'custom__user_stats_online_status', 10, 2 );
function custom__user_stats_online_status( $item, $online ) {
	return $item;
}

// Attachment BBCode: Image Defaults
add_filter( 'bbpc_attachment_image_defaults', 'custom__attachment_image_defaults', 10, 2 );
function custom__attachment_image_defaults( $defaults, $attachment_id ) {
	$defaults = [
		'a'     => [
			'target' => '_blank',
			'rel'    => '',
			'style'  => '',
			'class'  => '',
			'title'  => '',
		],
		'img'   => [
			'width'  => '',
			'height' => '',
			'alt'    => '',
		],
		'thumb' => 'full',
	];

	return $defaults;
}

// Attachment BBCode: File Defaults
add_filter( 'bbpc_attachment_file_defaults', 'custom__attachment_file_defaults', 10, 2 );
function custom__attachment_file_defaults( $defaults, $attachment_id ) {
	$defaults = [
		'target' => '',
		'rel'    => '',
		'style'  => '',
		'class'  => '',
		'title'  => '',
	];

	return $defaults;
}

// Icon: New Replies
add_filter( 'bbpc_icon_for_new_replies', 'custom__icon_for_new_replies', 10, 2 );
function custom__icon_for_new_replies( $icon, $mode ) {
	return $icon;
}

// Icon: Private Topic
add_filter( 'bbpc_icon_for_private_topic', 'custom__icon_for_private_topic', 10, 2 );
function custom__icon_for_private_topic( $icon, $mode ) {
	return $icon;
}

// Icon: Replied to Topic
add_filter( 'bbpc_icon_for_replied_to_topic', 'custom__icon_for_replied_to_topic', 10, 2 );
function custom__icon_for_replied_to_topic( $icon, $mode ) {
	return $icon;
}

// Icon: Sticky Topic
add_filter( 'bbpc_icon_for_sticky_topic', 'custom__icon_for_sticky_topic', 10, 2 );
function custom__icon_for_sticky_topic( $icon, $mode ) {
	return $icon;
}

// Icon: Locked Topic
add_filter( 'bbpc_icon_for_locked_topic', 'custom__icon_for_locked_topic', 10, 2 );
function custom__icon_for_locked_topic( $icon, $mode ) {
	return $icon;
}

// Icon: Attchments in Topic
add_filter( 'bbpc_icon_for_attachments', 'custom__icon_for_attachments', 10, 3 );
function custom__icon_for_attachments( $icon, $count, $mode ) {
	return $icon;
}

// Badge: New Topic
add_filter( 'bbpc_topic_badge_new', 'custom__topic_badge_new', 10, 3 );
function custom__topic_badge_new( $badge, $topic_id, $data ) {
	return $badge;
}

// Badge: Unread Topic
add_filter( 'bbpc_topic_badge_unread', 'custom__topic_badge_unread', 10, 3 );
function custom__topic_badge_unread( $badge, $topic_id, $data ) {
	return $badge;
}

// Badge: Topic new replies
add_filter( 'bbpc_topic_badge_new_replies', 'custom__topic_badge_new_replies', 10, 4 );
function custom__topic_badge_new_replies( $badge, $topic_id, $url, $data ) {
	return $badge;
}
