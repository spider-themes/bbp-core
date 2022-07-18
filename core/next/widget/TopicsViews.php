<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TopicsViews extends Widget {
	public $widget_base = 'd4p_bbw_topicsviews';
	public $widget_class = 'gdbbx-widget gdbbx-widget-topicsviews';

	public $defaults = array(
		'title' => 'Topics Views List',
		'views' => array()
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "Topics Views", "bbp-core" );
		$this->widget_description = __( "List of the selected topic views.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'topics-views-content' ),
				'class'   => 'gdbbx-tab-topics-views'
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['views'] = array();
		if ( isset( $new_instance['views'] ) ) {
			$_views = (array) $new_instance['views'];
			$_all   = array_keys( bbp_get_views() );

			foreach ( $_views as $key ) {
				if ( in_array( $key, $_all ) ) {
					$instance['views'][] = $key;
				}
			}
		}

		return $instance;
	}

	public function the_render( $instance, $results = false ) {
		if ( empty( $instance['views'] ) ) {
			$instance['views'] = array_keys( bbp_get_views() );
		}

		echo '<ul>' . D4P_EOL;

		$current_view = bbp_is_single_view() ? get_query_var( 'bbp_view' ) : '';

		foreach ( $instance['views'] as $view ) {
			if ( isset( bbpress()->views[ $view ] ) ) {
				$view = bbp_get_view_id( $view );

				$class = 'bbp-view-' . $view;
				if ( $view == $current_view ) {
					$class .= ' current';
				}

				echo '<li class="' . $class . '">' . D4P_EOL . D4P_TAB;
				echo '<a title="' . sprintf( __( "Topic View: %s", "bbp-core" ), bbp_get_view_title( $view ) ) . '" href="' . bbp_get_view_url( $view ) . '">' . bbp_get_view_title( $view ) . '</a>';
				echo D4P_EOL . "</li>" . D4P_EOL;
			}
		}

		echo '</ul>' . D4P_EOL;
	}
}
