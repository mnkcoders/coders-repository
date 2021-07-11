<?php defined('ABSPATH') or die; ?>
<div class="container" id="login-form">
    <form name="login" method="post" action="<?php print $this->login_url ?>">
        <fieldset>
            <label for="id_email"><?php print __('Login') ?></label>
                <?php print $this->display_email(__('Your email address', 'coders_artpad')) ?>
                <?php print $this->action_invite(__('Sendme Access Link!','coders_artpad')); ?>
        </fieldset>
    </form>
</div>
<div class="container" id="register-form">
    <form name="register" method="post" action="<?php print $this->register_url ?>">
        <fieldset>
            <label for="id_email"><?php print __('Register') ?></label>
            
            <?php print $this->display_email(__('Your email address', 'coders_artpad')) ?>

            <?php print $this->display_name(__('Your avatar name', 'coders_artpad')) ?>

            <?php print $this->action_register(__('Register Me!','coders_artpad')); ?>

        </fieldset>
    </form>
</div>