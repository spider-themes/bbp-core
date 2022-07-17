<?php

namespace SpiderDevs\Plugin\BBPC\Attachments;

use PclZip;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bulk {
	public function __construct() {
	}

	public static function instance() : Bulk {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Bulk();
		}

		return $instance;
	}

	public function run() {
		if ( isset( $_GET['bbpc-bulk-download'] ) && ! empty( $_GET['bbpc-bulk-download'] ) ) {
			$id = absint( $_GET['bbpc-bulk-download'] );

			if ( $id > 0 ) {
				if ( bbpc_attachments()->is_bulk_download_allowed() ) {
					$files = bbpc_get_post_attachments( $id );
					$url   = bbp_is_topic( $id )
						? get_permalink( $id )
						:
						( bbp_is_reply( $id ) ? bbp_get_reply_url( $id ) : site_url() );

					if ( ! empty( $files ) ) {
						$this->bulk_download( $files, $id );
					}

					wp_redirect( $url );
					exit;
				}
			}
		}
	}

	public function bulk_download( $files, $id ) {
		$dir = wp_upload_dir();

		if ( $dir['error'] === false ) {
			$file_name = 'attachments-for-' . $id . '-' . time() . '.zip';
			$file_temp = 'att-' . time() . '-' . $id . '.bbx';
			$file_path = trailingslashit( $dir['basedir'] ) . 'bbpc/';

			$proceed = true;
			if ( ! file_exists( $file_path ) ) {
				$proceed = wp_mkdir_p( $file_path );
			}

			if ( $proceed ) {
				$this->bulk_htaccess( $file_path );

				if ( ! defined( 'PCLZIP_TEMPORARY_DIR' ) ) {
					define( 'PCLZIP_TEMPORARY_DIR', $file_path );
				}

				require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );

				$zip = new PclZip( $file_path . $file_temp );

				foreach ( $files as $file ) {
					$path   = get_attached_file( $file->ID );
					$folder = pathinfo( $path, PATHINFO_DIRNAME );

					$zip->add( $path, PCLZIP_OPT_REMOVE_PATH, $folder, PCLZIP_OPT_ADD_TEMP_FILE_ON );
				}

				if ( ! wp_next_scheduled( 'bbpc_clear_bulk_directory' ) ) {
					wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'bbpc_clear_bulk_directory' );
				}

				d4p_includes( array(
					array( 'name' => 'file-download', 'directory' => 'functions' )
				), BBPC_D4PLIB );

				d4p_download_resume( $file_path . $file_temp, $file_name );

				exit;
			}
		}
	}

	public function bulk_htaccess( $file_path ) {
		$htaccess = $file_path . '.htaccess';

		if ( file_exists( $htaccess ) ) {
			return;
		}

		$file = array(
			'Options All -Indexes',
			'',
			'<Files ~ ".*\..*">',
			'order allow,deny',
			'deny from all',
			'</Files>'
		);

		file_put_contents( $htaccess, implode( PHP_EOL, $file ) );
	}
}