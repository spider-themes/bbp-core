<?php

use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if (!defined('ABSPATH')) { exit; }

$pages = gdbbx_admin()->menu_items;
$_page = gdbbx_admin()->page;
$_panel = gdbbx_admin()->panel;

if ( Plugin::instance()->is_enabled('canned-replies')) {
    $pages = array_merge(array_slice($pages, 0, 4),
                         array('canned' => array('title' => __("Canned Replies", "bbp-core"), 'icon' => 'reply')),
                         array_slice($pages, 4));
}

if (!empty($panels)) {
    if ($_panel === false || empty($_panel)) {
        $_panel = 'index';
    }

    $_available = array_keys($panels);

    if (!in_array($_panel, $_available)) {
        $_panel = 'index';
        gdbbx_admin()->panel = false;
    }
}

$_classes = array('d4p-wrap', 'wpv-'.GDBBX_WPV, 'd4p-page-'.$_page);

if ($_panel !== false) {
    $_classes[] = 'd4p-panel';
    $_classes[] = 'd4p-panel-'.$_panel;
}

$_message = '';

if (isset($_GET['message']) && $_GET['message'] != '') {
    $msg = d4p_sanitize_slug($_GET['message']);

    switch ($msg) {
        case 'free-disabled':
            $_message = _x("Free plugins are now disabled.", "Operation completed message", "bbp-core");
            break;
        case 'saved':
            $_message = _x("Settings are saved.", "Operation completed message", "bbp-core");
            break;
	    case 'completed':
		    $_message = _x("The operation completed.", "Operation completed message", "bbp-core");
		    break;
        case 'removed':
            $_message = _x("Removal operation completed.", "Operation completed message", "bbp-core");
            break;
        case 'imported':
            $_message = _x("Import operation completed.", "Operation completed message", "bbp-core");
            break;
        case 'invalid-import':
            $_message = _x("Import file is not valid, import can't be done.", "Operation completed message", "bbp-core");
            break;
        case 'nothing-to-import':
            $_message = _x("No settings have been selected for import.", "Operation completed message", "bbp-core");
            break;
        case 'nothing':
            $_message = _x("Nothing to do.", "Operation completed message", "bbp-core");
            break;
        case 'closed':
            $topics = absint($_GET['topics']);

            $_message = sprintf(_x("Total %s topics closed.", "Operation completed message", "bbp-core"), $topics);
            break;
        case 'ips-removed':
            $ips = absint($_GET['ips']);

            $_message = sprintf(_x("Total %s IP records removed.", "Operation completed message", "bbp-core"), $ips);
            break;
        case 'cleanup-thanks':
            $thanks = absint($_GET['thanks']);

            $_message = sprintf(_x("Total %s orphaned thanks records removed.", "Operation completed message", "bbp-core"), $thanks);
            break;
        case 'attachments-detach':
            $_message = _x("Selected attachments are no longer attached.", "Operation completed message", "bbp-core");
            break;
        case 'attachment-detach':
            $_message = _x("Attachment is no longer attached.", "Operation completed message", "bbp-core");
            break;
        case 'attachments-delete':
            $_message = _x("Selected attachments deleted from media library.", "Operation completed message", "bbp-core");
            break;
        case 'attachment-delete':
            $_message = _x("Attachment deleted from media library.", "Operation completed message", "bbp-core");
            break;
        case 'errors-deleted':
            $_message = _x("Selected errors records deleted from database.", "Operation completed message", "bbp-core");
            break;
        case 'error-deleted':
            $_message = _x("Error record deleted from database.", "Operation completed message", "bbp-core");
            break;
        case 'users-updated':
            $_message = _x("Selected users have been updated.", "Operation completed message", "bbp-core");
            break;
    }
}

?>
<div class="<?php echo join(' ', $_classes); ?>">
    <div class="d4p-header">
        <div class="d4p-navigator">
            <ul>
                <li class="d4p-nav-button">
                    <a href="#"><i aria-hidden="true" class="<?php echo d4p_get_icon_class($pages[$_page]['icon']); ?>"></i> <?php echo $pages[$_page]['title']; ?></a>
                    <ul>
                        <?php

                        foreach ($pages as $page => $obj) {
                            $url = 'admin.php?page=gd-bbpress-toolbox-'.$page;

                            if ($page == 'canned') {
                                $url = 'edit.php?post_type=bbx_canned_reply';
                            }

                            if ($page != $_page) {
                                echo '<li><a href="'.$url.'"><i aria-hidden="true" class="'.(d4p_get_icon_class($obj['icon'], 'fw')).'"></i> '.$obj['title'].'</a></li>';
                            } else {
                                echo '<li class="d4p-nav-current"><i aria-hidden="true" class="'.(d4p_get_icon_class($obj['icon'], 'fw')).'"></i> '.$obj['title'].'</li>';
                            }
                        }

                        ?>
                    </ul>
                </li>
                <?php if (!empty($panels)) { ?>
                <li class="d4p-nav-button">
                    <a href="#"><i aria-hidden="true" class="<?php echo d4p_get_icon_class($panels[$_panel]['icon']); ?>"></i> <?php echo $panels[$_panel]['title']; ?></a>
                    <ul>
                        <?php

                        foreach ($panels as $panel => $obj) {
                            if ($panel != $_panel) {
                                echo '<li><a href="admin.php?page=gd-bbpress-toolbox-'.$_page.'&panel='.$panel.'"><i aria-hidden="true" class="'.(d4p_get_icon_class($obj['icon'], 'fw')).'"></i> '.$obj['title'].'</a></li>';
                            } else {
                                echo '<li class="d4p-nav-current"><i aria-hidden="true" class="'.(d4p_get_icon_class($obj['icon'], 'fw')).'"></i> '.$obj['title'].'</li>';
                            }
                        }

                        ?>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="d4p-plugin">
            GD bbPress Toolbox Pro
        </div>
    </div>
    <?php

    if ($_message != '') {
        echo '<div class="updated">'.$_message.'</div>';
    }

    ?>
    <div class="d4p-content">
