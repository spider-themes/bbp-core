<?php

use SpiderDevs\Plugin\BBPC\Features\PostAnonymously;

$label   = apply_filters( 'bbpc_post_anonymously_reply_checkbox_label', __( 'Post reply anonymously', 'bbp-core' ) );
$checked = PostAnonymously::instance()->is_checked ? ' checked="checked"' : '';

?>

<p>
	<input name="bbpc_post_anonymously" id="bbpc_post_anonymously" type="checkbox"<?php echo $checked; ?> value="1"/>
	<label for="bbpc_post_anonymously"><?php echo $label; ?></label>
</p>
