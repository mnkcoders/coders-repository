<?php defined('ABSPATH') or die; ?>
<h1><?php print $this->title ?></h1>
<ul>
    <?php foreach( $this->list_items as $id => $item ) : ?>
    <li>
        <?php if( $this->is_image( $item['type'])) : ?>
        <img src="<?php
                print $this->media_url( $item['public_id'] ) ?>" alt="<?php
                print $item['name'] ?>" title="<?php
                print $item['title'] ?>" class="media" />
        <?php else :?>
        
        <?php endif;?>
        <a href="<?php
                print $this->item_url( $id ) ?>" target="_self" class="title"><?php
                print strlen($item['title']) ? $item['title'] : $item['name'];  ?></a>
    </li>
    <?php endforeach; ?>
</ul>
