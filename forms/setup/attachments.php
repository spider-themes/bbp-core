<h3><?php _e( "Attachments assignments", "bbp-core" ); ?></h3>
<?php

require_once( GDBBX_PATH . 'core/admin/install.php' );

$rows = gdbbx_convert_attachments_assignments();

if ( $rows > 0 ) {
	_e( "Attachments conversion completed.", "bbp-core" );
} else {
	_e( "Nothing to convert.", "bbp-core" );
}
