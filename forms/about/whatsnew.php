<?php include(GDBBX_PATH.'forms/about/minor.php'); ?>

<div class="d4p-about-whatsnew">
    <div class="d4p-whatsnew-section d4p-whatsnew-heading">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit whole align-center">
                <h2 style="font-size: 52px;">The Attachments</h2>
                <p class="lead-description">
                    The new and the old (rewritten)
                </p>
                <p>
                    Attachments are now rewritten and improved, and placed inside the Features panel, with unified settings and more. The plugin also brings several performance improvements and few major new features.
                </p>

				<?php if ( isset( $_GET['install'] ) && $_GET['install'] == 'on' ) { ?>
                    <a class="button-primary" href="admin.php?page=gd-bbpress-toolbox-wizard"><?php _e( "Run Setup Wizard", "bbp-core" ); ?></a>
				<?php } ?>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit half align-left">
                <h3>Attachments</h3>
                <p>
                    Not only attachments have been rewritten, they have some new features added. One of the most requested features is to show the list of all attachments for the topic and all the topic replies. It is AJAX powered, paged and can show attachments as list or thumbnails.
                </p>
            </div>
            <div class="d4p-layout-unit half align-left">
                <img src="https://dev4press.s3.amazonaws.com/plugins/gd-bbpress-toolbox/6.5/about/attachments.jpg"/>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit half align-left">
                <h3>Post Anonymously</h3>
                <p>
                    A new, and very useful feature is ability of registered users to post anonymously without logging out. The feature allows great degree of control of allowed user roles, forums where it can be used and more. Link between real user and anonymous posts can be kept (optional).
                </p>
            </div>
            <div class="d4p-layout-unit half align-left">
                <h3>Journal Topic</h3>
                <p>
                    Another new, and useful feature is to allow creation of the topic where only topic author can post replies, and all other users can only read the content of the topic. This way, the thread looks like the list of uninterrupted journal entries from single author.
                </p>
            </div>
        </div>
    </div>

    <div class="d4p-whatsnew-section">
        <div class="d4p-layout-grid">
            <div class="d4p-layout-unit half align-left">
                <h3>And more new features</h3>
                <p>
                    Many of the plugin existing features have been improved and expanded with new options. This includes: private topics and replies, visitors redirect, statistics and more.
                </p>
            </div>
            <div class="d4p-layout-unit half align-left">
                <h3>With updates and fixes</h3>
                <p>
                    And, as usual, many other things have been improved, and many more things fixed, including several attachments related bugs, problems with statistics, auto close topics and more.
                </p>
            </div>
        </div>
    </div>
</div>
