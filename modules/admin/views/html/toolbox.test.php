<div class="toolbox container solid clearfix centered">
    <?php print $this->display_navigator ?>
    <ul class="toggler right" id="id_artpad_toggler">
        <li class="right button" data-action="upload"><?php print __('Upload','coders_artpad') ?></li>
        <?php if( $this->is_post ) : ?>
        <li class="button" data-action="post"><?php print __('Post','coders_artpad') ?></li>
        <?php endif; ?>
    </ul>
    <ul id="id_artpad_grid" class="grid inline panel right">
        <?php foreach( $this->list_grid as $size ) : ?>
        <li class="option button <?php
                print $this->grid_option == $size ? 'button-primary' : '' ?>" data-size="<?php
                print $size ?>"><?php printf('x%s',$size) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
