<?php
/**
 * Cannot access directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Show notice if other Knowledge Base plugins are active.
 */
add_action( 'admin_notices', function () {
    $conflicting_plugins = [
        'bbpress/bbpress.php' => [
            'name'   => 'bbPress',
            'migrate'=> false
        ]
    ];
    foreach ( $conflicting_plugins as $plugin_file => $plugin_data ) :
        if ( is_plugin_active( $plugin_file ) ) :
            ?>
            <div class="notice notice-warning bbpc-notice">
                <p>
                    <?php esc_html_e( 'We have detected the bbPress plugin is installed on the site.', 'bbp-core' ); ?>
                    <br>
                    <?php esc_html_e( 'To ensure BBP Core works properly and without conflicts, please deactivate bbPress.', 'bbp-core' ); ?>
                </p>
                <p>
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'bbpc_deactivate' => $plugin_file ] ), 'bbpc_deactivate_plugin' ) ); ?>" class="button-primary button-large red-bg">
                        <?php 
						// translators: %s is the name of the plugin being deactivated.
						printf( esc_html__( 'Deactivate %s', 'bbp-core' ), esc_html( $plugin_data['name'] ) ); 
						?>
                    </a>
                </p>
            </div>
            <?php
        endif;
    endforeach;
});

/**
 * Deactivate other Knowledge Base plugins securely.
 */
add_action( 'admin_init', function () {
    if ( isset( $_GET['bbpc_deactivate'] ) && check_admin_referer( 'bbpc_deactivate_plugin' ) ) {

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        $plugin_file = sanitize_text_field( wp_unslash( $_GET['bbpc_deactivate'] ) );

        if ( is_plugin_active( $plugin_file ) ) {
            deactivate_plugins( $plugin_file );

            wp_safe_redirect( add_query_arg( [
                'deactivated' => 'true',
                'plugin'      => urlencode( $plugin_file )
            ], admin_url( 'plugins.php' ) ) );
            exit;
        }
    }
});