                <div class="d4p-wizard-panel-footer">
                    <?php

                    if (gdbbx_wizard()->is_last_panel()) {
                        
                    ?><a class="button-primary" href="admin.php?page=gd-bbpress-toolbox-front"><?php _e("Finish", "bbp-core"); ?></a><?php

                    } else {
                        
                    ?><input type="submit" class="button-primary" value="<?php _e("Save and Continue", "bbp-core"); ?>" /><?php

                    }

                    ?>
                </div>
            </form>
        </div>
    </div>
</div>
