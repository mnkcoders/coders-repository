<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<div class="widefat fixed">
    <h2><?php print __('Invaild Account', 'coders_artpad')  ?></h2>
    <a href="<?php
        print $this->main_url ?>" target="_self" class="button button-primary"><?php
        print __('Back','coders_artpad'); ?></a>
</div>
