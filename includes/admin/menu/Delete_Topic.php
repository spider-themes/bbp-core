<?php
namespace admin\menu;

/**
 * Class Delete_Post
 * @package BBP-core\Admin\Menu
 */
class Delete_Topic {

	/**
	 * Create_Post constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'delete_topic' ] );
	}

	/**
	 * Delete Parent Doc
	 */
	public function delete_topic() {
 
		if ( ! empty ( $_GET['topic_ID'] ) ) {
			$topic_id 				= $_GET['topic_ID'] ?? '';
			$topics	= get_children(
				[
					'post_parent' 	=> $_GET['topic_ID'],
					'post_type'   	=> 'reply',
					'orderby'     	=> 'menu_order',
					'order'       	=> 'asc',
				]
			);

			$forum_topics 			= '';
			if ( is_array( $topics ) ) :
				foreach ( $topics as $topic ) :
					$forum_topics 	.= $topic->ID . ',';
				endforeach;
			endif;

			$topics_id              = $topic_id . ',' . $forum_topics;
			$topic_ids              = explode( ',', $topics_id );
			$topic_id_int           = array_map( 'intval', $topic_ids );
			foreach ( $topic_id_int as $delete_topic ) {
				wp_trash_post( $delete_topic, true ); 
			}
			wp_safe_redirect(admin_url( 'admin.php?page=bbp-core' ));
		}
	}
}
new Delete_Topic();