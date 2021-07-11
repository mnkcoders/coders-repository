<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="collection">
    <?php foreach( $this->list_posts() as $id => $post ) : ?>
    <p>
        <a href="<?php print $this->display_edit( $id ) ?>" target="_self" class="link">
            <?php print $post['title'] ?>
        </a>
    </p>
    <?php endforeach; ?>
</div>

