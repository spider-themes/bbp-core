<?php
// if ( ! defined( 'ABSPATH' ) ) {
// 	exit; // Exit if accessed directly.
// }

// function bbp_voting_settings_link( $links ) {
// 	// Build and escape the URL.
// 	$url = esc_url(
// 		add_query_arg(
// 			'page',
// 			'bbp_voting',
// 			get_admin_url() . 'admin.php'
// 		)
// 	);
// 	// Create the link.
// 	$settings_link = '<a href="' . $url . '">' . __( 'Settings' ) . '</a>';
// 	array_push( $links, $settings_link );
// 	$pro_link = '<a href="https://wpforthewin.com/product/bbpress-voting-pro/" target="_blank">Get Pro</a>';
// 	array_push( $links, $pro_link );
// 	return $links;
// }
// add_filter( "plugin_action_links_$plugin", 'bbp_voting_settings_link' );

// // Admin Settings Menu.
// add_action( 'admin_menu', 'bbp_voting_settings_menu' );
// function bbp_voting_settings_menu() {
// 	add_options_page(
// 		'bbPress Voting',
// 		'bbPress Voting',
// 		'manage_options',
// 		'bbp_voting',
// 		'bbp_voting_settings_page'
// 	);
// }

// // Register Settings.
// add_action( 'admin_init', 'bbp_voting_register_settings' );
// function bbp_voting_register_settings() {
// 	$bbp_voting_hooks = [
// 		'bbp_voting_show_labels'                    => 'bool',
// 		'bbp_voting_helpful'                        => 'string',
// 		'bbp_voting_not_helpful'                    => 'string',
// 		'bbp_voting_display_vote_nums'              => 'select',
// 		'bbp_voting_only_topics'                    => 'bool',
// 		'bbp_voting_only_replies'                   => 'bool',
// 		'bbp_voting_disable_voting_for_visitors'    => 'bool',
// 		'bbp_voting_disable_voting_on_closed_topic' => 'bool',
// 		'bbp_voting_disable_down_votes'             => 'bool',
// 		'bbp_voting_disable_author_vote'            => 'bool',
// 		'bbp_voting_admin_bypass'                   => 'bool',
// 		'sort_bbpress_topics_by_votes'              => 'bool',
// 		'sort_bbpress_replies_by_votes'             => 'bool',
// 		'bbp_voting_lead_topic'                     => 'bool',
// 	];

// 	foreach ( $bbp_voting_hooks as $bbp_voting_hook => $bbp_voting_hook_type ) {
// 		register_setting( 'bbp_voting_settings', $bbp_voting_hook );
// 	}
// }

// // Admin Settings Page

// function bbp_voting_settings_page() {
// 	// Get the active tab from the $_GET param
// 	$default_tab = null;
// 	$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : $default_tab;
// 	?>
// 	<style>
// 		.bbp-voting-pro-green {
// 			background-color: #1c9b11;
// 			color: white;
// 		}
// 		.bbp-voting-pro-badge {
// 			display: inline-block;
// 			padding: 0 5px;
// 			border-radius: 10px;
// 		}
// 	</style>
// 	<div class="wrap">
// 		<h1>bbPress Voting 
// 		<?php
// 		if ( defined( 'BBPVOTINGPRO' ) ) {
// 			echo 'Pro';}
// 		?>
// 		</h1>
// 		<!-- Tabs -->
// 		<nav class="nav-tab-wrapper">
// 			<?php do_action( 'bbp_voting_settings_tabs', $tab ); ?>
// 		</nav>
// 		<!-- Form -->
// 		<div class="tab-content">
// 			<?php do_action( 'bbp_voting_settings_form', $tab ); ?>
// 		</div>
// 	</div>
// 	<?php
// }

// // Settings Tabs

// add_action( 'bbp_voting_settings_tabs', 'bbp_voting_settings_tabs' );
// function bbp_voting_settings_tabs( $tab ) {
// 	?>
// 	<a href="?page=bbp_voting" class="nav-tab 
// 	<?php
// 	if ( $tab === null ) :
// 		?>
// 		nav-tab-active<?php endif; ?>">Features & Settings</a>
// 	<!-- <a href="?page=bbp_voting&tab=behavior" class="nav-tab <?php // if($tab==='behavior'): ?>nav-tab-active<?php // endif; ?>">Behavior</a> -->
// 	<?php if ( ! defined( 'BBPVOTINGPRO' ) ) { ?>
// 		<a href="?page=bbp_voting&tab=go_pro" class="nav-tab 
// 		<?php
// 		if ( $tab === 'go_pro' ) :
// 			?>
// 			nav-tab-active<?php endif; ?> bbp-voting-pro-green">Go Pro!</a>
// 		<?php
// 	}
// }

