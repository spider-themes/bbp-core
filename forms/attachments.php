<?php

if (!defined('ABSPATH')) { exit; }

$panels = array();

include(GDBBX_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-right d4p-content-full">
    <form method="get" action="">
        <input type="hidden" name="page" value="gd-bbpress-toolbox-attachments" />
        <input type="hidden" name="gdbbx_handler" value="getback" />

        <?php 

        require_once(GDBBX_PATH.'core/grids/attachments.php');

        $_grid = new gdbbx_grid_attachments();
        $_grid->prepare_items();

        $_grid->search_box(__("Search", "bbp-core"), 'attachment-name');

        $_grid->display();

        ?>
    </form>
</div>

<?php

include(GDBBX_PATH.'forms/shared/bottom.php');
