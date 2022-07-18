<?php

use Dev4Press\Plugin\GDBBX\Attachments\Form;

?>
<fieldset class="bbp-form gdbbx-fieldset-attachments-edit">
    <legend><?php _e( "Current Attachments", "bbp-core" ); ?>:</legend>
    <div>
        <div class="gdbbx-attachments-form-current">
			<?php echo Form::instance()->embed_edit_form(); ?>
        </div>
    </div>
</fieldset>