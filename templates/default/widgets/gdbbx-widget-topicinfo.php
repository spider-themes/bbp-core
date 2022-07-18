<div class="gdbbx-widget-the-info-table">
    <table>
        <tbody>
            <?php

            foreach ($results as $code => $item) {
                echo '<tr class="'.$code.'"><th>'.$item['label'].'</th><td>'.$item['value'].'</td></tr>';
            }

            ?>
        </tbody>
    </table>
</div>
