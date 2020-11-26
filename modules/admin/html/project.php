<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline">
    <?php print __('Project','coders_repository') ?> :
    <strong class="data-title"><?php print $this->title; ?></strong></h1>

<ul class="tab-menu container inline widefat fixed solid pad-sm">
    <li class="tab" data-tab="project"><?php print __('Project','coders_repository') ?></li>
    <li class="tab" data-tab="tiers"><?php print __('Tiers','coders_repository') ?></li>
    <li class="tab" data-tab="integrations"><?php print __('Integrations','coders_repository') ?></li>
</ul>
<div class="tab-content widefat fixed">
    <?php print $this->view_project_main ?>
    <?php print $this->view_project_tiers ?>
    <?php print $this->view_project_integrations ?>
</div>