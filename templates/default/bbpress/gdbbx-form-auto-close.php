<?php

use Dev4Press\Plugin\GDBBX\Features\AutoCloseTopics;

$topic_id = bbp_get_topic_id();

$_topic_modify = 'auto';
$_topic_days   = 0;

if ( $topic_id > 0 ) {
	$_meta_modify = get_post_meta( $topic_id, '_gdbbx_modify_auto_close', true );
	$_meta_days   = get_post_meta( $topic_id, '_gdbbx_modify_auto_close_days', true );

	if ( $_meta_modify !== false && ! empty( $_meta_modify ) ) {
		$_topic_modify = $_meta_modify;
	}

	if ( $_meta_days !== false && ! empty( $_meta_days ) ) {
		$_meta_days = absint( $_meta_days );
		$_topic_days = $_meta_days >= AutoCloseTopics::minimum_days_allowed() ? $_meta_days : $_topic_days;
	}
}

$_modify_values = array(
	'auto' => __( "Inherit auto close action for this topic", "bbp-core" ),
	'yes'  => __( "Auto close this topic", "bbp-core" ),
	'no'   => __( "Do not auto close this topic", "bbp-core" )
);

?>
<fieldset class="bbp-form gdbbx-fieldset-auto-close">
    <legend><?php _e( "Topic auto close terms", "bbp-core" ); ?>:</label></legend>
    <div>
        <label for="gdbbx_auto_close_modify"><?php _e( "Auto closing rule", "bbp-core" ); ?>
			<?php gdbbx_render_select_dropdown( $_modify_values, $_topic_modify, array(
				'name' => 'gdbbx_auto_close_modify',
				'id'   => 'gdbbx_auto_close_modify'
			) ); ?>
        </label>
    </div>
    <div>
        <label for="gdbbx_auto_close_days"><?php _e( "Auto closing days", "bbp-core" ); ?>
            <input name="gdbbx_auto_close_days" id="gdbbx_auto_close_days" type="number" min="0" step="1" value="<?php echo $_topic_days; ?>"/>
        </label>
        <span class="description"><?php _e( "Use 0 to inhert the value from forum or global settings.", "bbp-core" ); ?></span>
    </div>
</fieldset>
