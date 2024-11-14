<?php

// Select docs page
$args = array(
	'post_type'      => 'topic',
	'posts_per_page' => 1,
	'orderby'        => 'menu_order',
	'order'          => 'desc'
);

$topic_permalink    = '';
foreach ( get_posts( $args ) as $post ) {
	$topic_permalink = get_permalink( $post->ID );
}

$forum_url 	= admin_url('customize.php?url=') . site_url( '/' ) . get_option( '_bbp_root_slug' ) . '?autofocus[panel]=bbp-core-settings&autofocus[section]=forum-archive-page';
$topic_url  = admin_url( 'customize.php?url=' ) . $topic_permalink . '?autofocus[panel]=bbp-core-settings&autofocus[section]=topics_fields';

CSF::createSection( $prefix, array(
	'id'     => 'design_fields',
	'title'  => esc_html__( 'Customizer', 'eazydocs' ),
	'fields' => [

		array(
			'id'         => 'customizer_visibility',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Options Visibility on Customizer', 'eazydocs' ),
			'text_on'    => esc_html__( 'Enabled', 'eazydocs' ),
			'text_off'   => esc_html__( 'Disabled', 'eazydocs' ),
			'text_width' => 100
		),

		array(
			'type'       => 'content',
			'content'    => '<a href="' . esc_url( $forum_url ) . '" target="_blank" id="bbpc_forum_option_link">' . esc_html__( 'Forum', 'eazydocs' ) . '</a> '.'<a href="' . esc_url( $topic_url ) . '" target="_blank" id="bbpc_topic_option_link">' . esc_html__( 'Topic', 'eazydocs' ) . '</a>',
			'dependency' => array(
				array( 'customizer_visibility', '==', true ),
			),
		)
	]
) );