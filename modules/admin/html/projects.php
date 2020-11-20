<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<ul class="projects">
    <?php foreach( $this->list_projects  as $id => $project) : ?>
    <li>
        <div class="project">
            <?php print $this->display_image( $project['image_id'] )  ?>
            <a class="title" href="<?php
                print $this->url( $id ) ?>" target="_self"><?php
                print $project['title'] ?></a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>