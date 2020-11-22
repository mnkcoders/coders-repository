<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline">
    <?php print get_admin_page_title();  ?>
</h1>
<div class="widefat fixed">
    <form name="new-account" method="post" action="<?php print $this->save_form_url ?>">
        <table class="table widefat fixed">
            <thead>
                <tr>
                    <th colspan="2">
                        <h2 class="centered"><?php print $this->name; ?></h2>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php print $this->label_ID ?></td>
                    <td class="strong"><?php print $this->ID ?></td>
                </tr>
                <tr>
                    <td><?php print $this->label_token ?></td>
                    <td class="strong"><?php print $this->token ?></td>
                </tr>
                <tr>
                    <td><?php print $this->label_name ?></td>
                    <td class="strong"><?php print $this->input_name ?></td>
                </tr>
                <tr>
                    <td><?php print $this->label_email_address ?></td>
                    <td class="strong"><?php print $this->input_email_address ?></td>
                </tr>
                <tr>
                    <td><?php print $this->label_date_created ?></td>
                    <td class="strong"><?php print $this->date_created ?></td>
                </tr>
                <tr>
                    <td><?php print $this->label_date_updated ?></td>
                    <td class="strong"><?php print $this->date_updated ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <a href="<?php
                            print $this->main_url ?>" target="_self" class="button inline"><?php
                            print __('Back','coders_repository'); ?></a>
                        <?php print $this->action_create(
                                __('Create', 'coders_repository') ,
                                'button-primary right') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
    <hr>
    <table class="widefat fixed table">
        <thead>
            <tr>
                <th colspan="3"><h2><?php print __('Subscriptions','coders_repository') ?></h2></th>
            </tr>
            <tr>
                <td colspan="3">
                    <form name="subscriptions" method="post" action="<?php print $this->subscription_url ?>">
                        <?php print $this->input_subscription ?>
                        <?php print $this->action_create ?>
                    </form>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Silver Tier</td>
                <td><?php print date('Y-m-d H:i:s') ?></td>
                <td><?php print 'expired' ?></td>
            </tr>
        </tbody>
        <footer>
            <tr>
                <td colspan="3"></td>
            </tr>
        </footer>
    </table>
</div>
