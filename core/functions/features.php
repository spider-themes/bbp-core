<?php

use SpiderDevs\Plugin\BBPC\Basic\Plugin;
use SpiderDevs\Plugin\BBPC\Basic\Posts;
use SpiderDevs\Plugin\BBPC\Features\BBCodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bbpc_get_template_part( $name ) {
	$stack = bbp_get_template_stack();

	$found = false;
	foreach ( $stack as $path ) {
		if ( file_exists( trailingslashit( $path ) . $name ) ) {
			$found = trailingslashit( $path ) . $name;
			break;
		}
	}

	if ( $found === false ) {
		$found = BBPC_PATH . 'templates/default/widgets/' . $name;

		if ( ! file_exists( $found ) ) {
			$found = BBPC_PATH . 'templates/default/bbpress/' . $name;
		}
	}

	return $found;
}

function bbpc_get_user_roles() : array {
	$roles = [];

	$dynamic_roles = bbp_get_dynamic_roles();

	foreach ( $dynamic_roles as $role => $obj ) {
		$roles[ $role ] = bbp_translate_user_role( $obj['name'] );
	}

	return $roles;
}

function bbpc_list_user_roles() : array {
	$dynamic_roles = bbp_get_dynamic_roles();

	return array_keys( $dynamic_roles );
}

function bbpc_get_new_topics( $timestamp, $offset = 0, $limit = 1000 ) : array {
	$topics = [];
	$list   = Posts::instance()->get_new_posts(
		[
			'timestamp' => $timestamp,
			'offset'    => $offset,
			'limit'     => $limit,
		]
	);

	foreach ( $list as $item ) {
		$topics[] = $item['type'] == bbp_get_topic_post_type() ? $item['id'] : $item['parent'];
	}

	return $topics;
}

function bbpc_get_post_attachments( $post_id ) : array {
	$ids = bbpc_cache()->attachments_get_attachments_ids( $post_id );

	if ( empty( $ids ) ) {
		return [];
	}

	$args = apply_filters(
		'bbpc_get_post_attachments_args',
		[
			'post_type'           => 'attachment',
			'numberposts'         => - 1,
			'post_status'         => null,
			'post__in'            => $ids,
			'orderby'             => 'ID',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
		]
	);

	return get_posts( $args );
}

function bbpc_default_forum_settings() : array {
	return [
		'attachments_status'                   => 'inherit',
		'attachments_hide_from_visitors'       => 'inherit',
		'attachments_preview_for_visitors'     => 'inherit',
		'attachments_topic_form'               => 'inherit',
		'attachments_reply_form'               => 'inherit',
		'attachments_max_file_size_override'   => 'inherit',
		'attachments_max_file_size'            => 512,
		'attachments_max_to_upload_override'   => 'inherit',
		'attachments_max_to_upload'            => 4,
		'attachments_mime_types_list_override' => 'inherit',
		'attachments_mime_types_list'          => [],
		'topic_auto_close_after_active'        => 'inherit',
		'topic_auto_close_after_notice'        => 'inherit',
		'topic_auto_close_after_days'          => '',
		'privacy_lock_topic_form'              => 'inherit',
		'privacy_lock_topic_form_message'      => '',
		'privacy_lock_reply_form'              => 'inherit',
		'privacy_lock_reply_form_message'      => '',
		'privacy_enable_topic_private'         => 'inherit',
		'privacy_enable_reply_private'         => 'inherit',
	];
}

function bbpc_get_online_users_list( $limit = 0, $avatar = true, $show = 'profile_link', $before = '<span class="bbpc-online-user">', $after = '</span>' ) : array {
	$online = bbpc_module_online()->online();

	$_users = [];
	foreach ( $online['roles'] as $role => $users ) {
		if ( ! empty( $users ) && $limit > 0 ) {
			$users = array_slice( $users, 0, $limit );
		}

		if ( ! empty( $users ) ) {
			foreach ( $users as $user ) {
				switch ( $show ) {
					default:
					case 'profile_link':
						$item = bbp_get_user_profile_link( $user );
						break;
					case 'display_name':
						$u    = get_userdata( $user );
						$item = $u->display_name;
						break;
				}

				if ( $avatar ) {
					$item = get_avatar( $user, '16' ) . ' ' . $item;
				}

				$_users[ $role ][] = $before . $item . $after;
			}
		}
	}

	return $_users;
}

function bbpc_update_shorthand_bbcodes( $content ) {
	$bbcodes = [
		'quote',
		'topic',
		'reply',
		'url',
		'email',
		'size',
		'color',
		'area',
		'anchor',
		'hide',
		'img',
		'embed',
		'youtube',
		'vimeo',
	];

	foreach ( $bbcodes as $bbc ) {
		if ( strpos( $content, '[' . $bbc . '=' ) !== false ) {
			$content = str_replace( '[' . $bbc . '=', '[' . $bbc . ' ' . $bbc . '=', $content );
		}
	}

	return $content;
}

function bbpc_get_forum_children_ids( $forum_id ) {
	bbpc_cache()->tracking_run_bulk_forums();

	$ids = bbpc_cache()->get( 'forums-parent-child', $forum_id, [] );

	if ( ! empty( $ids ) ) {
		$children = [];

		foreach ( $ids as $id ) {
			if ( $list = bbpc_get_forum_children_ids( $id ) ) {
				$children = array_merge( $children, $list );
			}
		}

		$ids = array_merge( $ids, $children );
	}

	return $ids;
}

function bbpc_get_keymasters() : array {
	$users = get_users(
		[
			'role__in' => bbp_get_keymaster_role(),
		]
	);

	return (array) apply_filters( 'bbpc_get_keymasters', $users );
}

function bbpc_get_moderators() : array {
	$users = get_users(
		[
			'role__in' => bbp_get_moderator_role(),
		]
	);

	return (array) apply_filters( 'bbpc_get_moderators', $users );
}

function bbpc_get_attachment_id_from_name( $file ) : int {
	if ( empty( $file ) ) {
		return 0;
	} elseif ( is_numeric( $file ) ) {
		return absint( $file );
	} else {
		$file = strtolower( sanitize_file_name( $file ) );

		$id = bbpc_db()->get_attachment_id_from_name( $file );

		if ( $id > 0 ) {
			return $id;
		}

		return bbpc_db()->get_attachment_id_from_name_alt( $file );
	}
}

function bbpc_topic_thread_has_attachments( $topic_id ) : bool {
	return bbpc_cache()->attachments_has_topic_attachments( $topic_id ) > 0;
}

function bbpc_topic_thread_get_attachments( $topic_id ) : array {
	return bbpc_db()->get_topic_thread_attachments_ids( $topic_id );
}

function bbpc_render_select_dropdown( $values, $selected, $args = [] ) {
	$defaults = [
		'id'    => '',
		'name'  => '',
		'class' => 'bbp_dropdown',
	];

	$args = wp_parse_args( $args, $defaults );

	echo '<select class="' . esc_attr( $args['class'] ) . '" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '">';

	foreach ( $values as $key => $label ) :
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $selected, false ) . '>' . esc_html( $label ) . '</option>';
	endforeach;

	echo '</select>';
}

function bbpc_get_wordpress_user_roles() : array {
	global $wp_roles;

	$roles = [];

	foreach ( $wp_roles->role_names as $role => $title ) {
		$roles[ $role ] = $title;
	}

	return $roles;
}

function bbpc_render_the_bbcode( $name, $atts, $content = null ) : string {
	if ( ! Plugin::instance()->is_enabled( 'bbcodes' ) ) {
		return '';
	}

	return BBCodes::instance()->render( $name, $atts, $content );
}
