<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;
use Dev4Press\Plugin\GDBBX\Basic\Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NewPosts extends Widget {
	public $results_cachable = true;

	public $widget_base = 'd4p_bbw_newposts';
	public $widget_class = 'gdbbx-widget gdbbx-widget-newposts';

	public $defaults = array(
		'title'                 => 'New Posts',
		'template'              => 'gdbbx-widget-newposts.php',
		'period'                => 'last_day',
		'scope'                 => 'topic,reply',
		'display_thumbnail'     => 'no',
		'display_date'          => 'yes',
		'display_author'        => 'no',
		'display_author_avatar' => 'no',
		'display_prefixes'      => 'no',
		'display_tags'          => 'no',
		'display_forum'         => 'no',
		'exclude_private'       => 'yes',
		'exclude_forums_ids'    => array(),
		'include_forums_ids'    => array(),
		'limit'                 => 10,
		'before'                => '',
		'after'                 => ''
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "New Posts", "bbp-core" );
		$this->widget_description = __( "New topics or topics with new replies.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'filter'  => array(
				'name'    => __( "Filter", "gd-topic-polls" ),
				'include' => array( 'new-posts-filter' )
			),
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'new-posts-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['period']                = d4p_sanitize_basic( $new_instance['period'] );
		$instance['scope']                 = d4p_sanitize_basic( $new_instance['scope'] );
		$instance['limit']                 = absint( $new_instance['limit'] );
		$instance['display_thumbnail']     = d4p_sanitize_basic( $new_instance['display_thumbnail'] );
		$instance['display_date']          = d4p_sanitize_basic( $new_instance['display_date'] );
		$instance['display_author']        = d4p_sanitize_basic( $new_instance['display_author'] );
		$instance['display_author_avatar'] = d4p_sanitize_basic( $new_instance['display_author_avatar'] );
		$instance['exclude_private']       = d4p_sanitize_basic( $new_instance['exclude_private'] );
		$instance['display_prefixes']      = d4p_sanitize_basic( $new_instance['display_prefixes'] );
		$instance['display_tags']          = d4p_sanitize_basic( $new_instance['display_tags'] );
		$instance['display_forum']         = d4p_sanitize_basic( $new_instance['display_forum'] );

		$instance['exclude_forums_ids'] = d4p_ids_from_string( $new_instance['exclude_forums_ids'] );
		$instance['include_forums_ids'] = d4p_ids_from_string( $new_instance['include_forums_ids'] );

		return $instance;
	}

	public function the_results( $instance ) {
		$instance = $this->instance( $instance );

		$month = 0;
		$days  = 0;
		$years = 0;
		$hours = 1;

		$scope = $instance['scope'];

		switch ( $instance['period'] ) {
			case 'last_hour':
				$hours = 2;
				break;
			default:
			case 'last_day':
				$days = 1;
				break;
			case 'last_week':
				$days = 7;
				break;
			case 'last_fortnight':
				$days = 14;
				break;
			case 'last_month':
				$month = 1;
				break;
			case 'last_3months':
				$month = 3;
				break;
			case 'last_6months':
				$month = 6;
				break;
			case 'last_year':
				$years = 1;
				break;
		}

		$timestamp = mktime( date( 'H' ) - $hours, 0, 0, date( 'n' ) - $month, date( 'j' ) - $days, date( 'Y' ) - $years );
		$timestamp = $timestamp + d4p_gmt_offset() * 3600;

		$atts = array(
			'timestamp'      => $timestamp,
			'limit'          => $instance['limit'],
			'access_check'   => $instance['exclude_private'],
			'include_forums' => $instance['include_forums_ids'],
			'exclude_forums' => $instance['exclude_forums_ids']
		);

		switch ( $scope ) {
			case 'topic,reply':
				return Posts::instance()->get_new_posts( $atts );
			case 'topic':
				return Posts::instance()->get_new_topics( $atts );
			case 'reply':
				return Posts::instance()->get_new_replies( $atts );
		}
	}

	public function the_render( $instance, $results = false ) {
		if ( empty( $results ) ) {
			echo '<span class="gdbbx-no-topics">' . __( "No topics found", "bbp-core" ) . '</span>';
		} else {
			echo '<ul>' . D4P_EOL;

			$show_thumbnail = isset( $instance['display_thumbnail'] ) && $instance['display_thumbnail'] == 'yes';
			$show_date      = isset( $instance['display_date'] ) && $instance['display_date'] == 'yes';
			$show_author    = isset( $instance['display_author'] ) && $instance['display_author'] == 'yes';
			$show_avatar    = isset( $instance['display_author_avatar'] ) && $instance['display_author_avatar'] == 'yes';
			$show_prefixes  = isset( $instance['display_prefixes'] ) && $instance['display_prefixes'] == 'yes' && d4p_has_plugin( 'gd-topic-prefix' );
			$show_tags      = isset( $instance['display_tags'] ) && $instance['display_tags'] == 'yes';
			$show_forum     = isset( $instance['display_forum'] ) && $instance['display_forum'] == 'yes';

			$template = apply_filters( 'gdbbx-widget-newposts-template', $instance['template'], $results, $this );
			$path     = gdbbx_get_template_part( $template );

			foreach ( $results as $post ) {
				include( $path );
			}

			echo '</ul>' . D4P_EOL;
		}
	}
}
