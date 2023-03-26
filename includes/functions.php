<?php

/**
 * Get forum title
 * @return string
 */
function bbpc_forum_title(){
    $forum_id       = bbp_get_forum_id();
    $forum_title    = get_the_title( $forum_id );
    return $forum_title;
}