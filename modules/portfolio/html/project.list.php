<?php defined('ABSPATH') or die; ?>
<ul class="portfolio container list inline">
    <?php foreach( $this->list_projects as $ID => $data ) :  ?>
    <li>
        <p><a target="_self" class="button button-primary" href="<?php
                print $this->project_link( $ID ) ?>"><?php
                print $data['title'] ?></a></p>
        <p><?php print $data['content'] ?></p>
        <p><?php print $this->display_wp_image( $data['image_id'] ) ?></p>
        <p><?php print __('Since','coders_artpad') . ' ' . $this->calendar_date( $data['date_created'] ) ?></p>
    </li>
    <?php endforeach; ?>
</ul>