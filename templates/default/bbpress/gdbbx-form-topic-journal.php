<?php

$label   = apply_filters( 'gdbbx_journal_topic_checkbox_label', __( "Make this a Journal Topic", "bbp-core" ) );

?>

<p>
    <input name="gdbbx_journal_topic" id="gdbbx_journal_topic" type="checkbox" value="1"/>
    <label for="gdbbx_journal_topic"><?php echo $label; ?></label>
</p>
