<?php

namespace SpiderDevs\Plugin\BBPC\BBCodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Source {
	public $map = array(
		'abap',
		'apache',
		'assembly, asm',
		'avrassembly, avrasm',
		'c,cpp, c++',
		'csharp',
		'css',
		'cython',
		'cordpro',
		'diff',
		'docker, dockerfile',
		'generic, standard',
		'groovy',
		'go, golang',
		'html',
		'ini, conf',
		'java',
		'js, javascript, jquery, mootools, ext.js',
		'json',
		'jsx',
		'kotlin',
		'latex',
		'less',
		'lighttpd',
		'lua',
		'mariadb',
		'gfm, md, markdown',
		'octave, matlab',
		'mssql',
		'nginx',
		'nsis',
		'oracledb',
		'php',
		'powershell',
		'prolog',
		'py, python',
		'purebasic, pb',
		'qml',
		'r',
		'raw',
		'routeros',
		'ruby',
		'rust',
		'scala',
		'scss, sass',
		'shell, bash',
		'sql',
		'squirrel',
		'swift',
		'typescript',
		'vhdl',
		'visualbasic, vb',
		'verilog',
		'xml, html',
		'yaml'
	);

	public $enqueued = false;
	public $brushes = array();

	public function __construct() {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			remove_filter( 'bbp_new_topic_pre_content', 'bbp_encode_bad', 10 );
			remove_filter( 'bbp_new_reply_pre_content', 'bbp_encode_bad', 10 );
			remove_filter( 'bbp_edit_topic_pre_content', 'bbp_encode_bad', 10 );
			remove_filter( 'bbp_edit_reply_pre_content', 'bbp_encode_bad', 10 );

			remove_filter( 'bbp_new_topic_pre_content', 'bbp_filter_kses', 30 );
			remove_filter( 'bbp_new_reply_pre_content', 'bbp_filter_kses', 30 );
			remove_filter( 'bbp_edit_topic_pre_content', 'bbp_filter_kses', 30 );
			remove_filter( 'bbp_edit_reply_pre_content', 'bbp_filter_kses', 30 );

			add_filter( 'bbp_new_topic_pre_content', array( $this, 'kses' ), 30 );
			add_filter( 'bbp_new_reply_pre_content', array( $this, 'kses' ), 30 );
			add_filter( 'bbp_edit_topic_pre_content', array( $this, 'kses' ), 30 );
			add_filter( 'bbp_edit_reply_pre_content', array( $this, 'kses' ), 30 );
		}

		if ( has_filter( 'bbp_get_topic_content', 'bbp_make_clickable' ) ) {
			remove_filter( 'bbp_get_topic_content', 'bbp_make_clickable', 4 );

			add_filter( 'bbp_get_topic_content', array( $this, 'make_clickable' ), 4 );
		}

		if ( has_filter( 'bbp_get_reply_content', 'bbp_make_clickable' ) ) {
			remove_filter( 'bbp_get_reply_content', 'bbp_make_clickable', 4 );

			add_filter( 'bbp_get_reply_content', array( $this, 'make_clickable' ), 4 );
		}

		add_filter( 'bbp_get_topic_content', array( $this, 'escape_code' ), 1 );
		add_filter( 'bbp_get_reply_content', array( $this, 'escape_code' ), 1 );
	}

	public static function instance() : Source {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Source();
		}

		return $instance;
	}

	public function enqueue() {
		if ( ! $this->enqueued ) {
			$theme = strtolower( bbpc()->get( 'bbcodes_scode_enlighter', 'tools' ) );

			wp_enqueue_style( 'bbpc-enlighter', BBPC_URL . 'd4pjs/enlighter/enlighterjs.min.css', array(), bbpc()->info_version );
			wp_enqueue_style( 'bbpc-enlighter-theme', BBPC_URL . 'd4pjs/enlighter/enlighterjs.' . $theme . '.min.css', array(), bbpc()->info_version );
			wp_enqueue_script( 'bbpc-enlighter', BBPC_URL . 'd4pjs/enlighter/enlighterjs.min.js', array(), bbpc()->info_version );
		}
	}

	public function make_clickable( $content ) {
		if ( ! preg_match( '|(\[scode[^\]]*?\])(.*?)(\[/scode\])|is', $content ) ) {
			$content = bbp_make_clickable( $content );
		}

		return $content;
	}

	public function kses( $content ) {
		if ( ! preg_match( '|\[scode[^\]]*?\].*?\[/scode\]|is', $content ) ) {
			$content = bbp_encode_bad( $content );

			return bbp_filter_kses( $content );
		}

		kses_remove_filters();

		$content = preg_replace_callback( '|(\[scode[^\]]*?\])(.*?)(\[/scode\])|is', array(
			$this,
			'kses_before'
		), $content );
		$content = bbp_filter_kses( $content );

		return preg_replace_callback( '|(\[scode[^\]]*?\])(.*?)(\[/scode\])|is', array(
			$this,
			'kses_after'
		), $content );
	}

	public function kses_before( $matches ) : string {
		$replaced_code = str_replace( '<', '%%BBPCLT%%', $matches[2] );
		$replaced_code = str_replace( '>', '%%BBPCRT%%', $replaced_code );

		return $matches[1] . $replaced_code . $matches[3];
	}

	public function kses_after( $matches ) : string {
		$replaced_code = str_replace( '%%BBPCLT%%', '<', $matches[2] );
		$replaced_code = str_replace( '%%BBPCRT%%', '>', $replaced_code );

		return $matches[1] . $replaced_code . $matches[3];
	}

	public function escape_code( $content ) {
		return preg_replace_callback( '|(\[scode[^\]]*?\])(.*?)(\[/scode\])|is', array(
			$this,
			'escape_code_callback'
		), $content );
	}

	public function escape_code_callback( $matches ) : string {
		$code = $matches[2];

		if ( strpos( $code, "<" ) !== false || strpos( $code, ">" ) !== false || strpos( $code, '"' ) !== false || strpos( $code, "'" ) !== false || preg_match( '/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $code ) ) {
			if ( strpos( $code, "<" ) === false && strpos( $code, ">" ) === false && ! preg_match( '/&(?!lt;)(?!gt;)(?!amp;)(?!quot;)(?!#039;)/i', $code ) ) {
				$pre_replaced_code = str_replace( array( '"', '”', '“' ), '&quot;', $code );

				$replaced_code = $matches[1] . str_replace( "'", '&#039;', $pre_replaced_code ) . $matches[3];
			} else {
				$replaced_code = $matches[1] . htmlspecialchars( $code, ENT_QUOTES ) . $matches[3];
			}
		} else {
			$replaced_code = $matches[1] . $code . $matches[3];
		}

		return $replaced_code;
	}

	public function is_brush_valid( $brush ) : bool {
		$brushes = strtolower( join( ' ', $this->map ) );
		$brushes = explode( ' ', $brushes );

		return in_array( $brush, $brushes );
	}
}