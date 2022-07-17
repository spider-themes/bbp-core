<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

$_panel = bbpc_admin()->panel === false ? 'whatsnew' : bbpc_admin()->panel;

if ( ! in_array( $_panel, [ 'changelog', 'whatsnew', 'info', 'dev4press' ] ) ) {
	$_panel = 'whatsnew';
}

require BBPC_PATH . 'forms/about/header.php';

require BBPC_PATH . 'forms/about/' . $_panel . '.php';

require BBPC_PATH . 'forms/about/footer.php';
