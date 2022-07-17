				<div class="d4p-wizard-panel-footer">
					<?php

					if ( bbpc_wizard()->is_last_panel() ) {

						?><a class="button-primary" href="admin.php?page=bbp-core-front"><?php _e( 'Finish', 'bbp-core' ); ?></a>
						<?php

					} else {

						?>
					<input type="submit" class="button-primary" value="<?php _e( 'Save and Continue', 'bbp-core' ); ?>" />
						<?php

					}

					?>
				</div>
			</form>
		</div>
	</div>
</div>
