<?php

require BBPC_PATH . 'forms/shared/top.php';

?>

<div class="d4p-content-left">
	<div class="d4p-panel-title">
		<i aria-hidden="true" class="fa fa-bug"></i>
		<h3><?php _e( 'Invalid Request', 'bbp-core' ); ?></h3>
	</div>
</div>
<div class="d4p-content-right">
	<h3><?php _e( 'Error', 'bbp-core' ); ?></h3>
	<?php
		_e( "Current request URL is invalid, and it can't be processed.", 'bbp-core' );
	?>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