// // Settings Header

// add_action( 'bbp_voting_settings_form', 'bbp_voting_settings_form_header', 1 );
// function bbp_voting_settings_form_header( $tab ) {
// 	$tabs_with_no_form = [ 'license', 'go_pro' ];
// 	if ( in_array( $tab, $tabs_with_no_form ) ) {
// 		return;
// 	}
// 	?>
// 	<form method="post" action="options.php">
// 		<?php settings_fields( 'bbp_voting_settings' ); ?>
// 		<?php do_settings_sections( 'bbp_voting_settings' ); ?>
// 	<?php
// }

// // Settings Footer

// add_action( 'bbp_voting_settings_form', 'bbp_voting_settings_form_footer', 99 );
// function bbp_voting_settings_form_footer( $tab ) {
// 	$tabs_with_no_form = [ 'license', 'go_pro' ];
// 	if ( in_array( $tab, $tabs_with_no_form ) ) {
// 		return;
// 	}
// 	?>
// 		<?php submit_button(); ?>
// 	</form>
// 	<?php
// }

// // Settings Forms
// //TODO: REMOVE THESE SETTINGS
// add_action( 'bbp_voting_settings_form', 'bbp_voting_settings_form', 10 );
// function bbp_voting_settings_form( $tab ) {
// 	if ( $tab === null ) {
// 		?>

// 		<hr>
// 		<h2>Voting Labels</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_show_labels',
// 			'Show Labels',
// 			'Show the labels that describe what up and down mean?'
// 		);
// 								bbp_voting_field(
// 									'bbp_voting_helpful',
// 									'Upvote Label',
// 									'',
// 									'Change the upvote label from "Helpful" to something else',
// 									'text'
// 								);
// 								bbp_voting_field(
// 									'bbp_voting_not_helpful',
// 									'Downvote Label',
// 									'',
// 									'Change the downvote label from "Not Helpful" to something else',
// 									'text'
// 								);
// 								bbp_voting_field(
// 									'bbp_voting_display_vote_nums',
// 									'Display Vote Numbers',
// 									'',
// 									'Choose how to display the number of up votes and down votes',
// 									[ 'hover', 'always-show', 'hide' ]
// 								);
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Disable Voting</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_only_replies',
// 			'Disable Voting on Topics',
// 			'Remove scores and voting buttons from topics',
// 			'You can override this at the forum level'
// 		);
// 								bbp_voting_field(
// 									'bbp_voting_only_topics',
// 									'Disable Voting on Replies',
// 									'Remove scores and voting buttons from replies',
// 									'You can override this at the forum level'
// 								);
// 								bbp_voting_field(
// 									'bbp_voting_disable_down_votes',
// 									'Disable Down Votes',
// 									'Only allow up votes'
// 								);
// 		?>
// 		</table>

// 		<hr>
// 		<h2>View-Only Scores</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_disable_voting_for_visitors',
// 			'View-Only Scores for Visitors',
// 			'Disable voting for visitors who are not logged in',
// 			'Scores will display (if configured to), but voting will be disabled if not logged in'
// 		);
// 								bbp_voting_field(
// 									'bbp_voting_disable_voting_on_closed_topic',
// 									'View-Only Scores on Closed Topics',
// 									'Disable adding new votes after a topic is closed',
// 									'Scores will display (if configured to), but new votes for the topic or the topic\'s replies will be disabled'
// 								);
// 								bbp_voting_field(
// 									'bbp_voting_disable_author_vote',
// 									'View-Only Score for Author',
// 									'Don\'t allow authors to vote on their own topic/reply',
// 									'They can still vote on other people\'s topics/replies'
// 								);
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Admin</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field( 'bbp_voting_admin_bypass', 'Admin Bypass', 'Allow any administrator user to vote as much as they want?' );
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Sort by Voting Scores</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field( 'sort_bbpress_topics_by_votes', 'Sort Topics by Votes', 'Sort topics in a forum using their voting scores?', '(highest voted topics on top)' );
// 		bbp_voting_field( 'sort_bbpress_replies_by_votes', 'Sort Replies by Votes', 'Sort replies on a topic using their voting scores?', '(highest voted replies on top)' );
// 		bbp_voting_field(
// 			'bbp_voting_order_by_weighted_score',
// 			'Sort on Weighted Score',
// 			'Use a Weighted Score for Topic/Reply Sorting',
// 			'Utilize the <a href="https://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval" target="_blank" rel="noopener">Wilson binomial proportion confidence interval formula</a> to sort topics and replies by weighted scores instead of the simple score derived from up votes minus down votes',
// 			'bool',
// 			true
// 		); // Pro
// 								  bbp_voting_field( 'bbp_voting_sort_by_dropdown', '"Sort By" Dropdown', 'Add a "Sort By" dropdown to let the user choose.', '(Choice between default chronological and "Best" based on voting scores)', 'bool', true ); // Pro
// 								  bbp_voting_field( 'bbp_voting_lead_topic', 'Lead Topic', 'Break out the lead topic to separate it from the replies', 'Simply enabled the built-in bbPress hook, bbp_show_lead_topic.  This is useful to resolve a bug in bbPress when sort order is messed up when Threaded Replies are enabled in bbPress.', 'bool' );
// 								  // bbp_voting_field('key', 'name', 'label', 'descr', 'text');
// 		?>
// 		</table>

