<?php

use SpiderDevs\Plugin\BBPC\Attachments\Form;

?>
<fieldset class="bbp-form bbpc-fieldset-attachments-edit">
	<legend><?php _e( 'Current Attachments', 'bbp-core' ); ?>:</legend>
	<div>
		<div class="bbpc-attachments-form-current">
			<?php echo Form::instance()->embed_edit_form(); ?>
		</div>
	</div>
</fieldset>
