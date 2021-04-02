<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="tab-container container solid widefat fixed">
    <!--ul class="tab-menu inline widefat fixed solid pad-sm">
        <li class="tab" data-tab="project"><?php print __('Project','coders_artpad') ?></li>
        <li class="tab" data-tab="tiers"><?php print __('Tiers','coders_artpad') ?></li>
        <li class="tab" data-tab="integrations"><?php print __('Integrations','coders_artpad') ?></li>
    </ul-->
    <div class="tab-content">
        <div class="tab-panel" data-tab="project">
            <?php print $this->view_project_main ?>
        </div>
        <div class="tab-panel" data-tab="tiers">
            <?php print $this->view_project_tiers ?>
        </div>
        <div class="tab-panel" data-tab="integrations">
            <?php print $this->view_project_integrations ?>
        </div>
    </div>
</div>