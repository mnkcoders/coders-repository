<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="toolbox container solid clearfix centered">
    <?php print $this->display_navigator ?>
    <!--ul id="id_artpad_nav" class="navigator panel left inline">
        <li class="item home"><?php print $this->action(
                'admin.collection',
                __('Home','coders_artpad'),
                'dashicons dashicons-art link') ?></li>
        <?php foreach( $this->list_route as $id => $title ) : ?>
        <li class="item"><a href="<?php 
                print $this->action('admin.collection',array('ID'=>$id)) ?>" target="_self"><?php
                print $title ?></a></li>
        <?php endforeach; ?>
    </ul-->
    <ul class="toggler right" id="id_artpad_toggler">
        <li class="right button" data-action="upload"><?php print __('Upload','coders_artpad') ?></li>
        <?php if( $this->is_post ) : ?>
        <li class="button" data-action="post"><?php print __('Post','coders_artpad') ?></li>
        <?php endif; ?>
    </ul>
    <ul id="id_artpad_grid" class="grid inline panel right">
        <!-- COLLECTION GRID SIZE -->
        <?php foreach( $this->list_grid as $size ) : ?>
        <li class="option button <?php
                print $this->grid_option == $size ? 'button-primary' : '' ?>" data-size="<?php
                print $size ?>"><?php printf('x%s',$size) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php if( $this->is_post ) : ?>
<div class="table widefat fixed post-data" data-tab="post">
    <form name="artpad-post" action="<?php print $this->form_url; ?>" method="post">
        <div class="post-data container">
            <input type="text" name="title" id="id_artpad_title" class="title" placeholder="<?php
                    print $this->name ?>" value="<?php
                    print $this->title ?>">
            <div class="text container inline grid-8">
                <?php wp_editor($this->content, 'content'); ?>
            </div>
            <div class="meta container inline grid-4">
                <div class="media" id="id_artpad_media">
                        <!-- render here image -->
                        <?php if ($this->is_image) : ?>
                            <img src="<?php print $this->link ?>" title="<?php print $this->name ?>" class="media"/>
                        <?php elseif ($this->is_text) : ?>
                            <div class="display"><?php print $this->attachment_content ?></div>
                        <?php endif; ?>
                </div>
                <div class="table widefat fixed access-level" data-tab="access">
                    <fieldset>
                        <label for="id_tier"><?php print __('Tier','coders_artpad') ?></label>
                        <?php print $this->input_tier ?>
                    </fieldset>
                </div>
                <button type="submit" name="_action" value="save" class="button button-primary big right">
                    <?php print __('Save', 'coders_artpad') ?>
                </button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
<div id="id_artpad_uploader" class="uploader container" data-tab="uploader">
    <form name="collection" method="POST" action="<?php print $this->upload_url ?>" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php
                print $this->file_size ?>">
        <!--input class="hidden" id="id_uploader" type="file" name="upload[]" multiple="true"-->
        <label class="dashicons-before dashicons-media-default button" for="id_collection"><?php
                print __('Select files','coders_artpad') ?></label>
        <button class="button button-primary dashicons-before dashicons-upload" type="submit" name="_action" value="upload"><?php
                print __('Upload','coders_artpad') ?></button>
    </form>
</div>
<ul id="id_artpad_collection" class="collection grid-4" data-id="<?php print $this->post_id ?>">
    <!-- RENDER HERE ALL RESOURCES -->
    <?php foreach( $this->list_items as $id => $item ) : ?>
    <li class="item" data-id="<?php print $id ?>">
        <div class="content <?php print $item['type'] ?>">
            <!--img alt="<?php print $item['name'] ?>" title="<?php print $item['title'] ?>" src="<?php print $item['url'] ?>"-->
            <?php print $this->display_resource($item) ?>
            <a class="caption title" target="_self" href="<?php print $this->post_url($id); ?>"><?php print $item['title'] ?></a>
            <a class="action open dashicons dashicons-admin-links top-right rounded"
               target="_blank"
               title="<?php print __('Display','coders_artpad') ?>"
               href=""></a>
            <span class="action remove dashicons dashicons-trash bottom-left rounded"></span>
        </div>
    </li>
    <?php endforeach; ?>
</ul>




