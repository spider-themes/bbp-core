<?php

/*
Plugin Name:       BBP Core
Plugin URI:        https://spider-themes.net/bbp-core
Description:       Expand bbPress powered forums with attachments upload, BBCodes support, signatures, widgets, quotes, toolbar menu, activity tracking, enhanced widgets, extra views...
Author:            SpiderDevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.0
Requires at least: 5.3
Tested up to:      5.9
Requires PHP:      7.2
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'GDBBX_RUN_INSTALL' ) ) {
	define( 'GDBBX_RUN_INSTALL', true );
}

if ( ! defined( 'GDBBX_RUN_UPDATE' ) ) {
	define( 'GDBBX_RUN_UPDATE', true );
}

$gdbbx_dirname_basic = dirname( __FILE__ ) . '/';
$gdbbx_urlname_basic = plugins_url( '/', __FILE__ );

define( 'GDBBX_PATH', $gdbbx_dirname_basic );
define( 'GDBBX_URL', $gdbbx_urlname_basic );
define( 'GDBBX_D4PLIB', $gdbbx_dirname_basic . 'd4plib/' );

require_once( GDBBX_D4PLIB . 'd4p.core.php' );

d4p_prepare_object_cache( GDBBX_D4PLIB );

d4p_includes( array(
	array( 'name' => 'cache-wordpress', 'directory' => 'functions' ),
	array( 'name' => 'transient-dbquery', 'directory' => 'functions' ),
	array( 'name' => 'datetime', 'directory' => 'core' ),
	'functions',
	'sanitize',
	'access',
	'wp'
), GDBBX_D4PLIB );

global $_gdbbx_settings;

require_once( GDBBX_PATH . 'core/functions/bbpress.php' );
require_once( GDBBX_PATH . 'core/functions/features.php' );
require_once( GDBBX_PATH . 'core/functions/conditionals.php' );

require_once( GDBBX_PATH . 'core/version.php' );
require_once( GDBBX_PATH . 'core/settings.php' );

require_once( GDBBX_PATH . 'core/next/autoload.php' );
require_once( GDBBX_PATH . 'core/next/bridge.php' );

gdbbx_plugin();

$_gdbbx_settings = new gdbbx_core_settings();

/** @return gdbbx_core_settings */
function gdbbx() {
	global $_gdbbx_settings;

	return $_gdbbx_settings;
}

if ( D4P_ADMIN ) {
	d4p_includes( array(
		array( 'name' => 'functions', 'directory' => 'admin' ),
		array( 'name' => 'walkers', 'directory' => 'admin' )
	), GDBBX_D4PLIB );

	require_once( GDBBX_PATH . 'core/admin.php' );
}

if ( D4P_AJAX ) {
	gdbbx_ajax();
}

require_once( GDBBX_PATH . 'core/functions/deprecated.php' );
require_once( GDBBX_PATH . 'core/functions/public.php' );
