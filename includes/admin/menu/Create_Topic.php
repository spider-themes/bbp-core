<?php
namespace Admin;

class Create_Topic {
	/**
	 * Create_Forum constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'bbp_create_topic' ] );
	}

    /**
     * Create parent Doc post
     */
    public function bbp_create_topic() {

	    if ( isset ( $_GET['is_bbp_section'] ) && ! empty ( $_GET['is_bbp_section'] ) ) {

			$parentID      = ! empty ( $_GET['bbp_parentID'] ) ? absint( $_GET['bbp_parentID'] ) : 0;
			$section_title = ! empty ( $_GET['is_bbp_section'] ) ? sanitize_text_field( $_GET['is_bbp_section'] ) : '';
			$parent_item   = get_children( array(
				'post_parent' => $parentID,
				'post_type'   => 'topic'
			) );

			$add   = 2;
			$order = count( $parent_item );
			$order = $order + $add;

			// Create post object
			$post = array(
				'post_title'   => $section_title,
				'post_parent'  => $parentID,
				'post_content' => '',
				'post_type'    => 'topic',
				'post_status'  => 'publish',
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
			wp_safe_redirect( admin_url('admin.php?page=bbp-core') );
		}  
		
    }	
} 
new Create_Topic();