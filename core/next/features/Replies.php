<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Replies extends Feature {
	public $feature_name = 'replies';
	public $settings = array(
		'new_reply_minmax_active'            => false,
		'new_reply_min_title_words'          => 0,
		'new_reply_min_title_length'         => 0,
		'new_reply_min_content_length'       => 0,
		'new_reply_max_title_length'         => 0,
		'new_reply_max_content_length'       => 0,
		'tags_in_reply_form_only_for_author' => false,
		'reply_titles'                       => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->settings['new_reply_minmax_active'] ) {
			add_filter( 'bbp_new_reply_pre_title', array( $this, 'new_reply_title' ) );
			add_filter( 'bbp_new_reply_pre_content', array( $this, 'new_reply_content' ) );
		}

		if ( $this->settings['reply_titles'] ) {
			add_action( 'bbp_theme_before_reply_form_content', array( $this, 'reply_titles_form_field' ) );
			add_action( 'bbp_theme_before_reply_content', array( $this, 'reply_titles_print_title' ) );
		}

		if ( $this->settings['tags_in_reply_form_only_for_author'] ) {
			add_action( 'bbp_theme_before_reply_form', array( $this, 'theme_before_reply_form' ) );
		}
	}

	public static function instance() : Replies {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Replies();
		}

		return $instance;
	}

	public function new_reply_title( $title ) {
		$length = strlen( $title );
		$added  = false;

		if ( $this->settings['new_reply_min_title_words'] > 0 ) {
			$words = explode( ' ', $title );

			if ( count( $words ) < $this->settings['new_reply_min_title_words'] ) {
				bbp_add_error( 'bbp_reply_title', sprintf( __( "<strong>ERROR</strong>: Your reply title must have at least %s words.", "bbp-core" ), $this->settings['new_reply_min_title_words'] ) );

				$added = true;
			}
		}

		if ( ! $added ) {
			if ( $this->settings['new_reply_min_title_length'] > 0 ) {
				if ( $length < $this->settings['new_reply_min_title_length'] ) {
					bbp_add_error( 'bbp_reply_title', __( "<strong>ERROR</strong>: Your reply title is too short.", "bbp-core" ) );
				}
			}

			if ( $this->settings['new_reply_max_title_length'] > 0 ) {
				if ( $length > $this->settings['new_reply_max_title_length'] ) {
					bbp_add_error( 'bbp_reply_title', __( "<strong>ERROR</strong>: Your reply title is too long.", "bbp-core" ) );
				}
			}
		}

		return $title;
	}

	public function new_reply_content( $content ) {
		$length = strlen( $content );

		if ( $length > 0 ) {
			if ( $this->settings['new_reply_min_content_length'] > 0 ) {
				if ( $length < $this->settings['new_reply_min_content_length'] ) {
					bbp_add_error( 'bbp_reply_content', __( "<strong>ERROR</strong>: Your reply is too short.", "bbp-core" ) );
				}
			}

			if ( $this->settings['new_reply_max_content_length'] > 0 ) {
				if ( $length > $this->settings['new_reply_max_content_length'] ) {
					bbp_add_error( 'bbp_reply_content', __( "<strong>ERROR</strong>: Your reply is too long.", "bbp-core" ) );
				}
			}
		}

		return $content;
	}

	public function reply_titles_print_title() {
		remove_filter( 'the_title', 'bbp_get_reply_title_fallback', 2 );

		$topic_title = bbp_get_reply_title();

		add_filter( 'the_title', 'bbp_get_reply_title_fallback', 2, 2 );

		if ( $topic_title && $topic_title !== bbp_get_topic_title() ) {
			echo '<h4 class="bbp-reply-title">' . $topic_title . '</h4>';
		}
	}

	public function reply_titles_form_field() {
		?>

        <p>
            <label for="bbp_reply_title"><?php printf( __( "Reply Title (Maximum Length: %d):", "bbp-core" ), bbp_get_title_max_length() ); ?></label><br/>
            <input type="text" id="bbp_topic_title" value="<?php bbp_form_reply_title(); ?>" size="40" name="bbp_reply_title" maxlength="<?php bbp_title_max_length(); ?>"/>
        </p>

		<?php
	}

	public function theme_before_reply_form() {
		$topic_id = bbp_get_topic_id();

		if ( get_current_user_id() != bbp_get_topic_author_id( $topic_id ) ) {
			add_filter( 'bbp_allow_topic_tags', '__return_false', 10000 );
		}
	}
}