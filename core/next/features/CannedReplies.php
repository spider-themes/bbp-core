<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CannedReplies extends Feature {
	public $feature_name = 'canned-replies';
	public $settings = array(
		'canned_roles'         => array( 'bbp_keymaster', 'bbp_moderator' ),
		'post_type_singular'   => 'Canned Reply',
		'post_type_plural'     => 'Canned Replies',
		'use_taxonomy'         => false,
		'taxonomy_singular'    => 'Category',
		'taxonomy_plural'      => 'Categories',
		'auto_close_on_insert' => true
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbpc_init', array( $this, 'init' ) );

		add_filter( 'bbpc_script_values', array( $this, 'script_values' ) );
		add_action( 'bbp_theme_before_reply_form_content', array( $this, 'form' ) );
	}

	public static function instance() : CannedReplies {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new CannedReplies();
		}

		return $instance;
	}

	public function script_values( $values ) {
		$values['load'][]         = 'canned_replies';
		$values['canned_replies'] = apply_filters( 'bbpc_canned_replies_script_values', array(
			'auto_close_on_insert' => $this->settings['auto_close_on_insert']
		) );

		return $values;
	}

	public function form() {
		if ( current_user_can( 'moderate' ) || is_super_admin() || $this->allowed( 'canned' ) ) {
			include( bbpc_get_template_part( 'bbpc-list-canned-replies.php' ) );

			Enqueue::instance()->core();
		}
	}

	public function posttype_labels( $singular, $plural ) {
		$labels = array(
			'name'          => $plural,
			'singular_name' => $singular,
			'menu_name'     => $plural
		);

		$labels['add_new']               = sprintf( _x( "Add New %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['add_new_item']          = sprintf( _x( "Add New %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['edit_item']             = sprintf( _x( "Edit %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['new_item']              = sprintf( _x( "New %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['view_item']             = sprintf( _x( "View %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['view_items']            = sprintf( _x( "View %s", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['search_items']          = sprintf( _x( "Search %s", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['not_found']             = sprintf( _x( "No %s found", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['not_found_in_trash']    = sprintf( _x( "No %s found in trash", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['parent_item_colon']     = sprintf( _x( "Parent %s:", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['all_items']             = sprintf( _x( "All %s", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['archives']              = sprintf( _x( "%s Archives", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['attributes']            = sprintf( _x( "%s Attributes", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['insert_into_item']      = sprintf( _x( "Insert into %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['uploaded_to_this_item'] = sprintf( _x( "Uploaded to this %s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );
		$labels['featured_image']        = sprintf( _x( "Featured Image", "Post type: Canned Reply", "bbp-core" ) );
		$labels['set_featured_image']    = sprintf( _x( "Set featured image", "Post type: Canned Reply", "bbp-core" ) );
		$labels['remove_featured_image'] = sprintf( _x( "Remove featured image", "Post type: Canned Reply", "bbp-core" ) );
		$labels['use_featured_image']    = sprintf( _x( "Use featured image", "Post type: Canned Reply", "bbp-core" ) );
		$labels['menu_name']             = sprintf( _x( "%s", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['filter_items_list']     = sprintf( _x( "Filter %s List", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['items_list_navigation'] = sprintf( _x( "%s List Navigation", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['items_list']            = sprintf( _x( "%s List", "Post type: Canned Reply, Plural", "bbp-core" ), $plural );
		$labels['name_admin_bar']        = sprintf( _x( "%s", "Post type: Canned Reply, Singular", "bbp-core" ), $singular );

		return $labels;
	}

	public function taxonomy_labels( $singular, $plural ) {
		$labels = array(
			'name'          => $plural,
			'singular_name' => $singular,
			'menu_name'     => $plural
		);

		$labels['parent_item']                = sprintf( _x( "Parent %s", "Taxonomy: Canned Category, Singular", "bbp-core" ), $singular );
		$labels['search_items']               = sprintf( _x( "Search %s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['popular_items']              = sprintf( _x( "Popular %s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['all_items']                  = sprintf( _x( "All %s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['edit_item']                  = sprintf( _x( "Edit %s", "Taxonomy: Canned Category, Singular", "bbp-core" ), $singular );
		$labels['view_item']                  = sprintf( _x( "View %s", "Taxonomy: Canned Category, Singular", "bbp-core" ), $singular );
		$labels['update_item']                = sprintf( _x( "Update %s", "Taxonomy: Canned Category, Singular", "bbp-core" ), $singular );
		$labels['add_new_item']               = sprintf( _x( "Add New %s", "Taxonomy: Canned Category, Singular", "bbp-core" ), $singular );
		$labels['add_or_remove_items']        = sprintf( _x( "Add or remove %s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['choose_from_most_used']      = sprintf( _x( "Choose from the most used %s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['parent_item_colon']          = sprintf( _x( "Parent %s:", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['new_item_name']              = sprintf( _x( "New %s Name", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['not_found']                  = sprintf( _x( "No %s Found", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['separate_items_with_commas'] = sprintf( _x( "Separate %s with commas", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );
		$labels['menu_name']                  = sprintf( _x( "%s", "Taxonomy: Canned Category, Plural", "bbp-core" ), $plural );

		return $labels;
	}

	public function init() {
		$this->post_type();

		if ( $this->settings['use_taxonomy'] ) {
			$this->taxonomy();
		}
	}

	public function taxonomy() {
		$reg = array(
			'labels'            => $this->taxonomy_labels(
				__( $this->settings['taxonomy_singular'], "bbp-core" ),
				__( $this->settings['taxonomy_plural'], "bbp-core" ) ),
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => true,
			'public'            => false,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'show_admin_column' => true,
			'capabilities'      => array(
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
				'assign_terms' => 'manage_categories'
			),
			'show_in_nav_menus' => false
		);

		$data = apply_filters( 'bbpc_registration_canned_replies_category', $reg );

		register_taxonomy( 'bbx_canned_category', array( 'bbx_canned_reply' ), $data );
	}

	public function post_type() {
		$reg = array(
			'labels'              => $this->posttype_labels(
				__( $this->settings['post_type_singular'], "bbp-core" ),
				__( $this->settings['post_type_plural'], "bbp-core" )
			),
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'public'              => false,
			'rewrite'             => false,
			'show_in_menu'        => 'bbp-core-front',
			'show_in_admin_bar'   => false,
			'has_archive'         => false,
			'query_var'           => true,
			'supports'            => array( 'title', 'editor', 'author', 'revisions' ),
			'show_ui'             => true,
			'can_export'          => true,
			'show_in_nav_menus'   => false,
			'_edit_link'          => 'post.php?post=%d'
		);

		$data = apply_filters( 'bbpc_registration_canned_replies_post_type', $reg );

		register_post_type( 'bbx_canned_reply', $data );
	}

	public function categories() {
		return $this->settings['use_taxonomy'] ? get_terms( 'bbx_canned_category' ) : array();
	}

	public function replies( $category = array() ) {
		$args = array(
			'post_type'   => 'bbx_canned_reply',
			'post_status' => 'publish',
			'nopaging'    => true
		);

		if ( $category === - 1 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'bbx_canned_category',
					'operator' => 'NOT EXISTS'
				)
			);
		} else if ( ! empty( $category ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'bbx_canned_category',
					'field'    => 'term_id',
					'terms'    => (array) $category
				)
			);
		}

		$args = apply_filters( 'bbpc_get_canned_replies_query', $args );

		return new WP_Query( $args );
	}
}
