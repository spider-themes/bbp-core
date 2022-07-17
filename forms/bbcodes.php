<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panels = [];

require BBPC_PATH . 'forms/shared/top.php';

?>

	<div class="d4p-content-right d4p-content-full">
		<form method="post" action="">
			<?php settings_fields( 'bbp-core-bbcodes' ); ?>
			<input type="hidden" name="bbpc_handler" value="postback"/>
			<input type="submit" value="<?php _e( 'Save BBCodes Settings', 'bbp-core' ); ?>" class="button-primary"/>

			<a style="float:right;" class="button-secondary" href="admin.php?page=bbp-core-features&panel=bbcodes"><?php _e( 'BBCodes Settings', 'bbp-core' ); ?></a>
			<a style="float:right; margin-right: 10px" class="button-secondary" href="admin.php?page=bbp-core-tools&panel=bbcodes"><?php _e( 'Preview all BBCodes', 'bbp-core' ); ?></a>

			<?php

			require_once BBPC_PATH . 'core/grids/bbcodes.php';

			$_grid = new bbpc_grid_bbcodes();
			$_grid->prepare_items();

			$_grid->display();

			?>
		</form>
	</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
