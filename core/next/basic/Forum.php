<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Forum {
	private $keys = array(
		'attachments_status',
		'attachments_topic_form',
		'attachments_reply_form',
		'attachments_hide_from_visitors',
		'attachments_preview_for_visitors',
		'attachments_max_file_size_override',
		'attachments_max_to_upload_override',
		'attachments_mime_types_list_override',
		'topic_auto_close_after_active',
		'topic_auto_close_after_notice',
		'topic_auto_close_after_days',
		'privacy_lock_topic_form',
		'privacy_lock_topic_form_message',
		'privacy_lock_reply_form',
		'privacy_lock_reply_form_message',
		'privacy_enable_topic_private',
		'privacy_enable_reply_private'
	);

	private $keys_valued = array(
		'topic_auto_close_after_days',
		'privacy_lock_topic_form_message',
		'privacy_lock_reply_form_message'
	);

	private $keys_connected = array(
		'attachments_max_file_size_override'   => array(
			'attachments_max_file_size'
		),
		'attachments_max_to_upload_override'   => array(
			'attachments_max_to_upload'
		),
		'attachments_mime_types_list_override' => array(
			'attachments_mime_types_list'
		)
	);

	public $_current = '';
	public $_forum = 0;

	static public $forums = array();

	public function __construct( $id ) {
		$this->_forum = $id;

		if ( ! isset( self::$forums[ $id ] ) && $id > 0 ) {
			$meta                = get_post_meta( $id, '_bbpc_settings', true );
			self::$forums[ $id ] = wp_parse_args( $meta, bbpc_default_forum_settings() );

			$list = get_post_ancestors( $id );

			foreach ( $list as $anc ) {
				if ( ! isset( self::$forums[ $anc ] ) ) {
					$meta                 = get_post_meta( $anc, '_bbpc_settings', true );
					self::$forums[ $anc ] = wp_parse_args( $meta, bbpc_default_forum_settings() );
				}
			}

			foreach ( self::$forums[ $id ] as $key => &$value ) {
				if ( in_array( $key, $this->keys_valued ) ) {
					if ( empty( $value ) ) {
						foreach ( $list as $anc ) {
							if ( ! empty( self::$forums[ $anc ][ $key ] ) ) {
								$value = self::$forums[ $anc ][ $key ];
							}
						}
					}
				} else if ( $value == 'inherit' ) {
					if ( ! empty( $list ) ) {
						foreach ( $list as $anc ) {
							if ( self::$forums[ $anc ][ $key ] != 'inherit' ) {
								$value = self::$forums[ $anc ][ $key ];

								if ( $value == 'yes' && isset( $this->keys_connected[ $key ] ) ) {
									foreach ( $this->keys_connected[ $key ] as $sub ) {
										self::$forums[ $id ][ $sub ] = self::$forums[ $anc ][ $sub ];
									}
								}

								break;
							}
						}
					}
				}

				if ( $value == 'inherit' ) {
					$value = 'default';
				}
			}
		}
	}

	public static function instance( $id = 0 ) : Forum {
		static $instance = array();

		$id = $id == 0 ? bbp_get_forum_id() : $id;

		if ( ! isset( $instance[ $id ] ) ) {
			$instance[ $id ] = new Forum( $id );
		}

		return $instance[ $id ];
	}

	public function topic_auto_close() : Forum {
		$this->_current = 'topic_auto_close_after';

		return $this;
	}

	public function attachments() : Forum {
		$this->_current = 'attachments';

		return $this;
	}

	public function privacy() : Forum {
		$this->_current = 'privacy';

		return $this;
	}

	public function id() {
		return $this->_current;
	}

	public function forum() {
		return $this->_forum;
	}

	public function get( $name, $submeta = '', $default = '' ) {
		$submeta = $submeta == '' ? $this->_current : $submeta;

		return $this->_forum > 0 ? self::$forums[ $this->_forum ][ $submeta . '_' . $name ] : $default;
	}

	public function all( $submeta = '' ) {
		$all = array();

		if ( $this->_forum > 0 ) {
			$submeta = $submeta == '' ? $this->_current : $submeta;
			$submeta .= '_';

			foreach ( self::$forums[ $this->_forum ] as $key => $val ) {
				if ( substr( $key, 0, strlen( $submeta ) ) == $submeta ) {
					$all[ substr( $key, strlen( $submeta ) ) ] = $val;
				}
			}
		}

		return $all;
	}
}
