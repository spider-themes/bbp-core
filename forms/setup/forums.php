<h3><?php _e( 'Individual forum settings', 'bbp-core' ); ?></h3>
<?php

require_once BBPC_PATH . 'core/admin/install.php';

$info = bbpc_convert_forum_settings();

if ( $info['forums'] > 0 ) {
	_e( 'Converted forum settings for', 'bbp-core' );

	echo ': ' . sprintf( _n( '%s forum', '%s forums', $info['forums'], 'bbp-core' ), $info['forums'] ) . '.';
} else {
	_e( 'Nothing to convert.', 'bbp-core' );
}

/**
 * ?>
 * <h3><?php _e("Forums last post date", "bbp-core"); ?></h3>
 * <?php
 *
 * $info = bbpc_forum_last_post_date();
 *
 * if ($info['forums'] > 0) {
 * _e("Updated last post date for", "bbp-core");
 *
 * echo ': '.sprintf(_n("%s forum", "%s forums", $info['forums'], "bbp-core"), $info['forums']).'.';
 * } else {
 * _e("Nothing to update.", "bbp-core");
 * }
 */
