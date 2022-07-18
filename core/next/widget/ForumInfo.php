<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ForumInfo extends Widget {
	public $widget_base = 'd4p_bbw_foruminfo';
	public $widget_class = 'gdbbx-widget gdbbx-widget-foruminfo';

	public $defaults = array(
		'title'               => 'Forum Information',
		'template'            => 'gdbbx-widget-foruminfo.php',
		'show_parent_forum'   => true,
		'show_count_topics'   => true,
		'show_count_replies'  => true,
		'show_last_post_user' => true,
		'show_last_activity'  => true,
		'show_subscribe'      => true
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "Forum Information", "bbp-core" );
		$this->widget_description = __( "Information about current forum.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function is_visible( $instance ) : bool {
		return bbp_is_single_forum();
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'forum-info-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['show_parent_forum']   = isset( $new_instance['show_parent_forum'] );
		$instance['show_count_topics']   = isset( $new_instance['show_count_topics'] );
		$instance['show_count_replies']  = isset( $new_instance['show_count_replies'] );
		$instance['show_last_post_user'] = isset( $new_instance['show_last_post_user'] );
		$instance['show_last_activity']  = isset( $new_instance['show_last_activity'] );
		$instance['show_subscribe']      = isset( $new_instance['show_subscribe'] );

		return $instance;
	}

	public function the_results( $instance ) {
		$instance = $this->instance( $instance );

		$forum_id = bbp_get_forum_id();

		$list = array();

		if ( $instance['show_parent_forum'] ) {
			$parent_forum_id = bbp_get_forum_parent_id();

			if ( $parent_forum_id > 0 ) {
				$list['show_parent_forum'] = array(
					'type'  => 'link',
					'icon'  => 'folder',
					'label' => _x( "Parent forum", "Forum Information Widget", "bbp-core" ),
					'value' => '<a href="' . bbp_get_forum_permalink( $parent_forum_id ) . '">' . bbp_get_forum_title( $parent_forum_id ) . '</a>'
				);
			}
		}

		if ( $instance['show_count_topics'] ) {
			$value = bbp_get_forum_topic_count( $forum_id );

			$list['show_count_topics'] = array(
				'type'      => 'count',
				'icon'      => 'message-text',
				'label'     => _x( "Topics", "Forum Information Widget", "bbp-core" ),
				'label_alt' => _nx( "%s topic", "%s topics", $value, "Forum Information Widget", "bbp-core" ),
				'value'     => $value
			);
		}

		if ( $instance['show_count_replies'] ) {
			$value = bbp_get_forum_reply_count( $forum_id );

			$list['show_count_replies'] = array(
				'type'      => 'count',
				'icon'      => 'message-check',
				'label'     => _x( "Replies", "Forum Information Widget", "bbp-core" ),
				'label_alt' => _nx( "%s reply", "%s replies", $value, "Forum Information Widget", "bbp-core" ),
				'value'     => $value
			);
		}

		if ( $instance['show_last_post_user'] ) {
			$last_active = bbp_get_forum_last_active_id( $forum_id );

			if ( $last_active > 0 ) {
				$list['show_last_post_user'] = array(
					'type'  => 'link',
					'icon'  => 'user',
					'label' => _x( "Last post by", "Forum Information Widget", "bbp-core" ),
					'value' => bbp_get_user_profile_link( bbp_get_reply_author_id( $last_active ) )
				);
			}
		}

		if ( $instance['show_last_activity'] ) {
			$last_active = bbp_get_forum_last_active_time( $forum_id );

			if ( ! empty( $last_active ) ) {
				$list['show_last_activity'] = array(
					'type'  => 'date',
					'icon'  => 'clock',
					'label' => _x( "Last activity", "Forum Information Widget", "bbp-core" ),
					'value' => $last_active
				);
			}
		}

		if ( is_user_logged_in() && $instance['show_subscribe'] && ! bbp_is_forum_category() ) {
			$list['show_subscribe'] = array(
				'type'  => 'action',
				'icon'  => 'bookmark',
				'label' => _x( "Actions", "Forum Information Widget", "bbp-core" ),
				'value' => bbp_get_forum_subscription_link()
			);
		}

		return apply_filters( 'gdbbx-widget-foruminfo-list', $list, $this );
	}

	public function the_render( $instance, $results = false ) {
		$instance = $this->instance( $instance );

		$template = apply_filters( 'gdbbx-widget-foruminfo-template', $instance['template'], $results, $this );

		include( gdbbx_get_template_part( $template ) );
	}
}