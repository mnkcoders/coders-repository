<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<h2 class="wp-heading-inline">
    <?php print __('Dashboard','coders_artpad'); ?>
    <?php print $this->tier_title ?>
</h2>
<div class="container tier">
    
    <p><?php print $this->project_id ?></p>
    <p><?php print $this->tier ?></p>
    <p><?php print $this->title ?></p>
    <p><?php print $this->description ?></p>
    <p><?php print $this->image_id ?></p>
    <p><?php print $this->status ?></p>
    <p><?php print $this->date_created ?></p>
    <p><?php print $this->date_updated ?></p>

</div>