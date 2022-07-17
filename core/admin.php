<?php
use SpiderDevs\Plugin\BBPC\Admin\Enqueue;
use SpiderDevs\Plugin\BBPC\Admin\Features;
use SpiderDevs\Plugin\BBPC\Admin\Grids;
use SpiderDevs\Plugin\BBPC\Admin\Help;
use SpiderDevs\Plugin\BBPC\Admin\MetaBoxes;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;
use SpiderDevs\Plugin\BBPC\Features\CannedReplies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bbpc_admin_core {
	public $plugin = 'bbp-core';

	public $debug;

	public $page  = false;
	public $panel = false;
	public $free  = [];

	public $menu_items;
	public $page_ids = [];

	function __construct() {
		add_action( 'bbpc_plugin_core_ready', [ $this, 'core' ] );

		if ( is_multisite() ) {
			add_filter( 'wpmu_drop_tables', [ $this, 'wpmu_drop_tables' ] );
		}
	}

	public function wpmu_drop_tables( $drop_tables ) {
		return array_merge( $drop_tables, bbpc_db()->db_site );
	}

	public function core() {
		$this->debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		if ( bbpc_has_bbpress() ) {
			$this->init();

			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ], 9 );
			add_action( 'current_screen', [ $this, 'current_screen' ] );
			add_filter( 'bbpc_save_settings_value', [ $this, 'save_settings' ], 10, 3 );
		}

		if ( bbpc()->is_install() ) {
			add_action( 'admin_notices', [ $this, 'install_notice' ] );
		}

		if ( bbpc()->is_update() ) {
			add_action( 'admin_notices', [ $this, 'update_notice' ] );
		}

		$this->free = bbpc()->has_free_plugins();

		if ( ! empty( $this->free ) ) {
			add_action( 'admin_notices', [ $this, 'free_plugins_notice' ] );
		}

		Features::instance();
		MetaBoxes::instance();
		Grids::instance();
		Help::instance();
		Enqueue::instance();
	}

	public function save_settings( $group, $name, $value ) {
		if ( $group == 'load' && $name == 'rewriter' ) {
			flush_rewrite_rules();

			remove_filter( 'bbpc_save_settings_value', [ $this, 'save_settings' ], 10 );
		}
	}

	public function current_url( $with_panel = true ) : string {
		$page = 'admin.php?page=' . $this->plugin . '-';

		$page .= $this->page;

		if ( $with_panel && $this->panel !== false && $this->panel != '' ) {
			$page .= '&panel=' . $this->panel;
		}

		return self_admin_url( $page );
	}

	public function free_plugins_notice() {
		if ( ! empty( $this->free ) ) {
			echo '<div class="error"><p>';
			echo sprintf(
				__( 'BBP Core detected that following plugins are still active: %s. They need to be disabled before you can use BBP Core.', 'bbp-core' ),
				'<strong>' . join( '</strong>, <strong>', $this->free ) . '</strong>'
			);
			echo '<br>' . sprintf( __( "You can <a href='%1\$s'>open plugins page</a> to disable them manually, or <a href='%2\$s'>click here</a> to disabled them automatically.", 'bbp-core' ), admin_url( 'plugins.php' ), admin_url( 'admin.php?page=gd-bbpress-toolbox-front&bbpc_handler=getback&action=bbpc-disable-free' ) );
			echo '</p></div>';
		}
	}

	public function update_notice() {
		if ( current_user_can( 'install_plugins' ) && $this->page === false ) {
			echo '<div class="updated"><p>';
			echo __( 'BBP Core is updated, and you need to review the update process.', 'bbp-core' );
			echo ' <a href="' . admin_url( 'admin.php?page=gd-bbpress-toolbox-front' ) . '">' . __( 'Click Here', 'bbp-core' ) . '</a>.';
			echo '</p></div>';
		}
	}

	public function install_notice() {
		if ( current_user_can( 'install_plugins' ) && $this->page === false ) {
			echo '<div class="updated"><p>';
			echo __( 'BBP Core is activated and it needs to finish installation.', 'bbp-core' );
			echo ' <a href="' . admin_url( 'admin.php?page=gd-bbpress-toolbox-front' ) . '">' . __( 'Click Here', 'bbp-core' ) . '</a>.';
			echo '</p></div>';
		}
	}

	public function init() {
		$this->menu_items = apply_filters(
			'bbpc_admin_menu_items',
			[
				'front'          => [
					'title' => __( 'Dashboard', 'bbp-core' ),
					'icon'  => 'home',
					'cap'   => 'bbpc_moderation',
				],
				'about'          => [
					'title' => __( 'About', 'bbp-core' ),
					'icon'  => 'info-circle',
				],
				'features'       => [
					'title' => __( 'Features', 'bbp-core' ),
					'icon'  => 'puzzle-piece',
				],
				'settings'       => [
					'title' => __( 'Settings', 'bbp-core' ),
					'icon'  => 'cogs',
				],
				'users'          => [
					'title' => __( 'Users', 'bbp-core' ),
					'icon'  => 'users',
					'cap'   => 'bbpc_moderation_users',
				],
				'attachments'    => [
					'title' => __( 'Attachments', 'bbp-core' ),
					'icon'  => 'file-text-o',
					'cap'   => 'bbpc_moderation_attachments',
				],
				'reported-posts' => [
					'title' => __( 'Reported Posts', 'bbp-core' ),
					'icon'  => 'exclamation-triangle',
					'cap'   => 'bbpc_moderation_report',
				],
				'thanks-list'    => [
					'title' => __( 'Thanks List', 'bbp-core' ),
					'icon'  => 'check-square',
					'cap'   => 'bbpc_moderation_attachments',
				],
				'errors'         => [
					'title' => __( 'Errors Log', 'bbp-core' ),
					'icon'  => 'bug',
					'cap'   => 'bbpc_moderation',
				],
				'bbcodes'        => [
					'title' => __( 'BBCodes', 'bbp-core' ),
					'icon'  => 'code',
					'cap'   => 'bbpc_moderation',
				],
				'wizard'         => [
					'title' => __( 'Setup Wizard', 'bbp-core' ),
					'icon'  => 'magic',
				],
				'tools'          => [
					'title' => __( 'Tools', 'bbp-core' ),
					'icon'  => 'wrench',
				],
			]
		);

		if ( ! Plugin::instance()->is_enabled( 'bbcodes' ) ) {
			unset( $this->menu_items['bbcodes'] );
		}

		if ( ! Plugin::instance()->is_enabled( 'thanks' ) ) {
			unset( $this->menu_items['thanks-list'] );
		}

		if ( ! Plugin::instance()->is_enabled( 'report' ) ) {
			unset( $this->menu_items['reported-posts'] );
		}
	}

	public function admin_init() {
		global $submenu;

		d4p_include( 'grid', 'admin', BBPC_D4PLIB );

		if ( Plugin::instance()->is_enabled( 'canned-replies' ) ) {
			if ( isset( $submenu['gd-bbpress-toolbox-front'] ) ) {
				$index = count( $this->menu_items );

				$canned    = $submenu['gd-bbpress-toolbox-front'][ $index ];
				$canned[0] = __( CannedReplies::instance()->settings['post_type_plural'], 'bbp-core' );
				unset( $submenu['gd-bbpress-toolbox-front'][ $index ] );

				array_splice( $submenu['gd-bbpress-toolbox-front'], 4, 0, [ $canned ] );
			}
		}
	}

	public function admin_menu() {
		$parent = 'gd-bbpress-toolbox-front';

		$this->page_ids[] = add_menu_page(
			'BBP Core',
			'bbPress Toolbox',
			'bbpc_moderation',
			$parent,
			[ $this, 'panel_general' ],
			bbpc_plugin()->svg_icon
		);

		foreach ( $this->menu_items as $item => $data ) {
			$cap = isset( $data['cap'] ) ? $data['cap'] : 'bbpc_moderation';

			$this->page_ids[] = add_submenu_page(
				$parent,
				'BBP Core: ' . $data['title'],
				$data['title'],
				$cap,
				'gd-bbpress-toolbox-' . $item,
				[ $this, 'panel_general' ]
			);
		}

		$this->admin_load_hooks();
	}

	public function get_post_type() {
		$post_type = '';

		if ( isset( $_GET['post_type'] ) ) {
			$post_type = $_GET['post_type'];
		} else {
			global $post;

			if ( $post ) {
				$post_type = $post->post_type;
			}
		}

		if ( in_array(
			$post_type,
			[
				bbp_get_forum_post_type(),
				bbp_get_topic_post_type(),
				bbp_get_reply_post_type(),
			]
		) ) {
			return $post_type;
		} else {
			return false;
		}
	}

	public function admin_load_hooks() {
		foreach ( $this->page_ids as $id ) {
			add_action( 'load-' . $id, [ $this, 'load_admin_page' ] );
		}

		do_action( 'bbpc_admin_load_hooks' );
	}

	public function load_admin_page() {
		do_action( 'bbpc_load_admin_page_' . $this->page );

		if ( $this->panel !== false && $this->panel != '' ) {
			do_action( 'bbpc_load_admin_page_' . $this->page . '_' . $this->panel );
		}

		Help::instance()->plugin();
	}

	public function current_screen( $screen ) {
		if ( isset( $_GET['panel'] ) && $_GET['panel'] != '' ) {
			$this->panel = d4p_sanitize_slug( $_GET['panel'] );
		}

		$id = $screen->id;

		if ( $id == 'toplevel_page_gd-bbpress-toolbox-front' ) {
			$this->page = 'front';
		} elseif ( substr( $id, 0, 40 ) == 'bbpress-toolbox_page_gd-bbpress-toolbox-' ) {
			$this->page = substr( $id, 40 );
		}

		if ( isset( $_POST['bbpc_handler'] ) && $_POST['bbpc_handler'] == 'postback' ) {
			require_once BBPC_PATH . 'core/admin/postback.php';

			new bbpc_admin_postback();
		} elseif ( isset( $_GET['bbpc_handler'] ) && $_GET['bbpc_handler'] == 'getback' ) {
			require_once BBPC_PATH . 'core/admin/getback.php';

			new bbpc_admin_getback();
		}
	}

	public function install_or_update() : bool {
		if ( bbpc()->is_install() && BBPC_RUN_INSTALL ) {
			include BBPC_PATH . 'forms/install.php';

			return true;
		} elseif ( bbpc()->is_update() && BBPC_RUN_UPDATE ) {
			include BBPC_PATH . 'forms/update.php';

			return true;
		}

		return false;
	}

	public function panel_general() {
		if ( ! $this->install_or_update() ) {
			$_current_page = $this->page;

			$path = apply_filters( 'bbpc_admin_menu_panel_' . $_current_page, BBPC_PATH . 'forms/' . $_current_page . '.php' );

			if ( ! file_exists( $path ) ) {
				$path = BBPC_PATH . 'forms/shared/invalid.php';
			}

			include $path;
		}
	}
}

global $_bbpc_core_admin;
$_bbpc_core_admin = new bbpc_admin_core();

function bbpc_admin() : bbpc_admin_core {
	global $_bbpc_core_admin;
	return $_bbpc_core_admin;
}
