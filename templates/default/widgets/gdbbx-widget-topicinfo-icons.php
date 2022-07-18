<div class="gdbbx-widget-the-info-table">
    <table>
        <tbody>
            <?php

            foreach ($results as $code => $item) {
                echo '<tr class="'.$code.'"><th><i class="gdbbx-icon gdbbx-fw gdbbx-icon-'.$item['icon'].'"></i> '.$item['label'].'</th><td>'.$item['value'].'</td></tr>';
            }

            ?>
        </tbody>
    </table>
</div>
