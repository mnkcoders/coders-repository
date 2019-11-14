<?php defined ('ABSPATH') or die; ?>
<ul class="collections">
    <?php foreach( ( ($storage = $this->storage ) !== FALSE ? $storage : array()) as $item ) : ?>
    <li>
        <a href="<?php print '#' ?>" target="_self"><?php print $item ?></a>
    </li>
    <?php endforeach; ?>
    <li class="create">
        <input type="text" name="coders.repo.create" placeholder="<?php print __('Your new collection','coders_repository') ?>" />
        <button type="button" class="button button-primary">Add collection</button>
    </li>
</ul>
<ul class="collection">
    <?php foreach( ( ($collection = $this->collection( $this->selected ) ) !== FALSE ? $collection : array()) as $item ) : ?>
    <li class="item">
        <a href="<?php print \CodersRepo::url($item['public_id'])  ?>" target="_blank"><?php
        
        print $item['name'];
        
        ?></a>
    </li>
    <?php endforeach; ?>
    <li class="container upload dropzone">
        <label class="caption">Upload here your items</label>
    </li>
    <li class="container upload">
        <form name="upload" action="<?php print $this->form_action  ?>" method="post" enctype="multipart/form-data">
        
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            <input type="file" name="coders.repo.upload" />
            <button type="submit" name="coders.repo.action" value="upload">Upload</button>
        </form>
    </li>
    
</ul>