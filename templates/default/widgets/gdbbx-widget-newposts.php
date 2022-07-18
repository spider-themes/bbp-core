<?php

$url   = $post['type'] == 'topic' ? bbp_get_topic_permalink( $post['id'] ) : bbp_get_reply_url( $post['id'] );
$title = $post['type'] == 'topic' ? bbp_get_topic_title( $post['id'] ) : bbp_get_reply_title( $post['id'] );

$thumbnail = '';

if ( $show_thumbnail ) {
	$size = apply_filters( 'gdbbx_newposts_widget_thumbnail_size', 'post-thumbnail' );

	$thumbnail = get_the_post_thumbnail( $post['id'], $size );
}

?>

<li class="gdbbx-widget-newpost-default">
	<?php echo $thumbnail; ?>
    <div class="gdbbx-post-inner">
        <h4 class="gdbbx-title">
            <a href="<?php echo esc_attr( $url ); ?>" title="<?php echo esc_attr( $title ); ?>"><?php echo $title; ?></a>
        </h4>

		<?php if ( $show_forum ) { ?>
            <div class="gdbbx-post-forum">
				<?php

				if ( $post['type'] == 'topic' ) {
					$forum = bbp_get_topic_forum_id( $post['id'] );
				} else {
					$forum = bbp_get_reply_forum_id( $post['id'] );
				}

				$title = bbp_get_forum_title( $forum );

				echo sprintf( _x( "Forum: %s", "New posts widget forum", "bbp-core" ), '<a href="' . get_permalink( $forum ) . '">' . $title . '</a>' )

				?>
            </div>
		<?php } ?>

		<?php if ( $show_author || $show_date ) { ?>
            <div class="gdbbx-post-meta">
				<?php if ( $show_date ) { ?>
                    <em class="gdbbx-last-active"><?php echo $post['activity']; ?></em>
				<?php } ?>

				<?php if ( $show_author ) { ?>
					<?php $author = bbp_get_author_link( array(
						'post_id' => $post['id'],
						'size'    => 20,
						'type'    => ( $show_avatar ? 'both' : 'name' )
					) ); ?>

                    <em class="gdbbx-author"><?php echo sprintf( _x( "by %s", "New posts widget author", "bbp-core" ), $author ); ?></em>
				<?php } ?>
            </div>
		<?php } ?>

		<?php if ( $show_prefixes && $post['type'] == 'topic' && function_exists( 'gdtox_topic_prefixes' ) ) { ?>
            <div class="gdbbx-post-prefixes">
				<?php gdtox_topic_prefixes( $post['id'] ); ?>
            </div>
		<?php } ?>

		<?php if ( $show_tags && $post['type'] == 'topic' ) { ?>
            <div class="gdbbx-post-tags">
				<?php bbp_topic_tag_list( $post['id'] ); ?>
            </div>
		<?php } ?>
    </div>
</li>
