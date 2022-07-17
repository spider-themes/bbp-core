<?php

namespace SpiderDevs\Plugin\BBPC\Widget;

use SpiderDevs\Plugin\BBPC\Base\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Search extends Widget {
	public $widget_base = 'd4p_bbw_search';
	public $widget_class = 'bbpc-widget bbpc-widget-search';

	public $forum_id = 0;

	public $defaults = array(
		'title'         => 'Search Forums',
		'title_current' => 'Search current forum',
		'search_mode'   => 'global'
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'BBP Core: ' . __( "Search", "bbp-core" );
		$this->widget_description = __( "Expanded search widget.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'search-content' )
			)
		);
	}

	protected function widget_output( $args, $instance ) {
		$this->forum_id = bbp_get_forum_id();

		parent::widget_output( $args, $instance );
	}

	public function form_unique_id( $instance ) {
		return 'bbpc-search-form-' . $this->number;
	}

	public function title( $instance ) : string {
		if ( $instance['search_mode'] == 'current' && $this->forum_id > 0 ) {
			return $instance['title_current'];
		}

		return $instance['title'];
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['title_current'] = d4p_sanitize_basic( $new_instance['title_current'] );
		$instance['search_mode']   = d4p_sanitize_basic( $new_instance['search_mode'] );

		return $instance;
	}

	public function the_render( $instance, $results = false ) {
		$template = apply_filters( 'bbpc-widget-search-template', 'bbpc-widget-search.php', $this );

		include( bbpc_get_template_part( $template ) );
	}
}
