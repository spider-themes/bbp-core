<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;
use Dev4Press\Plugin\GDBBX\Basic\Statistics as Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Statistics extends Widget {
	public $widget_base = 'd4p_bbw_statistics';
	public $widget_class = 'gdbbx-widget gdbbx-widget-statistics';

	public $defaults = array(
		'title'    => 'Forum Statistics',
		'template' => 'gdbbx-widget-statistics-list.php',
		'stats'    => array()
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "Statistics", "bbp-core" );
		$this->widget_description = __( "Forum statistics information.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'statistics-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['stats'] = array();
		if ( isset( $new_instance['stats'] ) ) {
			$_stats = (array) $new_instance['stats'];
			$_all   = array_keys( Stats::instance()->forums_stats_elements() );

			foreach ( $_stats as $key ) {
				if ( in_array( $key, $_all ) ) {
					$instance['stats'][] = $key;
				}
			}
		}

		return $instance;
	}

	public function the_render( $instance, $results = false ) {
		$elements   = Stats::instance()->forums_stats_elements();
		$statistics = Stats::instance()->forums_stats();

		if ( empty( $instance['stats'] ) ) {
			$instance['stats'] = array_keys( $elements );
		}

		$template = apply_filters( 'gdbbx-widget-statistics-template', $instance['template'], $results, $this );

		include( gdbbx_get_template_part( $template ) );
	}
}
