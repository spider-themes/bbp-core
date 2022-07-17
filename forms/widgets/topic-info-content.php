<?php

$_templates = apply_filters(
	'bbpc-widget-topicinfo-templates',
	[
		'bbpc-widget-topicinfo.php'            => __( 'Default table layout', 'bbp-core' ) . ' [' . 'bbpc-widget-topicinfo.php' . ']',
		'bbpc-widget-topicinfo-icons.php'      => __( 'Default table layout', 'bbp-core' ) . ', ' . __( 'with icons', 'bbp-core' ) . ' [' . 'bbpc-widget-topicinfo-icons.php' . ']',
		'bbpc-widget-topicinfo-list.php'       => __( 'Enhanced list layout', 'bbp-core' ) . ' [' . 'bbpc-widget-topicinfo-list.php' . ']',
		'bbpc-widget-topicinfo-list-icons.php' => __( 'Enhanced list layout', 'bbp-core' ) . ', ' . __( 'with icons', 'bbp-core' ) . ' [' . 'bbpc-widget-topicinfo-list-icons.php' . ']',
	]
);

?>

<h4><?php _e( 'Display Template', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-singular">
			<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Select template', 'bbp-core' ); ?>:</label>
			<?php
			d4p_render_select(
				$_templates,
				[
					'id'       => $this->get_field_id( 'template' ),
					'class'    => 'widefat',
					'name'     => $this->get_field_name( 'template' ),
					'selected' => $instance['template'],
				]
			);
			?>
		</td>
	</tr>
	</tbody>
</table>

<h4><?php _e( 'Information to show', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-left">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_forum' ); ?>">
					<input class="widefat" <?php echo $instance['show_forum'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_forum' ); ?>" name="<?php echo $this->get_field_name( 'show_forum' ); ?>"/>
					<?php _e( 'Show forum', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
					<input class="widefat" <?php echo $instance['show_author'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>"/>
					<?php _e( 'Show author', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_post_date' ); ?>">
					<input class="widefat" <?php echo $instance['show_post_date'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_post_date' ); ?>" name="<?php echo $this->get_field_name( 'show_post_date' ); ?>"/>
					<?php _e( 'Show post date', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_last_activity' ); ?>">
					<input class="widefat" <?php echo $instance['show_last_activity'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_last_activity' ); ?>" name="<?php echo $this->get_field_name( 'show_last_activity' ); ?>"/>
					<?php _e( 'Show last activity', 'bbp-core' ); ?></label>
			</div>
		</td>
		<td class="cell-right">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_status' ); ?>">
					<input class="widefat" <?php echo $instance['show_status'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_status' ); ?>" name="<?php echo $this->get_field_name( 'show_status' ); ?>"/>
					<?php _e( 'Show status', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_count_replies' ); ?>">
					<input class="widefat" <?php echo $instance['show_count_replies'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_count_replies' ); ?>" name="<?php echo $this->get_field_name( 'show_count_replies' ); ?>"/>
					<?php _e( 'Show replies count', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_count_voices' ); ?>">
					<input class="widefat" <?php echo $instance['show_count_voices'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_count_voices' ); ?>" name="<?php echo $this->get_field_name( 'show_count_voices' ); ?>"/>
					<?php _e( 'Show voices count', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_participants' ); ?>">
					<input class="widefat" <?php echo $instance['show_participants'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_participants' ); ?>" name="<?php echo $this->get_field_name( 'show_participants' ); ?>"/>
					<?php _e( 'Show participants', 'bbp-core' ); ?></label>
			</div>
		</td>
	</tr>
	</tbody>
</table>

<h4><?php _e( 'Actions to show', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-single">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_subscribe_favorite' ); ?>">
					<input class="widefat" <?php echo $instance['show_subscribe_favorite'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_subscribe_favorite' ); ?>" name="<?php echo $this->get_field_name( 'show_subscribe_favorite' ); ?>"/>
					<?php _e( 'Show subscribe and favorite buttons', 'bbp-core' ); ?></label>
			</div>
		</td>
	</tr>
	</tbody>
</table>
