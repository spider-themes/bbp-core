<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

bbpc_wizard();

require BBPC_PATH . 'forms/wizard/header.php';

require BBPC_PATH . 'forms/wizard/' . bbpc_wizard()->current_panel() . '.php';

require BBPC_PATH . 'forms/wizard/footer.php';
