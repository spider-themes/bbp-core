<?php global $_meta; ?>

<input type="hidden" name="bbpc_privacy_forum_meta" value="edit" />

<h4><?php _e( 'Private Content Checkbox', 'bbp-core' ); ?>:</h4>
<p>
	<label for="bbpc_settings_privacy_enable_topic_private"><?php _e( 'Enable for topic form', 'bbp-core' ); ?></label>
	<?php
	d4p_render_select(
		bbpc_select_forum_settings(),
		[
			'id'       => 'bbpc_settings_privacy_enable_topic_private',
			'name'     => 'bbpc_settings[privacy_enable_topic_private]',
			'class'    => 'widefat',
			'selected' => $_meta['privacy_enable_topic_private'],
		]
	);
	?>
</p>
<p>
	<label for="bbpc_settings_privacy_enable_reply_private"><?php _e( 'Enable for reply form', 'bbp-core' ); ?></label>
	<?php
	d4p_render_select(
		bbpc_select_forum_settings(),
		[
			'id'       => 'bbpc_settings_privacy_enable_reply_private',
			'name'     => 'bbpc_settings[privacy_enable_reply_private]',
			'class'    => 'widefat',
			'selected' => $_meta['privacy_enable_reply_private'],
		]
	);
	?>
</p>
