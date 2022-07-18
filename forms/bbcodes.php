<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panels = array();

include( GDBBX_PATH . 'forms/shared/top.php' );

?>

    <div class="d4p-content-right d4p-content-full">
        <form method="post" action="">
			<?php settings_fields( 'gd-bbpress-toolbox-bbcodes' ); ?>
            <input type="hidden" name="gdbbx_handler" value="postback"/>
            <input type="submit" value="<?php _e( "Save BBCodes Settings", "bbp-core" ); ?>" class="button-primary"/>

            <a style="float:right;" class="button-secondary" href="admin.php?page=gd-bbpress-toolbox-features&panel=bbcodes"><?php _e( "BBCodes Settings", "bbp-core" ); ?></a>
            <a style="float:right; margin-right: 10px" class="button-secondary" href="admin.php?page=gd-bbpress-toolbox-tools&panel=bbcodes"><?php _e( "Preview all BBCodes", "bbp-core" ); ?></a>

			<?php

			require_once( GDBBX_PATH . 'core/grids/bbcodes.php' );

			$_grid = new gdbbx_grid_bbcodes();
			$_grid->prepare_items();

			$_grid->display();

			?>
        </form>
    </div>

<?php

include( GDBBX_PATH . 'forms/shared/bottom.php' );
