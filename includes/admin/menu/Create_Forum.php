<?php
namespace Admin;

class Create_Forum {
	/**
	 * Create_Forum constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'bbp_create_forum' ] );
	}

    /**
     * Create parent Doc post
     */
    public function bbp_create_forum() {
	        if ( ! empty ( $_GET['bbp_parent_title'] ) ) {
            $bbp_parent_title = ! empty ( $_GET['bbp_parent_title'] ) ? sanitize_text_field( $_GET['bbp_parent_title'] ) : '';
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
                'post_title'   => $bbp_parent_title,
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
} 
new Create_Forum();