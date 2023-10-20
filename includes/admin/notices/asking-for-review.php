<?php
$optionReview = get_option('bbpc_notify_review');
if ( time() >= (int)$optionReview && $optionReview !== '0' ) {
    add_action('admin_notices', 'bbpc_notify_give_review');
    wp_enqueue_script( 'bbpc-notify-review' );
}
add_action('wp_ajax_bbpc_notify_save_review', 'bbpc_notify_save_review');

/**
 ** Give Notice
 **/
function bbpc_notify_give_review() {
    ?>
    <div class="notice notice-success is-dismissible" id="bbpc_notify_review">
        <h3> <?php _e('Give BBP Core a review', 'bbp-core'); ?> </h3>
        <p>
            <?php _e('Thank you for choosing BBP Core. We hope you love it. Could you take a couple of seconds posting a nice review to share your happy experience?', 'bbp-core')?>
        </p>
        <p class="bbpc_notify_review_subheading">
            <?php _e('We will be forever grateful. Thank you in advance.', 'bbp-core'); ?>
        </p>
        <p>
            <a href="javascript:;" data="rateNow" class="button button-primary" style="margin-right: 5px"><?php _e('Rate now', 'bbp-core')?></a>
            <a href="javascript:;" data="later" class="button" style="margin-right: 5px"><?php _e('Later', 'bbp-core')?></a>
            <a href="javascript:;" data="alreadyDid" class="button"><?php _e('Already did', 'bbp-core')?></a>
        </p>
    </div>
    <?php
}

/**
 ** Save Notice
 **/
function bbpc_notify_save_review() {
    if ( isset( $_POST ) ) {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;
        $field = isset( $_POST['field'] ) ? sanitize_text_field( $_POST['field'] ) : null;

        if ( ! wp_verify_nonce( $nonce, 'bbpc-admin-nonce' ) ) {
            wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
            exit();
        }

        if ( $field == 'later' ) {
            update_option('bbpc_notify_review', time() + 3*60*60*24); //After 3 days show
        } else if ($field == 'alreadyDid') {
            update_option('bbpc_notify_review', 0);
        }
        wp_send_json_success();
    }
    wp_send_json_error( array( 'message' => 'Update fail!' ) );
}