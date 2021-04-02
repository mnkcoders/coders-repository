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
        <?php print $this->label_status; ?>
        <?php print $this->display_status ?>
    </div>
    <div>
        <?php print $this->label_access_level; ?>
        <?php print $this->display_access_level ?>
    </div>
    <div>
        <?php print $this->label_connect_patreon ?>
        <?php print $this->display_patreon_integration ?>
    </div>
    <div>
        <?php print $this->label_connect_wc ?>
        <?php print $this->display_woocommerce_integration ?>
    </div>
    <ul class="container solid widefat">
        <?php if( $this->has_collections ) : ?>
        <?php foreach( $this->list_collections as $id => $collection ) : ?>
        <li class="collection">
            <a href="<?php print '#' ?>" target="_self"><?php print $id ?></a>
        </li>
        <?php endforeach; ?>
        <?php else: ?>
        <li class="collection">
            <a href="<?php print \CODERS\ArtPad\Request::url('admin.collection') ?>" target="_self"><?php print __('Find Collections','coders_artpad'); ?></a>
        </li>
        <?php endif; ?>
    </ul>
    <div>
        <?php wp_editor($this->content, 'content'); ?>
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