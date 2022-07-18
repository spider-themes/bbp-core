<div class="gdbbx-widget-the-info-dl">
    <dl>
        <?php

        foreach ($instance['stats'] as $stat) {
            echo '<dt class="gdbbx-stat-'.$stat.' gdbbx-stat-item-label">'.$elements[$stat].'</dt>';
            echo '<dd class="gdbbx-stat-'.$stat.' gdbbx-stat-item-value">'.$statistics[$stat].'</dd>';
        }

        ?>
    </dl>
</div>