// 		<hr>
// 		<h2>"Who Voted" Avatars</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_show_who_voted',
// 			'Show Who Voted',
// 			'Show the avatars of who voted below each reply',
// 			'They have green or red borders depending on which why they voted',
// 			'bool',
// 			true
// 		); // Pro
// 								bbp_voting_field(
// 									'bbp_voting_max_avatars',
// 									'Max "Who Voted" Avatars',
// 									'',
// 									'Limit the number of avatars to the most recent x number',
// 									'number',
// 									true
// 								); // Pro
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Accepted Answers</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_accepted_answers',
// 			'Enable Accepted Answers',
// 			'If enabled, the author of a topic can choose one of the replies as the accepted answer',
// 			'That reply will show a green checkmark to everyone showing that it is the accepted answer',
// 			'bool',
// 			true
// 		); // Pro
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Email Notifications</h2>

// 		<table class="form-table">
// 		<?php
// 		bbp_voting_field(
// 			'bbp_voting_enable_author_emails',
// 			'Enable Voting Email Notifications to Author',
// 			'If enabled, every time someone votes on a topic or reply, the author of that topic or reply will receive an email notification letting who voted and which way',
// 			'(only when logged in users vote)',
// 			'bool',
// 			true
// 		); // Pro
// 		?>
// 		</table>

// 		<hr>
// 		<h2>Trending Topics Widget</h2>

// 		<table class="form-table">
// 		<?php
// 		// Build and escape the widgets URL.
// 		$url = esc_url( admin_url( 'widgets.php' ) );
// 		// Create the link.
// 		$widgets_link = '<a href="' . $url . '">' . __( 'Widgets' ) . '</a>';
// 		bbp_voting_field(
// 			'bbp_voting_trending_topics',
// 			'Trending Topics Widget',
// 			'',
// 			'With Pro, add the Trending Topics widget by visiting the ' . $widgets_link . ' page.',
// 			'hidden',
// 			true
// 		); // Pro
// 								  // bbp_voting_field('key', 'name', 'label', 'descr', 'text');
// 		?>
// 		</table>

// 		<?php
// 	}
// 	if ( $tab === 'go_pro' ) {
// 		?>
// 		<a href="https://wpforthewin.com/product/bbpress-voting-pro/" target="_blank" rel="noopener"><img src="https://wpforthewin.com/wp-content/uploads/2020/08/bbPress-Voting-Pro-Banner.png" style="margin-top: 15px; box-shadow: 0 0 15px rgb(0 0 0 / 40%);"></a>
// 		<h2>bbPress Voting Pro</h2>
// 		<p>With bbPress Voting Pro, you get even more awesome features to enhance your bbPress forum's voting experience.</p>
// 		<p><strong>Pro features include:</strong></p>
// 		<p>
// 			* Accepted answers<br>
// 			* "Who voted" avatars<br>
// 			* Sort dropdown<br>
// 			* Sort on weighted score<br>
// 			* Schema for Q&A rich snippets (Great for SEO!)<br>
// 			* Trending topics widget<br>
// 			* Voting email notification to author
// 		</p>
// 		<p>If your bbPress forum is important to you, then this plugin is a must-have!</p>
		
// 		<p><a href="https://wpforthewin.com/product/bbpress-voting-pro/" target="_blank" rel="noopener" class="button button-primary">Get bbPress Voting Pro!</a></p>
// 		<?php
// 	}
// }
