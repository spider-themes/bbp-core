<?php

if (!defined('ABSPATH')) { exit; }

gdbbx_wizard();

include(GDBBX_PATH.'forms/wizard/header.php');

include(GDBBX_PATH.'forms/wizard/'.gdbbx_wizard()->current_panel().'.php');

include(GDBBX_PATH.'forms/wizard/footer.php');
