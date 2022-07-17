<?php

/*
Name:    d4pLib_Sanitize
Version: v2.8.13
Author:  Milan Petrovic
Email:   support@dev4press.com
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2020 Milan Petrovic (email: support@dev4press.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

if ( ! function_exists( 'd4p_sanitize_file_path' ) ) {
	function d4p_sanitize_file_path( $filename ) {
		$filename_raw = $filename;

		$special_chars = apply_filters(
			'd4p_sanitize_file_path_chars',
			[
				'?',
				'[',
				']',
				'/',
				'\\',
				'=',
				'<',
				'>',
				':',
				';',
				',',
				"'",
				'"',
				'&',
				'$',
				'#',
				'*',
				'(',
				')',
				'|',
				'~',
				'`',
				'!',
				'{',
				'}',
				'%',
				'+',
				chr( 0 ),
			],
			$filename_raw
		);

		$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
		$filename = str_replace( $special_chars, '', $filename );
		$filename = str_replace( [ '%20', '+' ], '-', $filename );
		$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
		$filename = trim( $filename, '.-_' );

		return apply_filters( 'd4p_sanitize_file_path', $filename, $filename_raw );
	}
}

if ( ! function_exists( 'd4p_sanitize_key_expanded' ) ) {
	function d4p_sanitize_key_expanded( $key ) {
		$key = strtolower( $key );
		$key = preg_replace( '/[^a-z0-9._\-]/', '', $key );

		return $key;
	}
}

if ( ! function_exists( 'd4p_sanitize_extended' ) ) {
	function d4p_sanitize_extended( $text, $tags = null, $protocols = [], $strip_shortcodes = false ) {
		$tags = is_null( $tags ) ? wp_kses_allowed_html( 'post' ) : $tags;
		$text = stripslashes( $text );

		if ( $strip_shortcodes ) {
			$text = strip_shortcodes( $text );
		}

		return wp_kses( trim( $text ), $tags, $protocols );
	}
}

if ( ! function_exists( 'd4p_sanitize_basic' ) ) {
	function d4p_sanitize_basic( $text, $strip_shortcodes = true ) {
		$text = stripslashes( $text );

		if ( $strip_shortcodes ) {
			$text = strip_shortcodes( $text );
		}

		return trim( wp_kses( $text, [] ) );
	}
}

if ( ! function_exists( 'd4p_sanitize_html' ) ) {
	function d4p_sanitize_html( $text, $tags = null, $protocols = [] ) {
		$tags = is_null( $tags ) ? wp_kses_allowed_html( 'post' ) : $tags;

		return wp_kses( trim( stripslashes( $text ) ), $tags, $protocols );
	}
}

if ( ! function_exists( 'd4p_sanitize_slug' ) ) {
	function d4p_sanitize_slug( $text ) {
		return trim( sanitize_title_with_dashes( stripslashes( $text ) ), "-_ \t\n\r\0\x0B" );
	}
}

if ( ! function_exists( 'd4p_sanitize_html_classes' ) ) {
	function d4p_sanitize_html_classes( $classes ) {
		$list = explode( ' ', trim( stripslashes( $classes ) ) );
		$list = array_map( 'sanitize_html_class', $list );

		return trim( join( ' ', $list ) );
	}
}

if ( ! function_exists( 'd4p_sanitize_basic_array' ) ) {
	function d4p_sanitize_basic_array( $input, $strip_shortcodes = true ) {
		$output = [];

		foreach ( $input as $key => $value ) {
			$output[ $key ] = d4p_sanitize_basic( $value, $strip_shortcodes );
		}

		return $output;
	}
}

if ( ! function_exists( 'd4p_ids_from_string' ) ) {
	function d4p_ids_from_string( $input, $delimiter = ',', $map = 'absint' ) {
		$ids = strip_tags( stripslashes( $input ) );

		$ids = explode( $delimiter, $ids );
		$ids = array_map( 'trim', $ids );
		$ids = array_map( $map, $ids );
		$ids = array_filter( $ids );

		return $ids;
	}
}

if ( ! function_exists( 'd4p_kses_wide_list_of_tags' ) ) {
	function d4p_kses_wide_list_of_tags() {
		return array_merge(
			d4p_kses_expanded_list_of_tags(),
			[
				'head'  => [],
				'title' => [],
				'html'  => [
					'lang' => true,
				],
				'link'  => [
					'rel'   => true,
					'href'  => true,
					'media' => true,
				],
				'style' => [
					'type'  => true,
					'media' => true,
				],
				'meta'  => [
					'property'   => true,
					'name'       => true,
					'content'    => true,
					'http-equiv' => true,
					'charset'    => true,
				],
				'body'  => [
					'class' => true,
					'style' => true,
				],
			]
		);
	}
}

if ( ! function_exists( 'd4p_kses_expanded_list_of_tags' ) ) {
	function d4p_kses_expanded_list_of_tags() {
		return [
			'a'          => [
				'class'    => true,
				'href'     => true,
				'title'    => true,
				'rel'      => true,
				'style'    => true,
				'download' => true,
				'target'   => true,
			],
			'abbr'       => [
				'class' => true,
				'style' => true,
			],
			'blockquote' => [
				'class' => true,
				'style' => true,
				'cite'  => true,
			],
			'div'        => [
				'class' => true,
				'style' => true,
			],
			'span'       => [
				'class' => true,
				'style' => true,
			],
			'code'       => [
				'class' => true,
				'style' => true,
			],
			'p'          => [
				'class' => true,
				'style' => true,
			],
			'pre'        => [
				'class' => true,
				'style' => true,
			],
			'em'         => [
				'class' => true,
				'style' => true,
			],
			'i'          => [
				'class' => true,
				'style' => true,
			],
			'b'          => [
				'class' => true,
				'style' => true,
			],
			'strong'     => [
				'class' => true,
				'style' => true,
			],
			'del'        => [
				'datetime' => true,
				'class'    => true,
				'style'    => true,
			],
			'h1'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'h2'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'h3'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'h4'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'h5'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'h6'         => [
				'align' => true,
				'class' => true,
				'style' => true,
			],
			'ul'         => [
				'class' => true,
				'style' => true,
			],
			'ol'         => [
				'class' => true,
				'style' => true,
				'start' => true,
			],
			'li'         => [
				'class' => true,
				'style' => true,
			],
			'img'        => [
				'class'  => true,
				'style'  => true,
				'src'    => true,
				'border' => true,
				'alt'    => true,
				'height' => true,
				'width'  => true,
			],
			'table'      => [
				'cellpadding' => true,
				'cellspacing' => true,
				'align'       => true,
				'bgcolor'     => true,
				'border'      => true,
				'class'       => true,
				'style'       => true,
			],
			'tbody'      => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
			'td'         => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
			'tfoot'      => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
			'th'         => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
			'thead'      => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
			'tr'         => [
				'align'  => true,
				'valign' => true,
				'class'  => true,
				'style'  => true,
			],
		];
	}
}
