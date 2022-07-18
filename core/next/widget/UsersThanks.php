<?php

namespace Dev4Press\Plugin\GDBBX\Widget;

use Dev4Press\Plugin\GDBBX\Base\Widget;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UsersThanks extends Widget {
	public $widget_base = 'd4p_bbw_usersthanks';
	public $widget_class = 'gdbbx-widget gdbbx-widget-usersthanks';

	public $defaults = array(
		'title'    => 'Top Thanked Users',
		'template' => 'gdbbx-widget-usersthanks.php',
		'limit'    => 10
	);

	public function __construct( $id_base = false, $name = '', $widget_options = array(), $control_options = array() ) {
		$this->widget_name        = 'GD bbPress Toolbox: ' . __( "Top Thanked Users", "bbp-core" );
		$this->widget_description = __( "List of users with most thanks received.", "bbp-core" );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function the_form( $instance ) : array {
		return array(
			'content' => array(
				'name'    => __( "Content", "gd-topic-polls" ),
				'include' => array( 'users-thanks-content' )
			)
		);
	}

	public function update( $new_instance, $old_instance ) : array {
		$instance = parent::update( $new_instance, $old_instance );

		$instance['template'] = d4p_sanitize_basic( $new_instance['template'] );

		$instance['limit'] = absint( $new_instance['limit'] );

		return $instance;
	}

	public function the_results( $instance ) {
		if ( ! Plugin::instance()->is_enabled( 'thanks' ) ) {
			return array();
		}

		return gdbbx_say_thanks()->get_list_top_thanked_users( array(
			'limit'  => $instance['limit'],
			'return' => 'list'
		) );
	}

	public function the_render( $instance, $results = false ) {
		if ( empty( $results ) ) {
			echo '<span class="gdbbx-no-users">' . __( "No users found", "bbp-core" ) . '</span>';
		} else {
			echo '<ul>' . D4P_EOL;

			$template = apply_filters( 'gdbbx-widget-usersthanks-template', $instance['template'], $results, $this );
			$path     = gdbbx_get_template_part( $template );

			foreach ( $results as $user ) {
				include( $path );
			}

			echo '</ul>' . D4P_EOL;
		}
	}
}
