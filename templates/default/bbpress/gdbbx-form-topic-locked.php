<?php if ( ! bbp_is_single_forum() ) : ?>
    <div id="bbpress-forums">
	<?php bbp_breadcrumb(); ?>
<?php endif; ?>

    <div id="new-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-form">
        <div class="bbp-template-notice">
            <p><?php echo gdbbx_lock_forums()->message_topic_form_locked(); ?></p>
        </div>
    </div>

<?php if ( ! bbp_is_single_forum() ) : ?>
    </div>
<?php endif;