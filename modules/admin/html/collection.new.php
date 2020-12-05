<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print $this->display_navigator ?></h1>
<hr class="spacer" />
<div class="toolbox container inline solid clearfix centered">
    <ul id="id_artpad_nav" class="navigator panel left inline">
        <!-- PATH ROUTE -->
    </ul>
    <ul id="id_artpad_grid" class="grid inline panel right">
        <!-- COLLECTION GRID SIZE -->
    </ul>
</div>
<table class="table widefat fixed collection-box" data-tab="collection">
    <tbody>
        <tr>
            <td>
                <div id="id_artpad_uploader" class="uploader item container">
                    <form name="collection" method="POST" action="<?php print '' ?>" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php print 256*256 ?>">
                        <input class="hidden" id="id_uploader" type="file" name="upload[]" multiple="true">
                        <button class="button button-primary dashicons-before dashicons-upload" type="submit" name="_action" value="upload">Upload</button>
                    </form>
                    <label class="dashicons-before dashicons-media-default button" for="id_collection">Select files</label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <ul id="id_artpad_collection" class="collection grid-4" data-id="0">
                    <!-- RENDER HERE ALL RESOURCES -->
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<table class="table widefat fixed post-data" data-tab="post">
    <tbody>
        <tr>
            <th colspan="2">
                <input type="text" name="title" id="id_artpad_title" class="title" placeholder="">
            </th>
        </tr>
        <tr>
            <td class="media" id="id_artpad_media">
                <!-- render here image -->
            </td>
            <td>
                <p><?php wp_editor( '', 'content' ); ?></p>
                <button type="submit" name="_action" value="save" class="button button-primary big"><?php
                        print __('Save','coders_artpad') ?></button>
            </td>
        </tr>
    </tbody>
</table>




