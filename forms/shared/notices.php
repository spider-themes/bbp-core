<?php

if (!defined('ABSPATH')) { exit; }

if (!d4p_has_plugin('gd-power-search-for-bbpress') && gdbbx()->get('notice_gdpos_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-power-search-for-bbpress/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

<div class="d4p-notice-info">
    Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Power Search Pro for bbPress</strong>:<br/>
    <blockquote>Enhanced and powerful search for bbPress powered forums, with options to filter results by post author, forums, publication period, topic tags and few other things.</blockquote>
    <a target="_blank" href="<?php echo $url; ?>" class="button-primary">Plugin Home Page</a>
    <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-power-search" class="button-secondary">Do not show this notice anymore</a>
</div>

    <?php
} else if (!d4p_has_plugin('gd-topic-prefix') && gdbbx()->get('notice_gdtox_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-topic-prefix/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

<div class="d4p-notice-info">
    Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Topic Prefix Pro for bbPress</strong>:<br/>
    <blockquote>Implements topic prefixes system, with support for styling customization, forum specific prefix groups with use of user roles, default prefixes, filtering of topics by prefix and more.</blockquote>
    <a target="_blank" href="<?php echo $url; ?>" class="button-primary">Plugin Home Page</a>
    <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-topic-prefix" class="button-secondary">Do not show this notice anymore</a>
</div>

    <?php
} else if (!d4p_has_plugin('gd-topic-polls') && gdbbx()->get('notice_gdpol_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-topic-polls/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

<div class="d4p-notice-info">
    Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Topic Polls Pro for bbPress</strong>:<br/>
    <blockquote>Implements polls system for bbPress powered forums, where users can add polls to topics, with a wide range of settings to control voting, poll closing, display of results and more.</blockquote>
    <a target="_blank" href="<?php echo $url; ?>" class="button-primary">Plugin Home Page</a>
    <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-topic-polls" class="button-secondary">Do not show this notice anymore</a>
</div>

    <?php

} else if (!d4p_has_plugin('gd-quantum-theme-for-bbpress') && gdbbx()->get('notice_gdqnt_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-quantum-theme-for-bbpress/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

    <div class="d4p-notice-info">
        Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Quantum Theme Pro for bbPress</strong>:<br/>
        <blockquote>Responsive and modern theme to fully replace default bbPress theme templates and styles, with multiple colour schemes and Customizer integration for more control.</blockquote>
        <a target="_blank" href="<?php echo $url; ?>" class="button-primary"><?php _e("Plugin Home Page", "bbp-core"); ?></a>
        <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-quantum-theme" class="button-secondary"><?php _e("Do not show this notice anymore", "bbp-core"); ?></a>
    </div>

    <?php

} else if (!d4p_has_plugin('gd-members-directory-for-bbpress') && gdbbx()->get('notice_gdmed_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-members-directory-for-bbpress/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

    <div class="d4p-notice-info">
        Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Members Directory Pro for bbPress</strong>:<br/>
        <blockquote>Easy to use plugin for adding forum members directory page into bbPress powered forums including members filtering and additional widgets for listing members in the sidebar.</blockquote>
        <a target="_blank" href="<?php echo $url; ?>" class="button-primary"><?php _e("Plugin Home Page", "bbp-core"); ?></a>
        <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-members-directory" class="button-secondary"><?php _e("Do not show this notice anymore", "bbp-core"); ?></a>
    </div>

    <?php

} else if (!d4p_has_plugin('gd-forum-notices-for-bbpress') && gdbbx()->get('notice_gdfon_hide', 'core') === false) {
    $web = parse_url(get_bloginfo('url'), PHP_URL_HOST);

    $url = 'https://plugins.dev4press.com/gd-forum-notices-for-bbpress/';
    $url = add_query_arg('utm_source', $web, $url);
    $url = add_query_arg('utm_medium', 'plugin-gd-bbpress-toolbox', $url);
    $url = add_query_arg('utm_campaign', 'front-panel', $url);

    ?>

    <div class="d4p-notice-info">
        Please, take a few minutes to check out Dev4Press plugin for bbPress: <strong>GD Forum Notices Pro for bbPress</strong>:<br/>
        <blockquote>Easy to use and highly configurable plugin for adding notices throughout the bbPress powered forums, with powerful rules editor to control each notice display and location.</blockquote>
        <a target="_blank" href="<?php echo $url; ?>" class="button-primary"><?php _e("Plugin Home Page", "bbp-core"); ?></a>
        <a href="<?php echo gdbbx_admin()->current_url(false); ?>&gdbbx_handler=getback&action=dismiss-forum-notices" class="button-secondary"><?php _e("Do not show this notice anymore", "bbp-core"); ?></a>
    </div>

    <?php

}
