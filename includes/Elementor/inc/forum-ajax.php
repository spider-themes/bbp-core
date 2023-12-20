<?php
/**
 * Ajax Actions for forum filtering
 * @since 0.0.1
 */
add_action( 'wp_ajax_bbpc_ajax_forum', 'bbpc_ajax_forum' );
add_action( 'wp_ajax_nopriv_bbpc_ajax_forum', 'bbpc_ajax_forum' );

function bbpc_ajax_forum() {
	// received values from front end forum
	$data_forum = sanitize_text_field( $_POST['data_forum'] );

	$q = array(
		'post_type'      => 'topic',
		'posts_per_page' => 9,
	);

	if ( 'solved' === $data_forum ) {
		$meta_query = [
			'meta_query' => array(
				array(
					'key'   => '_bbp_topic_is_solved',
					'value' => true,
				),
			),
		];
		$q 	= array_merge( $q, $meta_query );

	} elseif ( 'unsolved' === $data_forum ) {
		$meta_query = [
			'meta_query' => array(
				array(
					'key'     => '_bbp_topic_is_solved',
					'compare' => 'NOT EXISTS',
				),
			),
		];

		$q = array_merge( $q, $meta_query );

	} elseif ( 'recent' === $data_forum ) {
		$query = [
			'order' => 'DESC',
		];

		$q = array_merge( $q, $query );

	} elseif ( 'popular' === $data_forum ) {
		$query = [
			'meta_key' => '_btv_view_count',
			'orderby'  => 'meta_value_num',
			'order'    => 'DESC'
		];

		$q = array_merge( $q, $query );

	} elseif ( 'featured' === $data_forum ) {
		$query = array(
			'show_stickies' => true,
			'post__in' => bbp_get_super_stickies()
		);

		$q = array_merge( $q, $query );
	}

	// WP_Query arguments
	$forum_topics = new WP_Query( $q );

	if ( $forum_topics->have_posts() ) {
		$i = 0;
		while ( $forum_topics->have_posts() ):
			$forum_topics->the_post();
			$item_id    = get_the_ID();
			$author_id  = get_post_field( 'post_author', $item_id );
			$topic_id   = $forum_topics->posts[ $i ]->ID;
			$vote_count = get_post_meta( $topic_id, "bbpv-votes", true );
			$forum_id   = bbp_get_topic_forum_id();
			?>
            <div class="single-forum-post-widget">
                <div class="post-content">
                    <div class="post-title">
                        <h6> <a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a> </h6>
                    </div>
                    <div class="post-info">
                        <div class="author">
                            <img src="<?php echo BBPC_ASSETS ?>/img/forum_tab/user-circle-alt.svg" alt="<?php esc_attr_e( 'User circle icon', 'bbpc-core' ); ?>">
							<?php 
                            echo bbp_get_topic_author_link( 
                                array( 
                                    'post_id' 	=> $topic_id, 
                                    'type' 		=> 'name' 
                                )
                            );
                            ?>
                        </div>

                        <div class="post-time">
                            <img src="<?php echo BBPC_ASSETS ?>/img/forum_tab/time-outline.svg" alt="<?php esc_attr_e( 'Time outline icon', 'bbpc-core' ); ?>">
							<?php echo bbp_forum_last_active_time( get_the_ID() ); ?>
                        </div>
                    </div>

                    <div class="post-category">
                        <a href="<?php echo get_the_permalink( $forum_id ) ?>">
							<?php echo get_the_post_thumbnail( $forum_id ); ?>
							<?php echo get_the_title( $forum_id ) ?>
                        </a>
                    </div>
                </div>
                <div class="post-reach">
                    <div class="post-view">
                        <img src="<?php echo BBPC_ASSETS ?>/img/forum_tab/eye-outline.svg" alt="<?php esc_attr_e( 'Eye outline icon', 'bbpc-core' ); ?>">
						
						<?php 
						bbp_topic_view_count( $topic_id );
						echo '&nbsp;';
						_e( 'Views', 'bbp-core' );
						?>

                    </div>
                    <div class="post-like">
                        <img src="<?php echo BBPC_ASSETS ?>/img/forum_tab/thumbs-up-outline.svg" alt="<?php esc_attr_e( 'Thumbs up icon', 'bbpc-core' ); ?>">
						
						<?php 
						if ( $vote_count ) {
							echo $vote_count;
						} else {
							echo "0";
						}

						echo '&nbsp;';
						_e( 'Likes', 'bbp-core' ); 
						?>
                    </div>
                    <div class="post-comment">
                        <img src="<?php echo BBPC_ASSETS ?>/img/forum_tab/chatbubbles-outline.svg" alt="<?php esc_attr_e( 'Chatbubbles outline icon', 'bbpc-core' ); ?>">
						
						<?php 
						echo bbp_topic_reply_count( $topic_id );
						echo '&nbsp;';
						_e( 'Replies', 'bbp-core' );
						?>
                    </div>
                </div>
            </div>
			<?php
			$i ++;
		endwhile;
		unset( $i );
	} else {
		echo 'no posts found';
	}

	wp_reset_postdata();
	wp_die();
}