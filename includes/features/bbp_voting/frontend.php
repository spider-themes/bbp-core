<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Vote Buttons and Score hooks. 
add_action( 'bbp_theme_after_topic_author_details', 'bbp_voting_buttons' ); // ✅ New Hook
add_action( 'bbp_theme_after_reply_author_details', 'bbp_voting_buttons' );
add_action( 'bbp_voting_cpt', 'bbp_voting_buttons', 10, 1 );

function bbp_voting_buttons( $post_obj = false ) {
	$current_action = current_action();

	if ( $current_action === 'bbp_voting_cpt' ) {
		if ( ! $post_obj ) return;
		$post = $post_obj;
	} else {
		$topic_post_type = bbp_get_topic_post_type();
		$reply_post_type = bbp_get_reply_post_type();
		$this_post_type  = ''; // ✅ Init variable to prevent warning

		if ( in_array( $current_action, [
			'bbp_theme_before_topic_title',
			'bbp_template_before_lead_topic',
			'bbp_theme_after_topic_author_details' // ✅ Support new hook
		] ) ) {
			$this_post_type = $topic_post_type;
		}

		if ( in_array( $current_action, [
			'bbp_theme_before_reply_content',
			'bbp_theme_after_reply_author_details'
		] ) ) {
			$this_post_type = bbp_voting_get_current_post_type();
		}

		// Exit if unknown context
		if ( empty( $this_post_type ) ) return;

		if ( $this_post_type === $topic_post_type ) {
			$post = bbpress()->topic_query->post;
		} elseif ( $this_post_type === $reply_post_type ) {
			$post = bbpress()->reply_query->post;
		}
	}

	if ( empty( $post ) ) return;

	$post_id = $post->ID;

	if ( $current_action === 'bbp_voting_cpt' ) {
		$post_setting  = true;
		$broad_disable = false;
	} else {
		switch ( $this_post_type ) {
			case $topic_post_type:
				$forum_id      = bbp_get_topic_forum_id( $post_id );
				$post_setting  = get_post_meta( $forum_id, 'bbp_voting_forum_enable_topics', true );
				$broad_disable = bbpc_get_opt( 'is_voting_disabled_topics', 0 );
				break;
			case $reply_post_type:
				$forum_id      = bbp_get_reply_forum_id( $post_id );
				$post_setting  = get_post_meta( $forum_id, 'bbp_voting_forum_enable_replies', true );
				$broad_disable = bbpc_get_opt( 'is_voting_disabled_replies', 0 );
				break;
			default:
				return;
		}

		if ( ! apply_filters( 'bbp_voting_allowed_on_forum', true, $forum_id ) ) return;

		if ( ! empty( $post_setting ) && $post_setting === 'false' ) return;
		if ( empty( $post_setting ) && $broad_disable === '0' ) return;
	}

	$score = (int) get_post_meta( $post_id, 'bbp_voting_score', true );
	$ups   = (int) get_post_meta( $post_id, 'bbp_voting_ups', true );
	$downs = (int) get_post_meta( $post_id, 'bbp_voting_downs', true );

	$calc_score = $ups + $downs;
	if ( $score > $calc_score ) {
		$diff = $score - $calc_score;
		$ups += $diff;
		update_post_meta( $post_id, 'bbp_voting_ups', $ups );
	}

	$voting_log    = get_post_meta( $post_id, 'bbp_voting_log', true );
	$voting_log    = is_array( $voting_log ) ? $voting_log : [];
	$client_ip     = $_SERVER['REMOTE_ADDR'];
	$identifier    = is_user_logged_in() ? get_current_user_id() : $client_ip;
	$existing_vote = $voting_log[ $identifier ] ?? 0;

	$is_admin_can_vote_unlimited = bbpc_get_opt( 'is_admin_can_vote_unlimited', false );
	$admin_bypass                = current_user_can( 'administrator' ) && $is_admin_can_vote_unlimited;

	$is_disabled_voting_non_logged = bbpc_get_opt( 'is_disabled_voting_for_non_logged_users', false );
	$view_only                     = ! is_user_logged_in() && $is_disabled_voting_non_logged;

	if ( ! $view_only && $current_action !== 'bbp_voting_cpt' ) {
		$topic_id                 = ( $this_post_type === $topic_post_type ) ? $post_id : bbp_get_reply_topic_id( $post_id );
		$topic_status             = get_post_status( $topic_id );
		$is_disabled_closed       = bbpc_get_opt( 'is_disabled_voting_closed_topics', false );
		if ( $topic_status === 'closed' && $is_disabled_closed ) $view_only = true;

		if ( ! $view_only ) {
			$is_disabled_own = bbpc_get_opt( 'is_disabled_voting_own_topic_reply', false );
			if ( $is_disabled_own && $post->post_author == get_current_user_id() ) $view_only = true;
		}
	}

	$disable_down        = bbpc_get_opt( 'is_down_votes_disabled', 0 );
	$vote_number_display = bbpc_get_opt( 'vote_numbers_display', 'hover' );
	$display_vote_nums   = 'num-' . $vote_number_display;

	$html  = '';
	$float = in_array( $current_action, [
		'bbp_theme_before_reply_content',
		'bbp_voting_cpt'
	] );

	$html .= '<div class="bbp-voting bbp-voting-post-' . $post_id
		. ( $view_only ? ' view-only' : ( $existing_vote == 1 ? ' voted-up' : ( $existing_vote == -1 ? ' voted-down' : '' ) ) )
		. ( $admin_bypass ? ' admin-bypass' : '' )
		. ( $float ? ' bbp-voting-float' : '' )
		. ' ' . get_post_type( $post_id ) . '">';

	$is_label = bbpc_get_opt( 'is_label', true );
	if ( $is_label ) {
		$upvote_label = bbpc_get_opt( 'upvote_label' );
		if ( $upvote_label ) {
			$html .= '<div class="bbp-voting-label helpful">' . esc_html( $upvote_label ) . '</div>';
		}
	}

	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		$post_url = admin_url( 'admin-ajax.php' );
		$plusups  = $ups ? '+' . $ups : ' ';
		$html .= '<form name="amp-form' . $post_id . '" method="post" action-xhr="' . esc_url( $post_url ) . '" target="_top" on="submit-success: AMP.setState({\'voteup' . $post_id . '\': ' . ( $ups + 1 ) . '})">
			<input type="hidden" name="action" value="bbpress_post_vote_link_clicked">
			<input type="hidden" name="post_id" value="' . esc_attr( $post_id ) . '" />
			<input type="hidden" name="direction" value="1" />
			<input type="submit" class="nobutton upvote-amp" value="🔺" />
			<span class="vote up" [text]="voteup' . $post_id . ' ? \'+\' + voteup' . $post_id . ' : \'' . esc_html( $plusups ) . '\'">' . esc_html( $plusups ) . '</span>
		</form>';

		if ( $disable_down === '1' ) {
			$html .= '<form name="amp-form' . $post_id . '" method="post" action-xhr="' . esc_url( $post_url ) . '" target="_top" on="submit-success: AMP.setState({\'votedown' . $post_id . '\': ' . ( $downs - 1 ) . '})">
				<input type="hidden" name="action" value="bbpress_post_vote_link_clicked">
				<input type="hidden" name="post_id" value="' . esc_attr( $post_id ) . '" />
				<input type="hidden" name="direction" value="-1" />
				<input type="submit" class="nobutton downvote-amp" value="🔻" />
				<span class="vote down" [text]="votedown' . $post_id . ' || \'' . esc_html( $downs ? $downs : ' ' ) . '\'">' . esc_html( $downs ? $downs : '' ) . '</span>
			</form>';
		}
	} else {
		$html .= '<a class="vote up ' . esc_attr( $display_vote_nums ) . '" data-votes="' . ( $ups ? '+' . $ups : '' ) . '" onclick="bbpress_post_vote_link_clicked(' . $post_id . ', 1); return false;">Up</a>';
		$html .= '<div class="score">' . $score . '</div>';
		if ( $disable_down === '1' ) {
			$html .= '<a class="vote down ' . esc_attr( $display_vote_nums ) . '" data-votes="' . ( $downs ? $downs : '' ) . '" onclick="bbpress_post_vote_link_clicked(' . $post_id . ', -1); return false;">Down</a>';
		}
	}

	if ( $disable_down === '1' && $is_label ) {
		$downvote_label = bbpc_get_opt( 'downvote_label' );
		if ( $downvote_label ) {
			$html .= '<div class="bbp-voting-label not-helpful">' . esc_html( $downvote_label ) . '</div>';
		}
	}

	if ( $this_post_type === $reply_post_type ) {
		$html = apply_filters( 'bbp_voting_after_reply_voting_buttons', $html, $post_id );
	}

	$html .= '</div><span style="display:none;">::</span>';

	echo $html;
}


