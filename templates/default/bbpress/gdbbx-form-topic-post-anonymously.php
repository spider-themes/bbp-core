<?php

use Dev4Press\Plugin\GDBBX\Features\PostAnonymously;

$label   = apply_filters( 'gdbbx_post_anonymously_topic_checkbox_label', __( "Post topic anonymously", "bbp-core" ) );
$checked = PostAnonymously::instance()->is_checked ? ' checked="checked"' : '';

?>

<p>
    <input name="gdbbx_post_anonymously" id="gdbbx_post_anonymously" type="checkbox"<?php echo $checked; ?> value="1"/>
    <label for="gdbbx_post_anonymously"><?php echo $label; ?></label>
</p>
