<h3><?php _e( "Individual forum settings", "bbp-core" ); ?></h3>
<?php

require_once( GDBBX_PATH . 'core/admin/install.php' );

$info = gdbbx_convert_forum_settings();

if ( $info['forums'] > 0 ) {
	_e( "Converted forum settings for", "bbp-core" );

	echo ': ' . sprintf( _n( "%s forum", "%s forums", $info['forums'], "bbp-core" ), $info['forums'] ) . '.';
} else {
	_e( "Nothing to convert.", "bbp-core" );
}

/**
 * ?>
 * <h3><?php _e("Forums last post date", "bbp-core"); ?></h3>
 * <?php
 *
 * $info = gdbbx_forum_last_post_date();
 *
 * if ($info['forums'] > 0) {
 * _e("Updated last post date for", "bbp-core");
 *
 * echo ': '.sprintf(_n("%s forum", "%s forums", $info['forums'], "bbp-core"), $info['forums']).'.';
 * } else {
 * _e("Nothing to update.", "bbp-core");
 * }
 */
