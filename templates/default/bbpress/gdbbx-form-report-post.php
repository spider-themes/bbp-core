<div class="gdbbx-report-template" style="display: none;">
    <div>
        <div class="gdbbx-report-form">
            <label>
                <span><?php _e( "Report Message", "bbp-core" ); ?></span>
                <input type="text" value=""/>
            </label>
            <p class="description"><?php _e( "Use this only to report spam, harassment, fighting, or rude content.", "bbp-core" ); ?></p>
            <button class="gdbbx-report-send"><?php _e( "Send Report", "bbp-core" ); ?></button>
            <button class="gdbbx-report-cancel"><?php _e( "Cancel", "bbp-core" ); ?></button>
        </div>
        <div class="gdbbx-report-sending" style="display: none;">
			<?php _e( "Please wait, sending report.", "bbp-core" ); ?>
        </div>
        <div class="gdbbx-report-sent" style="display: none;">
			<?php _e( "Thank you, your report has been sent.", "bbp-core" ); ?>
        </div>
    </div>
</div>