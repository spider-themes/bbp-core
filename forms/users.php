<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$panels = [];

require BBPC_PATH . 'forms/shared/top.php';

?>

<div class="d4p-content-right d4p-content-full">
	<?php

	require_once BBPC_PATH . 'core/grids/users.php';

	$_grid = new bbpc_grid_users();
	$_grid->prepare_items();
	$_grid->views();

	?>

	<form method="get" action="">
		<input type="hidden" name="page" value="bbp-core-users" />
		<input type="hidden" name="bbpc_handler" value="getback" />
		<input type="hidden" name="view" value="<?php echo $_grid->current_view; ?>" />

		<?php

		$_grid->search_box( __( 'Search', 'bbp-core' ), 'user' );

		$_grid->display();

		?>
	</form>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
