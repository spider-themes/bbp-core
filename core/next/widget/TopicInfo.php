<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TopicInfo extends Widget {
	public $widget_base = 'd4p_bbw_topicinfo';
	public $widget_class = 'gdbbx-widget gdbbx-widget-topicinfo';

	public $defaults = array(
		'title'                   => 'Topic Information',
		'template'                => 'gdbbx-widget-topicinfo.php',
		'show_forum'              => true,
		'show_author'             => true,
		'show_post_date'          => true,
		'show_last_activity'      => true,
		'show_status'             => true,
		'show_count_replies'      => true,
		'show_count_voices'       => true,
		'show_participants'       => true,
		'show_subscribe_favorite' => false
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "Topic Information", "bbp-core" );
		$this->widget_description = __( "Information about current topic.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function is_visible( $instance ) : bool {
		return bbp_is_single_topic();
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'topic-info-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['show_forum']              = isset( $new_instance['show_forum'] );
		$instance['show_author']             = isset( $new_instance['show_author'] );
		$instance['show_post_date']          = isset( $new_instance['show_post_date'] );
		$instance['show_last_activity']      = isset( $new_instance['show_last_activity'] );
		$instance['show_status']             = isset( $new_instance['show_status'] );
		$instance['show_count_replies']      = isset( $new_instance['show_count_replies'] );
		$instance['show_count_voices']       = isset( $new_instance['show_count_voices'] );
		$instance['show_participants']       = isset( $new_instance['show_participants'] );
		$instance['show_subscribe_favorite'] = isset( $new_instance['show_subscribe_favorite'] );

		return $instance;
	}

	public function the_results( $instance ) {
		$instance = $this->instance( $instance );

		$topic_id = bbp_get_topic_id();

		$list = array();

		if ( $instance['show_forum'] ) {
			$forum_id = bbp_get_topic_forum_id();

			if ( bbp_get_forum_permalink( $forum_id ) ) {
				$list['show_forum'] = array(
					'type'  => 'link',
					'icon'  => 'folder',
					'label' => __( "Forum", "bbp-core" ),
					'value' => '<a href="' . bbp_get_forum_permalink( $forum_id ) . '">' . bbp_get_forum_title( $forum_id ) . '</a>'
				);
			}
		}

		if ( $instance['show_author'] ) {
			$list['show_author'] = array(
				'type'  => 'link',
				'icon'  => 'user',
				'label' => _x( "Author", "Topic Information Widget", "bbp-core" ),
				'value' => bbp_get_topic_author_link( array( 'type' => 'name' ) )
			);
		}

		if ( $instance['show_post_date'] ) {
			$post_date = get_post_field( 'post_date', $topic_id );

			$list['show_post_date'] = array(
				'type'  => 'date',
				'icon'  => 'calendar',
				'label' => _x( "Posted", "Topic Information Widget", "bbp-core" ),
				'value' => bbp_get_time_since( bbp_convert_date( $post_date ) )
			);
		}

		if ( $instance['show_last_activity'] ) {
			$list['show_last_activity'] = array(
				'type'  => 'date',
				'icon'  => 'clock',
				'label' => _x( "Last activity", "Topic Information Widget", "bbp-core" ),
				'value' => bbp_get_topic_last_active_time( $topic_id )
			);
		}

		if ( $instance['show_status'] ) {
			$list['show_status'] = array(
				'type'  => 'text',
				'icon'  => 'thumbtack',
				'label' => _x( "Status", "Topic Information Widget", "bbp-core" ),
				'value' => bbp_is_topic_open( $topic_id ) ? __( "Open", "bbp-core" ) : __( "Closed", "bbp-core" )
			);
		}

		if ( $instance['show_count_replies'] ) {
			$value = bbp_get_topic_reply_count( $topic_id );

			$list['show_count_replies'] = array(
				'type'      => 'count',
				'icon'      => 'message-check',
				'label'     => _x( "Replies", "Topic Information Widget", "bbp-core" ),
				'label_alt' => _nx( "%s reply", "%s replies", $value, "Topic Information Widget", "bbp-core" ),
				'value'     => $value
			);
		}

		if ( $instance['show_count_voices'] ) {
			$value = bbp_get_topic_voice_count( $topic_id );

			$list['show_count_voices'] = array(
				'type'      => 'count',
				'icon'      => 'users',
				'label'     => _x( "Voices", "Topic Information Widget", "bbp-core" ),
				'label_alt' => _nx( "%s voice", "%s voices", $value, "Topic Information Widget", "bbp-core" ),
				'value'     => $value
			);
		}

		if ( $instance['show_participants'] && bbp_get_topic_voice_count( $topic_id ) > 1 ) {
			$users = gdbbx_db()->get_topic_participants( $topic_id );

			$participants = array();
			foreach ( $users as $id ) {
				if ( get_userdata( $id ) !== false ) {
					$participants[] = bbp_get_user_profile_link( $id );
				}
			}

			$list['show_participants'] = array(
				'type'  => 'list',
				'icon'  => 'users',
				'label' => _x( "Participants", "Topic Information Widget", "bbp-core" ),
				'value' => join( ', ', $participants )
			);
		}

		if ( is_user_logged_in() && $instance['show_subscribe_favorite'] ) {
			$list['show_subscribe_favorite'] = array(
				'type'  => 'action',
				'icon'  => 'bookmark',
				'label' => _x( "Actions", "Forum Information Widget", "bbp-core" ),
				'value' => bbp_get_topic_subscription_link( array(
						'before' => '',
						'after'  => ''
					) ) . '<br/>' . bbp_get_topic_favorite_link( array( 'before' => '', 'after' => '' ) )
			);
		}

		$list = apply_filters( 'gdbbx-widget-topicinfo-list', $list, $this );

		return $list;
	}

	public function the_render( $instance, $results = false ) {
		$template = apply_filters( 'gdbbx-widget-topicinfo-template', $instance['template'], $results, $this );
		include( gdbbx_get_template_part( $template ) );
	}
}
