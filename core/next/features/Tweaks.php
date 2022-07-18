<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\BB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tweaks extends Feature {
	public $feature_name = 'tweaks';
	public $settings = array(
		'topic_load_search_for_all_topics' => false,
		'forum_load_search_for_all_forums' => false,
		'fix_404_headers_error'            => true,
		'title_length_override'            => false,
		'title_length_value'               => 80,
		'remove_private_title_prefix'      => false,
		'participant_media_library_upload' => false,
		'kses_allowed_override'            => 'bbpress',
		'disable_bbpress_breadcrumbs'      => false,
		'apply_fitvids_to_content'         => true,
		'alternative_freshness_display'    => false,
		'hide_user_roles_from_users'       => false
	);

	public function __construct() {
		parent::__construct();

		add_filter( 'bbp_kses_allowed_tags', array( $this, 'kses_allowed_tags' ), 10000 );

		if ( $this->settings['disable_bbpress_breadcrumbs'] ) {
			add_filter( 'bbp_no_breadcrumb', '__return_true' );
		}

		if ( $this->settings['title_length_override'] ) {
			add_filter( 'bbp_get_title_max_length', array( $this, 'custom_title_length' ) );
		}

		if ( $this->settings['forum_load_search_for_all_forums'] ) {
			add_action( 'bbp_template_before_single_forum', array( $this, 'load_seach_form_template' ) );
		}

		if ( $this->settings['topic_load_search_for_all_topics'] ) {
			add_action( 'bbp_template_before_single_topic', array( $this, 'load_seach_form_template' ) );
		}

		if ( $this->settings['alternative_freshness_display'] ) {
			add_filter( 'bbp_get_time_since', array( $this, 'alternative_freshness' ), 20, 3 );
		}

		if ( $this->settings['apply_fitvids_to_content'] ) {
			add_filter( 'gdbbx_script_values', array( $this, 'script_values' ) );
		}

		if ( $this->settings['hide_user_roles_from_users'] ) {
			add_filter( 'bbp_after_get_reply_author_link_parse_args', array( $this, 'hide_user_roles' ) );
		}

		if ( ! is_admin() ) {
			if ( $this->settings['remove_private_title_prefix'] ) {
				add_filter( 'private_title_format', array( $this, 'private_title_format' ), 10, 2 );
			}

			if ( $this->settings['fix_404_headers_error'] ) {
				add_action( 'parse_query', array( $this, 'fix_404_issues' ), 100000 );
				add_action( 'wp', array( $this, 'fix_404_issues_status' ), 100000 );
			}
		}
	}

	public static function instance() : Tweaks {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Tweaks();
		}

		return $instance;
	}

	public function kses_allowed_tags( $list ) {
		$tags = $this->settings['kses_allowed_override'];

		if ( $tags == 'bbpress' ) {
			$list['div']          = array(
				'class' => true,
				'title' => true
			);
			$list['img']['class'] = true;
		} else if ( $tags == 'post' ) {
			$list = wp_kses_allowed_html( 'post' );
		} else if ( $tags == 'expanded' ) {
			$list = d4p_kses_expanded_list_of_tags();
		}

		return $list;
	}

	public function maybe_fix_404() {
		return ( isset( $wp_query->bbp_is_single_user ) && $wp_query->bbp_is_single_user ) ||
		       ( isset( $wp_query->bbp_is_single_user_profile ) && $wp_query->bbp_is_single_user_profile ) ||
		       ( isset( $wp_query->bbp_is_view ) && $wp_query->bbp_is_view );
	}

	public function fix_404_issues() {
		global $wp_query;

		if ( $this->maybe_fix_404() ) {
			$wp_query->is_404 = false;
		}
	}

	public function fix_404_issues_status() {
		global $wp_query;

		if ( $this->maybe_fix_404() ) {
			$wp_query->is_404 = false;

			status_header( 200 );
			nocache_headers();
		}
	}

	public function custom_title_length( $value ) {
		$custom = (int) $this->settings['title_length_value'];

		if ( $custom > 0 ) {
			$value = $custom;
		}

		return $value;
	}

	public function private_title_format( $prefix, $post ) {
		if ( BB::i()->is_bbpress_post_type( $post->post_type ) ) {
			$prefix = '%s';
		}

		return $prefix;
	}

	public function load_seach_form_template() {
		include( gdbbx_get_template_part( 'gdbbx-search-form-block.php' ) );
	}

	public function alternative_freshness( $output, $older_date, $newer_date ) {
		$unknown_text   = apply_filters( 'bbp_core_time_since_unknown_text', esc_html_x( "sometime", "Freshness display, for undefined period..", "bbp-core" ) );
		$right_now_text = apply_filters( 'bbp_core_time_since_right_now_text', esc_html_x( "right now", "Freshness display, for recent period.", "bbp-core" ) );
		$ago_text       = apply_filters( 'bbp_core_time_since_ago_text', esc_html_x( "%s ago", "Freshness display, for time period.", "bbp-core" ) );

		$chunks = array(
			array( YEAR_IN_SECONDS, _n_noop( "%s year", "%s years", "bbp-core" ) ),
			array( MONTH_IN_SECONDS, _n_noop( "%s month", "%s months", "bbp-core" ) ),
			array( WEEK_IN_SECONDS, _n_noop( "%s week", "%s weeks", "bbp-core" ) ),
			array( DAY_IN_SECONDS, _n_noop( "%s day", "%s days", "bbp-core" ) ),
			array( HOUR_IN_SECONDS, _n_noop( "%s hour", "%s hours", "bbp-core" ) ),
			array( MINUTE_IN_SECONDS, _n_noop( "%s minute", "%s minutes", "bbp-core" ) ),
			array( 1, _n_noop( "%s second", "%s seconds", "bbp-core" ) ),
		);

		$since = intval( $newer_date - $older_date );

		if ( 0 > $since ) {
			$output = $unknown_text;
		} else {
			for ( $i = 0, $j = count( $chunks ); $i < $j; ++ $i ) {
				$seconds = $chunks[ $i ][0];

				$count = floor( $since / $seconds );
				if ( 0 != $count ) {
					break;
				}
			}

			if ( ! isset( $chunks[ $i ] ) ) {
				$output = $right_now_text;
			} else {
				$output = sprintf( translate_nooped_plural( $chunks[ $i ][1], $count, 'bbpress' ), bbp_number_format_i18n( $count ) );

				if ( ! (int) trim( $output ) ) {
					$output = $right_now_text;
				}
			}
		}

		if ( $output != $right_now_text ) {
			$output = sprintf( $ago_text, $output );
		}

		return $output;
	}

	public function script_values( $values ) {
		$values['load'][] = 'fitvids';

		return $values;
	}

	public function hide_user_roles( $r ) {
		if ( ! gdbbx_can_user_moderate() ) {
			$r['show_role'] = false;
		}

		return $r;
	}
}
