<?php
/**
 * Check If the Page is Forum page
 */
function bbpc_is_forum_page() {
	if ( in_array( 'bbpress', get_body_class() ) ) {
		return true;
	}
}

/**
 * Posts Arraty
 * @param object Post Type
 */
function bbp_core_get_posts( $post_type = 'forum' ) {
	$posts = get_pages(
		[
			'post_type' => $post_type,
			'parent'    => 0,
		]
	);

	$posts_array = [];

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$posts_array[ $post->ID ] = $post->post_title;
		}
	}

	return $posts_array;
}

/**
 * Limit letter
 * @param $string
 * @param $limit_length
 * @param string $suffix
 */
function bbp_core_limit_letter( $string, $limit_length, $suffix = '...' ) {
	if ( strlen( $string ) > $limit_length ) {
		echo strip_shortcodes( substr( $string, 0, $limit_length ) . $suffix );
	} else {
		echo strip_shortcodes( esc_html( $string ) );
	}
}

/**
 * Return the topic view count.
 *
 * @param int $topic_id Optional. Topic id
 *
 * @return int The view count
 * @uses get_post_meta() To get the view count meta
 * @uses bbp_get_topic_id() To get the topic id
 */
function bbp_get_topic_view_count( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );

	if ( empty( $topic_id ) ) {
		return 0;
	}

	$views = (int) get_post_meta( $topic_id, '_btv_view_count', true );

	return $views;
}

/**
 * Output the topic view count.
 *
 * @param int $topic_id Optional. Topic id
 *
 * @uses bbp_get_topic_id() To get the topic id
 * @uses btv_get_topic_view_count() To get the view count for the topic
 */
function bbp_topic_view_count( $topic_id = 0 ) {
	$topic_id   = bbp_get_topic_id( $topic_id );
	$view_count = bbp_get_topic_view_count( $topic_id );
	return $view_count;
}



// /**
// 	 * Limit letter
// 	 *
// 	 * @param $string
// 	 * @param $limit_length
// 	 * @param string       $suffix
// 	 */
// 	function bbp_core_limit_letter( $string, $limit_length, $suffix = '...' ) {
// 		if ( strlen( $string ) > $limit_length ) {
// 			echo strip_shortcodes( substr( $string, 0, $limit_length ) . $suffix );
// 		} else {
// 			echo strip_shortcodes( esc_html( $string ) );
// 		}
// 	}