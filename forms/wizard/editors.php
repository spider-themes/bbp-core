<div>
	<p>
		<?php _e( 'You can easily replace default, basic editor used for topics and replies with one of the additional editors supported by bbPress and BBP Core.', 'bbp-core' ); ?>
	</p>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
	<p><?php _e( 'Do you want to replace basic content editor?', 'bbp-core' ); ?></p>
	<div>
		<em><?php _e( 'Select editor that you think will provide best experience for your users. Each editor has good and bad sides, and it is not simple to give recommendation.', 'bbp-core' ); ?></em>
		<span>
			<input class="bbpc-wizard-connect-switch" data-connect="bbpc-wizard-connect-editor-replace" type="radio" name="bbpc[wizard][editors][replace]" value="yes" id="bbpc-wizard-editors-replace-yes" />
			<label for="bbpc-wizard-editors-replace-yes"><?php _e( 'Yes', 'bbp-core' ); ?></label>
		</span>
		<span>
			<input class="bbpc-wizard-connect-switch" data-connect="bbpc-wizard-connect-editor-replace" type="radio" name="bbpc[wizard][editors][replace]" value="no" id="bbpc-wizard-editors-replace-no" checked />
			<label for="bbpc-wizard-editors-replace-no"><?php _e( 'No', 'bbp-core' ); ?></label>
		</span>
	</div>
</div>

<div class="d4p-wizard-connect-wrapper" id="bbpc-wizard-connect-editor-replace" style="display: none;">
	<div class="d4p-wizard-option-block d4p-wizard-block-select">
		<p><?php _e( 'Which editor type you want to use?', 'bbp-core' ); ?></p>
		<div>
			<em><?php _e( 'Pick one of the available editors.', 'bbp-core' ); ?></em>
			<span>
				<label for="bbpc-wizard-editors-mime"><?php _e( 'Topic Content Editor', 'bbp-core' ); ?></label>
				<select name="bbpc[wizard][editors][editor]" id="bbpc-wizard-attachments-mime">
					<option value="basic"><?php _e( 'Basic textarea', 'bbp-core' ); ?></option>
					<option value="quicktags"><?php _e( 'Quicktags textarea', 'bbp-core' ); ?></option>
					<option value="tinymce"><?php _e( 'TinyMCE Rich Editor', 'bbp-core' ); ?></option>
					<option value="teeny"><?php _e( 'TinyMCE Teeny Editor', 'bbp-core' ); ?></option>
					<?php if ( bbpc()->get( 'bbcodes_active', 'tools' ) ) { ?>
						<option value="bbcodes"><?php _e( 'BBCodes Toolbar with Basic Textarea', 'bbp-core' ); ?></option>
					<?php } ?>
				</select>
			</span>
		</div>
	</div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
	<p><?php _e( 'Do you want to allow use of Media Library to regular users?', 'bbp-core' ); ?></p>
	<div>
		<em><?php _e( 'If you have set editor to TinyMCE, depending on the user role, some users will be able to use Media Library, but regular users and moderators will not be able to use it due to the roles restrictions. This option will change that, and participants and moderators will see Media button in the TinyMCE editor.', 'bbp-core' ); ?></em>
		<span>
			<input type="radio" name="bbpc[wizard][editors][library]" value="yes" id="bbpc-wizard-editors-library-yes" />
			<label for="bbpc-wizard-editors-library-yes"><?php _e( 'Yes', 'bbp-core' ); ?></label>
		</span>
		<span>
			<input type="radio" name="bbpc[wizard][editors][library]" value="no" id="bbpc-wizard-editors-library-no" checked />
			<label for="bbpc-wizard-editors-library-no"><?php _e( 'No', 'bbp-core' ); ?></label>
		</span>
	</div>
</div>
