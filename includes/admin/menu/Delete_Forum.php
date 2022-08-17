<?php
namespace admin\menu;

/**
 * Class Delete_Post
 * @package eazyDocs\Admin
 */
class Delete_Forum {

	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'delete_forum' ] );
	}

	/**
	 * Delete Parent Doc
	 */
	public function delete_forum() {
 
		if ( ! empty ( $_GET['DeleteID'] ) ) {
			$parent_forum_id 	  = $_GET['DeleteID'] ?? '';
			$children = get_children(
				[
					'post_parent' => $_GET['DeleteID'],
					'post_type'   => 'topic',
					'orderby'     => 'menu_order',
					'order'       => 'asc',
				]
			);

			$topics 			  = '';
			$topic_replies 		  = '';
			if ( is_array( $children ) ) :
				foreach ( $children as $child ) :
					$replies = get_children(
						[
							'post_parent' => $child->ID,
							'post_type'   => 'reply',
							'post_status' => [ 'publish', 'draft' ],
						]
					);

					$topics .= $child->ID . ',';
					if ( is_array( $replies ) ) :
						foreach ( $replies as $reply ) :
							$topic_replies .= $reply->ID . ',';
						endforeach;
					endif;
					
				endforeach;
			endif;
			  
			$forum_ids              = $parent_forum_id . ',' . $topic_replies . $topics;
			$forum_id                  = explode( ',', $forum_ids );
			$forum_id_int              = array_map( 'intval', $forum_id );
			foreach ( $forum_id_int as $deletes ) {
				wp_delete_post( $deletes, true );
			}
			wp_safe_redirect(admin_url( 'admin.php?page=bbp-core' ));
		}
	}
}
new Delete_Forum();