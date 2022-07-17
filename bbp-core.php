<?php
/*
Plugin Name:       BBP Core
Plugin URI:        https://helpdesk.spider-themes.net/bbp-core
Description:       Responsive and modern theme to fully replace default bbPress theme templates and styles, with multiple colour schemes, options panel and customizer control.
Author:            spiderdevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.0
Requires at least: 5.0
Tested up to:      5.9
Requires PHP:      7.4
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'BBPC_RUN_INSTALL' ) ) {
	define( 'BBPC_RUN_INSTALL', true );
}

if ( ! defined( 'BBPC_RUN_UPDATE' ) ) {
	define( 'BBPC_RUN_UPDATE', true );
}

$bbpc_dirname_basic = dirname( __FILE__ ) . '/';
$bbpc_urlname_basic = plugins_url( '/', __FILE__ );

define( 'BBPC_PATH', $bbpc_dirname_basic );
define( 'BBPC_URL', $bbpc_urlname_basic );
define( 'BBPC_D4PLIB', $bbpc_dirname_basic . 'd4plib/' );

require_once BBPC_D4PLIB . 'd4p.core.php';

d4p_prepare_object_cache( BBPC_D4PLIB );

d4p_includes(
	[
		[
			'name'      => 'cache-wordpress',
			'directory' => 'functions',
		],
		[
			'name'      => 'transient-dbquery',
			'directory' => 'functions',
		],
		[
			'name'      => 'datetime',
			'directory' => 'core',
		],
		'functions',
		'sanitize',
		'access',
		'wp',
	],
	BBPC_D4PLIB
);

global $_bbpc_settings;

require_once BBPC_PATH . 'core/functions/bbpress.php';
require_once BBPC_PATH . 'core/functions/features.php';
require_once BBPC_PATH . 'core/functions/conditionals.php';

require_once BBPC_PATH . 'core/version.php';
require_once BBPC_PATH . 'core/settings.php';

require_once BBPC_PATH . 'core/next/autoload.php';
require_once BBPC_PATH . 'core/next/bridge.php';

bbpc_plugin();

$_bbpc_settings = new bbpc_core_settings();

/** @return bbpc_core_settings */
function bbpc() {
	global $_bbpc_settings;

	return $_bbpc_settings;
}

if ( D4P_ADMIN ) {
	d4p_includes(
		[
			[
				'name'      => 'functions',
				'directory' => 'admin',
			],
			[
				'name'      => 'walkers',
				'directory' => 'admin',
			],
		],
		BBPC_D4PLIB
	);

	require_once BBPC_PATH . 'core/admin.php';
}

if ( D4P_AJAX ) {
	bbpc_ajax();
}

require_once BBPC_PATH . 'core/functions/deprecated.php';
require_once BBPC_PATH . 'core/functions/public.php';

//TODO:
