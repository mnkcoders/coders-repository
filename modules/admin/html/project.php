<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="tab-container container">
    <!--ul class="tab-menu inline widefat fixed solid pad-sm">
        <li class="tab" data-tab="project"><?php print __('Project','coders_artpad') ?></li>
        <li class="tab" data-tab="tiers"><?php print __('Tiers','coders_artpad') ?></li>
        <li class="tab" data-tab="integrations"><?php print __('Integrations','coders_artpad') ?></li>
    </ul-->
    <div class="tab-content widefat fixed">
        <?php print $this->view_project_main ?>
        <?php print $this->view_project_tiers ?>
        <?php print $this->view_project_integrations ?>
    </div>
</div>