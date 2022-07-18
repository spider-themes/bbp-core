<?php

if (!defined('ABSPATH')) { exit; }

$panels = array();

include(GDBBX_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-right d4p-content-full">
    <form method="get" action="">
        <input type="hidden" name="page" value="gd-bbpress-toolbox-errors" />
        <input type="hidden" name="gdbbx_handler" value="getback" />

        <?php 

        require_once(GDBBX_PATH.'core/grids/errors.php');

        $_grid = new gdbbx_grid_errors();
        $_grid->prepare_items();

        $_grid->search_box(__("Search", "bbp-core"), 'subscriber');

        $_grid->display();

        ?>
    </form>
</div>

<?php 

include(GDBBX_PATH.'forms/shared/bottom.php');
