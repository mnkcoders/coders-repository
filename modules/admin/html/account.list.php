<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<table class="accounts widefat fixed">
    <thead>
        <tr class="new">
            <td class="inline" colspan="4">
                <form name="new-account" method="post" action="<?php print $this->form_url ?>">
                    <?php print $this->input_name ?>
                    <?php print $this->error_name ?>
                    <?php print $this->input_email_address ?>
                    <?php print $this->error_email_address ?>
                    <?php print $this->action_create(__('Create', 'coders_repository'),'button-primary') ?>
                </form>
            </td>
        </tr>
        <tr>
            <th><?php print __('Name', 'coders_repository') ?></th>
            <th><?php print __('Email', 'coders_repository') ?></th>
            <th><?php print __('Status', 'coders_repository') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody class="accounts-content">
    <?php foreach( $this->list_accounts as $account ) : ?>
    <!--<?php var_dump($account) ?> -->
    <tr>
        <td>
            <a href="<?php print $this->account_url( $account['ID']); ?>" target="_self" class="link">
            <?php print $account['name'] ?>
            </a>
        </td>
        <td><?php print $account['email_address'] ?></td>
        <td><?php print $this->display_status( $account['status' ] ) ?></td>
        <td>
            <button type="button" name="_action" value="action1" class="button">
                <?php print __('Action 1','coders_repository') ?>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">
                <ul class="pagination center inline">
                    <li>Before</li>
                    <li>1</li>
                    <li>2</li>
                    <li>3</li>
                    <li>After</li>
                </ul>
            </td>
        </tr>
    </tfoot>
</table>