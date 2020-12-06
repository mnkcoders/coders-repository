<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<?php if( $this->is_post ) : ?>
<table class="table widefat fixed post-data" data-tab="post">
    <tbody>
        <tr>
            <th colspan="2">
                <input type="text" name="title" id="id_artpad_title" class="title" placeholder="<?php
                        print $this->name ?>" value="<?php
                        print $this->title ?>">
            </th>
        </tr>
        <tr>
            <td class="media" id="id_artpad_media">
                <!-- render here image -->
                <?php if( $this->is_image ) : ?>
                <img src="<?php 
                        print $this->attachment_url ?>" title="<?php
                        print $this->name ?>" class="media"/>
                <?php elseif( $this->is_text ) : ?>
                <div class="display"><?php
                    print $this->attachment_content ?></div>
                <?php endif; ?>
            </td>
            <td>
                <p><?php wp_editor( $this->content, 'content' ); ?></p>
                <button type="submit" name="_action" value="save" class="button button-primary big"><?php
                        print __('Save','coders_artpad') ?></button>
            </td>
        </tr>
    </tbody>
</table>
<?php endif; ?>
<div class="toolbox container inline solid clearfix centered">
    <ul id="id_artpad_nav" class="navigator panel left inline">
        <!-- PATH ROUTE -->
        <?php foreach( $this->list_route as $id => $title ) : ?>
        <li class="item"><a href="<?php 
                print $this->action('admin.collection',array('ID'=>$id)) ?>" target="_self"><?php
                print $title ?></a></li>
        <?php endforeach; ?>
    </ul>
    <ul id="id_artpad_grid" class="grid inline panel right">
        <!-- COLLECTION GRID SIZE -->
        <?php foreach( $this->list_grid as $size ) : ?>
        <li class="size" data-size="<?php print $size ?>"><?php printf('x%s',$size) ?></li>
        <?php endforeach; ?>
    </ul>
    <span class="right button button-primary"><?php print __('Upload','coders_artpad') ?></span>
</div>
<div id="id_artpad_uploader" class="uploader item container">
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
</ul>




