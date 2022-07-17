<?php

$topic_id = bbp_get_topic_id();

$_topic_future  = '';
$_topic_publish = 'now';

if ( $topic_id > 0 ) {
	$_topic_future  = get_post_time( 'Y-m-d H:i:s', false, $topic_id );
	$_topic_publish = 'future';
}

$_when_values = [
	'now'    => __( 'Publish the topic now', 'bbp-core' ),
	'future' => __( 'Schedule the topic for future publish', 'bbp-core' ),
];

?>
<fieldset class="bbp-form bbpc-fieldset-scheduler">
	<legend><?php _e( 'Schedule the topic publishing', 'bbp-core' ); ?>:</legend>
	<div>
		<label for="bbpc_schedule_when"><?php _e( 'When to publish the topic', 'bbp-core' ); ?>
			<?php
			bbpc_render_select_dropdown(
				$_when_values,
				$_topic_publish,
				[
					'name' => 'bbpc_schedule_when',
					'id'   => 'bbpc_schedule_when',
				]
			);
			?>
		</label>
	</div>
	<div style="display: <?php echo $_topic_publish == 'future' ? 'block' : 'none'; ?>">
		<label for="bbpc_schedule_datetime"><?php _e( 'Publish on date', 'bbp-core' ); ?>
			<input name="bbpc_schedule_datetime" id="bbpc_schedule_datetime" type="text" value="<?php echo $_topic_future; ?>"/>
		</label>
	</div>
</fieldset>
