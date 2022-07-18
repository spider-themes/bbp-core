<?php

$thanks_given = gdbbx_cache()->thanks_get_count_given(bbpress()->displayed_user->ID);
$thanks_received = gdbbx_cache()->thanks_get_count_received(bbpress()->displayed_user->ID);

?>
<div class="bbp-user-profile-about">
    <h4><?php _e("Thanks Counts", "bbp-core"); ?></h4>

    <p class="bbp-user-thanks-received">
        <?php _e("Has thanked", "bbp-core"); ?>:
        <strong class="gdbbx-value"><?php printf(_n("%s time", "%s times", $thanks_given, "bbp-core"), $thanks_given); ?></strong>
    </p>
    <p class="bbp-user-thanks-given">
        <?php _e("Been thanked", "bbp-core"); ?>:
        <strong class="gdbbx-value"><?php printf(_n("%s time", "%s times", $thanks_received, "bbp-core"), $thanks_received); ?></strong>
    </p>
</div>
