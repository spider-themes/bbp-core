<div class="bbpc-widget-the-info-list">
	<?php if ( isset( $results['show_forum'] ) ) { ?>
	<div class="bbpc-widget-the-info-forum">
		<label><?php echo $results['show_forum']['label']; ?>: </label>
		<span><?php echo $results['show_forum']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_author'] ) ) { ?>
	<div class="bbpc-widget-the-info-author">
		<label><?php echo $results['show_author']['label']; ?>: </label>
		<span><?php echo $results['show_author']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_post_date'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-activity">
		<label><?php echo $results['show_post_date']['label']; ?>: </label>
		<span><?php echo $results['show_post_date']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_last_activity'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-activity">
		<label><?php echo $results['show_last_activity']['label']; ?>: </label>
		<span><?php echo $results['show_last_activity']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_status'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-status">
		<label><?php echo $results['show_status']['label']; ?>: </label>
		<span><?php echo $results['show_status']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_replies'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-replies">
		<span><?php echo sprintf( $results['show_count_replies']['label_alt'], $results['show_count_replies']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_voices'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-voices">
		<span><?php echo sprintf( $results['show_count_voices']['label_alt'], $results['show_count_voices']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_participants'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-participants">
		<label><?php echo $results['show_participants']['label']; ?>: </label>
		<span><?php echo $results['show_participants']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_subscribe_favorite'] ) ) { ?>
	<div class="bbpc-widget-the-info-subscribe">
		<span><?php echo $results['show_subscribe_favorite']['value']; ?></span>
	</div>
	<?php } ?>
</div>
