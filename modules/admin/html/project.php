<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print __('Project','coders_artpad') ?></h1>
<h2 class="wp-heading-inline">
    <?php print __('Dashboard','coders_artpad'); ?>
    <a href="#" target="_self"><?php print $this->title; ?></a>
</h2>

<ul class="tab-menu container inline widefat fixed solid pad-sm">
    <li class="tab" data-tab="project"><?php print __('Project','coders_artpad') ?></li>
    <li class="tab" data-tab="tiers"><?php print __('Tiers','coders_artpad') ?></li>
    <li class="tab" data-tab="integrations"><?php print __('Integrations','coders_artpad') ?></li>
</ul>
<div class="tab-content widefat fixed">
    <?php print $this->view_project_main ?>
    <?php print $this->view_project_tiers ?>
    <?php print $this->view_project_integrations ?>
</div>