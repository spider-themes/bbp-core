<?php
add_action( 'init', 'btv_load_textdomain' );
add_action( 'wp', 'btv_session' );
add_action( 'bbp_theme_after_topic_title', 'btv_add_count', 99 );
add_action( 'bbp_theme_before_topic_started_by', 'btv_add_count' );
add_action( 'bbp_register_views', 'btv_register_views' );
add_action( 'bbp_register_admin_settings', 'btv_register_admin_settings' );
add_filter( 'plugin_action_links', 'btv_add_settings_link', 11, 2 );

/**
 * Register the textdomain for the plugin
 */
function btv_load_textdomain() {
	load_plugin_textdomain( 'bbp-topic-views', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Checks if the add to title option is enabled
 *
 * @param $default bool Optional. Default value
 *
 * @return bool Is the option enabled or not
 * @uses get_option() To get the add to title option
 */
function btv_add_to( $default = 'meta' ) {
	return get_option( '_btv_add_to', $default );
}

/**
 * Checks if the add to title option is enabled
 *
 * @param $default bool Optional. Default value
 *
 * @return bool Is the option enabled or not
 * @uses get_option() To get the add to title option
 */
function btv_add_to_verification( $option = 'meta' ) {
	return in_array( $option, array( 'title', 'meta', 'nowhere' ) ) ? $option : 'meta';
}

/**
 * Force bbpress to open a session, if it hasn't already, which will help to
 * avoid double-counting views (see below). Also update the view count.
 *
 * @uses bbp_is_single_topic() To check if it's the topic page
 * @uses bbp_get_topic_id() To get the topic id
 * @uses btv_get_topic_view_count() To get the view count for the topic
 * @uses bbp_get_topic_post_count() To get the topic post count
 * @uses bbp_get_topic_hidden_reply_count() To get the hidden reply count
 * @uses update_post_meta() To update the view count of the topic
 * @return int The new view count
 */
function btv_session() {
	if ( ! bbp_is_single_topic() ) {
		return;
	}

	// Only start session if not already stared and it's a topic page
	if ( ! session_id() )
		// eaccelerator_set_session_handlers();	//
		// @session_cache_limiter( 'public' );	// allows back button to work without losing form data - update: bad idea, causes other problems
	{
		session_start();
	}

	$topic_id = bbp_get_topic_id();

	if ( empty( $topic_id ) || ( ! empty( $_SESSION['last_topic_id'] ) && $topic_id == $_SESSION['last_topic_id'] ) ) {
		return;
	}

	$view_count = (int) btv_get_topic_view_count( $topic_id );

	// If the view count is empty, then set it to the number of posts it has
	// Because the topic would have been viewed at least that number of times.
	// This is good for already well-established bbPress forums, who later want to install this plugin.
	if ( empty( $view_count ) ) {
		$post_count = bbp_get_topic_post_count( $topic_id );

		if ( $post_count > 1 ) {
			$view_count = $post_count + bbp_get_topic_reply_count_hidden( $topic_id );
		}
	}

	$view_count ++;

	update_post_meta( $topic_id, '_btv_view_count', $view_count );

	// Sets the session variable so it is there the next time we view a topics page
	$_SESSION['last_topic_id'] = $topic_id;

	return $view_count;

}

/**
 * This function adds the view count to the end of the title on the front,
 * forum, tags and view pages.  You can comment this out if you want to use the
 * {@link btv_topic_view_count()} to place the view count somewhere else
 * instead.
 *
 * @param string $title Topic Title to which the count is to be added
 *
 * @return string Topic Title
 * @uses bbp_is_query_name() To check if we're not currently in a widget query
 * @uses bbp_is_single_forum() To check if it's a forum page
 * @uses bbp_is_topic_archive() To check if it's a topic archive
 * @uses bbp_is_topic_tag() To check if it's a topic tag page
 * @uses bbp_is_single_view() To check if it's a view page
 * @uses btv_add_to() To check if the add to title option is enabled
 * @uses bbp_get_topic_id() To get the topic id
 * @uses btv_get_topic_view_count() To get the view count for the topic
 */
function btv_add_count() {
	if ( bbp_is_query_name( 'bbp_widget' ) || ( ! bbp_is_single_forum() && ! bbp_is_topic_archive() && ! bbp_is_topic_tag() && ! bbp_is_single_view() ) ) {
		return;
	}

	$option = btv_add_to();

	if ( 'nowhere' == $option ) {
		return;
	}

	$view_count = btv_get_topic_view_count();

	switch ( current_filter() ) {

		// Topic title
		case 'bbp_theme_after_topic_title' :
			if ( 'title' == $option ) {
				printf( '<small><em>(' . _n( '%d view', '%d views', $view_count, 'bbp-topic-views' ) . ')</em></small>', $view_count );
			}
			break;

		// Topic meta
		case 'bbp_theme_before_topic_started_by' :
			if ( 'meta' == $option ) {
				printf( ' ' . __( 'Views: %d', 'bbp-topic-views' ), $view_count );
			}
			break;
	}
}

/**
 * Output the topic view count.
 *
 * @param int $topic_id Optional. Topic id
 *
 * @uses bbp_get_topic_id() To get the topic id
 * @uses btv_get_topic_view_count() To get the view count for the topic
 */
function btv_topic_view_count( $topic_id = 0 ) {
	$topic_id   = bbp_get_topic_id( $topic_id );
	$view_count = btv_get_topic_view_count( $topic_id );
	echo $view_count;
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
function btv_get_topic_view_count( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );

	if ( empty( $topic_id ) ) {
		return 0;
	}

	$views = (int) get_post_meta( $topic_id, '_btv_view_count', true );

	return $views;
}

/**
 * Register our custom view for topic views
 *
 * @uses bbp_register_view() To register the view
 */
function btv_register_views() {

	// Topics arranged in the order of most views
	$most_views = array(
		'meta_key' => '_btv_view_count',
		'order'    => 'desc'
	);

	bbp_register_view( 'btv-most-viewed', __( 'Topics with most views', 'bbp-topic-views' ), $most_views );
}

/**
 * Add Settings link to plugins area
 *
 * @param array $links Links array in which we would prepend our link
 * @param string $file Current plugin basename
 *
 * @return array Processed links
 */
function btv_add_settings_link( $links, $file ) {
	if ( plugin_basename( __FILE__ ) == $file ) {
		$settings_link = '<a href="' . add_query_arg( array( 'page' => 'bbpress' ), admin_url( 'options-general.php' ) ) . '#_btv_add_to">' . __( 'Settings', 'btv-topic-views' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Register the settings
 *
 * @uses add_settings_section() To add our own settings section
 * @uses add_settings_field() To add various settings fields
 * @uses register_setting() To register various settings
 */
function btv_register_admin_settings() {

	// Add the topic views section
	add_settings_section( 'bbp_btv', __( 'bbP Topic Views', 'bbp-topic-views' ), 'btv_admin_setting_callback_btv_section', 'bbpress' );

	// Add to title setting
	add_settings_field( '_btv_add_to', __( 'Where to add the count on forum, topic archive, tag & view pages', 'bbp-topic-views' ), 'btv_admin_setting_callback_add_to', 'bbpress', 'bbp_btv' );
	register_setting( 'bbpress', '_btv_add_to', 'btv_add_to_verification' );
}

/**
 * Settings section description
 */
function btv_admin_setting_callback_btv_section() {
	?>

    <p id="btv-option-section"><?php _e( 'Settings for the bbP Topic Views Plugin', 'bbp-topic-views' ); ?></p>

	<?php
}

/**
 * Add count to setting field
 *
 * @uses checked() To display the checked attribute
 * @uses btv_add_to() To check if the add to title option is enabled
 */
function btv_admin_setting_callback_add_to() {
	?>

    <input id="_btv_add_to_title" name="_btv_add_to" type="radio"
           value="title"<?php checked( btv_add_to(), 'title' ); ?> />
    <label for="_btv_add_to_title"><?php _e( 'After the topic title', 'bbp-topic-views' ); ?></label><br/>

    <input id="_btv_add_to_meta" name="_btv_add_to" type="radio"
           value="meta"<?php checked( btv_add_to(), 'meta' ); ?> />
    <label for="_btv_add_to_meta"><?php _e( 'Before the topic meta', 'bbp-topic-views' ); ?></label><br/>

    <input id="_btv_add_to_nowhere" name="_btv_add_to" type="radio"
           value="nowhere"<?php checked( btv_add_to(), 'nowhere' ); ?> />
    <label for="_btv_add_to_nowhere"><?php printf( __( 'Add nowhere (<a href="%s">custom configuration</a>)', 'bbp-topic-views' ), 'http://wordpress.org/extend/plugins/bbp-topic-views/other_notes/' ); ?></label>

	<?php
}

?>