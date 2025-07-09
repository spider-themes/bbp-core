<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Vote Buttons and Score hooks. 
add_action( 'bbp_theme_after_topic_author_details', 'bbp_voting_buttons' ); // ✅ New Hook
add_action( 'bbp_theme_after_reply_author_details', 'bbp_voting_buttons' );
add_action( 'bbp_voting_cpt', 'bbp_voting_buttons', 10, 1 );

function bbp_voting_buttons( $post_obj = false ) {
	// Don't show voting buttons on user profile pages
	if ( function_exists( 'bbp_is_single_user' ) && bbp_is_single_user() ) {
		return;
	}

	$current_action = current_action();
	$topic_post_type = bbp_get_topic_post_type();
	$reply_post_type = bbp_get_reply_post_type();
	$this_post_type = '';
	$post = null;

	// Get post object based on context
	if ( $current_action === 'bbp_voting_cpt' ) {
		if ( ! $post_obj ) return;
		$post = $post_obj;
	} else {
		// Determine post type from current action
		if ( in_array( $current_action, [
			'bbp_theme_before_topic_title',
			'bbp_template_before_lead_topic',
			'bbp_theme_after_topic_author_details'
		] ) ) {
			$this_post_type = $topic_post_type;
			$post = bbpress()->topic_query->post;
		} elseif ( in_array( $current_action, [
			'bbp_theme_before_reply_content',
			'bbp_theme_after_reply_author_details'
		] ) ) {
			$this_post_type = bbp_voting_get_current_post_type();
			$post = bbpress()->reply_query->post;
		}

		// Exit if unknown context or no post
		if ( empty( $this_post_type ) || empty( $post ) ) return;

		// Check forum settings
		$forum_id = ($this_post_type === $topic_post_type) 
			? bbp_get_topic_forum_id( $post->ID ) 
			: bbp_get_reply_forum_id( $post->ID );

		// Check if voting is allowed on this forum
		if ( ! apply_filters( 'bbp_voting_allowed_on_forum', true, $forum_id ) ) return;

		// Get post-specific settings
		$post_setting = ($this_post_type === $topic_post_type)
			? get_post_meta( $forum_id, 'bbp_voting_forum_enable_topics', true )
			: get_post_meta( $forum_id, 'bbp_voting_forum_enable_replies', true );

		$broad_disable = ($this_post_type === $topic_post_type)
			? bbpc_get_opt( 'is_voting_disabled_topics', 0 )
			: bbpc_get_opt( 'is_voting_disabled_replies', 0 );

		// Check if voting is disabled
		if ( ! empty( $post_setting ) && $post_setting === 'false' ) return;
		if ( empty( $post_setting ) && $broad_disable === '0' ) return;
	}

	$post_id = $post->ID;

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

	// Check if admin can bypass voting restrictions
	$admin_bypass = current_user_can( 'administrator' ) && 
		bbpc_get_opt( 'is_admin_can_vote_unlimited', false );

	// Determine if this is view-only mode
	$view_only = ! is_user_logged_in() && 
		bbpc_get_opt( 'is_disabled_voting_for_non_logged_users', false );

	// Additional view-only checks for regular context (not CPT)
	if ( ! $view_only && $current_action !== 'bbp_voting_cpt' ) {
		// Check if topic is closed and voting on closed topics is disabled
		$topic_id = ( $this_post_type === $topic_post_type ) ? 
			$post_id : bbp_get_reply_topic_id( $post_id );

		if ( get_post_status( $topic_id ) === 'closed' && 
			bbpc_get_opt( 'is_disabled_voting_closed_topics', false ) ) {
			$view_only = true;
		}

		// Check if voting on own posts is disabled
		if ( ! $view_only && 
			bbpc_get_opt( 'is_disabled_voting_own_topic_reply', false ) && 
			$post->post_author == get_current_user_id() ) {
			$view_only = true;
		}
	}

	$disable_down        = bbpc_get_opt( 'is_down_votes_disabled', 0 );
	$vote_number_display = bbpc_get_opt( 'vote_numbers_display', 'hover' );
	$display_vote_nums   = 'num-' . $vote_number_display;

	// Determine if floating style should be applied
	$float = in_array( $current_action, [
		'bbp_theme_before_reply_content',
		'bbp_voting_cpt'
	] );

	// Build CSS classes for the voting container
	$vote_classes = [
		'bbp-voting',
		'bbp-voting-post-' . $post_id,
		get_post_type( $post_id )
	];

	if ( $view_only ) {
		$vote_classes[] = 'view-only';
	} elseif ( $existing_vote == 1 ) {
		$vote_classes[] = 'voted-up';
	} elseif ( $existing_vote == -1 ) {
		$vote_classes[] = 'voted-down';
	}

	if ( $admin_bypass ) $vote_classes[] = 'admin-bypass';
	if ( $float ) $vote_classes[] = 'bbp-voting-float';

	// Start building HTML
	$html = '<div class="' . implode(' ', $vote_classes) . '">';

	// Add label if enabled
	$is_label = bbpc_get_opt( 'is_label', true );
	if ( $is_label ) {
		$upvote_label = bbpc_get_opt( 'upvote_label' );
		if ( $upvote_label ) {
			$html .= '<div class="bbp-voting-label helpful">' . esc_html( $upvote_label ) . '</div>';
		}
	}

	// Generate voting HTML based on whether we're in AMP mode or not
	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		// AMP version of voting buttons
		$post_url = admin_url( 'admin-ajax.php' );
		$plusups  = $ups ? '+' . $ups : ' ';

		// Upvote form
		$html .= sprintf(
			'<form name="amp-form%1$s" method="post" action-xhr="%2$s" target="_top" on="submit-success: AMP.setState({\'voteup%1$s\': %3$s})">
				<input type="hidden" name="action" value="bbpress_post_vote_link_clicked">
				<input type="hidden" name="post_id" value="%4$s" />
				<input type="hidden" name="direction" value="1" />
				<input type="submit" class="nobutton upvote-amp" value="🔺" />
				<span class="vote up" [text]="voteup%1$s ? \'+\' + voteup%1$s : \'%5$s\'">%5$s</span>
			</form>',
			$post_id,
			esc_url( $post_url ),
			( $ups + 1 ),
			esc_attr( $post_id ),
			esc_html( $plusups )
		);

		// Downvote form (if enabled)
		if ( $disable_down === '1' ) {
			$html .= sprintf(
				'<form name="amp-form%1$s" method="post" action-xhr="%2$s" target="_top" on="submit-success: AMP.setState({\'votedown%1$s\': %3$s})">
					<input type="hidden" name="action" value="bbpress_post_vote_link_clicked">
					<input type="hidden" name="post_id" value="%4$s" />
					<input type="hidden" name="direction" value="-1" />
					<input type="submit" class="nobutton downvote-amp" value="🔻" />
					<span class="vote down" [text]="votedown%1$s || \'%5$s\'">%6$s</span>
				</form>',
				$post_id,
				esc_url( $post_url ),
				( $downs - 1 ),
				esc_attr( $post_id ),
				esc_html( $downs ? $downs : ' ' ),
				esc_html( $downs ? $downs : '' )
			);
		}
	} else {
		// Standard version of voting buttons
		$html .= '<a class="vote up ' . esc_attr( $display_vote_nums ) . '" data-votes="' . 
			( $ups ? '+' . $ups : '' ) . '" onclick="bbpress_post_vote_link_clicked(' . 
			$post_id . ', 1); return false;">Up</a>';

		$html .= '<div class="score">' . $score . '</div>';

		if ( $disable_down === '1' ) {
			$html .= '<a class="vote down ' . esc_attr( $display_vote_nums ) . '" data-votes="' . 
				( $downs ? $downs : '' ) . '" onclick="bbpress_post_vote_link_clicked(' . 
				$post_id . ', -1); return false;">Down</a>';
		}
	}

	if ( $disable_down === '1' && $is_label ) {
		$downvote_label = bbpc_get_opt( 'downvote_label' );
		if ( $downvote_label ) {
			$html .= '<div class="bbp-voting-label not-helpful">' . esc_html( $downvote_label ) . '</div>';
		}
	}

	// Apply filter for reply voting buttons
	if ( $this_post_type === $reply_post_type ) {
		$html = apply_filters( 'bbp_voting_after_reply_voting_buttons', $html, $post_id );
	}

	// Close the voting container
	$html .= '</div><span style="display:none;">::</span>';

	echo $html;
}


