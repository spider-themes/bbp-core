<?php

namespace SpiderDevs\Plugin\BBPC\Attachments;

use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Features\Icons;
use WP_Query;

class Topic {
	public $total = 0;
	public $topic_id = 0;
	public $format = 'list';
	public $items = 8;
	public $columns = 4;
	public $nonce = '';

	public $icons;
	public $type;
	public $download;

	public function __construct() {
	}

	public static function instance() : Topic {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Topic();
		}

		return $instance;
	}

	public function run() {
		$action   = apply_filters( 'bbpc_attachments_topic_thread_list_action', bbpc_attachments()->get( 'topic_thread_list_action' ) );
		$priority = apply_filters( 'bbpc_attachments_topic_thread_list_priority', 10 );

		if ( $action !== 'skip' ) {
			add_action( $action, array( $this, 'display' ), $priority );
		}
	}

	public function display() {
		$this->total = bbpc_cache()->attachments_count_topic_attachments( bbp_get_topic_id() );

		if ( $this->total > 0 ) {
			$this->topic_id = bbp_get_topic_id();
			$this->format   = bbpc_attachments()->get( 'topic_thread_list_format' );
			$this->items    = bbpc_attachments()->get( 'topic_thread_list_items' );
			$this->nonce    = wp_create_nonce( 'bbpc-attachments-thread-' . $this->topic_id );

			include( bbpc_get_template_part( 'bbpc-thread-attachments.php' ) );

			Enqueue::instance()->attachments();
		}
	}

	public function placeholders() : string {
		$pages  = ceil( $this->total / $this->items );
		$render = array(
			'<div class="bbpc-attachments-thread-pages" data-pages="' . $pages . '">'
		);

		for ( $i = 0; $i < $pages; $i ++ ) {
			$render[] = '<div style="display: none" class="bbpc-attachments-thread-page bbpc-attachments-thread-page-' . ( $i + 1 ) . ' bbpc-attachments-thread-empty" data-page="' . ( $i + 1 ) . '"></div>';
		}

		$render[] = '</div>';

		if ( $pages > 1 ) {
			$render[] = '<div style="display: none" class="bbpc-attachments-thread-pager bbpc-thread-current-first">';
			$render[] = '<span class="__prev">' . __( "Previous", "bbp-core" ) . '</span>';
			$render[] = '<span class="__current">1</span>';
			$render[] = '<span class="__next">' . __( "Next", "bbp-core" ) . '</span>';
			$render[] = '</div>';
		}

		return join( '', $render );
	}

	public function files( $topic, $page = 1 ) : string {
		$this->topic_id = $topic;

		$this->format   = bbpc_attachments()->get( 'topic_thread_list_format' );
		$this->items    = bbpc_attachments()->get( 'topic_thread_list_items' );
		$this->columns  = bbpc_attachments()->get( 'topic_thread_list_columns' );
		$this->download = bbpc_attachments()->get( 'download_link_attribute' ) ? ' download' : '';
		$this->icons    = bbpc_attachments()->get( 'attachment_icons' );
		$this->type     = Icons::instance()->mode();

		$render = '';
		$query  = $this->query( $this->topic_id, $this->items, $page );

		if ( $this->format == 'list' ) {
			$render = $this->render_list( $query );
		} else if ( $this->format == 'thumbnails' ) {
			$render = $this->render_thumbnails( $query );
		}

		return $render;
	}

	protected function render_thumbnails( WP_Query $query ) : string {
		$items = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				global $post;

				$file = get_attached_file( get_the_ID() );

				if ( ( $file === false || empty( $file ) ) ) {
					continue;
				}

				$items[] = Display::instance()->render_file_as_thumbnail( $post, $file, $this->topic_id, false );
			}

			$render = '<div class="bbpc-attachments-files-container">';
			$render .= '<ol class="__files-list __with-thumbnails __columns-' . $this->columns . '">' . join( '', $items ) . '</ol>';
			$render .= '</div>';

			return $render;
		}

		return '';
	}

	protected function render_list( WP_Query $query ) : string {
		$items = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				global $post;

				$file = get_attached_file( get_the_ID() );

				if ( ( $file === false || empty( $file ) ) ) {
					continue;
				}

				$items[] = Display::instance()->render_file_as_link( $post, $file, $this->topic_id, false );
			}
		}

		if ( ! empty( $items ) ) {
			$list_class = $this->type == 'images' ? '__with-icons' : '__with-font-icons';

			$render = '<div class="bbpc-attachments-files-container">';
			$render .= '<ol class="__files-list __without-thumbnails ' . $list_class . '">' . join( '', $items ) . '</ol>';
			$render .= '</div>';

			return $render;
		}

		return '';
	}

	protected function query( $topic, $posts_per_page, $page ) : WP_Query {
		$ids = bbpc_db()->get_attachments_for_topic_thread( $topic );

		$args = apply_filters( 'bbpc_get_post_attachments_thread_args', array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => $posts_per_page,
			'paged'                  => $page,
			'post__in'               => $ids,
			'orderby'                => array( 'parent' => 'ASC', 'ID' => 'ASC' ),
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true
		), $topic );

		return new WP_Query( $args );
	}
}