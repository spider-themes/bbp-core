<div class="bbpc-post">
	<?php

	if ( bbp_is_topic( get_the_ID() ) ) {

		?>

		<span class="bbpc-post-type"><?php _e( 'Topic', 'bbp-core' ); ?></span>
		<a href="<?php bbp_topic_permalink( get_the_ID() ); ?>"><?php bbp_topic_title( get_the_ID() ); ?></a>

		<?php

	} else {

		?>

		<span class="bbpc-post-type"><?php _e( 'Reply', 'bbp-core' ); ?></span>
		<a href="<?php bbp_reply_url( get_the_ID() ); ?>"><?php bbp_reply_title( get_the_ID() ); ?></a>

		<?php

	}

	?>
</div>
<div class="bbpc-information">
	<span class="bbpc-post-time"><?php echo sprintf( __( '%s ago', 'bbp-core' ), human_time_diff( get_the_date( 'U' ) ) ); ?></span>
	<span class="bbpc-post-author"><?php _e( 'by', 'bbp-core' ); ?>
											 <?php
												bbp_author_link(
													[
														'post_id' => get_the_ID(),
														'size' => 14,
													]
												);
												?>
		</span>
</div>
