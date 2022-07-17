<?php use SpiderDevs\Plugin\BBPC\Features\LockTopics; ?>

<div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form">
	<div class="bbp-template-notice">
		<p><?php echo LockTopics::instance()->message_topic_reply_lock(); ?></p>
	</div>
</div>
