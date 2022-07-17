<?php

$_templates = apply_filters(
	'bbpc-widget-userprofile-templates',
	[
		'bbpc-widget-userprofile.php'          => __( 'Default two columns layout', 'bbp-core' ) . ' [' . 'bbpc-widget-userprofile.php' . ']',
		'bbpc-widget-userprofile-enhanced.php' => __( 'Enhanced single column layout', 'bbp-core' ) . ' [' . 'bbpc-widget-topicinfo-enhanced.php' . ']',
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

<h4><?php _e( 'Avatar', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-left">
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e( 'Avatar Size', 'bbp-core' ); ?> (px):</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" type="text" value="<?php echo $instance['avatar_size']; ?>"/>
		</td>
		<td class="cell-right">

		</td>
	</tr>
	</tbody>
</table>

<h4><?php _e( 'Login and Logout', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-left">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_logout' ); ?>">
					<input class="widefat" <?php echo $instance['show_logout'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_logout' ); ?>" name="<?php echo $this->get_field_name( 'show_logout' ); ?>"/>
					<?php _e( 'Show logout link', 'bbp-core' ); ?></label>
			</div>
		</td>
		<td class="cell-right">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_login' ); ?>">
					<input class="widefat" <?php echo $instance['show_login'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_login' ); ?>" name="<?php echo $this->get_field_name( 'show_login' ); ?>"/>
					<?php _e( 'Show login and registration', 'bbp-core' ); ?></label>
			</div>
		</td>
	</tr>
	</tbody>
</table>

<h4><?php _e( 'Other Settings', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-left">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_edit' ); ?>">
					<input class="widefat" <?php echo $instance['show_edit'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_edit' ); ?>" name="<?php echo $this->get_field_name( 'show_edit' ); ?>"/>
					<?php _e( 'Show edit profile link', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_profile' ); ?>">
					<input class="widefat" <?php echo $instance['show_profile'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_profile' ); ?>" name="<?php echo $this->get_field_name( 'show_profile' ); ?>"/>
					<?php _e( 'Show profile title', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_stats' ); ?>">
					<input class="widefat" <?php echo $instance['show_stats'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_stats' ); ?>" name="<?php echo $this->get_field_name( 'show_stats' ); ?>"/>
					<?php _e( 'Show user statistics', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_role' ); ?>">
					<input class="widefat" <?php echo $instance['show_role'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_role' ); ?>" name="<?php echo $this->get_field_name( 'show_role' ); ?>"/>
					<?php _e( 'Show user forum role', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_topics' ); ?>">
					<input class="widefat" <?php echo $instance['show_topics'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_topics' ); ?>" name="<?php echo $this->get_field_name( 'show_topics' ); ?>"/>
					<?php _e( 'Show started topics link', 'bbp-core' ); ?></label>
			</div>
		</td>
		<td class="cell-right">
			<div class="d4plib-checkbox-list">
				<label for="<?php echo $this->get_field_id( 'show_replies' ); ?>">
					<input class="widefat" <?php echo $instance['show_replies'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_replies' ); ?>" name="<?php echo $this->get_field_name( 'show_replies' ); ?>"/>
					<?php _e( 'Show posted replies link', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_favorites' ); ?>">
					<input class="widefat" <?php echo $instance['show_favorites'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_favorites' ); ?>" name="<?php echo $this->get_field_name( 'show_favorites' ); ?>"/>
					<?php _e( 'Show favorite topics link', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_subscriptions' ); ?>">
					<input class="widefat" <?php echo $instance['show_subscriptions'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_subscriptions' ); ?>" name="<?php echo $this->get_field_name( 'show_subscriptions' ); ?>"/>
					<?php _e( 'Show subscribed topics link', 'bbp-core' ); ?></label>

				<label for="<?php echo $this->get_field_id( 'show_engagements' ); ?>">
					<input class="widefat" <?php echo $instance['show_engagements'] == 1 ? 'checked="checked"' : ''; ?> type="checkbox" id="<?php echo $this->get_field_id( 'show_engagements' ); ?>" name="<?php echo $this->get_field_name( 'show_engagements' ); ?>"/>
					<?php _e( 'Show engagements link', 'bbp-core' ); ?></label>
			</div>
		</td>
	</tr>
	</tbody>
</table>
