<?php

if (!defined('ABSPATH')) { exit; }

$_panel = gdbbx_admin()->panel === false ? 'whatsnew' : gdbbx_admin()->panel;

if (!in_array($_panel, array('changelog', 'whatsnew', 'info', 'dev4press'))) {
    $_panel = 'whatsnew';
}

include(GDBBX_PATH.'forms/about/header.php');

include(GDBBX_PATH.'forms/about/'.$_panel.'.php');

include(GDBBX_PATH.'forms/about/footer.php');
