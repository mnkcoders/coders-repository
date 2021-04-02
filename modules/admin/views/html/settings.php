<?php defined('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<form name="settings" method="post" action="<?php print $this->settings_url ?>">
    <table class="settings widefat fixed">
        <!--thead>
            <tr>
                <th colspan="2">
                    <h2><?php print __('Section', 'coders_artpad') ?></h2>
                </th>
            </tr>
        </thead-->
        <tbody>
            <tr>
                <td><label for="id_root_path"><?php print __('Collections Path', 'coders_artpad') ?></label></td>
                <td><?php print $this->display_root_path ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?php print $this->action('admin.settings.test_email',__('Send Test Mail','coders_artpad')) ?>
                    <button type="submit" name="_action" value="save" class="button button-primary right"><?php print __('Save', 'coders_artpad') ?></button>
                </td>
            </tr>
        </tfoot>
    </table>
</form>



