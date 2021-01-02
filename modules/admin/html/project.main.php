<?php defined('ABSPATH') or die; ?>

<form name="project" action="<?php print $this->save_project_url($this->ID) ?>" method="post" class="artpad-container">
    <ul class="inline toolbar">
        <li><?php print $this->action('admin.main', __('Back', 'coders_artpad')); ?></li>
        <li><?php print $this->action_save(__('Save', 'coders_artpad'), 'button-primary right'); ?></li>
        <li><a href="<?php
            print $this->public_url ?>" target="_blank"><?php
            print __('Public','coders_artpad') ?></a>
        </li>
    </ul>
    <div class="title"><?php print $this->display_title(__('Your project title :D', 'coders_artpad')) ?></div>
    <div class="media">
        <?php print $this->display_image ?>
    </div>
    <div>
        <?php print $tihs->label_collection ?>
        <?php print $this->display_collection ?>
    </div>
    <div>
        <?php print $this->label_status; ?>
        <?php print $this->display_status ?>
    </div>
    <div>
        <?php print $this->label_access_level; ?>
        <?php print $this->display_access_level ?>
    </div>
    <div>
        <?php wp_editor($this->value_content, 'content'); ?>
    </div>
    <ul class="status inline">
        <li class="right"><?php
            printf('%s: %s',
                    __('Created', 'coders_artpad'),
                    $this->date_created)
            ?></li>
        <li class="right"><?php
            printf('%s: %s',
                    __('Last Updated', 'coders_artpad'),
                    $this->date_updated)
            ?></li>
    </ul>
</form>