<div class="bbpc-widget-the-info-list">
	<?php if ( isset( $results['show_parent_forum'] ) ) { ?>
	<div class="bbpc-widget-the-info-parent-forum">
		<label><?php echo $results['show_parent_forum']['label']; ?>: </label>
		<span><?php echo $results['show_parent_forum']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_topics'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-topics">
		<span><?php echo sprintf( $results['show_count_topics']['label_alt'], $results['show_count_topics']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_replies'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-replies">
		<span><?php echo sprintf( $results['show_count_replies']['label_alt'], $results['show_count_replies']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_last_post_user'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-post-user">
		<label><?php echo $results['show_last_post_user']['label']; ?>: </label>
		<span><?php echo $results['show_last_post_user']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_last_activity'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-activity">
		<label><?php echo $results['show_last_activity']['label']; ?>: </label>
		<span><?php echo $results['show_last_activity']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_subscribe'] ) ) { ?>
	<div class="bbpc-widget-the-info-subscribe">
		<span><?php echo $results['show_subscribe']['value']; ?></span>
	</div>
	<?php } ?>
</div>
