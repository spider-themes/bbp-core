<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$panels = [];

require BBPC_PATH . 'forms/shared/top.php';

?>

<div class="d4p-content-right d4p-content-full">
	<form method="get" action="">
		<input type="hidden" name="page" value="bbp-core-errors" />
		<input type="hidden" name="bbpc_handler" value="getback" />

		<?php

		require_once BBPC_PATH . 'core/grids/errors.php';

		$_grid = new bbpc_grid_errors();
		$_grid->prepare_items();

		$_grid->search_box( __( 'Search', 'bbp-core' ), 'subscriber' );

		$_grid->display();

		?>
	</form>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
