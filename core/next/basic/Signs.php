<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

use SpiderDevs\Plugin\BBPC\Features\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Signs {
	public $mode = 'font';

	public function __construct() {
		$this->mode = Icons::instance()->mode();
	}

	public static function instance() : Signs {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Signs();
		}

		return $instance;
	}

	public function attachments( $count ) : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-paperclip" title="' . sprintf( _n( "%s attachment", "%s attachments", $count, "bbp-core" ), $count ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-paperclip" title="' . sprintf( _n( "%s attachment", "%s attachments", $count, "bbp-core" ), $count ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_attachments', $render, $this->mode );
	}

	public function new_replies() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-arrow" title="' . __( "First new reply", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-message-dots" title="' . __( "First new reply", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_new_replies', $render, $this->mode );
	}

	public function private_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-private" title="' . __( "Private topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-eye-slash" title="' . __( "Private topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_private_topic', $render, $this->mode );
	}

	public function private_replies() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-private" title="' . __( "Topic has private replies", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-eye-slash" title="' . __( "Topic has private replies", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_private_replies', $render, $this->mode );
	}

	public function replied_to_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-reply" title="' . __( "Replied to this topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-messages" title="' . __( "Replied to this topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_replied_to_topic', $render, $this->mode );
	}

	public function sticky_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-stick" title="' . __( "This is sticky topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-thumbtack" title="' . __( "This is sticky topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_sticky_topic', $render, $this->mode );
	}

	public function locked_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-lock" title="' . __( "Locked Topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-lock" title="' . __( "Locked Topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_locked_topic', $render, $this->mode );
	}

	public function closed_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-close" title="' . __( "Closed Topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-circle-xmark" title="' . __( "Closed Topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_closed_topic', $render, $this->mode );
	}

	public function hidden_forum() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-private" title="' . __( "Hidden forum", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-eye-slash" title="' . __( "Hidden forum", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_private_forum', $render, $this->mode );
	}

	public function private_forum() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-private" title="' . __( "Private forum", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-user-secret" title="' . __( "Private forum", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_private_forum', $render, $this->mode );
	}

	public function closed_forum() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-close" title="' . __( "Closed Forum", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-circle-xmark" title="' . __( "Closed Forum", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_closed_forum', $render, $this->mode );
	}

	public function journal_topic() : string {
		$render = '';

		if ( $this->mode == 'images' ) {
			$render = '<span class="bbpc-image-mark bbpc-image-book" title="' . __( "Journal Topic", "bbp-core" ) . '"></span>';
		} else if ( $this->mode == 'font' ) {
			$render = '<i class="bbpc-icon-mark bbpc-icon bbpc-icon-book" title="' . __( "Journal Topic", "bbp-core" ) . '"></i> ';
		}

		return (string) apply_filters( 'bbpc_icon_for_journal_topic', $render, $this->mode );
	}
}
