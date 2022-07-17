<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$_classes = [
	'd4p-wrap',
	'wpv-' . BBPC_WPV,
	'd4p-page-' . bbpc_admin()->page,
	'd4p-panel',
	'd4p-panel-' . $_panel,
];

$_tabs = [
	'whatsnew'  => __( 'What&#8217;s New', 'bbp-core' ),
	'info'      => __( 'Info', 'bbp-core' ),
	'changelog' => __( 'Changelog', 'bbp-core' ),
	'dev4press' => __( 'Dev4Press', 'bbp-core' ),
];

?>

<div class="<?php echo join( ' ', $_classes ); ?>">
	<h1><?php printf( __( 'Welcome to BBP Core&nbsp;%s', 'bbp-core' ), bbpc()->info_version ); ?></h1>
	<p class="d4p-about-text">
		Expand bbPress powered forums with attachments upload, BBCodes support, signatures, widgets, quotes, toolbar menu, activity tracking, enhanced widgets, extra views...
	</p>
	<div class="d4p-about-badge" style="background-color: #224760;">
		<img src="<?php echo BBPC_URL; ?>admin/gfx/logo.svg" width="96" height="96" alt="BBP Core Logo" />
		<?php printf( __( 'Version %s', 'bbp-core' ), bbpc()->info_version ); ?>
	</div>

	<h2 class="nav-tab-wrapper wp-clearfix">
		<?php

		foreach ( $_tabs as $_tab => $_label ) {
			echo '<a href="admin.php?page=bbp-core-about&panel=' . $_tab . '" class="nav-tab' . ( $_tab == $_panel ? ' nav-tab-active' : '' ) . '">' . $_label . '</a>';
		}

		?>
	</h2>

	<div class="d4p-about-inner">
