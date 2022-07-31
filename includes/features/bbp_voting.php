<?php
namespace features;

class bbp_voting {
	protected $bbp_voting_hooks = [];

	function __construct() {
		$this->include_files();
		$this->load_functions();
	}

	public function include_files() {
		define( 'BBPC_VOTE_PATH', plugin_dir_path( __FILE__ ) . 'bbp_voting/' );

		// The plugin basename, "folder/file.php"
		$plugin = plugin_basename( __FILE__ );

		// Helpers are helpful.
		require_once BBPC_VOTE_PATH . 'helpers.php';

		$this->bbp_voting_hooks = [
			'bbp_voting_show_labels'                    => 'bool',
			'bbp_voting_helpful'                        => 'string',
			'bbp_voting_not_helpful'                    => 'string',
			'bbp_voting_display_vote_nums'              => 'select',
			'bbp_voting_only_topics'                    => 'bool',
			'bbp_voting_only_replies'                   => 'bool',
			'bbp_voting_disable_voting_for_visitors'    => 'bool',
			'bbp_voting_disable_voting_on_closed_topic' => 'bool',
			'bbp_voting_disable_down_votes'             => 'bool',
			'bbp_voting_disable_author_vote'            => 'bool',
			'bbp_voting_admin_bypass'                   => 'bool',
			'sort_bbpress_topics_by_votes'              => 'bool',
			'sort_bbpress_replies_by_votes'             => 'bool',
			'bbp_voting_lead_topic'                     => 'bool',
		];

		foreach ( $this->bbp_voting_hooks as $bbp_voting_hook => $bbp_voting_hook_type ) {
			add_filter( $bbp_voting_hook, 'bbp_voting_hook_setting' );
		}
	}

	public function load_functions() {
		// Require only the appropriate files.
		if ( wp_doing_ajax() ) {
			// Ajax.
			require_once BBPC_VOTE_PATH . 'ajax.php';
		} elseif ( is_admin() ) {
			require_once BBPC_VOTE_PATH . 'backend.php';
			require_once BBPC_VOTE_PATH . 'metabox.php';
		} else {
			// Frontend.
			require_once BBPC_VOTE_PATH . 'frontend.php';
		}

	}
}


