<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Toolbar extends Feature {
	public $feature_name = 'toolbar';
	public $settings = array(
		'super_admin' => true,
		'visitor'     => true,
		'roles'       => null,
		'title'       => 'Forums',
		'information' => true
	);

	public $title = '';

	public function __construct() {
		parent::__construct();

		if ( $this->allowed() ) {
			$this->title = $this->settings['title'] ? _x( "Forums", "Toolbar menu default title", "bbp-core" ) : __( $this->settings['title'], "bbp-core" );

			add_action( 'bbpc_init', array( $this, 'init' ) );
		}
	}

	public static function instance() : Toolbar {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Toolbar();
		}

		return $instance;
	}

	public function init() {
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 100 );

		add_action( 'admin_head', array( $this, 'admin_bar_icon' ) );
		add_action( 'wp_head', array( $this, 'admin_bar_icon' ) );
	}

	public function admin_bar_icon() { ?>
        <style type="text/css">
            #wpadminbar #wp-admin-bar-gdbb-toolbar .ab-icon:before {
                content: "\f477";
                top: 2px;
            }

            @media screen and ( max-width: 782px ) {
                #wpadminbar li#wp-admin-bar-gdbb-toolbar {
                    display: block;
                }
            }
        </style>
	<?php }

	public function admin_bar_menu() {
		global $wp_admin_bar;

		$title = $this->title;

		$icon  = '<span class="ab-icon"></span>';
		$title = $icon . '<span class="ab-label">' . $this->title . '</span>';

		$wp_admin_bar->add_menu( array(
			'id'    => 'gdbb-toolbar',
			'title' => $title,
			'href'  => get_post_type_archive_link( bbp_get_forum_post_type() ),
			'meta'  => array( 'class' => 'icon-gdbb-toolbar' )
		) );

		$wp_admin_bar->add_group( array(
			'parent' => 'gdbb-toolbar',
			'id'     => 'gdbb-toolbar-public'
		) );

		$query = array(
			'post_parent'    => 0,
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'orderby'        => 'menu_order',
			'order'          => 'ASC'
		);

		$forums = bbp_get_forums_for_current_user( $query );

		if ( is_array( $forums ) && count( $forums ) > 0 ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-public',
				'id'     => 'gdbb-toolbar-forums',
				'title'  => __( "Forums", "bbp-core" ),
				'href'   => bbp_get_forums_url()
			) );

			foreach ( $forums as $forum ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'gdbb-toolbar-forums',
					'id'     => 'gdbb-toolbar-forums-' . $forum->ID,
					'title'  => apply_filters( 'the_title', $forum->post_title, $forum->ID ),
					'href'   => get_permalink( $forum->ID )
				) );
			}
		}

		$views = bbp_get_views();
		if ( is_array( $views ) && count( $views ) > 0 ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-public',
				'id'     => 'gdbb-toolbar-views',
				'title'  => __( "Views", "bbp-core" )
			) );

			foreach ( $views as $view => $args ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'gdbb-toolbar-views',
					'id'     => 'gdbb-toolbar-views-' . $view,
					'title'  => bbp_get_view_title( $view ),
					'href'   => bbp_get_view_url( $view )
				) );
			}
		}

		if ( current_user_can( BBPC_CAP ) ) {
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar',
				'id'     => 'gdbb-toolbar-admin'
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-admin',
				'id'     => 'gdbb-toolbar-new',
				'title'  => __( "New", "bbp-core" ),
				'href'   => ''
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-new',
				'id'     => 'gdbb-toolbar-new-forum',
				'title'  => __( "Forum", "bbp-core" ),
				'href'   => admin_url( 'post-new.php?post_type=' . bbp_get_forum_post_type() )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-new',
				'id'     => 'gdbb-toolbar-new-topic',
				'title'  => __( "Topic", "bbp-core" ),
				'href'   => admin_url( 'post-new.php?post_type=' . bbp_get_topic_post_type() )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-new',
				'id'     => 'gdbb-toolbar-new-reply',
				'title'  => __( "Reply", "bbp-core" ),
				'href'   => admin_url( 'post-new.php?post_type=' . bbp_get_reply_post_type() )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-admin',
				'id'     => 'gdbb-toolbar-edit',
				'title'  => __( "Edit", "bbp-core" ),
				'href'   => ''
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-edit',
				'id'     => 'gdbb-toolbar-edit-forums',
				'title'  => __( "Forums", "bbp-core" ),
				'href'   => admin_url( 'edit.php?post_type=' . bbp_get_forum_post_type() )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-edit',
				'id'     => 'gdbb-toolbar-edit-topics',
				'title'  => __( "Topics", "bbp-core" ),
				'href'   => admin_url( 'edit.php?post_type=' . bbp_get_topic_post_type() )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-edit',
				'id'     => 'gdbb-toolbar-edit-replies',
				'title'  => __( "Replies", "bbp-core" ),
				'href'   => admin_url( 'edit.php?post_type=' . bbp_get_reply_post_type() )
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-admin',
				'id'     => 'gdbb-toolbar-settings',
				'title'  => __( "bbPress", "bbp-core" ),
				'href'   => ''
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-settings',
				'id'     => 'gdbb-toolbar-settings-main',
				'title'  => __( "Settings", "bbp-core" ),
				'href'   => admin_url( 'options-general.php?page=bbpress' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-settings',
				'id'     => 'gdbb-toolbar-settings-repair',
				'title'  => __( "Tools", "bbp-core" ),
				'href'   => admin_url( 'tools.php?page=bbp-repair' )
			) );
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar-settings',
				'id'     => 'gdbb-toolbar-settings-third'
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-admin',
				'id'     => 'gdbb-toolbar-toolbox',
				'title'  => __( "Toolbox", "bbp-core" ),
				'href'   => ''
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox',
				'id'     => 'gdbb-toolbar-toolbox-front',
				'title'  => __( "Front Page", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-front' )
			) );
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar-toolbox',
				'id'     => 'gdbb-toolbar-toolbox-third'
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-features',
				'title'  => __( "Features", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-features' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-settings',
				'title'  => __( "Settings", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-settings' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-attachments',
				'title'  => __( "Attachments", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-attachments' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-users',
				'title'  => __( "Users", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-users' )
			) );

			if ( Plugin::instance()->is_enabled( 'canned-replies' ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'gdbb-toolbar-toolbox-third',
					'id'     => 'gdbb-toolbar-toolbox-canned',
					'title'  => __( "Canned Replies", "bbp-core" ),
					'href'   => admin_url( 'edit.php?post_type=bbx_canned_reply' )
				) );
			}

			if ( Plugin::instance()->is_enabled( 'report' ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'gdbb-toolbar-toolbox-third',
					'id'     => 'gdbb-toolbar-toolbox-reported-posts',
					'title'  => __( "Reported Posts", "bbp-core" ),
					'href'   => admin_url( 'admin.php?page=gd-bbpress-reported-posts' )
				) );
			}

			if ( Plugin::instance()->is_enabled( 'thanks' ) ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'gdbb-toolbar-toolbox-third',
					'id'     => 'gdbb-toolbar-toolbox-thanks-list',
					'title'  => __( "Thanks List", "bbp-core" ),
					'href'   => admin_url( 'admin.php?page=bbp-core-thanks-list' )
				) );
			}

			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-errors',
				'title'  => __( "Errors Log", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-errors' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-toolbox-third',
				'id'     => 'gdbb-toolbar-toolbox-tools',
				'title'  => __( "Tools", "bbp-core" ),
				'href'   => admin_url( 'admin.php?page=bbp-core-tools' )
			) );
		}

		if ( $this->settings['information'] ) {
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar',
				'id'     => 'gdbb-toolbar-info',
				'meta'   => array( 'class' => 'ab-sub-secondary' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info',
				'id'     => 'gdbb-toolbar-info-links',
				'title'  => __( "Information", "bbp-core" )
			) );
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar-info-links',
				'id'     => 'gdbb-toolbar-info-links-bbp',
				'meta'   => array( 'class' => 'ab-sub-secondary' )
			) );
			$wp_admin_bar->add_group( array(
				'parent' => 'gdbb-toolbar-info-links',
				'id'     => 'gdbb-toolbar-info-links-toolbox',
				'meta'   => array( 'class' => 'ab-sub-secondary' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info-links-bbp',
				'id'     => 'gdbb-toolbar-bbp-home',
				'title'  => __( "bbPress Homepage", "bbp-core" ),
				'href'   => 'https://bbpress.org/',
				'meta'   => array( 'target' => '_blank', 'rel' => 'nofollow' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info-links-bbp',
				'id'     => 'gdbb-toolbar-d4p-home',
				'title'  => __( "Dev4Press Homepage", "bbp-core" ),
				'href'   => 'https://www.dev4press.com/',
				'meta'   => array( 'target' => '_blank', 'rel' => 'nofollow' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info-links-toolbox',
				'id'     => 'gdbb-toolbar-toolbox-home',
				'title'  => __( "Plugin Homepage", "bbp-core" ),
				'href'   => 'https://plugins.dev4press.com/gd-bbpress-toolbox/',
				'meta'   => array( 'target' => '_blank', 'rel' => 'nofollow' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info-links-toolbox',
				'id'     => 'gdbb-toolbar-toolbox-kb',
				'title'  => __( "Knowledge Base", "bbp-core" ),
				'href'   => 'https://support.dev4press.com/kb/product/gd-bbpress-toolbox/',
				'meta'   => array( 'target' => '_blank', 'rel' => 'nofollow' )
			) );
			$wp_admin_bar->add_menu( array(
				'parent' => 'gdbb-toolbar-info-links-toolbox',
				'id'     => 'gdbb-toolbar-toolbox-forum',
				'title'  => __( "Support Forum", "bbp-core" ),
				'href'   => 'https://support.dev4press.com/forums/forum/plugins/gd-bbpress-toolbox/',
				'meta'   => array( 'target' => '_blank', 'rel' => 'nofollow' )
			) );
		}
	}
}
