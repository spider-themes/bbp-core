<?php

if (!defined('ABSPATH')) { exit; }

$panels = array();

include(GDBBX_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-right d4p-content-full">
    <?php

    require_once(GDBBX_PATH.'core/grids/users.php');

    $_grid = new gdbbx_grid_users();
    $_grid->prepare_items();
    $_grid->views();

    ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="gd-bbpress-toolbox-users" />
        <input type="hidden" name="gdbbx_handler" value="getback" />
        <input type="hidden" name="view" value="<?php echo $_grid->current_view; ?>" />

        <?php 

        $_grid->search_box(__("Search", "bbp-core"), 'user');

        $_grid->display();

        ?>
    </form>
</div>

<?php 

include(GDBBX_PATH.'forms/shared/bottom.php');
