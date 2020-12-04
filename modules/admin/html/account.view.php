<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<ul class="container widefat fixed inline">
    <li class="button" data-tab="profile"><?php print __('Profile','coders_artpad') ?></li>
    <li class="button" data-tab="subscriptions"><?php print __('Subscriptions','coders_artpad') ?></li>
    <li class="button" data-tab="points"><?php print __('Points','coders_artpad') ?></li>
</ul>
<div class="container widefat fixed">
    <div class="panel" data-tab="profile">
        <form name="new-account" method="post" action="<?php print $this->save_form_url ?>">
            <table class="table widefat fixed">
                <thead>
                    <tr>
                        <th>
                            <h2 class="centered"><?php print __('Profile','coders_artpad'); ?></h2>
                        </th>
                        <th>
                            <h2><?php print $this->status_button; ?></h2>
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
                        <td class="strong"><?php print $this->name ?></td>
                    </tr>
                    <tr>
                        <td><?php print $this->label_email_address ?></td>
                        <td class="strong"><?php print $this->email_address ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td class="detail">
                            <ul class="inline">
                                <li><?php printf('%s <span class="data">%s</span>',
                                $this->label_date_created,
                                $this->date_created) ?></li>
                                <li><?php printf('%s <span class="data">%s</span>',
                                $this->label_date_updated,
                                $this->date_updated) ?></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="<?php
                                print $this->main_url ?>" target="_self" class="button inline"><?php
                                print __('Back','coders_artpad'); ?></a>
                            <?php print $this->action_update(
                                    __('Update', 'coders_artpad') ,
                                    'button-primary right') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
    <div class="panel" data-tab="subscriptions">
        <table class="widefat fixed table">
            <thead>
                <tr>
                    <th colspan="3"><h2><?php print __('Subscriptions','coders_artpad') ?></h2></th>
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
    <div class="panel" data-tab="points">
        <table class="widefat fixed table">
            <thead>
                <tr>
                    <th colspan="3"><h2><?php print __('Points','coders_artpad') ?></h2></th>
                </tr>
                <tr>
                    <th><?php print __('Date','coders_artpad') ?></th>
                    <th><?php print __('Type','coders_artpad') ?></th>
                    <th><?php print __('Amount','coders_artpad') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->list_transactions as $transaction ) : ?>
                <tr>
                    <td><?php print $transaction['date'] ?></td>
                    <td><?php print $transaction['type'] ?></td>
                    <td><?php print $transaction['amount'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <strong class="right"><?php print __('Total','coders_artpad') ?></strong>
                    </td>
                    <td>
                        <strong class="total"><?php print $this->total_points ?></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


