<div class="bbpc-widget-the-info-list">
	<?php if ( isset( $results['show_forum'] ) ) { ?>
	<div class="bbpc-widget-the-info-forum">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_forum']['icon']; ?>"></i> <?php echo $results['show_forum']['label']; ?>: </label>
		<span><?php echo $results['show_forum']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_author'] ) ) { ?>
	<div class="bbpc-widget-the-info-author">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_author']['icon']; ?>"></i> <?php echo $results['show_author']['label']; ?>: </label>
		<span><?php echo $results['show_author']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_post_date'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-activity">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_post_date']['icon']; ?>"></i> <?php echo $results['show_post_date']['label']; ?>: </label>
		<span><?php echo $results['show_post_date']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_last_activity'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-activity">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_last_activity']['icon']; ?>"></i> <?php echo $results['show_last_activity']['label']; ?>: </label>
		<span><?php echo $results['show_last_activity']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_status'] ) ) { ?>
	<div class="bbpc-widget-the-info-last-status">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_status']['icon']; ?>"></i> <?php echo $results['show_status']['label']; ?>: </label>
		<span><?php echo $results['show_status']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_replies'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-replies">
		<span><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_count_replies']['icon']; ?>"></i> <?php echo sprintf( $results['show_count_replies']['label_alt'], $results['show_count_replies']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_count_voices'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-count-voices">
		<span><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_count_voices']['icon']; ?>"></i> <?php echo sprintf( $results['show_count_voices']['label_alt'], $results['show_count_voices']['value'] ); ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_participants'] ) ) { ?>
	<div class="bbpc-widget-the-info-count bbpc-widget-the-info-participants">
		<label><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_participants']['icon']; ?>"></i> <?php echo $results['show_participants']['label']; ?>: </label>
		<span><?php echo $results['show_participants']['value']; ?></span>
	</div>
	<?php } ?>

	<?php if ( isset( $results['show_subscribe_favorite'] ) ) { ?>
	<div class="bbpc-widget-the-info-subscribe">
		<span><i class="bbpc-icon bbpc-fw bbpc-icon-<?php echo $results['show_subscribe_favorite']['icon']; ?>"></i> <?php echo str_replace( '<br/>', ' &middot; ', $results['show_subscribe_favorite']['value'] ); ?></span>
	</div>
	<?php } ?>
</div>
