<?php defined ('ABSPATH') or die; ?>
<table class="widefat fixed tiers artpad-container">
    <thead>
        <tr>
            <th colspan="2">
                <h2><?php print __('Tiers','coders_artpad') ?></h2>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">
                <ul class="tiers">
                    <?php foreach( $this->list_tiers as $id => $tier) : ?>
                    <li class="tier">
                        <p><a href="<?php 
                                print $this->tier_url($id) ?>" target="_self" class="link"><?php
                                print $tier['title'] ?></a></p>
                        <p><?php print $tier['description'] ?></p>
                        <p><?php print $tier['image_id'] ?></p>
                        <p><?php print $tier['level'] ?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
