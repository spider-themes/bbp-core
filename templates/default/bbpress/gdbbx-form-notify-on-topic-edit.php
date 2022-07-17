<fieldset class="bbp-form bbpc-fieldset-notify-on-topic-edit">
	<legend><?php _e( 'On edit notifications', 'bbp-core' ); ?>:</legend>
	<?php if ( bbp_get_topic_author_id() != bbp_get_current_user_id() ) { ?>
		<div>
			<input name="bbpc_notify_on_edit_author" id="bbpc_notify_on_edit_author" type="checkbox" value="1"/>
			<label for="bbpc_notify_on_edit_author"><?php _e( 'Notify topic author', 'bbp-core' ); ?></label>
		</div>
	<?php } ?>
	<div>
		<input name="bbpc_notify_on_edit_subscribers" id="bbpc_notify_on_edit_subscribers" type="checkbox" value="1"/>
		<label for="bbpc_notify_on_edit_subscribers"><?php _e( 'Notify topic subscribers', 'bbp-core' ); ?></label>
	</div>
</fieldset>
