<?php
use SpiderDevs\Plugin\BBPC\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require BBPC_PATH . 'forms/shared/top.php';
?>

<div class="d4p-plugin-dashboard">
	<div class="d4p-content-left">
		<div class="d4p-dashboard-badge" style="background-color: #224760">
			<div aria-hidden="true" class="d4p-plugin-logo">
				<img src="<?php echo BBPC_URL; ?>admin/gfx/logo.svg" width="160" height="160" alt="BBP Core Logo" />
			</div>
			<h3>BBP Core</h3>

			<h5>
				<?php
				_e( 'Version', 'bbp-core' );
				echo ': ' . bbpc()->info->version;

				if ( bbpc()->info->status != 'stable' ) {
					echo ' - <span class="d4p-plugin-unstable" style="color: #fff; font-weight: 900;">' . strtoupper( bbpc()->info->status ) . '</span>';
				}
				?>
			</h5>
		</div>

		<div class="d4p-buttons-group">
			<a class="button-secondary" href="admin.php?page=bbp-core-features"><i aria-hidden="true" class="fa fa-puzzle-piece fa-fw"></i> <?php _e( 'Features', 'bbp-core' ); ?></a>
			<a class="button-secondary" href="admin.php?page=bbp-core-settings"><i aria-hidden="true" class="fa fa-cogs fa-fw"></i> <?php _e( 'Settings', 'bbp-core' ); ?></a>
			<a class="button-secondary" href="admin.php?page=bbp-core-attachments"><i aria-hidden="true" class="fa fa-paperclip fa-fw"></i> <?php _e( 'Attachments', 'bbp-core' ); ?></a>
			<a class="button-secondary" href="admin.php?page=bbp-core-tools"><i aria-hidden="true" class="fa fa-wrench fa-fw"></i> <?php _e( 'Tools', 'bbp-core' ); ?></a>
		</div>

		<div class="d4p-buttons-group">
			<a class="button-secondary" href="admin.php?page=bbp-core-about"><i aria-hidden="true" class="fa fa-info-circle fa-fw"></i> <?php _e( 'About', 'bbp-core' ); ?></a>
		</div>
	</div>
	<div class="d4p-content-right">
		<?php

		require BBPC_PATH . 'forms/dashboard/basic.php';
		require BBPC_PATH . 'forms/dashboard/users.php';

		?>
		<div class="d4p-clearfix"></div>
		<?php

		if ( Plugin::instance()->is_enabled( 'thanks' ) ) {
			include BBPC_PATH . 'forms/dashboard/thanks.php';
		}

		if ( Plugin::instance()->is_enabled( 'report' ) ) {
			include BBPC_PATH . 'forms/dashboard/report.php';
		}

		?>
		<div class="d4p-clearfix"></div>
			</div>
</div>

<?php

require BBPC_PATH . 'forms/shared/bottom.php';
