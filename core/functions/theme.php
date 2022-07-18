<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('gdbbx_email_clean_content')) {
    function gdbbx_email_clean_content($content, $strip_tags = true) : string {
        if ($strip_tags) {
            $content = strip_tags($content);
        }

        return wp_specialchars_decode(trim($content), ENT_QUOTES);
    }
}
