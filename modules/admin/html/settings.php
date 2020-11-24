<?php defined('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>

<form name="settings" method="post" action="<?php print $this->settings_url ?>">
    <table class="settings widefat fixed">
        <thead>
            <tr>
                <th colspan="2">
                    <h2><?php print __('Section', 'coders_repository') ?></h2>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><label for="id_setting"><?php print __('Setting', 'coders_repository') ?></label></td>
                <td><input id="id_setting" type="text" name="setting" value="" placeholder="Value" /></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?php print $this->action('admin.settings.test_email',__('Send Test Mail','coders_repository')) ?>
                    <button type="submit" name="_action" value="save" class="button button-primary right"><?php print __('Save', 'coders_repository') ?></button>
                </td>
            </tr>
        </tfoot>
    </table>
</form>