// Sort by Votes

add_filter( 'bbp_has_topics_query', 'sort_bbpress_posts_by_votes', 99 );
add_filter( 'bbp_has_replies_query', 'sort_bbpress_posts_by_votes', 99 );

function sort_bbpress_posts_by_votes( $args = [] ) {
	$forum_id = bbp_get_forum_id();
	$forum_post_type = bbp_get_forum_post_type();
	$topic_post_type = bbp_get_topic_post_type();

	// Determine post type and settings based on current filter
	$current_filter = current_filter();
	switch ( $current_filter ) {
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

	// Check if we should apply vote sorting
	$apply_sorting = false;

	// Check URL parameter first
	if ( isset( $_GET['bbp-voting-sort'] ) ) {
		if ( $_GET['bbp-voting-sort'] === 'best' ) {
			$apply_sorting = true;
		} elseif ( $_GET['bbp-voting-sort'] === 'default' || $_GET['bbp-voting-sort'] === '' ) {
			return $args;
		}
	} else {
		// Check forum settings
		if ( ! empty( $post_setting ) ) {
			if ( $post_setting !== 'false' ) {
				$apply_sorting = true;
			}
		} elseif ( $broad_enable !== '1' ) {
			$apply_sorting = true;
		}

		// Check if voting is allowed on this forum
		if ( ! apply_filters( 'bbp_voting_allowed_on_forum', true, $forum_id ) ) {
			$apply_sorting = false;
		}
	}

	// Return original args if sorting should not be applied
	if ( ! $apply_sorting ) {
		return $args;
	}

	// Get the meta key to sort by
	$sort_meta_key = apply_filters( 'bbp_voting_sort_meta_key', 'bbp_voting_score' );

	// Find and fill any posts missing the sort meta key
	$missing_meta_query = [
		'meta_query' => [
			[
				'key'     => $sort_meta_key,
				'compare' => 'NOT EXISTS',
				'value'   => '',
			],
		],
	];

	$query = new WP_Query( array_merge( $args, $missing_meta_query ) );
	foreach ( $query->posts as $post ) {
		$default_value = apply_filters( 'bbp_voting_sort_meta_key_default_value', '0', $post->ID );
		update_post_meta( $post->ID, $sort_meta_key, $default_value );
	}

	// Clear existing sort parameters
	$args = array_diff_key( $args, array_flip( ['meta_key', 'meta_type', 'orderby', 'order'] ) );

	// Set up meta query for sorting
	$args['meta_query'] = [
		'relation'     => 'AND',
		'score_clause' => [
			'key'     => $sort_meta_key,
			'compare' => 'EXISTS',
		],
	];

	// Set numeric type for score meta if using the default key
	if ( $sort_meta_key === 'bbp_voting_score' ) {
		$args['meta_query']['score_clause']['type'] = 'NUMERIC';
	}

	// Set up orderby parameters
	$args['orderby'] = [
		'post_type'    => 'DESC',
		'score_clause' => 'DESC',
	];

	// Add additional sorting parameters based on post type
	if ( $this_post_type === $topic_post_type ) {
		$args['orderby']['date'] = 'ASC';
	} elseif ( $this_post_type === $forum_post_type ) {
		$args['meta_query']['orderby_freshness'] = [
			'key'  => '_bbp_last_active_time',
			'type' => 'DATETIME',
		];
		$args['orderby']['orderby_freshness'] = 'DESC';
	}

	return $args;
}

add_action( 'init', 'bbp_voting_lead_topic' );
function bbp_voting_lead_topic() {
	if ( bbpc_get_opt( 'is_lead_topic_broken', false ) ) {
		add_filter( 'bbp_show_lead_topic', '__return_true' );
	}
}
