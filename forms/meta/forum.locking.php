<?php global $_meta; ?>

<h4><?php _e( 'Lock topic form', 'bbp-core' ); ?>:</h4>
<p>
	<label for="bbpc_settings_privacy_lock_topic_form"><?php _e( 'Status', 'bbp-core' ); ?></label>
	<?php
	d4p_render_select(
		bbpc_select_forum_settings(),
		[
			'id'       => 'bbpc_settings_privacy_lock_topic_form',
			'name'     => 'bbpc_settings[privacy_lock_topic_form]',
			'class'    => 'widefat',
			'selected' => $_meta['privacy_lock_topic_form'],
		]
	);
	?>
</p>
<p>
	<label for="bbpc_settings_privacy_lock_topic_form_message"><?php _e( 'Lock Message', 'bbp-core' ); ?></label>
	<input name="bbpc_settings[privacy_lock_topic_form_message]" id="bbpc_settings_privacy_lock_topic_form_message" value="<?php echo esc_attr( $_meta['privacy_lock_topic_form_message'] ); ?>" class="widefat" type="text" />
	<em><?php _e( 'Leave empty to use global lock message.', 'bbp-core' ); ?></em>
</p>
<hr/>
<h4><?php _e( 'Lock reply form', 'bbp-core' ); ?>:</h4>
<p>
	<label for="bbpc_settings_privacy_lock_reply_form"><?php _e( 'Status', 'bbp-core' ); ?></label>
	<?php
	d4p_render_select(
		bbpc_select_forum_settings(),
		[
			'id'       => 'bbpc_settings_privacy_lock_reply_form',
			'name'     => 'bbpc_settings[privacy_lock_reply_form]',
			'class'    => 'widefat',
			'selected' => $_meta['privacy_lock_reply_form'],
		]
	);
	?>
</p>
<p>
	<label for="bbpc_settings_privacy_lock_reply_form_message"><?php _e( 'Lock Message', 'bbp-core' ); ?></label>
	<input name="bbpc_settings[privacy_lock_reply_form_message]" id="bbpc_settings_privacy_lock_reply_form_message" value="<?php echo esc_attr( $_meta['privacy_lock_reply_form_message'] ); ?>" class="widefat" type="text" />
	<em><?php _e( 'Leave empty to use global lock message.', 'bbp-core' ); ?></em>
</p>
