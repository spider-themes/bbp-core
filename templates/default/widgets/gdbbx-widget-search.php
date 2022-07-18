<form role="search" method="get" id="bbp-search-form" action="<?php bbp_search_url(); ?>">
    <div>
        <input type="hidden" name="action" value="bbp-search-request" />
        <input type="hidden" name="bbx-mode" value="<?php echo esc_attr($instance['search_mode']); ?>" />
        <input type="hidden" name="bbx-forum" value="<?php echo esc_attr(bbp_get_forum_id()); ?>" />

        <label class="screen-reader-text hidden" for="<?php echo $this->form_unique_id($instance); ?>"><?php _e("Search for:", "bbp-core"); ?></label>
            <input type="text" value="<?php echo esc_attr( bbp_get_search_terms() ); ?>" name="bbp_search" id="<?php echo $this->form_unique_id($instance); ?>" />
            <input class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e("Search", "bbp-core"); ?>" />
    </div>
</form>
