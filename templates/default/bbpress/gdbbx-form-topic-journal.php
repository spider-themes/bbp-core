<?php

$label = apply_filters( 'bbpc_journal_topic_checkbox_label', __( 'Make this a Journal Topic', 'bbp-core' ) );

?>

<p>
	<input name="bbpc_journal_topic" id="bbpc_journal_topic" type="checkbox" value="1"/>
	<label for="bbpc_journal_topic"><?php echo $label; ?></label>
</p>
