<?php

namespace Dev4Press\Plugin\GDBBX\Database;

use Dev4Press\Plugin\GDBBX\Library\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Core extends DB {
	public $_prefix = 'gdbbx';
	public $_tables = array(
		'actions',
		'actionmeta',
		'attachments',
		'online',
		'tracker'
	);
	public $_metas = array(
		'action' => 'action_id'
	);

	protected function _reply_inner_query( $topic_id ) {
		return $this->wpdb()->prepare(
			"SELECT ID FROM " . $this->wpdb()->posts . " WHERE post_parent = %d and post_type = %s",
			$topic_id,
			bbp_get_reply_post_type() );
	}
}