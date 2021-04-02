<?php defined('ABSPATH') or die; ?>
<div class="container">
    
    <h1 class="header"><?php printf('%s <strong>%s</strong>!' ,
            __('Welcome','coders_artpad') ,
            $this->name ) ?></h1>
    
    <a class="button button-primary centered" href="<?php 
            print $this->pad_url ?>" target="_self"><?php
            print __('Open my Dashboard!','coders_pad') ?></a>
</div>

