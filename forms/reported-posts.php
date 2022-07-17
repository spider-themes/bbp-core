<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$panels = [];

require BBPC_PATH . 'forms/shared/top.php';

?>

<div class="d4p-content-right d4p-content-full">
	<form method="get" action="">
		<input type="hidden" name="page" value="bbp-core-reported-posts" />
		<input type="hidden" name="bbpc_handler" value="getback" />

		<?php

		require_once BBPC_PATH . 'core/grids/reports.php';

		$_grid = new bbpc_grid_reports();
		$_grid->prepare_items();

		$_grid->display();

		?>
	</form>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