// Sort by Votes

add_filter( 'bbp_has_topics_query', 'sort_bbpress_posts_by_votes', 99 );
add_filter( 'bbp_has_replies_query', 'sort_bbpress_posts_by_votes', 99 );

function sort_bbpress_posts_by_votes( $args = [] ) {

	$forum_id = bbp_get_forum_id();
	// if($forum_id === 0) return $args;
	// $this_post_type = isset($args['post_type']) ? $args['post_type'] : bbp_voting_get_current_post_type();
	// $this_post_type = bbp_voting_get_current_post_type();
	$forum_post_type = bbp_get_forum_post_type();
	$topic_post_type = bbp_get_topic_post_type();
	switch ( current_filter() ) {
		case 'bbp_has_topics_query':
			$this_post_type = $forum_post_type;
			$post_setting   = get_post_meta( $forum_id, 'sort_bbpress_topics_by_votes_on_forum', true );
			$broad_enable   = bbpc_get_opt( 'is_sort_topic_by_votes', 0 );
			break;
		case 'bbp_has_replies_query':
			$this_post_type = $topic_post_type;
			$post_setting   = get_post_meta( $forum_id, 'sort_bbpress_replies_by_votes_on_forum', true );
			$broad_enable   = bbpc_get_opt( 'is_sort_reply_by_votes', 0 );
			break;
		default:
			return $args;
	}
	// Do "allowed" checks
	if ( isset( $_GET['bbp-voting-sort'] ) ) {
		// Sort dropdown used, skip the rest of the checks
		if ( $_GET['bbp-voting-sort'] === 'best' ) {
			// Proceed with score sorting
		} elseif ( $_GET['bbp-voting-sort'] === 'default' || $_GET['bbp-voting-sort'] === '' ) {
			// bbp default non-score sort
			return $args;
		}
	} else {
		// Sort dropdown not used... check the settings
		if ( ! empty( $post_setting ) ) {
			// Forum-specific override is set (not Default)
			if ( $post_setting === 'false' ) {
				return $args;
			}
		} else {
			// Use broad disable settings
			if ( $broad_enable === '1' ) {
				return $args;
			}
		}
		if ( ! apply_filters( 'bbp_voting_allowed_on_forum', true, $forum_id ) ) {
			return $args;
		}
	}
	// Done with "allowed" checks... let's do this
	// Filter the sort meta key.  Default = bbp_voting_score
	$sort_meta_key = apply_filters( 'bbp_voting_sort_meta_key', 'bbp_voting_score' );

	// Reset for testing only -------------------------------------
	// $argsreset = $args;
	// $query = new WP_Query($argsreset);
	// foreach($query->posts as $reply) {
	// delete_post_meta($reply->ID, $sort_meta_key);
	// }
	// -----------------------------------

	// Find any replies that are missing the bbp_voting_score post meta and fill them with 0
	$args2               = $args;
	$args2['meta_query'] = [
		[
			'key'     => $sort_meta_key,
			'compare' => 'NOT EXISTS',
			'value'   => '',
		],
	];
	$query               = new WP_Query( $args2 );
	foreach ( $query->posts as $reply ) {
		$fill_in_default = apply_filters( 'bbp_voting_sort_meta_key_default_value', '0', $reply->ID );
		update_post_meta( $reply->ID, $sort_meta_key, $fill_in_default );
	}

	// Now that all missing scores are filled in, we can sort the original args by the score
	// $args['meta_key'] = 'bbp_voting_score';
	// $args['orderby'] = [
	// 'post_type' => 'DESC',
	// 'meta_value_num' => 'DESC',
	// 'date' => 'DESC'
	// ];
	unset( $args['meta_key'] );
	unset( $args['meta_type'] );
	unset( $args['orderby'] );
	unset( $args['order'] );
	$args['meta_query'] = [
		'relation'     => 'AND',
		'score_clause' => [
			'key'     => $sort_meta_key,
			'compare' => 'EXISTS',
			// 'type' => 'DECIMAL' // https://stackoverflow.com/questions/30018711/wordpress-meta-query-not-working-with-decimal-type
		],
	];
	if ( $sort_meta_key == 'bbp_voting_score' ) {
		$args['meta_query']['score_clause']['type'] = 'NUMERIC';
	}
	$args['orderby'] = [
		'post_type'    => 'DESC',
		'score_clause' => 'DESC',
	];
	if ( $this_post_type === $topic_post_type ) {
		// Add Date as another orderby for topics on a forum
		$args['orderby']['date'] = 'ASC';
	}
	if ( $this_post_type === $forum_post_type ) {
		// Add Freshness as another orderby for topics on a forum
		$args['meta_query']['orderby_freshness'] = [
			'key'  => '_bbp_last_active_time',
			'type' => 'DATETIME',
		];
		$args['orderby']['orderby_freshness']    = 'DESC';
	}
	return $args;
}

add_action( 'init', 'bbp_voting_lead_topic' );
function bbp_voting_lead_topic() {
	if ( bbpc_get_opt( 'is_lead_topic_broken', false ) ) {
		add_filter( 'bbp_show_lead_topic', '__return_true' );
	}
}