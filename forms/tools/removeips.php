<div class="d4p-group d4p-group-information">
	<h3><?php _e( 'Important', 'bbp-core' ); ?></h3>
	<div class="d4p-group-inner">
		<p><?php _e( 'This tool will remove all IP addresses logged by the bbPress when users create topics or replies.', 'bbp-core' ); ?></p>
		<ul>
			<li><?php _e( "If you don't use these IP's for anything, it is best to remove them, because they can be considered privacy risk.", 'bbp-core' ); ?></li>
			<li><?php _e( "If you don't want to log user IP's, before you run this tool, disable IP logging from 'Features' -> 'Privacy'. After that, you can run this tool to remove previously logged IP's.", 'bbp-core' ); ?></li>
			<li><?php _e( 'This operation is not reversible! It is recommended to create database backup before proceeding, in case you change your mind later.', 'bbp-core' ); ?></li>
		</ul>
	</div>
</div>
<div class="d4p-group d4p-group-tools d4p-group-reset">
	<h3><?php _e( "Remove all logged IP's", 'bbp-core' ); ?></h3>
	<div class="d4p-group-inner">
		<input type="checkbox" class="widefat" name="bbpctools[removeips][remove]" value="on" /> <?php _e( "Remove all IP's logged for all forum content", 'bbp-core' ); ?>
	</div>
</div>
