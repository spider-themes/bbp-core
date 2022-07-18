<?php

if (!defined('ABSPATH')) { exit; }

$panels = array();

include(GDBBX_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-right d4p-content-full">
    <form method="get" action="">
        <input type="hidden" name="page" value="gd-bbpress-toolbox-reported-posts" />
        <input type="hidden" name="gdbbx_handler" value="getback" />

        <?php 

        require_once(GDBBX_PATH.'core/grids/reports.php');

        $_grid = new gdbbx_grid_reports();
        $_grid->prepare_items();

        $_grid->display();

        ?>
    </form>
</div>

<?php

include(GDBBX_PATH.'forms/shared/bottom.php');
