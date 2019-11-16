<?php defined ('ABSPATH') or die; ?>
<script type="text/javascript">CodersRepository.client('<?php print $this->form_action() ?>');</script>
<h1 class="header wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<form name="upload" action="<?php print $this->form_action ?>" method="post" enctype="multipart/form-data">
    <ul class="collection">
        <?php foreach( ( ($storage = $this->storage ) !== FALSE ? $storage : array()) as $item ) : ?>
        <li class="category inline">
            <input class="selector"
                   id="id_<?php print $item ?>"
                   <?php echo $this->selected === $item ? "checked" : "" ?>
                   type="radio"
                   name="coders.repo.collection"
                   value="<?php print $item ?>" />
            <label for="id_<?php print $item ?>"><?php print $item ?></label>
        </li>
        <?php endforeach; ?>
        <li class="inline create">
            <input type="text" name="coders.repo.create" placeholder="<?php print __('Your new collection','coders_repository') ?>" />
            <button type="submit" name="coders.repo.action" value="create_collection" class="button button-primary">Add collection</button>
            <button type="submit" name="coders.repo.action" value="refresh" class="button">Select</button>
        </li>
    </ul>
    <div class="container upload dropzone">
        <label class="caption separator">Upload here your items</label>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php print $this->max_file_size ?>" />
            <input type="file" name="coders.repo.upload" />
            <button class="button button-primary" type="submit" name="coders.repo.action" value="upload">Upload</button>
    </div>
</form>
<ul class="collection">
    <?php foreach( ( ($collection = $this->collection( $this->selected ) ) !== FALSE ? $collection : array()) as $item ) : ?>
    <li class="resource inline">
        <div class="item-box">
            <?php print $this->display_resource( $item ); ?>
            <a href="<?php print \CodersRepo::url($item['public_id']) ?>" target="_blank" class="action top right dashicons-external dashicons"></a>
            <a href="<?php print $this->form_action(array(
                'coders_repo_id'=>$item['ID'],
                'coders.repo.action'=>'remove'));
            ?>" target="_self" class="action bottom right dashicons-trash dashicons"></a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>