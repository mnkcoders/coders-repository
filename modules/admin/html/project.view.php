<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>

<table class="widefat fixed project">
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
            <td><?php print $this->label_status; ?></td>
            <td><?php print $this->display_status ?></td>
        </tr>
        <tr>
            <td>Public</td>
            <td>
                <select>
                    <option>Yes</option>
                    <option>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Label</td>
            <td> More project settings here</td>
        </tr>
        <tr>
            <td colspan="2"><?php wp_editor( $this->value_content, 'content' ); ?></td>
        </tr>
        <tr>
            <th colspan="2">
                <h2><?php print __('Tiers','coders_repository') ?></h2>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <ul class="tiers">
                    <li>Copper Coin</li>
                    <li>Silver Coin</li>
                    <li>Golden Coin</li>
                    <li>Platinum Coin</li>
                    <li>Diamond Coin</li>
                </ul>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <h2><?php print __('Integrations','coders_repository') ?></h2>
            </th>
        </tr>
        <tr>
            <td>Connect to WooCommerce</td>
            <td>
                <select>
                    <option>Yes</option>
                    <option>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Connect to Patreon</td>
            <td>
                <select>
                    <option>Yes</option>
                    <option>No</option>
                </select>
            </td>
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
