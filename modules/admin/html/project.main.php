<?php defined ('ABSPATH') or die; ?>
<table class="widefat fixed project tab-panel" data-tab="project">
    <thead>
        <tr>
            <td>
                <?php print $this->action('admin.main',__('Back','coders_repository')); ?>
            </td>
            <td>
                <?php print $this->action_save(__('Save','coders_repository'),'button-primary right'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php print $this->display_title(__('Your project title :D','coders_repository')) ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="media">
                    <?php print $this->display_image ?>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php print __('Collection','coders_repository'); ?></td>
            <td><?php print $this->display_collection ?></td>
        </tr>
        <tr>
            <td><?php print $this->label_status; ?></td>
            <td><?php print $this->display_status ?></td>
        </tr>
        <tr>
            <td><?php print __('Access Level','coders_repository') ?></td>
            <td>
                <select>
                    <option value="public">Public</option>
                    <option value="subscription">Subscription</option>
                    <option value="nsfw">NSFW Subscription</option>
                    <option value="private">Private</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php wp_editor( $this->value_content, 'content' ); ?></td>
        </tr>
        <tr>
            <th colspan="2">
                <h2><?php print __('Integrations','coders_repository') ?></h2>
            </th>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <i class="control right"><?php printf('%s: %s',
                                __('Created', 'coders_repository'),
                                $this->date_created) ?></i>
                <i class="control right"><?php printf('%s: %s',
                                __('Last Updated', 'coders_repository'),
                                $this->date_updated) ?></i>
            </td>
        </tr>
    </tfoot>
</table>