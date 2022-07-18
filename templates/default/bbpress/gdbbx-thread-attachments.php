<?php

use Dev4Press\Plugin\GDBBX\Attachments\Topic;

$files = Topic::instance()->total;

?>
<div id="gdbbx-attachments-thread-wrapper" class="gdbbx-attachments-thread gdbbx-attachments-thread-<?php echo Topic::instance()->format; ?>">
    <div class="gdbbx-attachments-thread-control">
        <a data-topic="<?php echo Topic::instance()->topic_id; ?>" data-nonce="<?php echo Topic::instance()->nonce; ?>" href="#">
			<?php _e( "Show all the attachments in this thread", "bbp-core" ); ?>
            (<?php echo sprintf( _n( "%s File", "%s Files", $files, "bbp-core" ), $files ); ?>)
        </a>
    </div>
    <fieldset class="bbp-form" style="display: none;">
        <legend><?php _e( "All the attachments for the thread", "bbp-core" ); ?></legend>
        <div class="gdbbx-attachments-thread-inner">
	        <?php echo Topic::instance()->placeholders(); ?>
        </div>
    </fieldset>
</div>