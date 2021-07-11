<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="post">
    <form name="artpad_post" action="<?php print $this->form_url ?>" method="POST">
        <p><input class="widefat" type="text" name="post_title" value="<?php print $this->title ?>" /></p>
        <p><?php wp_editor($this->content, 'content'); ?></p>
        <p><textarea  name="post_excerpt" class="widefat"><?php print $this->excerpt ?></textarea></p>
        <p><?php print $this->post_parent ?></p>
        <?php print $this->action_save('Save'); ?>
    </form>
</div>

