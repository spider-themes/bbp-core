<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$panels = [];

require BBPC_PATH . 'forms/shared/top.php';

?>

<div class="d4p-content-right d4p-content-full">
	<form method="get" action="">
		<input type="hidden" name="page" value="bbp-core-attachments" />
		<input type="hidden" name="bbpc_handler" value="getback" />

		<?php

		require_once BBPC_PATH . 'core/grids/attachments.php';

		$_grid = new bbpc_grid_attachments();
		$_grid->prepare_items();

		$_grid->search_box( __( 'Search', 'bbp-core' ), 'attachment-name' );

		$_grid->display();

		?>
	</form>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
