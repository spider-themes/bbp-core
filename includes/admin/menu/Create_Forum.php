<?php
namespace eazyDocs\Admin;

/**
 * Class Create_Post
 * @package eazyDocs\Admin
 */
class Create_Post {
	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'create_parent_doc' ] );
		add_action( 'admin_init', [ $this, 'create_section_doc' ] );
	}

    /**
     * Create parent Doc post
     */
    public function create_parent_doc() {
	        if ( isset ( $_GET['parent_title'] ) && ! empty ( $_GET['parent_title'] ) ) {
            $title = ! empty ( $_GET['parent_title'] ) ? sanitize_text_field( $_GET['parent_title'] ) : '';
            $args = [
                'post_type'   => 'forum',
                'post_parent' => 0
            ];

            $query = new \WP_Query( $args );
            $total = $query->found_posts;
            $add   = 2;
            $order = $total + $add;

            // Create post object
            $post = wp_insert_post( array(
                'post_title'   => $title,
                'post_parent'  => 0,
                'post_content' => '',
                'post_type'    => 'forum',
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'menu_order'   => $order,
            ) );
            wp_insert_post( $post, $wp_error = '' );			            
            wp_safe_redirect( admin_url('admin.php?page=bbp-core') );
        }
    }
	
	/**
	 * Create section doc post
	 */
	public function create_section_doc() {

		if ( isset ( $_GET['is_section'] ) && ! empty ( $_GET['is_section'] ) ) {

			$parentID      = ! empty ( $_GET['parentID'] ) ? absint( $_GET['parentID'] ) : 0;
			$section_title = ! empty ( $_GET['is_section'] ) ? sanitize_text_field( $_GET['is_section'] ) : '';
			$parent_item   = get_children( array(
				'post_parent' => $parentID,
				'post_type'   => bbp_get_topic_post_type()
			) );

			$add   = 2;
			$order = count( $parent_item );
			$order = $order + $add;

			// Create post object
			$post = array(
				'post_title'   => $section_title,
				'post_parent'  => $parentID,
				'post_content' => '',
				'post_type'    => bbp_get_topic_post_type(),
				'post_status'  => 'publish',
				'menu_order'   => $order
			);
			wp_insert_post( $post, $wp_error = '' );
			wp_safe_redirect( admin_url('admin.php?page=bbp-core') );
		}
	}	
}